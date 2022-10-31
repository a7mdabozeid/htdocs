<?php
namespace AGCFW\Models;

use AGCFW\Abstracts\Abstract_Main_Plugin_Class;
use AGCFW\Helpers\Helper_Functions;
use AGCFW\Helpers\Plugin_Constants;
use AGCFW\Interfaces\Model_Interface;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class Script_Loader implements Model_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of Bootstrap.
     *
     * @since 1.0.0
     * @access private
     * @var Bootstrap
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 1.0.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 1.0.0
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
     * @since 1.0.0
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
     * @since 1.0.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Bootstrap
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {

        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants, $helper_functions);
        }

        return self::$_instance;

    }

    /**
     * Load backend js and css scripts.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $handle Unique identifier of the current backend page.
     */
    public function load_backend_scripts($handle)
    {
        $screen = get_current_screen();

        $post_type = get_post_type();
        if (!$post_type && isset($_GET['post_type'])) {
            $post_type = $_GET['post_type'];
        }

        if ('post' === $screen->base && 'product' === $screen->id) {
            wp_enqueue_style('edit-advanced-gift-cards', $this->_constants->CSS_ROOT_URL . 'edit-advanced-gift-card.css', array('woocommerce_admin_styles', 'vex', 'vex-theme-plain'), $this->_constants->VERSION, 'all');
            wp_enqueue_script('edit-advanced-gift-cards', $this->_constants->JS_ROOT_URL . 'edit-advanced-gift-card.js', array('jquery', 'vex'), $this->_constants->VERSION, true);
        }

        // network license page
        if ('toplevel_page_agcfw-ms-license-settings-network' === $screen->base) {
            wp_enqueue_style('agcfw_slmw_css', $this->_constants->CSS_ROOT_URL . 'agcfw-slmw-license.css', array('vex', 'vex-theme-plain'), 'all');
            wp_enqueue_script('agcfw_slmw_js', $this->_constants->JS_ROOT_URL . 'agcfw-slmw-license.js', array('vex'), true);
            wp_add_inline_script('agcfw_slmw_js', 'vex.defaultOptions.className = "vex-theme-plain"', 'after');
            wp_localize_script('agcfw_slmw_js', 'slmw_args', array(
                'agcfw_slmw_activation_email'        => get_option('agcfw_slmw_activation_email'),
                'agcfw_slmw_license_key'             => get_option('agcfw_slmw_license_key'),
                'nonce_activate_license'            => wp_create_nonce('agcfw_activate_license'),
                'i18n_activate_license'             => __('Activate Key', 'advanced-gift-cards-for-woocommerce'),
                'i18n_activating_license'           => __('Activating. Please wait...', 'advanced-gift-cards-for-woocommerce'),
                'i18n_please_fill_activation_creds' => __('Please fill in activation email and license key', 'advanced-gift-cards-for-woocommerce'),
                'i18n_failed_to_activate_license'   => __('Failed to activated license. Server error occurred on ajax request. Please contact support.', 'advanced-gift-cards-for-woocommerce'),
                'i18n_license_activated'            => __('License is Active', 'advanced-gift-cards-for-woocommerce'),
                'i18n_license_not_active'           => __('Not Activated Yet', 'advanced-gift-cards-for-woocommerce'),
            ));
        }
    }

    /**
     * Enqueue admin app scripts.
     * 
     * @since 1.0
     * @access public
     */
    public function enqueue_admin_app_scripts()
    {
        wp_enqueue_script('agcfw_admin_app', $this->_constants->JS_ROOT_URL . 'apps/admin-app/dist/admin-app.js', array("moment"), $this->_constants->VERSION, true);
        wp_localize_script('agcfw_admin_app', 'agcfwAdminApp', array(
            'homeUrl' => home_url(),
            'license_page' => array(
                'title'          => __('Advanced Gift Cards License Activation', 'advanced-gift-cards-for-woocommerce'),
                'license_status' => __('Your current license for Advanced Gift Cards', 'advanced-gift-cards-for-woocommerce'),
                'about_content'  => __('Advanced Gift Cards lets you sell redeemable digital gift cards on your WooCommerce store via a simple product listing. Gift Cards can then be redeemed for store credit that your customers can use towards orders. Activate your license key to enable continued support & updates for Advanced Gift Cards as well as access to premium features.', 'advanced-gift-cards-for-woocommerce'),
                'indicator'      => array(
                    'active'   => __('License is Active', 'advanced-gift-cards-for-woocommerce'),
                    'inactive' => __('Not Activated Yet', 'advanced-gift-cards-for-woocommerce'),
                ),
                'specs'          => array(
                    array(
                        'label' => __('Plan', 'advanced-gift-cards-for-woocommerce'),
                        'value' => __('Advanced Gift Cards', 'advanced-gift-cards-for-woocommerce'),
                    ),
                    array(
                        'label' => __('Version', 'advanced-gift-cards-for-woocommerce'),
                        'value' => $this->_constants->VERSION
                    ),
                ),
                'formlabels'     => array(
                    'license_key' => __('License Key:', 'advanced-gift-cards-for-woocommerce'),
                    'email'       => __('Activation Email:', 'advanced-gift-cards-for-woocommerce'),
                    'button'      => __('Activate Key', 'advanced-gift-cards-for-woocommerce'),
                    'help'        => array(
                        'text'  => __('Can’t find your key?', 'advanced-gift-cards-for-woocommerce'),
                        'link'  => 'https://advancedcouponsplugin.com/my-account/?utm_source=agcfw&utm_medium=license&utm_campaign=findkey',
                        'login' => __('Login to your account', 'advanced-gift-cards-for-woocommerce'),
                    ),
                ),
                'spinner_img'    => $this->_constants->IMAGES_ROOT_URL . 'spinner-2x.gif',
                '_formNonce'     => wp_create_nonce("agcfw_activate_license")
            )
        ));

        wp_enqueue_style('agcfw_admin_app', $this->_constants->JS_ROOT_URL . 'apps/admin-app/dist/admin-app.css', array(), $this->_constants->VERSION, 'all');
    }

    /**
     * Load frontend js and css scripts.
     *
     * @since 1.0.0
     * @access public
     */
    public function load_frontend_scripts()
    {
        global $post, $wp, $wp_query;

        $product = $post && "product" === $post->post_type ? wc_get_product($post->ID) : null;

        // register styles and scripts.
        wp_register_style('agcfw-redeem-gift-card', $this->_constants->CSS_ROOT_URL . 'redeem-gift-card.css', array('jquery-webui-popover'), $this->_constants->VERSION, 'all');
        wp_register_script('agcfw-redeem-gift-card', $this->_constants->JS_ROOT_URL . 'redeem-gift-card.js', array('jquery', 'jquery-webui-popover'), $this->_constants->VERSION, true);

        if ($product && "advanced_gift_card" === $product->get_type()) {
            wp_enqueue_style('agcfw-single-product', $this->_constants->CSS_ROOT_URL . 'single-product.css', array('jquery-webui-popover'), $this->_constants->VERSION, 'all');
            wp_enqueue_script('agcfw-single-product', $this->_constants->JS_ROOT_URL . 'single-product.js', array('jquery', 'jquery-webui-popover'), $this->_constants->VERSION, true);
        }

        if ((is_account_page() && isset($wp_query->query['acfw-store-credits'])) || is_checkout()) {
            wp_enqueue_style('agcfw-redeem-gift-card');
            wp_enqueue_script('agcfw-redeem-gift-card');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Gutenberg scripts
    |--------------------------------------------------------------------------
     */

    /**
     * Load script and styles for gutenberg editor.
     *
     * @since 1.0
     * @access public
     */
    public function load_gutenberg_editor_scripts()
    {
        $edit_block_assets = include $this->_constants->JS_ROOT_PATH . 'apps/blocks/dist/agcfw-blocks.asset.php';

        wp_enqueue_style(
            'agcfw-blocks-edit',
            $this->_constants->JS_ROOT_URL . 'apps/blocks/dist/agcfw-blocks.css',
            array(),
            $edit_block_assets['version'],
            "all"
        );

        wp_enqueue_script(
            'agcfw-blocks-edit',
            $this->_constants->JS_ROOT_URL . 'apps/blocks/dist/agcfw-blocks.js',
            $edit_block_assets['dependencies'],
            $edit_block_assets['version']
        );

        wp_localize_script('agcfw-blocks-edit', 'agcfwBlocksi18n', array(
            'redeemFormBlockTexts' => array(
                'title'       => __('Advanced Gift Cards Redeem Form', 'advanced-gift-cards-for-woocommerce'),
                'description' => __('Display the redeem form for advanced gift cards.', 'advanced-gift-cards-for-woocommerce'),
                'defaults'    => array(
                    'title'             => __('Redeem a gift card?', 'advanced-gift-cards-for-woocommerce'),
                    'description'       => __('Enter your gift card claim code.', 'advanced-gift-cards-for-woocommerce'),
                    'tooltip_link_text' => __('How do I find the claim code?', 'advanced-gift-cards-for-woocommerce'),
                    'tooltip_title'     => __('Gift Card Claim Code', 'advanced-gift-cards-for-woocommerce'),
                    'tooltip_content'   => __('Your gift card claim code is found inside the email sent from the store when the gift card was purchased.', 'advanced-gift-cards-for-woocommerce'),
                    'input_placeholder' => __('Enter code', 'advanced-gift-cards-for-woocommerce'),
                    'button_text'       => __('Redeem', 'advanced-gift-cards-for-woocommerce'),
                ),
                'labels'      => array(
                    'main'              => __('Main', 'advanced-gift-cards-for-woocommerce'),
                    'title'             => __('Title', 'advanced-gift-cards-for-woocommerce'),
                    'description'       => __('Description', 'advanced-gift-cards-for-woocommerce'),
                    'tooltip_content'   => __('Tooltip content', 'advanced-gift-cards-for-woocommerce'),
                    'link_text'         => __('Button/Link text', 'advanced-gift-cards-for-woocommerce'),
                    'content'           => __('Content', 'advanced-gift-cards-for-woocommerce'),
                    'form_fields'       => __('Form fields', 'advanced-gift-cards-for-woocommerce'),
                    'input_placeholder' => __('Input placeholder', 'advanced-gift-cards-for-woocommerce'),
                    'button_text'       => __('Button text', 'advanced-gift-cards-for-woocommerce'),
                ),
            ),
        ));
    }

    /**
     * Execute plugin script loader.
     *
     * @since 1.0.0
     * @access public
     */
    public function run()
    {
        add_action('admin_enqueue_scripts', array($this, 'load_backend_scripts'), 10, 1);
        add_action('acfw_admin_app_enqueue_scripts_after', array($this, 'enqueue_admin_app_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'load_frontend_scripts'));
        add_action('enqueue_block_editor_assets', array($this, 'load_gutenberg_editor_scripts'));
    }

}
