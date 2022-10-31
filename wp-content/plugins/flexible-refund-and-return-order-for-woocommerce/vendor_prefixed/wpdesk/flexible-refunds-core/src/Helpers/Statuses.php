<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers;

class Statuses
{
    const REQUESTED_STATUS = 'requested';
    const APPROVED_STATUS = 'approved';
    const SHIPMENT_STATUS = 'shipment';
    const VERIFYING_STATUS = 'verifying';
    const REFUSED_STATUS = 'refused';
    public static function get_statuses($exclude = []) : array
    {
        $statuses = [self::REQUESTED_STATUS => \esc_html__('Requested', 'flexible-refund-and-return-order-for-woocommerce'), self::APPROVED_STATUS => \esc_html__('Approved', 'flexible-refund-and-return-order-for-woocommerce'), self::SHIPMENT_STATUS => \esc_html__('Shipment', 'flexible-refund-and-return-order-for-woocommerce'), self::VERIFYING_STATUS => \esc_html__('Verifying', 'flexible-refund-and-return-order-for-woocommerce'), self::REFUSED_STATUS => \esc_html__('Refused', 'flexible-refund-and-return-order-for-woocommerce')];
        if (!empty($exclude)) {
            foreach ($exclude as $status) {
                unset($statuses[$status]);
            }
        }
        return $statuses;
    }
    /**
     * @param string $status_key
     *
     * @return string
     */
    public static function get_status_label(string $status_key) : string
    {
        $statuses = self::get_statuses();
        return $statuses[$status_key] ?? 'unknown';
    }
    /**
     * @return string[]
     */
    public static function get_all_statuses() : array
    {
        return [self::REQUESTED_STATUS, self::APPROVED_STATUS, self::SHIPMENT_STATUS, self::VERIFYING_STATUS, self::REFUSED_STATUS];
    }
}
