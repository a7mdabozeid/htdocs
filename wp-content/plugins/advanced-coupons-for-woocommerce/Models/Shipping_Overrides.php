<?php
namespace ACFWP\Models;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Interfaces\Initiable_Interface;
use ACFWP\Interfaces\Model_Interface;
use ACFWP\Models\Objects\Advanced_Coupon;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of extending the coupon system of woocommerce.
 * It houses the logic of handling coupon url.
 * Public Model.
 *
 * @since 2.0
 */
class Shipping_Overrides implements Model_Interface, Initiable_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 2.0
     * @access private
     * @var Shipping_Overrides
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 2.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 2.0
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

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
        $this->_constants        = $constants;
        $this->_helper_functions = $helper_functions;

        $main_plugin->add_to_all_plugin_models($this);
        $main_plugin->add_to_public_models($this);

    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 2.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Shipping_Overrides
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
    | Implementation related  functions.
    |--------------------------------------------------------------------------
     */

    /**
     * Implement shipping overrides.
     *
     * @since 2.0
     * @access public
     */
    public function implement_shipping_overrides()
    {
        foreach (\WC()->cart->get_applied_coupons() as $code) {
            $is_applied = $this->_implement_shipping_overrides_for_coupon($code);

            // don't proceed with other applied coupons if a discount was already applied.
            if ($is_applied) {
                break;
            }
        }

    }

    /**
     * Implement shipping overrides for coupon.
     *
     * @since 2.0
     * @access public
     *
     * @param string $coupon_code Coupon code.
     */
    private function _implement_shipping_overrides_for_coupon($coupon_code)
    {
        $coupon    = new Advanced_Coupon($coupon_code);
        $overrides = $coupon->get_advanced_prop('shipping_overrides', array());
        $discounts = array();

        if (!is_array($overrides) || empty($overrides)) {
            return false;
        }

        $classnames = \WC()->shipping->get_shipping_method_class_names();
        $cart_fees  = \WC()->cart->get_fees();

        // get chosen shipping methods.
        $chosen_methods = \WC()->cart->calculate_shipping();

        // detect shipping classes found in cart.
        $shipping_classes = $this->_find_shipping_classes_from_cart();

        foreach ($chosen_methods as $shipping_rate) {

            $instance_id = $shipping_rate->get_instance_id();
            $method_id   = $shipping_rate->get_method_id();

            // get the classname of the shipping method of current shipping rate.
            // added filter to allow 3rd party shipping plugins to override the classname value.
            $classname = isset($classnames[$method_id]) ? $classnames[$method_id] : '';
            $classname = apply_filters('acfwp_shipping_overrides_classname_support', $classname, $shipping_rate);

            // skip if class doesn't exist.
            if (!class_exists($classname)) {
                continue;
            }

            // get shipping method object.
            $method = new $classname($shipping_rate->get_instance_id());

            // filter the valid overrides.
            $valid_overrides = array_values(array_filter($overrides, function ($data) use ($instance_id, $method_id, $shipping_classes) {

                // return early for nozone options and just validate actual method selected method id.
                if ('nozone' === $data['shipping_zone']) {
                    return $data['shipping_method'] === $method_id;
                }

                if ($data['shipping_zone'] < 0) {
                    return false;
                }

                // check if shipping method option selected has a specific shipping class.
                if (strpos($data['shipping_method'], 'class') !== false) {
                    $temp            = explode('_class_', $data['shipping_method']);
                    $shipping_method = absint($temp[0]);
                    $shipping_class  = absint($temp[1]);

                    return $shipping_method === $instance_id && in_array($shipping_class, $shipping_classes);
                }

                // normal method under shipping zone.
                return absint($data['shipping_method']) === $instance_id;
            }));

            if (empty($valid_overrides) || !$classname) {
                continue;
            }

            // check if shipping rate is taxable or not.
            $taxable = !empty($shipping_rate->get_taxes());

            foreach ($valid_overrides as $override) {

                // calculate discount amount.
                $type   = $override['discount_type'];
                $value  = $override['discount_value'];
                $amount = \ACFWF()->Helper_Functions->calculate_discount_by_type($type, $value, $shipping_rate->get_cost());

                if ($amount <= 0) {
                    continue;
                }

                // get discount id and name.
                $fee_id   = sprintf('acfw-shipping-discount::%s::%s::%s', $coupon->get_code(), $method_id, $instance_id);
                $fee_name = apply_filters(
                    'acfw_shipping_override_fee_name',
                    __('Shipping discount', 'advanced-coupons-for-woocommerce'),
                    $method,
                    $instance_id,
                    $override,
                    $coupon
                );

                if (isset($discounts[$instance_id])) {
                    $discounts[$instance_id]['amount'] += $amount;
                } else {
                    $discounts[$instance_id] = array(
                        'id'      => $fee_id,
                        'name'    => $fee_name,
                        'amount'  => $amount,
                        'taxable' => $taxable,
                    );
                }

            }

        }

        // return false if there are no discounts to apply.
        if (empty($discounts)) {
            return false;
        }

        // add valid discounts via Fees API.
        foreach ($discounts as $instance_id => $discount) {
            \WC()->cart->fees_api()->add_fee(
                array(
                    'id'      => $discount['id'],
                    'name'    => $discount['name'],
                    'taxable' => $discount['taxable'],
                    'amount'  => $discount['amount'] * -1,
                )
            );
        }

        return true;
    }

    /**
     * Remove tax data for non-taxable shipping discounts.
     *
     * @since 2.6.1
     * @access public
     *
     * @param array  $taxes Fee taxes data.
     * @param object $fee  Fee object data in cart.
     * @return array Filtered fee taxes data.
     */
    public function remove_taxes_for_non_taxable_shipping_discounts($taxes, $fee)
    {
        if (strpos($fee->object->id, 'acfw-shipping-discount') !== false && !$fee->taxable) {
            return array();
        }

        return $taxes;
    }

    /**
     * Save shipping discount meta data on checkout process.
     *
     * @since 2.6.1
     * @access public
     *
     * @param WC_Order_Item_Fee $item    Fee item object.
     * @param string            $fee_key Loop key.
     * @param object            $fee     Fee data available in cart.
     */
    public function save_shipping_discount_metadata($item, $fee_key, $fee)
    {
        if (strpos($fee->id, 'acfw-shipping-discount') === false) {
            return;
        }

        $data = explode('::', $fee->id);
        $item->add_meta_data('acfw_fee_cart_id', $fee->id, true);
        $item->add_meta_data('acfw_fee_data', array(
            'coupon_code'          => isset($data[1]) ? $data[1] : '',
            'shipping_method_id'   => isset($data[2]) ? $data[2] : '',
            'shipping_instance_id' => isset($data[3]) ? $data[3] : '',
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Editing related  functions.
    |--------------------------------------------------------------------------
     */

    /**
     * Populate selectable shipping zones with methods data.
     *
     * @since 2.0
     * @since 2.2.3 Add support for non-shipping zone supported methods.
     * @access public
     *
     * @param array $options List of shipping zones with methods.
     * @return array Filtered list of shipping zones with methods.
     */
    public function populate_selectable_options($options = array())
    {
        // list to hold all registered methods under a shipping zone.
        $zoned_methods         = array();
        $zoned_methods_reducer = function ($c, $sm) {
            return array_merge($c, array($sm->id));
        };

        // get all shipping zones.
        $zones  = $this->_helper_functions->get_shipping_zones();
        $vl_map = function ($method) {
            return array('value' => $method->instance_id, 'label' => $method->title);
        };

        foreach ($zones as $zone) {

            $methods   = array_filter($zone['shipping_methods'], array($this, '_validate_shipping_method'));
            $options[] = array(
                'zone_id'   => $zone['zone_id'],
                'zone_name' => $zone['zone_name'],
                'methods'   => $this->_get_zone_shipping_method_options($methods),
            );
            $zoned_methods = array_reduce($methods, $zoned_methods_reducer, $zoned_methods);
        }

        // get shipping methods for "Locations not covered by your other zones".
        $zone_0        = \WC_Shipping_Zones::get_zone(0);
        $other_methods = array_filter($zone_0->get_shipping_methods(), array($this, '_validate_shipping_method'));

        if ($other_methods && !empty($other_methods)) {
            $options[] = array(
                'zone_id'   => 0,
                'zone_name' => __('Not covered locations', 'advanced-coupons-for-woocommerce'),
                'methods'   => array_values(array_map($vl_map, $other_methods)),
            );
            $zoned_methods = array_reduce($other_methods, $zoned_methods_reducer, $zoned_methods);
        }

        // get methods that doesn't support shipping zones.
        $not_zoned_methods = array_filter(\WC()->shipping()->get_shipping_methods(), function ($sm) use ($zoned_methods) {
            return !in_array($sm->id, $zoned_methods) && $this->_validate_shipping_method($sm);
        });

        // add non-zoned methods to a single option.
        if ($not_zoned_methods && !empty($not_zoned_methods)) {
            $options[] = array(
                'zone_id'   => 'nozone',
                'zone_name' => __('Non-shipping zone methods', 'advanced-coupons-for-woocommerce'),
                'methods'   => array_values(array_map(function ($m) {
                    return array('value' => $m->id, 'label' => $m->title);
                }, $not_zoned_methods)),
            );
        }

        return $options;
    }

    /**
     * Get shipping method options for a zone given its list of shipping methods.
     *
     * @since 2.3
     * @access private
     *
     * @param array $zone_methods Shipping zone list of shipping methods.
     * @return array list of shipping method options.
     */
    private function _get_zone_shipping_method_options($zone_methods)
    {
        $method_options      = array();
        $shipping_classes    = \WC()->shipping()->get_shipping_classes();
        $shippping_class_ids = array_map(function ($c) {
            return $c->term_id;
        }, $shipping_classes);

        foreach ($zone_methods as $zone_method) {

            $method_options[] = array('value' => $zone_method->instance_id, 'label' => $zone_method->title);

            if (!empty($shipping_classes) && in_array('instance-settings', $zone_method->supports)) {

                $method_classes = array_filter($shipping_classes, function ($c) use ($zone_method) {
                    $index = 'class_cost_' . $c->term_id;
                    return isset($zone_method->instance_settings[$index]);
                });

                if (empty($method_classes)) {
                    continue;
                }

                foreach ($method_classes as $class) {
                    $method_options[] = array(
                        'value' => sprintf('%s_class_%s', $zone_method->instance_id, $class->term_id),
                        'label' => sprintf('%s: %s', $zone_method->title, $class->name),
                    );
                }
            }
        }

        return $method_options;
    }

    /**
     * Sanitize shipping override data.
     *
     * @since 2.0
     * @access private
     *
     * @param array $data Shipping override data.
     * @return array Sanizied shipping override data.
     */
    private function _sanitize_shipping_override($data)
    {
        $sanitized = array();

        if ('empty' !== $data && !empty($data)) {
            foreach ($data as $key => $row) {

                $shipping_zone   = 'nozone' === $row['shipping_zone'] ? 'nozone' : absint($row['shipping_zone']);
                $sanitized[$key] = array(
                    'shipping_zone'   => $shipping_zone >= 0 ? $shipping_zone : 'nozone',
                    'shipping_method' => sanitize_text_field($row['shipping_method']),
                    'discount_type'   => sanitize_text_field($row['discount_type']),
                    'discount_value'  => (float) wc_format_decimal($row['discount_value']),
                );
            }
        }

        return $sanitized;
    }

    /**
     * Save shipping overrides.
     *
     * @since 2.0
     * @access private
     */
    private function _save_shipping_overrides($coupon_id, $overrides)
    {
        return update_post_meta($coupon_id, $this->_constants->META_PREFIX . 'shipping_overrides', $overrides);
    }

    /**
     * Validate shipping methods.
     *
     * @since 2.0
     * @since 2.2.3 Make validation less strict and add filterable list of disallowed methods.
     * @access private
     *
     * @param WC_Shipping_Method $sm Shipping method.
     * @return boolean True if valid, false otherwise.
     */
    private function _validate_shipping_method($sm)
    {
        $disallowed_methods = apply_filters('acfw_disallowed_shipping_methods_for_override', array('free_shipping'));
        return 'yes' == $sm->enabled && !in_array($sm->id, $disallowed_methods);
    }

    /*
    |--------------------------------------------------------------------------
    | Order admin related functions.
    |--------------------------------------------------------------------------
     */

    /**
     * Calculate correct order shipping total with discount.
     *
     * @deprecated 2.2.3
     *
     * @since 2.0
     * @access public
     *
     * @param bool     $calc_taxes Calculate taxes if true.
     * @param WC_Order $order      Order object.
     */
    public function order_recalculate_shipping_total_with_discount($calc_taxes, $order)
    {
    }

    /**
     * Trigger the order to recalculate totals when viewed via admin.
     *
     * @deprecated 2.2.3
     *
     * @since 2.0
     * @access public
     */
    public function admin_trigger_order_recalculate_totals()
    {
    }

    /**
     * Recalculate correct order shipping total with discount.
     *
     * @since 2.2.3
     * @access public
     */
    public function recalculate_shipping_total_with_discount()
    {
        $order_id = isset($_GET['post']) ? absint($_GET['post']) : 0;
        if (!$order_id || get_post_type($order_id) !== 'shop_order') {
            return;
        }

        // skip if order shipping values have already been recalculated.
        if (get_post_meta($order_id, 'acfw_shipping_discount_recalc', true) === 'yes') {
            return;
        }

        $order          = wc_get_order($order_id);
        $shipping_total = 0;
        $discount       = 0;

        // Sum shipping costs.
        foreach ($order->get_shipping_methods() as $shipping) {
            $shipping_total += (float) $shipping->get_total('edit');
        }

        foreach ($order->get_fees() as $fee) {
            if (
                strpos($fee->get_name(), '[shipping_discount]') !== false ||
                strpos($fee->get_meta('acfw_fee_cart_id'), 'acfw-shipping-discount') !== false
            ) {
                $discount += (float) $fee->get_total('edit');
            }
        }

        if (!$discount) {
            return;
        }

        // we add because discount value is negative already.
        $total = $shipping_total + $discount;

        // set shipping total and make sure value is not negative.
        $order->set_shipping_total($total >= 0 ? $total : 0);
        $order->add_meta_data('acfw_shipping_discount_recalc', 'yes');
        $order->save();
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX functions.
    |--------------------------------------------------------------------------
     */

    /**
     * AJAX save shipping overrides.
     *
     * @since 2.0
     * @access public
     */
    public function ajax_save_shipping_overrides()
    {
        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            $response = array('status' => 'fail', 'error_msg' => __('Invalid AJAX call', 'advanced-coupons-for-woocommerce'));
        } elseif (!current_user_can(apply_filters('acfw_ajax_save_bogo_deals', 'manage_woocommerce'))) {
            $response = array('status' => 'fail', 'error_msg' => __('You are not allowed to do this', 'advanced-coupons-for-woocommerce'));
        } elseif (!isset($_POST['coupon_id']) || !isset($_POST['overrides'])) {
            $response = array('status' => 'fail', 'error_msg' => __('Missing required post data', 'advanced-coupons-for-woocommerce'));
        } else {

            $coupon_id = absint($_POST['coupon_id']);
            $overrides = $this->_sanitize_shipping_override($_POST['overrides']);
            $check     = $this->_save_shipping_overrides($coupon_id, $overrides);

            if ($check) {
                $response = array('status' => 'success', 'message' => __('Shipping overrides have been saved successfully!', 'advanced-coupons-for-woocommerce'));
            } else {
                $response = array('status' => 'fail');
            }

        }

        @header('Content-Type: application/json; charset=' . get_option('blog_charset'));
        echo wp_json_encode($response);
        wp_die();
    }

    /**
     * AJAX clear shipping overrides.
     *
     * @since 2.0
     * @access public
     */
    public function ajax_clear_shipping_overrides()
    {
        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            $response = array('status' => 'fail', 'error_msg' => __('Invalid AJAX call', 'advanced-coupons-for-woocommerce'));
        } elseif (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'acfw_clear_shipping_overrides') || !current_user_can(apply_filters('acfw_ajax_clear_add_products_data', 'manage_woocommerce'))) {
            $response = array('status' => 'fail', 'error_msg' => __('You are not allowed to do this', 'advanced-coupons-for-woocommerce'));
        } elseif (!isset($_POST['coupon_id'])) {
            $response = array('status' => 'fail', 'error_msg' => __('Missing required post data', 'advanced-coupons-for-woocommerce'));
        } else {

            $coupon_id  = intval($_POST['coupon_id']);
            $save_check = update_post_meta($coupon_id, $this->_constants->META_PREFIX . 'shipping_overrides', array());

            if ($save_check) {
                $response = array('status' => 'success', 'message' => __('Shipping overides has been cleared successfully!', 'advanced-coupons-for-woocommerce'));
            } else {
                $response = array('status' => 'fail', 'error_msg' => __('Failed on clearing or there were no changes to save.', 'advanced-coupons-for-woocommerce'));
            }

        }

        @header('Content-Type: application/json; charset=' . get_option('blog_charset'));
        echo wp_json_encode($response);
        wp_die();
    }

    /**
     * Find shipping classes from cart shipping packages.
     *
     * @since 2.4
     * @access private
     *
     * @return array List of detected shipping classes.
     */
    private function _find_shipping_classes_from_cart()
    {
        $shipping_classes = array();
        $packages         = \WC()->cart->get_shipping_packages();

        foreach ($packages as $package) {

            foreach ($package['contents'] as $item_id => $values) {

                if ($values['data']->needs_shipping()) {
                    $found_class = $values['data']->get_shipping_class_id();
                    if ($found_class) {
                        $shipping_classes[] = $found_class;
                    }

                }

            }

        }

        return $shipping_classes;
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 2.0
     * @access public
     * @implements ACFWP\Interfaces\Initializable_Interface
     */
    public function initialize()
    {
        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::SHIPPING_OVERRIDES_MODULE)) {
            return;
        }

        add_action('wp_ajax_acfw_save_shipping_overrides', array($this, 'ajax_save_shipping_overrides'));
        add_action('wp_ajax_acfw_clear_shipping_overrides', array($this, 'ajax_clear_shipping_overrides'));
    }

    /**
     * Execute Shipping_Overrides class.
     *
     * @since 2.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::SHIPPING_OVERRIDES_MODULE)) {
            return;
        }

        add_action('woocommerce_cart_calculate_fees', array($this, 'implement_shipping_overrides'));
        add_filter('woocommerce_cart_totals_get_fees_from_cart_taxes', array($this, 'remove_taxes_for_non_taxable_shipping_discounts'), 10, 2);
        add_action('woocommerce_checkout_create_order_fee_item', array($this, 'save_shipping_discount_metadata'), 10, 3);
        add_filter('acfw_shipping_override_selectable_options', array($this, 'populate_selectable_options'), 10, 1);
        add_action('admin_init', array($this, 'recalculate_shipping_total_with_discount'), 10);
    }

}
