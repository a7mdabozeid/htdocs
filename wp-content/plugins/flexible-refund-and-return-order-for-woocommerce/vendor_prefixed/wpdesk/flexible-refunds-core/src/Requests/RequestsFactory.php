<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Requests;

use Exception;
use FRFreeVendor\WPDesk\Persistence\PersistentContainer;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses;
class RequestsFactory
{
    /**
     * @var PersistentContainer
     */
    private $settings;
    public function __construct(\FRFreeVendor\WPDesk\Persistence\PersistentContainer $settings)
    {
        $this->settings = $settings;
    }
    /**
     * @throws Exception
     */
    public function get_request(string $status)
    {
        switch ($status) {
            case \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses::REQUESTED_STATUS:
                return new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Requests\Requested($this->settings);
            case \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses::APPROVED_STATUS:
                return new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Requests\Approved($this->settings);
            case \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses::VERIFYING_STATUS:
                return new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Requests\Verifying($this->settings);
            case \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses::SHIPMENT_STATUS:
                return new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Requests\Shipment($this->settings);
            case \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses::REFUSED_STATUS:
                return new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Requests\Refused($this->settings);
            default:
                throw new \Exception(\printf(\esc_html__('Unknown request status: %s', 'flexible-refund-and-return-order-for-woocommerce'), $status));
        }
    }
}
