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
 * @since 3.3
 */
class Percent_Discount_Cap implements Model_Interface, Initiable_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 3.3
     * @access private
     * @var Percent_Discount_Cap
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 3.3
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 3.3
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

    /**
     * Property that houses all coupon codes with discount caps feature.
     * 
     * @since 3.3
     * @access private
     * @var array
     */
    private $_coupon_discount_caps = array();

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 3.3
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
    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 3.3
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Percent_Discount_Cap
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
    | Admin
    |--------------------------------------------------------------------------
     */

    /**
     * Display percentage discount field under general tab.
     * 
     * @since 3.3
     * @access public
     * 
     * @param int       $coupon_id Coupon ID.
     * @param WC_Coupon $coupon    Coupon object.
     */
    public function display_percentage_discount_cap_field($coupon_id, $coupon)
    {
        $is_percent_type = 'percent' === $coupon->get_discount_type('edit');

        woocommerce_wp_text_input(
            array(
                'id'          => $this->_constants->META_PREFIX . 'percentage_discount_cap',
                'label'       => __( 'Percentage Discount Cap', 'woocommerce' ),
                'description' => __( 'The maximum discount amount value allowed for this percentage type coupon.', 'advanced-coupons-for-woocommerce' ),
                'data_type'   => 'price',
                'desc_tip'    => true,
                'value'       => $coupon->get_meta($this->_constants->META_PREFIX . 'percentage_discount_cap', true, 'edit')
            )
        );
    }

    /**
     * Save coupon data.
     *
     * @since 3.3
     * @access public
     *
     * @param int             $coupon_id Coupon ID.
     * @param Advanced_Coupon $coupon    Advanced coupon object.
     */
    public function save_percentage_discount_cap_value($coupon_id, $coupon)
    {
        $meta_name = $this->_constants->META_PREFIX . 'percentage_discount_cap';

        if ('percent' !== $coupon->get_discount_type('edit') || !isset($_POST[$meta_name])) {
            return;
        }

        $value = $_POST[$meta_name] ? \ACFWF()->Helper_Functions->sanitize_price($_POST[$meta_name]) : $_POST[$meta_name];

        update_post_meta($coupon_id, $this->_constants->META_PREFIX . 'percentage_discount_cap', $value);
    }

    /*
    |--------------------------------------------------------------------------
    | Frontend implementation
    |--------------------------------------------------------------------------
     */

    /**
     * Register the discount cap values for all percentage coupons applied to the cart before totals are calculated.
     * 
     * @since 3.3
     * @access public
     * 
     * @param WC_Cart $cart Cart object.
     */
    public function register_applied_coupons_discount_caps($cart)
    {
        foreach ($cart->get_coupons() as $coupon) {

            if ('percent' !== $coupon->get_discount_type()) {
                continue;
            }

            $discount_cap = apply_filters('acfw_filter_amount', (float) $coupon->get_meta($this->_constants->META_PREFIX . 'percentage_discount_cap', true));

            if ($discount_cap) {
                $this->_coupon_discount_caps[$coupon->get_code()] = \wc_add_number_precision($discount_cap);
            }
        }
    }

    /**
     * Apply discount cap for a given percentage coupon during coupon's discount amount calculation for each applicable items in the cart.
     * This function is called in a loop of cart items that are valid for the coupon. The discount cap registered for the coupon is deducted
     * every time this function is called. When the discount cap is lesser than the calculated discount value for an item, then the discount
     * cap value will be used as the discount amount for that item.
     * 
     * @since 3.3
     * @access public
     * 
     * @param float     $discount           Coupon discount amount for cart item.
     * @param float     $discounting_amount Amount that needs to be discounted.
     * @param array     $cart_item          Cart item data.
     * @param bool      $single             True if discounting a single qty item, false if its the line.
     * @param WC_Coupon $coupon             Coupon object.
     */
    public function apply_percentage_coupon_discount_cap($discount, $discounting_amount, $cart_item, $single, $coupon) 
    {
        // skip if coupon is not of 'percent' type, or when a discount cap value is not available for the coupon.
        if ('percent' !== $coupon->get_discount_type() || !isset($this->_coupon_discount_caps[$coupon->get_code()])) {
            return $discount;
        }

        $precise_discount = \wc_add_number_precision($discount);
        $discount_cap     = $this->_coupon_discount_caps[$coupon->get_code()];

        // use the discount cap value as the discount amount for the item when it's lesser or equal to the calculated discount amount.
        $precise_discount = $discount_cap <= $precise_discount ? $discount_cap : $precise_discount;

        // deduct the discount amount from the registered discount cap or set discount cap's value to zero when it's value is already on the negatives.
        if (0 >= $this->_coupon_discount_caps[$coupon->get_code()]) {
            $this->_coupon_discount_caps[$coupon->get_code()] = 0;
        } else {
            $this->_coupon_discount_caps[$coupon->get_code()] -= $precise_discount;
        }

        return \wc_remove_number_precision($precise_discount);
    }


    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 3.3
     * @access public
     * @implements ACFWP\Interfaces\Initializable_Interface
     */
    public function initialize()
    {
    }

    /**
     * Execute Percent_Discount_Cap class.
     *
     * @since 3.3
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        add_action('woocommerce_coupon_options', array($this, 'display_percentage_discount_cap_field'), 10, 2);
        add_action('acfw_before_save_coupon', array($this, 'save_percentage_discount_cap_value'), 10, 2);

        add_action('woocommerce_before_calculate_totals', array($this, 'register_applied_coupons_discount_caps'));
        add_filter('woocommerce_coupon_get_discount_amount', array($this, 'apply_percentage_coupon_discount_cap'), 10, 5);
    }

}
