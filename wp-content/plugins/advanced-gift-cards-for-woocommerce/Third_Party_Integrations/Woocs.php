<?php
namespace AGCFW\Third_Party_Integrations;

use AGCFW\Abstracts\Abstract_Main_Plugin_Class;
use AGCFW\Helpers\Helper_Functions;
use AGCFW\Helpers\Plugin_Constants;
use AGCFW\Interfaces\Model_Interface;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of the WPML_Support module.
 *
 * @since 1.1
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
     * @since 1.1
     * @access private
     * @var WPML_Support
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 1.1
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 1.1
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

    private $_email_currency;

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 1.1
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
     * @since 1.1
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
     * Force set WOOCS default currency to site currency and store.
     * Store the current default currency to a private property.
     * 
     * @since 1.1
     * @access public
     */
    public function force_set_woocs_email_default_currency_to_site_currency()
    {   
        global $WOOCS;

        // 
        $this->_email_currency   = $WOOCS->default_currency;
        $WOOCS->default_currency = get_option('woocommerce_currency'); // we use get_option here so WOOCS won't apply filters.
    }

    /**
     * Reset WOOCS default currency from the previous value it was set to.
     * 
     * @since 1.1
     * @access public
     */
    public function reset_woocs_email_default_currency()
    {
        global $WOOCS;

        // reset WOOCS default currency.
        $WOOCS->default_currency = $this->_email_currency;
        $this->_email_currency   = '';
    }


    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute WPML_Support class.
     *
     * @since 1.1
     * @access public
     * @inherit ACFWF\Interfaces\Model_Interface
     */
    public function run()
    {
        if (!$this->_helper_functions->is_plugin_active('woocommerce-currency-switcher/index.php')) {
            return;
        }

        add_action('agcfw_before_send_gift_card_email', array($this, 'force_set_woocs_email_default_currency_to_site_currency'));
        add_action('agcfw_after_send_gift_card_email', array($this, 'reset_woocs_email_default_currency'));
    }

}
