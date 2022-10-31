<?php
namespace LPFW\Models\Third_Party_Integrations;

use LPFW\Abstracts\Abstract_Main_Plugin_Class;
use LPFW\Helpers\Helper_Functions;
use LPFW\Helpers\Plugin_Constants;
use LPFW\Interfaces\Model_Interface;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of the WPML_Support module.
 *
 * @since 1.0
 */
class WPML_Support implements Model_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 1.0
     * @access private
     * @var WPML_Support
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 1.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 1.0
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
     * @since 1.0
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
     * @since 1.0
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
     * Register setting fields as translateable string in one package (domain).
     *
     * @since 1.0
     * @access public
     *
     * @param array $translate List of translatable options.
     * @return array Filtered list of translatable options.
     */
    public function register_translatable_setting_strings($translate)
    {
        $translate = array_merge($translate, array(
            array(
                'value' => get_option($this->_constants->POINTS_NAME),
                'name'  => $this->_constants->POINTS_NAME,
                'label' => __('Loyalty Program: Points name', 'loyalty-program-for-woocommerce'),
                'type'  => 'LINE',
            ),
            array(
                'value' => get_option($this->_constants->POINTS_EARN_CART_MESSAGE),
                'name'  => $this->_constants->POINTS_EARN_CART_MESSAGE,
                'label' => __('Loyalty Program: Points to earn message in cart', 'loyalty-program-for-woocommerce'),
                'type'  => 'AREA',
            ),
            array(
                'value' => get_option($this->_constants->POINTS_EARN_CHECKOUT_MESSAGE),
                'name'  => $this->_constants->POINTS_EARN_CHECKOUT_MESSAGE,
                'label' => __('Loyalty Program: Points to earn message in checkout', 'loyalty-program-for-woocommerce'),
                'type'  => 'AREA',
            ),
            array(
                'value' => get_option($this->_constants->POINTS_EARN_PRODUCT_MESSAGE),
                'name'  => $this->_constants->POINTS_EARN_PRODUCT_MESSAGE,
                'label' => __('Loyalty Program: Points to earn message in single product page', 'loyalty-program-for-woocommerce'),
                'type'  => 'AREA',
            ),
            array(
                'value' => get_option($this->_constants->POINTS_EXPIRY_MESSAGE),
                'name'  => $this->_constants->POINTS_EXPIRY_MESSAGE,
                'label' => __('Loyalty Program: Points expiry message', 'loyalty-program-for-woocommerce'),
                'type'  => 'AREA',
            ),
            array(
                'value' => get_option($this->_constants->POINTS_EARN_CART_MESSAGE_GUEST),
                'name'  => $this->_constants->POINTS_EARN_CART_MESSAGE_GUEST,
                'label' => __('Loyalty Program: Points to earn message in cart (guest)', 'loyalty-program-for-woocommerce'),
                'type'  => 'AREA',
            ),
            array(
                'value' => get_option($this->_constants->POINTS_EARN_CHECKOUT_MESSAGE_GUEST),
                'name'  => $this->_constants->POINTS_EARN_CHECKOUT_MESSAGE_GUEST,
                'label' => __('Loyalty Program: Points to earn message in checkout (guest)', 'loyalty-program-for-woocommerce'),
                'type'  => 'AREA',
            ),
            array(
                'value' => get_option($this->_constants->POINTS_EARN_PRODUCT_MESSAGE_GUEST),
                'name'  => $this->_constants->POINTS_EARN_PRODUCT_MESSAGE_GUEST,
                'label' => __('Loyalty Program: Points to earn message in single product page (guest)', 'loyalty-program-for-woocommerce'),
                'type'  => 'AREA',
            ),
            array(
                'value' => get_option($this->_constants->POINTS_REDEEM_ADDITIONAL_INFO),
                'name'  => $this->_constants->POINTS_REDEEM_ADDITIONAL_INFO,
                'label' => __('Loyalty Program: Points redemption additional info', 'loyalty-program-for-woocommerce'),
                'type'  => 'AREA',
            ),
        ));

        return $translate;
    }

    /**
     * Check if all required WPML plugins are active.
     *
     * @since 1.0
     * @access private
     *
     * @return bool True if all plugins active, false otherwise.
     */
    private function _is_wpml_requirements_installed()
    {
        return $this->_helper_functions->is_plugin_active('sitepress-multilingual-cms/sitepress.php')
        && $this->_helper_functions->is_plugin_active('woocommerce-multilingual/wpml-woocommerce.php')
        && $this->_helper_functions->is_plugin_active('wpml-string-translation/plugin.php');
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute WPML_Support class.
     *
     * @since 1.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        if (!$this->_is_wpml_requirements_installed()) {
            return;
        }

        add_filter('acfw_wpml_translate_setting_options', array($this, 'register_translatable_setting_strings'));
    }

}
