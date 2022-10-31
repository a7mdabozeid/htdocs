<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails;

use FRFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class RegisterEmails implements \FRFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    public function hooks()
    {
        \add_filter('woocommerce_email_classes', [$this, 'add_email_classes'], 999, 1);
    }
    public function add_email_classes($classes)
    {
        $classes[\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\EmailRefundRequested::ID] = new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\EmailRefundRequested();
        $classes[\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\EmailRefundApproved::ID] = new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\EmailRefundApproved();
        $classes[\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\EmailRefundVerifying::ID] = new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\EmailRefundVerifying();
        $classes[\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\EmailRefundShipment::ID] = new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\EmailRefundShipment();
        $classes[\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\EmailRefundRefused::ID] = new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\EmailRefundRefused();
        $classes[\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\EmailRefundRequestedAdmin::ID] = new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\EmailRefundRequestedAdmin();
        return $classes;
    }
}
