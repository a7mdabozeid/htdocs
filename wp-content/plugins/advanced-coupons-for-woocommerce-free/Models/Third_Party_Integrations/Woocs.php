<?php
namespace ACFWF\Models\Third_Party_Integrations;

use ACFWF\Abstracts\Abstract_Main_Plugin_Class;
use ACFWF\Helpers\Helper_Functions;
use ACFWF\Helpers\Plugin_Constants;
use ACFWF\Interfaces\Model_Interface;
use ACFWF\Models\Objects\Advanced_Coupon;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of the WPML_Support module.
 *
 * @since 4.1
 */
class Woocs implements Model_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 4.1
     * @access private
     * @var WPML_Support
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 4.1
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 4.1
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
     * @since 4.1
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
     * @since 4.1
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return WPML_Support
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants, $helper_functions);
        }

        return self::$_instance;

    }

    /**
     * Remove all filters related to currency settings when the "acfw_rest_api_context" action hook is triggered.
     * 
     * @since 4.1
     * @access public
     * 
     * @param WP_REST_Request $request Full details about the request.
     */
    public function remove_currency_setting_filters($request)
    {
        if ($request->get_header('X-ACFW-Context') === 'admin') {
            remove_all_filters('option_woocommerce_currency');
            remove_all_filters('option_woocommerce_price_thousand_sep');
            remove_all_filters('option_woocommerce_price_decimal_sep');
            remove_all_filters('option_woocommerce_price_num_decimals');
            remove_all_filters('option_woocommerce_currency_pos');
            remove_all_filters('woocommerce_currency_symbol');
            remove_all_filters('woocommerce_price_format');
        }
    }

    /**
     * Convert amount from one currency to another.
     * NOTE: the WOOCS plugin doesn't have a usable stateless function to convert a currency, so we are recreating the
     *       conversion functionality here.
     * 
     * @since 4.1
     * @access private
     * 
     * @param float $amount         Amount to convert.
     * @param string $from_currency From currency.
     * @param string $to_currency   To Currency.
     * @return float Converted amount value.
     */
    private function _convert_amount($amount, $from_currency, $to_currency)
    {
        global $WOOCS;

        $currencies = $WOOCS->get_currencies();

        if (!isset($currencies[$from_currency]) || !isset($currencies[$to_currency])) {
            return $amount;
        }

        $to_currency_data = $currencies[$to_currency];
        $rate             = $to_currency_data['rate'] / $currencies[$from_currency]['rate'];
        $value            = (float) number_format($amount * $rate, $to_currency_data['decimals'], '.', '');

        return $value;
    }

    /**
     * Convert amount to from base currency to user selected currency (or reverse).
     *
     * @since 4.1
     * @access public
     *
     * @param float $amount Amount to convert.
     * @param bool  $is_reverse Convert from user to base currency if true.
     * @return float Converted amount.
     */
    public function convert_amount_to_user_selected_currency($amount, $is_reverse = false)
    {
        global $WOOCS;

        if ($WOOCS->default_currency === $WOOCS->current_currency) {
            return $amount;
        }

        if ($is_reverse) {
            return $this->_convert_amount($amount, $WOOCS->current_currency, $WOOCS->default_currency);
        } else {
            return $this->_convert_amount($amount, $WOOCS->default_currency, $WOOCS->current_currency);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Store Credits
    |--------------------------------------------------------------------------
    */

    /**
     * Save user currency to store credits discount session.
     * 
     * @since 4.1
     * @access public
     * 
     * @param array $sc_discount Session data.
     * @return array Filtered session data.
     */
    public function save_user_currency_to_store_credits_discount_session($sc_discount)
    {
        global $WOOCS;

        $sc_discount['currency'] = $WOOCS->current_currency;
        return $sc_discount;
    }

    /**
     * Validate the store credits discount currency on cart totals calculation.
     * When the currency saved in session is different from the users currency in Woocs, then we convert the currency from
     * session to the new value, and update the session data as well.
     * 
     * @since 4.1
     * @access public
     *
     * @param array $sc_discount Session data.
     * @return float Filtered discount amount.
     */
    public function validate_user_currency_on_apply_store_credits_discount($sc_discount)
    {
        global $WOOCS;

        if (isset($sc_discount['currency']) && $sc_discount['currency'] !== $WOOCS->current_currency) {

            $amount = $this->_convert_amount($sc_discount['amount'], $sc_discount['currency'], $WOOCS->current_currency);

            if (0 >= $amount) {
                \WC()->session->set(Plugin_Constants::STORE_CREDITS_SESSION, null);
            } else {
                
                $sc_discount['amount']   = $amount;
                $sc_discount['currency'] = $WOOCS->current_currency;

                \WC()->session->set(Plugin_Constants::STORE_CREDITS_SESSION, $sc_discount);
            }
        }
        
        return $sc_discount;
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute WPML_Support class.
     *
     * @since 4.1
     * @access public
     * @inherit ACFWF\Interfaces\Model_Interface
     */
    public function run()
    {
        if (!$this->_helper_functions->is_plugin_active('woocommerce-currency-switcher/index.php')) {
            return;
        }

        add_action('acfw_rest_api_context', array($this, 'remove_currency_setting_filters'));
        add_filter('acfw_filter_amount', array($this, 'convert_amount_to_user_selected_currency'), 10, 2);
        add_filter('acfw_store_credits_discount_session', array($this, 'save_user_currency_to_store_credits_discount_session'));
        add_filter('acfw_before_apply_store_credit_discount', array($this, 'validate_user_currency_on_apply_store_credits_discount'), 10);
    }

}
