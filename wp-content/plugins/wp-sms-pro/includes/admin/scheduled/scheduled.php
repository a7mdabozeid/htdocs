<?php
namespace WP_SMS;
?>

<div class="wrap wpsms-wrap wpsms-scheduled-page">
    <?php require_once WP_SMS_DIR . 'includes/templates/header.php'; ?>
    <div class="wpsms-wrap__main">
        <div class="wpsms-tab-group">
            <ul class="wpsms-tab">
                <li>
                    <a href="<?= get_admin_url() . 'admin.php?page=wp-sms-scheduled&tab=scheduled_messages' ?>" class="wpsms-nav-tab <?= $tab == 'scheduled_messages' ? 'active' : '' ?>">
                        <?php _e('Scheduled SMS', 'wp-sms-pro'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?= get_admin_url() . 'admin.php?page=wp-sms-scheduled&tab=repeating_messages' ?>" class="wpsms-nav-tab <?= $tab == 'repeating_messages' ? 'active' : '' ?>">
                        <?php _e('Repeating SMS', 'wp-sms-pro'); ?>
                    </a>
                </li>
            </ul>

        </div>
        <h2></h2>

        <?php
        echo Helper::loadTemplate('admin/quick-reply.php');
        ?>

        <form id="outbox-filter" method="get">
            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']) ?>"/>
            <input type="hidden" name="tab" value="<?php if (isset($_REQUEST['tab'])) {
                echo esc_attr($_REQUEST['tab']);
            } else {
                echo esc_attr('tab=scheduled_messages');
            } ?>"/>
            <?php $listTable->search_box(__('Search', 'wp-sms-pro'), 'search_id'); ?>
            <?php $listTable->display(); ?>
        </form>
    </div>
</div>