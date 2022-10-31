<?php
namespace AGCFW\Models;

use AGCFW\Abstracts\Abstract_Main_Plugin_Class;
use AGCFW\Helpers\Helper_Functions;
use AGCFW\Helpers\Plugin_Constants;
use AGCFW\Interfaces\Activatable_Interface;
use AGCFW\Interfaces\Initiable_Interface;
use AGCFW\Interfaces\Model_Interface;
use AGCFW\Objects\Report_Widgets\Gift_Cards_Sold;
use AGCFW\Objects\Report_Widgets\Gift_Cards_Claimed;
use ACFWF\Models\Objects\Report_Widgets\Section_Title;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of the Admin module.
 *
 * @since 1.0
 */
class Admin implements Model_Interface, Initiable_Interface, Activatable_Interface
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
     * @var Admin
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
     * @return Admin
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
    | Admin app
    |--------------------------------------------------------------------------
    */

    /**
     * Register Loyalty Program settings page.
     *
     * @since 1.0
     * @access public
     *
     * @param array $app_pages List of app pages.
     * @return array Filtered list of app pages.
     */
    public function register_advanced_gift_cards_menu($app_pages)
    {
        return $app_pages;
    }

    /**
     * Display admin app.
     *
     * @since 1.0
     * @access public
     */
    public function display_admin_app()
    {
        echo '<div id="agcfw_admin_app"></div>';
    }

    /*
    |--------------------------------------------------------------------------
    | Gutenberg blocks
    |--------------------------------------------------------------------------
    */

    /**
     * Register custom gutenberg blocks.
     *
     * @since 1.0
     * @access private
     */
    private function _register_blocks()
    {
        \register_block_type(
            'acfw/gift-card-redeem-form',
            array(
                'title'           => __('Advanced Gift Cards Redeem Form', 'advanced-gift-cards-for-woocommerce'),
                'description'     => __('Display the redeem form for advanced gift cards.', 'advanced-gift-cards-for-woocommerce'),
                'render_callback' => array(\AGCFW()->Redeem, 'render_redeem_form_block'),
                'attributes'      => array(
                    'title'             => array(
                        'type'    => 'string',
                        'default' => __('Redeem a gift card?', 'advanced-gift-cards-for-woocommerce'),
                    ),
                    'description'       => array(
                        'type'    => 'string',
                        'default' => __('Enter your gift card claim code.', 'advanced-gift-cards-for-woocommerce'),
                    ),
                    'tooltip_link_text' => array(
                        'type'    => 'string',
                        'default' => __('How do I find the claim code?', 'advanced-gift-cards-for-woocommerce'),
                    ),
                    'tooltip_title'     => array(
                        'type'    => 'string',
                        'default' => __('Gift Card Claim Code', 'advanced-gift-cards-for-woocommerce'),
                    ),
                    'tooltip_content'   => array(
                        'type'    => 'string',
                        'default' => __('Your gift card claim code is found inside the email sent from the store when the gift card was purchased.', 'advanced-gift-cards-for-woocommerce'),
                    ),
                    'input_placeholder' => array(
                        'type'    => 'string',
                        'default' => __('Enter code', 'advanced-gift-cards-for-woocommerce'),
                    ),
                    'button_text'       => array(
                        'type'    => 'string',
                        'default' => __('Redeem', 'advanced-gift-cards-for-woocommerce'),
                    ),
                ),
                'style'           => 'agcfw-redeem-gift-card',
                'script'          => 'agcfw-redeem-gift-card',
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Notices
    |--------------------------------------------------------------------------
    */

    /**
     * Register gift card admin notices.
     * 
     * @since 1.0
     * @access public
     * 
     * @param array $acfw_notices Advanced Coupons notices.
     * @return array Filtered Advanced Coupons notices.
     */
    public function register_gift_card_notices($acfw_notices)
    {
        $acfw_notices['agcfw_getting_started'] = $this->_constants->SHOW_GETTING_STARTED_NOTICE;
        return $acfw_notices;
    }

    /**
     * Register gift card notices data.
     * 
     * @since 1.1.2
     * @access public
     * 
     * @param array|null $notice_data Notice data.
     * @param string     $notice_key  Notice key.
     * @return array|null Filtered notice data.
     */
    public function register_gift_card_notices_data($notice_data, $notice_key)
    {
        if ('agcfw_getting_started' === $notice_key) {
            $notice_data = $this->_get_getting_started_notice_data();
        }

        return $notice_data;
    }

    /**
     * Get the data of the getting started guide notice.
     * 
     * @since 1.1.2
     * @access private
     * 
     * @return array Getting started notice data.
     */
    private function _get_getting_started_notice_data()
    {
        return array(
            'slug'           => 'agcfw_getting_started',
            'id'             => $this->_constants->SHOW_GETTING_STARTED_NOTICE,
            'logo_img'       => \ACFWF()->Plugin_Constants->IMAGES_ROOT_URL() . '/acfw-logo.png',
            'is_dismissable' => true,
            'type'           => 'success',
            'heading'        => __('IMPORTANT INFORMATION', 'advanced-gift-cards-for-woocommerce'),
            'content'        => array(
                __('Thank you for purchasing Advanced Gift Cards! This plugin gives WooCommerce store owners the ability to sell gift card products on their store. Gift cards are then redeemable as store credit.' , 'advanced-gift-cards-for-woocommerce' ),
                __('Ready to get started? Read the guide below then head over and create a new Product with the brand new Gift Card type.' , 'advanced-gift-cards-for-woocommerce'),
            ),
            'actions'        => array(
                array(
                    'key'         => 'primary',
                    'link'        => 'https://advancedcouponsplugin.com/knowledgebase/advanced-gift-cards-getting-started-guide/?utm_source=agc&utm_medium=kb&utm_campaign=agcgettingstarted',
                    'text'        => __( 'Read The Getting Started Guide →' , 'advanced-gift-cards-for-woocommerce' ),
                    'is_external' => true
                )
            ),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
     */

    /**
     * Register gift card report widgets.
     * 
     * @since 1.1.1
     * @since 1.1.2 Add section title.
     * @access public
     * 
     * @param array             $report_widgets Dashboard report widgets.
     * @param Date_Period_Range $report_period  Date period range object.
     * @return array Filtered dashboard report widgets.
     */
    public function register_gift_card_report_widgets($report_widgets, $report_period)
    {
        $report_widgets[] = new Section_Title('gift_cards_activity', __('Gift Cards Activity', 'advanced-gift-cards-for-woocommerce'));
        $report_widgets[] = new Gift_Cards_Sold($report_period);
        $report_widgets[] = new Gift_Cards_Claimed($report_period);

        return $report_widgets;
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 1.0
     * @access public
     * @implements AGCFW\Interfaces\Initializable_Interface
     */
    public function activate()
    {
        if ('dismissed' !== get_option($this->_constants->SHOW_GETTING_STARTED_NOTICE)) {
            update_option($this->_constants->SHOW_GETTING_STARTED_NOTICE, 'yes');
        }
    }

    /**
     * Execute codes that needs to run plugin init.
     *
     * @since 1.0
     * @access public
     * @implements AGCFW\Interfaces\Initializable_Interface
     */
    public function initialize()
    {
        $this->_register_blocks();
    }

    /**
     * Execute Admin class.
     *
     * @since 1.0
     * @access public
     * @inherit AGCFW\Interfaces\Model_Interface
     */
    public function run()
    {
        add_action('acfw_admin_app_pages', array($this, 'register_advanced_gift_cards_menu'));
        add_action('acfw_admin_app', array($this, 'display_admin_app'));
        add_filter('acfw_admin_notice_option_names', array($this, 'register_gift_card_notices'));
        add_filter('acfw_get_admin_notice_data', array($this, 'register_gift_card_notices_data'), 10, 2);
        add_filter('acfw_register_dashboard_report_widgets', array($this, 'register_gift_card_report_widgets'), 10, 2);
    }

}
