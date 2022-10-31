<?php
namespace ACFWP\Models\Virtual_Coupon;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Interfaces\Model_Interface;
use ACFWP\Models\Objects\Advanced_Coupon;
use ACFWP\Models\Objects\Virtual_Coupon;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of extending the coupon system of woocommerce.
 * It houses the logic of handling coupon url.
 * Public Model.
 *
 * @since 3.0
 */
class Frontend implements Model_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 3.0
     * @access private
     * @var Frontend
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 3.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 3.0
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

    /**
     * Property that holds the virtual coupon codes that are marked as invalid.
     *
     * @since 3.0
     * @access private
     * @var array
     */
    private $_invalid_virtual_codes = array();

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 3.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     */
    public function __construct(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        $this->_constants        = $constants;
        $this->_helper_functions = $helper_functions;

        $main_plugin->add_to_all_plugin_models($this);
        $main_plugin->add_to_public_models($this);

    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 3.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Frontend
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants, $helper_functions);
        }

        return self::$_instance;

    }

    /*
    |--------------------------------------------------------------------------
    | Implementation.
    |--------------------------------------------------------------------------
     */

    /**
     * Filter woocommerce_get_coupon_id_from_code function to get the coupon ID
     * if the given code is for a virtual coupon.
     *
     * @since 3.0
     * @since 3.5.1 Make sure the virtual coupon object can be properly fetched for installs with persistent object cache setup.
     * @access public
     *
     * @param int    $coupon_id   Coupon ID.
     * @param string $coupon_code Coupon code submitted in form.
     * @return int Filtered coupon ID.
     */
    public function get_coupon_id_for_virtual_coupon($coupon_id, $coupon_code)
    {
        // Validate context that we are running this in the frontend, sessions are available, and that the virtual coupon is not invalid.
        if (is_admin() || !is_object(\WC()->session) || isset($this->_invalid_virtual_codes[$coupon_code])) {
            return $coupon_id;
        }

        $data           = $coupon_id ? $this->_get_virtual_coupon_data_from_session($coupon_id) : $this->_get_virtual_coupon_data_from_session_by_code($coupon_code);
        $virtual_coupon = $data && $data['coupon_code'] === $coupon_code ? new Virtual_Coupon($data['id']) : Virtual_Coupon::create_from_coupon_code($coupon_code);

        if ($virtual_coupon->get_id() && $virtual_coupon->get_coupon_code() === $coupon_code) {

            // set the value to cache so we don't need to refetch the coupon ID.
            if (!$coupon_id) {
                 \wp_cache_set(
                    \WC_Cache_Helper::get_cache_prefix('coupons') . 'coupon_id_from_code_' . $coupon_code,
                    array($virtual_coupon->get_prop('coupon_id')),
                    'coupons'
                );
            }

            $coupon_id = $virtual_coupon->get_prop('coupon_id');
            
            // validate virtual coupon.
            if ($virtual_coupon->is_valid()) {

                // save virtual coupon to session.
                $check = $this->_add_virtual_coupon_to_session($coupon_id, $virtual_coupon);

                /**
                 * When virtual coupon is valid but was not added successfully to session, it means another virtual coupon
                 * with the same main coupon was already applied. We need to set this as an invalid virtual coupon code and
                 * set the error code to already applied enum value.
                 */
                if (!$check) {
                    $this->_invalid_virtual_codes[$coupon_code] = \WC_Coupon::E_WC_COUPON_ALREADY_APPLIED;
                }

            } else {
                $this->_invalid_virtual_codes[$coupon_code] = $virtual_coupon->get_error_code();
                $this->remove_unused_virtual_coupons_from_session_by_id($coupon_id);
            }
        }

        return $coupon_id;
    }

    /**
     * Override coupon code with virtual coupon when WC_Coupon object is loaded.
     * This function runs after `get_coupon_id_for_virtual_coupon`.
     *
     * @since 3.0
     * @access public
     *
     * @param WC_Coupon $coupon
     */
    public function override_coupon_code_with_virtual_coupon($coupon)
    {
        if (is_admin() || !is_object(\WC()->session) || !(bool) $coupon->get_meta('_acfw_enable_virtual_coupons')) {
            return;
        }

        /**
         * If the invalid virtual codes is not empty, then it means that the most recent virtual coupon code applied
         * has the same main coupon for the one already applied in the cart. We need to set the coupon code property of
         * WC_Coupon here so we can customize the error message in the validation check function.
         */
        if (!empty($this->_invalid_virtual_codes)) {
            $invalid_code_keys = array_keys($this->_invalid_virtual_codes);
            $invalid_code      = end($invalid_code_keys);

            if (in_array($invalid_code, \WC()->cart->get_applied_coupons())) {
                $coupon->set_code($invalid_code);
                return;
            }
        }

        $data           = $this->_get_virtual_coupon_data_from_session($coupon->get_id());
        $virtual_coupon = $data ? new Virtual_Coupon($data) : null;

        // check if virtual coupon data is present in session.
        if ($virtual_coupon && $virtual_coupon->get_id()) {

            // set coupon code to the virtual coupon code.
            $coupon->set_code($virtual_coupon->get_coupon_code());

            // set date created if available.
            if ($virtual_coupon->get_date_created() instanceof \WC_DateTime) {
                $coupon->set_date_created($virtual_coupon->get_date_created());
            }

            // set date expire if available.
            if ($virtual_coupon->get_date_expire() instanceof \WC_DateTime) {
                $coupon->set_date_expires($virtual_coupon->get_date_expire());
            }

            $coupon->apply_changes();
        }
    }

    /**
     * Filter the `woocommerce_coupon_is_valid` hook throw exception error objects for invalid virtual coupons.
     *
     * @since 3.0
     * @access public
     *
     * @param bool      $return Filter return value.
     * @param WC_Coupon $coupon WC_Coupon object.
     * @return bool True if valid, false otherwise.
     */
    public function filter_is_coupon_valid_for_virtual_coupon($return, $coupon)
    {
        $coupon = new Advanced_Coupon($coupon);
        $data   = $this->_get_virtual_coupon_data_from_session($coupon->get_id());

        if ($coupon->get_advanced_prop('enable_virtual_coupons')) {

            $post_object = get_post($coupon->get_id());

            if (
                'publish' !== $post_object->post_status
                || strtolower($post_object->post_title) === strtolower($coupon->get_code()) // restrict main coupon code to be applied.
                 || !$this->_validate_coupon_date_created($coupon)
            ) {
                throw new \Exception($coupon->get_coupon_error(\WC_Coupon::E_WC_COUPON_NOT_EXIST));
            }

            // explicitly return error when virtual coupon with same main coupon already applied.
            if (isset($this->_invalid_virtual_codes[$coupon->get_code()])) {
                throw new \Exception($coupon->get_coupon_error($this->_invalid_virtual_codes[$coupon->get_code()]));
            }
        }

        // explicitly return error when the virtual coupon code is the one invalidated.
        if ($data && isset($this->_invalid_virtual_codes[$data['coupon_code']])) {
            throw new \Exception($coupon->get_coupon_error($this->_invalid_virtual_codes[$coupon->get_code()]));
        }

        return $return;
    }

    /**
     * Compare coupon date create value with current date value.
     * If date create value is set for future, then we should invalidate the coupon.
     *
     * @since 3.0
     * @access private
     *
     * @param WC_Coupon $coupon Coupon object.
     * @return bool True if valid, false otherwise.
     */
    private function _validate_coupon_date_created($coupon)
    {
        $now_date = new \WC_Datetime("now", new \DateTimeZone(\ACFWP()->Helper_Functions->get_site_current_timezone()));
        return $coupon->get_date_created() && $coupon->get_date_created() <= $now_date;
    }

    /**
     * Override the ACFWP scheduler date expire value.
     *
     * @since 3.0
     * @access public
     *
     * @param string          $date Date value in mysql format.
     * @param string          $prop Property name.
     * @param Advanced_Coupon $coupon Coupon object.
     */
    public function override_scheduler_date_expire($date, $prop, $coupon)
    {
        // skip if prop is not for schedule end.
        if ('schedule_end' !== $prop) {
            return $date;
        }

        $data           = $this->_get_virtual_coupon_data_from_session($coupon->get_id());
        $virtual_coupon = $data ? new Virtual_Coupon($data) : null;

        if ($virtual_coupon && $virtual_coupon->get_id() && $virtual_coupon->get_date_expire() instanceof \WC_DateTime) {
            $date = $virtual_coupon->get_date_expire();
        }

        return $date;
    }

    /*
    |--------------------------------------------------------------------------
    | Session handlers.
    |--------------------------------------------------------------------------
     */

    /**
     * Add virtual coupon data to session.
     *
     * @since 3.0
     * @access private
     */
    private function _add_virtual_coupon_to_session($coupon_id, Virtual_Coupon $virtual_coupon)
    {
        if (!is_object(\WC()->session)) {
            return false;
        }

        $in_session = \WC()->session->get('acfw_virtual_coupons', array());
        $data       = isset($in_session[$coupon_id]) ? $in_session[$coupon_id] : null;

        // invalidate if a virtual coupon was already applied that shares the same main coupon.
        if ($data && $virtual_coupon->get_code() !== $data['virtual_coupon']) {
            return false;
        }

        // get virtual coupon session data.
        $new_session_data = $virtual_coupon->get_data_for_session();

        // return as valid when trying to apply virtual coupon and session data has not changed.
        if ($data && $virtual_coupon->get_code() === $data['virtual_coupon'] && $data === $new_session_data) {
            return true;
        }

        $in_session[$coupon_id] = $new_session_data;
        \WC()->session->set('acfw_virtual_coupons', $in_session);
        return true;
    }

    /**
     * Remove virtual coupon from session.
     *
     * @since 3.0
     * @access private
     */
    private function _remove_virtual_coupon_from_session($coupon_id)
    {
        if (!is_object(\WC()->session)) {
            return;
        }

        $in_session = \WC()->session->get('acfw_virtual_coupons', array());
        unset($in_session[$coupon_id]);

        \WC()->session->set('acfw_virtual_coupons', $in_session);
    }

    /**
     * Get virtual coupon from session.
     *
     * @since 3.0
     * @access private
     *
     * @param int $coupon_id
     * @return array Virtual coupon session data.
     */
    private function _get_virtual_coupon_data_from_session($coupon_id)
    {
        if (!is_object(\WC()->session)) {
            return null;
        }

        $in_session = \WC()->session->get('acfw_virtual_coupons', array());
        $data       = isset($in_session[$coupon_id]) ? $in_session[$coupon_id] : false;

        return $data ? $data : null;
    }

    /**
     * Get virtual coupon data from session by a given coupon code.
     *
     * @since 3.0
     * @access private
     *
     * @param int $coupon_code Coupon code
     * @return array Virtual coupon session data.
     */
    private function _get_virtual_coupon_data_from_session_by_code($coupon_code)
    {
        if (!is_object(\WC()->session)) {
            return null;
        }

        $in_session     = \WC()->session->get('acfw_virtual_coupons', array());
        $virtual_coupon = null;

        if (empty($in_session)) {
            return null;
        }

        $data = array_filter($in_session, function ($d) use ($coupon_code) {
            return $d['coupon_code'] === $coupon_code;
        });

        return !empty($data) ? current($data) : null;
    }

    /**
     * Remove unused virtual coupons from session.
     *
     * @since 3.0
     * @since 3.5.1 Remove main coupon code from list of applied coupons too. Prevent WC to re-apply virtual coupon code during removal.
     * @access public
     */
    public function remove_unused_virtual_coupons_from_session($coupon_code)
    {
        if (!is_object(\WC()->session)) {
            return;
        }

        $in_session = \WC()->session->get('acfw_virtual_coupons', array());

        if (!empty($in_session)) {
            $in_session = array_filter($in_session, function ($d) use ($coupon_code) {
                return $d['coupon_code'] !== $coupon_code;
            });
        }

        \WC()->session->set('acfw_virtual_coupons', !empty($in_session) ? $in_session : null);

        /**
         * For some reason WC keeps the main coupon code when trying to remove the virtual coupon code.
         * We need to make sure that the main coupon code from the list of applied coupons to prevent unneccessary error messages.
         * 
         * @since 3.5.1
         */
        $applied_coupons = WC()->cart->get_applied_coupons();
        foreach ($applied_coupons as $key => $applied_coupon) {
            if (strpos($coupon_code, $applied_coupon) !== false) {
                unset($applied_coupons[$key]);
            }
        }

        \WC()->cart->set_applied_coupons($applied_coupons);

        // Remove the filter to prevent WC to re-apply the virtual coupon code when trying to remove it.
        remove_filter('woocommerce_get_coupon_id_from_code', array($this, 'get_coupon_id_for_virtual_coupon'), 10, 2);
    }

    /**
     *
     * Clear virtual coupons session.
     *
     * @since 3.1.2
     * @access public
     */
    public function clear_virtual_coupons_session()
    {
        if (!is_object(\WC()->session)) {
            return;
        }

        $in_session = \WC()->session->get('acfw_virtual_coupons', array());

        if (!empty($in_session)) {
            \WC()->session->set('acfw_virtual_coupons', null);
        }
    }

    /**
     * Remove unused virtual coupons from session by ID.
     *
     * @since 3.1.1
     * @since 3.5.1 Add coupon code parameter which when set will only remove the virtual coupon data from session when the coupon code matches.
     * @access public
     *
     * @param int $coupon_id Coupon ID.
     */
    public function remove_unused_virtual_coupons_from_session_by_id($coupon_id, $coupon_code = false)
    {
        if (!is_object(\WC()->session)) {
            return;
        }

        $in_session = \WC()->session->get('acfw_virtual_coupons', array());

        if (isset($in_session[$coupon_id])) {
            if (!$coupon_code || $in_session[$coupon_id]['coupon_code'] === $coupon_code) {
                unset($in_session[$coupon_id]);
            }
        }

        \WC()->session->set('acfw_virtual_coupons', !empty($in_session) ? $in_session : null);
    }

    /**
     * Remove unused virtual coupons from session when the applied coupon is invalid.
     *
     * @since 3.1.1
     * @since 3.5.1 Remove the virtual coupon data from session only when a valid error code is provided.
     * @access public
     *
     * @param string         $message    Error message.
     * @param int            $error_code Error code.
     * @param WC_Coupon|null $coupon     Coupon object.
     * @return string Filtered error message.
     */
    public function remove_unused_virtual_coupons_from_session_on_coupon_error($message, $error_code, $coupon)
    {
        if ($error_code && $coupon) {
            $this->remove_unused_virtual_coupons_from_session_by_id($coupon->get_id(), $coupon->get_code());
        }
        
        return $message;
    }

    /*
    |--------------------------------------------------------------------------
    | Checkout process.
    |--------------------------------------------------------------------------
     */

    /**
     * Save virtual coupons applied in order to relative line item meta.
     *
     * @since 3.0
     * @access public
     *
     * @param int      $order_id    Order ID.
     * @param array    $posted_data Posted data from checkout form.
     * @param WC_Order $order       Order object.
     */
    public function save_virtual_coupons_data_to_order($order_id, $posted_data, $order)
    {
        // clear session data.
        if (is_object(\WC()->session)) {
            \WC()->session->set('acfw_virtual_coupons', null);
        }

        $coupons = $order->get_items('coupon'); // array of WC_Order_Item_Coupon

        foreach ($coupons as $coupon) {
            $virtual_coupon = Virtual_Coupon::create_from_coupon_code($coupon->get_code());

            if (!$virtual_coupon->get_id()) {
                continue;
            }

            // update virtual coupon status to used.
            $virtual_coupon->set_prop('status', 'used');
            $virtual_coupon->save();

            // save virtual coupon data to coupon order line item meta.
            $coupon->update_meta_data($this->_constants->VIRTUAL_COUPONS_META_PREFIX . 'data', $virtual_coupon->get_data_for_session());
            $coupon->update_meta_data($this->_constants->VIRTUAL_COUPONS_META_PREFIX . 'id', $virtual_coupon->get_id());
            $coupon->update_meta_data($this->_constants->VIRTUAL_COUPONS_META_PREFIX . 'main_id', $virtual_coupon->get_prop('coupon_id'));
            $coupon->save_meta_data();

            do_action('acfwp_virtual_coupon_used_in_order', $virtual_coupon->get_coupon_code(), $order_id, $virtual_coupon, $order);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute Frontend class.
     *
     * @since 3.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::VIRTUAL_COUPONS_MODULE)) {
            add_action('woocommerce_coupon_loaded', array($this, 'clear_virtual_coupons_session'), 1);
            return;
        }

        add_filter('woocommerce_get_coupon_id_from_code', array($this, 'get_coupon_id_for_virtual_coupon'), 10, 2);
        add_action('woocommerce_coupon_loaded', array($this, 'override_coupon_code_with_virtual_coupon'));
        add_filter('woocommerce_coupon_is_valid', array($this, 'filter_is_coupon_valid_for_virtual_coupon'), 10, 2);
        add_filter('acfw_get_coupon_schedule_date', array($this, 'override_scheduler_date_expire'), 10, 3);
        add_action('woocommerce_removed_coupon', array($this, 'remove_unused_virtual_coupons_from_session'));
        add_action('woocommerce_checkout_order_processed', array($this, 'save_virtual_coupons_data_to_order'), 10, 3);
        add_action('woocommerce_coupon_error', array($this, 'remove_unused_virtual_coupons_from_session_on_coupon_error'), 10, 3);
    }

}
