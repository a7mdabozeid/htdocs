<?php
namespace ACFWP\Models\SLMW;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;

use ACFWP\Interfaces\Model_Interface;

use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Helpers\Helper_Functions;

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Model that houses the logic of extending the coupon system of woocommerce.
 * It houses the logic of handling coupon url.
 * Public Model.
 *
 * @since 2.0
 */
class Settings implements Model_Interface {

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
     * @var Settings
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
    public function __construct( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

        $this->_constants        = $constants;
        $this->_helper_functions = $helper_functions;

        $main_plugin->add_to_all_plugin_models( $this );

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
     * @return Settings
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin , Plugin_Constants $constants , Helper_Functions $helper_functions ) {

        if ( !self::$_instance instanceof self )
            self::$_instance = new self( $main_plugin , $constants , $helper_functions );

        return self::$_instance;

    }




    /**
     * Register slmw settings menu.
     * 
     * @since 1.8
     * @access public
     */
    public function register_slmw_settings_menu() {

        add_menu_page( 
            __( "ACFW License" , "advanced-coupons-for-woocommerce" ), 
            __( "ACFW License" , "advanced-coupons-for-woocommerce" ), 
            "manage_sites", 
            "acfw-ms-license-settings", 
            array( $this , "generate_slmw_settings_page" )
        );

    }

    /**
     * Register slmw settings page.
     *
     * @since 1.8
     * @access public
     */
    public function generate_slmw_settings_page() {
    
        if ( is_multisite() ) {

            $license_activated = get_site_option( $this->_constants->OPTION_LICENSE_ACTIVATED );
            $activation_email  = get_site_option( $this->_constants->OPTION_ACTIVATION_EMAIL );
            $license_key       = get_site_option( $this->_constants->OPTION_LICENSE_KEY );

        } else {

            $license_activated = get_option( $this->_constants->OPTION_LICENSE_ACTIVATED );
            $activation_email  = get_option( $this->_constants->OPTION_ACTIVATION_EMAIL );
            $license_key       = get_option( $this->_constants->OPTION_LICENSE_KEY );

        }

        $constants = $this->_constants;

        include $this->_constants->VIEWS_ROOT_PATH . 'slmw' . DIRECTORY_SEPARATOR . 'view-license-settings-page.php';
    }

    /**
     * Register slmw settings section.
     *
     * @since 2.0
     * @access public
     *
     * @param array $settings_sections Array of settings sections.
     * @return array Filtered array of settings sections.
     */
    public function register_slmw_settings_section( $settings_sections ) {

        if ( array_key_exists( 'acfw_slmw_settings_section' , $settings_sections ) )
            return $settings_sections;

        $settings_sections[ 'acfw_slmw_settings_section' ] = __( 'License' , 'advanced-coupons-for-woocommerce' );

        return $settings_sections;

    }

    /**
     * Register slmw settings section options.
     *
     * @since 2.0
     * @access public
     *
     * @param array $settings_section_options Array of options per settings sections.
     * @return array Filtered array of options per settings sections.
     */
    public function register_slmw_settings_section_options( $settings , $current_settings_section ) {

        if ( $current_settings_section != 'acfw_slmw_settings_section' )
            return $settings;

        return array(
            array(
                'type' => 'acfw_license',
                'id'   => 'acfw_license_header'
            )
        );

    }
    
    /**
     * Render ACFW license settings header content.
     * 
     * @since 2.1
     * @access public
     * 
     * @param array $value Array of options data. May vary depending on option type.
     */
    public function render_slmw_license_page() {
        
        // hide save changes button.
        $GLOBALS[ 'hide_save_button' ] = true;

        $license_activated = get_option( $this->_constants->OPTION_LICENSE_ACTIVATED );
        $activation_email  = get_option( $this->_constants->OPTION_ACTIVATION_EMAIL );
        $license_key       = get_option( $this->_constants->OPTION_LICENSE_KEY );
        $constants         = $this->_constants;

        include $this->_constants->VIEWS_ROOT_PATH . 'slmw' . DIRECTORY_SEPARATOR . 'view-license-settings-page.php';
    }

    /**
     * Remove per subslite license page app.
     * 
     * @since 2.2
     * @access public
     * 
     * @param array $app_pages List of app pages.
     * @return array Filtered list of app pages.
     */
    public function remove_per_site_license_page_app( $app_pages ) {

        unset( $app_pages[ 'acfw-license' ] );

        return $app_pages;
    }




    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
    */

    /**
     * Execute Settings class.
     *
     * @since 2.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run() {

        if ( is_multisite() ) {

            // Add SLMW Settings In Multi-Site Environment
            add_action( "network_admin_menu" , array( $this , 'register_slmw_settings_menu' ) );
            
            add_filter( 'acfw_admin_app_pages' , array( $this , 'remove_per_site_license_page_app' ) );

        } else {

            add_filter( 'woocommerce_get_sections_acfw_settings' , array( $this , 'register_slmw_settings_section' ) );
            add_filter( 'woocommerce_get_settings_acfw_settings' , array( $this , 'register_slmw_settings_section_options' ) , 10 , 2 );
            add_filter( 'woocommerce_admin_field_acfw_license' , array( $this , 'render_slmw_license_page' ) );

        }
    }

}
