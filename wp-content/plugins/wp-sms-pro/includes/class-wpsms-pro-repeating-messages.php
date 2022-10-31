<?php

namespace WP_SMS\Pro;

use WP_SMS\Pro\Install;
use DateTime;
use DateTimeImmutable;
use DateInterval;

class RepeatingMessages
{
    const SEND_CALLBACK_HOOK_NAME  = 'wpsms_repeating_message_occurrence';
    const SCHEDULE_BASE_NAME       = 'wpsms_repeating_message_interval';

    /**
     * Register hooks needed for repeating messages
     *
     * @return void
     */
    public static function init()
    {
        self::registerSendHook();
        self::rescheduleMessages();
    }

    /**
     * Register callback hook for sending repeating messages
     *
     * @return void
     */
    private static function registerSendHook()
    {
        global $sms;

        add_action(self::SEND_CALLBACK_HOOK_NAME, function (int $messageId) use ($sms) {
            $record = self::getMessageById($messageId);

            if (!$record) {
                return;
            }

            $sms->from  = $record->sender;
            $sms->to    = $record->recipient;
            $sms->msg   = $record->message;
            $sms->media = $record->media;
            $sms->SendSMS();
        }, 10, 4);
    }

    /**
     * Register recurrence schedule for a message
     *
     * @param integer $messageId
     * @param integer $intervalInSeconds
     * @return string $scheduleName
     */
    private static function registerRecurrenceSchedule(int $messageId, int $intervalInSeconds)
    {
        $scheduleName = self::SCHEDULE_BASE_NAME.'_'. $messageId ;
        add_filter('cron_schedules', function ($schedules) use ($scheduleName, $intervalInSeconds, $messageId) {
            $schedules[$scheduleName] = [
                'interval' => $intervalInSeconds,
                'display'  => sprintf(__('WPSMS repeating message number %u', 'wp-sms-pro'), $messageId),
            ];

            return $schedules;
        });

        return $scheduleName;
    }

    /**
     * Reschedule ongoing messages
     *
     * @return void
     */
    private static function rescheduleMessages()
    {
        global $wpdb;

        $now = time();
        $tableName  = $wpdb->prefix . Install::TABLE_REPEATING;
        $ongoingMessages = $wpdb->get_results("SELECT * FROM {$tableName} WHERE (`ends_at` IS NULL OR `ends_at` > {$now})");

        foreach ($ongoingMessages as $message) {
            // Only reschedule if next occurrence is before ending time OR if the message is supposed to repeat forever
            try {
                $intervalInSeconds = (new DateTime("+{$message->interval} {$message->interval_unit}"))->getTimestamp() - time();
            } catch (\Throwable $e) {
                continue;
            }

            if (
                time() + $intervalInSeconds < (int) $message->ends_at
                || is_null($message->ends_at)
            ) {
                self::registerRecurrenceSchedule($message->ID, $intervalInSeconds);
            }
        }
    }

    /**
     * Add a new repeating message
     *
     * @param DateTime $start
     * @param DateTime|null $endDate
     * @param integer $interval
     * @param string $intervalUnit
     * @param string $sender
     * @param string $message
     * @param array $recipient
     * @param array $media
     * @return void
     */
    public static function add(
        DateTime $start,
        DateTime $endDate = null,
        int $interval,
        string $intervalUnit,
        string $sender,
        string $message,
        array $recipient,
        array $media = []
    ) {
        global $wpdb;

        // 1. Record the data
        $wpdb->insert(
            $wpdb->prefix . Install::TABLE_REPEATING,
            [
                'sender'        => $sender,
                'message'       => $message,
                'recipient'     => serialize($recipient),
                'media'         => serialize($media),
                'starts_at'     => $start->getTimeStamp(),
                'ends_at'       => (isset($endDate) ? $endDate->getTimeStamp() : null),
                'interval'      => $interval,
                'interval_unit' => $intervalUnit,
            ]
        );

        $id = (int) $wpdb->insert_id;

        // 2. Register message event
        $intervalInSeconds = (new DateTime("+{$interval} {$intervalUnit}"))->getTimestamp() - time();
        $scheduleName = self::registerRecurrenceSchedule($id, $intervalInSeconds);
        wp_schedule_event($start->getTimeStamp(), $scheduleName, self::SEND_CALLBACK_HOOK_NAME, [$id], true);
    }

    /**
     * Get single repeating message database record
     *
     * @param integer $id
     * @return object|null null on failure
     */
    public static function getMessageById(int $id)
    {
        global $wpdb;

        $tableName  = $wpdb->prefix . Install::TABLE_REPEATING;
        $record    = $wpdb->get_row("SELECT * FROM {$tableName} WHERE id='{$id}'", OBJECT);

        if (is_null($record)) {
            return;
        }

        $record = self::castDatabaseRecordAttributes($record);

        return $record;
    }

    /**
     * Get all repeating messages database records
     *
     * @return array
     */
    public static function getAllRepeatingMessage()
    {
        global $wpdb;

        $tableName = $wpdb->prefix . Install::TABLE_REPEATING;
        $results   = $wpdb->get_results("SELECT * FROM {$tableName}");

        return array_map([self::class, 'castDatabaseRecordAttributes'], $results);
    }

    /**
     * Cast a database record attributes to proper types
     *
     * @param object $record
     * @return object normalized $record
     */
    private static function castDatabaseRecordAttributes(object $record)
    {
        $record->ID              = (int) $record->ID;
        $record->starts_at       = (int) $record->starts_at;
        $record->interval        = (int) $record->interval;
        $record->ends_at         = (int) $record->ends_at ?: $record->ends_at;
        $record->recipient       = unserialize($record->recipient);
        $record->media           = unserialize($record->media);

        return $record;
    }

    /**
     * Delete a repeating message record in database
     *
     * Subsequently message will stop being rescheduled
     *
     * @param integer $messageId
     * @return void
     */
    public static function deleteMessageById(int $messageId)
    {
        global $wpdb;

        try {
            //* Deleting message from database will be enough, but just to be sure:
            $record = self::getMessageById($messageId);

            if (!$record) {
                return;
            }

            $result = wp_clear_scheduled_hook(
                self::SEND_CALLBACK_HOOK_NAME,
                [ $record->ID ],
                true
            );
        } finally {
            $tableName  = $wpdb->prefix . Install::TABLE_REPEATING;
            return $wpdb->delete($tableName, ['ID' => $messageId]);
        }
    }


    /**
     * Update an existing repeating-message in database
     *
     * @param integer $messageId
     * @param array $newAttributes array containing attributes that need to be updated
     *     $newAttributes = [
     *         'sender'          => string|optional,
     *         'message'         => string|optional,
     *         'media'           => array|optional,
     *         'recipient'       => array|optional,
     *         'ends_at'         => integer|optional,
     *         'repeat_interval' => integer|optional
     *     ];
     *
     * @return int|false
     */
    public static function updateMessageById(int $messageId, array $newAttributes)
    {
        global $wpdb;

        $tableName  = $wpdb->prefix . Install::TABLE_REPEATING;
        return $wpdb->update(
            $tableName,
            $newAttributes,
            [ 'ID' => $messageId ]
        );
    }
}

RepeatingMessages::init();
