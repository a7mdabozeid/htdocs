<?php
namespace ACFWP\Legacy;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the methods for the Scheduler module that are now deprecated.
 * Public Model.
 *
 * @since 2.0
 */
class Legacy_Scheduler
{
    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 2.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     */
    public function __construct(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
    }

    /*
    |--------------------------------------------------------------------------
    | Deprecated Implementation.
    |--------------------------------------------------------------------------
     */

    /**
     * Implement coupon schedule start feature.
     *
     * @since 2.0
     * @access public
     *
     * @param bool      $return Filter return value.
     * @param WC_Coupon $coupon WC_Coupon object.
     * @return bool True if valid, false otherwise.
     */
    public function implement_coupon_schedule_start($return, $coupon)
    {
        wc_deprecrated_function('ACFWP\Models\Scheduler::' . __FUNCTION__, '3.5', 'ACFWF\Models\Scheduler::' . __FUNCTION__);
        return $return;
    }

    /**
     * Implement coupon schedule start feature.
     *
     * @since 2.0
     * @access public
     *
     * @param bool      $return Filter return value.
     * @param WC_Coupon $coupon WC_Coupon object.
     * @return bool True if valid, false otherwise.
     */
    public function implement_coupon_schedule_expire($return, $coupon)
    {
        wc_deprecrated_function('ACFWP\Models\Scheduler::' . __FUNCTION__, '3.5', 'ACFWF\Models\Scheduler::' . __FUNCTION__);
        return $return;
    }

    /**
     * Disable WC default check for coupon expiry on frontend.
     *
     * @since 2.0
     * @access public
     */
    public function disable_wc_default_coupon_expiry_check()
    {
        wc_deprecrated_function('ACFWP\Models\Scheduler::' . __FUNCTION__, '3.5', 'ACFWF\Models\Scheduler::' . __FUNCTION__);
    }

    /**
     * Scheduler input field callback method.
     * This method is based on woocommerce_wp_text_input function.
     *
     * @since 2.1
     * @access public
     *
     * @param array $field Field data.
     */
    public function scheduler_input_field($field)
    {
        wc_deprecrated_function('ACFWP\Models\Scheduler::' . __FUNCTION__, '3.5', 'ACFWF\Models\Scheduler::' . __FUNCTION__);
    }
}
