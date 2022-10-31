<?php
namespace AGCFW\Models;

use AGCFW\Abstracts\Abstract_Main_Plugin_Class;
use AGCFW\Helpers\Helper_Functions;
use AGCFW\Helpers\Plugin_Constants;
use AGCFW\Interfaces\Initiable_Interface;
use AGCFW\Interfaces\Model_Interface;
use AGCFW\Objects\Advanced_Gift_Card;
use AGCFW\Objects\Email;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of the Emails module.
 *
 * @since 1.0
 */
class Emails implements Model_Interface, Initiable_Interface
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
     * @var Emails
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
     * @return Emails
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants, $helper_functions);
        }

        return self::$_instance;
    }

    /**
     * Register advanced gift card email.
     *
     * @since 1.0
     * @access public
     *
     * @param array $emails List of email objects.
     * @return array Filtered list of email objects.
     */
    public function register_advanced_gift_card_email($emails)
    {
        $emails['advanced_gift_card'] = new Email();

        return $emails;
    }

    /**
     * Override template file check to make sure our custom email templates are found by WC.
     *
     * @since 1.0
     * @access public
     */
    public function override_template_file_path_check($core_file, $template, $template_base, $email_id)
    {
        if ('advanced_gift_card' === $email_id) {
            return $this->_constants->TEMPLATES_ROOT_PATH . $template;
        }

        return $core_file;
    }

    /*
    |--------------------------------------------------------------------------
    | Email preview
    |--------------------------------------------------------------------------
     */

    /**
     * Get email preview content.
     *
     * @since 1.0
     * @access private
     *
     * @param array $args Arguments.
     * @return string Email preview content.
     */
    private function _get_email_preview_content($args = array())
    {
        $args = wp_parse_args($args, array(
            'value'     => 0.0,
            'design'    => 'default',
            'custom_bg' => null,
        ));

        extract($args);

        $mailer        = \WC()->mailer(); // this also instansiates the WC_Email classes.
        $email_heading = __('Advanced Gift Cards email preview', 'advanced-gift-cards-for-woocommerce');
        $email         = new Email();
        $gift_card     = new Advanced_Gift_Card();

        $gift_card->set_prop('value', $value);
        $gift_card->set_prop('code', 'gc-EXAMPLECODE');

        $expire_date = new \WC_DateTime('next year', new \DateTimeZone($this->_helper_functions->get_site_current_timezone()));
        $expire_date->setTime(0,0,0);
        $gift_card->set_date_prop('date_expire', $expire_date->format('Y-m-d H:i:s'));

        if ($custom_bg) {
            $design_src = wp_get_attachment_image_url($custom_bg, 'full');
        } else {
            $design_src = \AGCFW()->Helper_Functions->get_builtin_design_src($design);
        }

        $email->set_gift_card($gift_card);
        $email->set_design_image($design_src);
        $email->set_message(__('This is an example message', 'advanced-gift-cards-for-woocommerce'));

        // generate email content.
        $message = $email->get_content_html();

        return $email->style_inline($message);
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX Functions
    |--------------------------------------------------------------------------
     */

    /**
     * AJAX gift card email preview.
     *
     * @since 1.0
     * @access public
     */
    public function ajax_gift_card_email_preview()
    {
        $error_msg = '';

        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            $error_msg = __('Invalid AJAX call', 'advanced-gift-cards-for-woocommerce');
        } elseif (!current_user_can('manage_woocommerce') || !isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'agcfw_gift_card_preview_email')) {
            $error_msg = __('You are not allowed to do this', 'advanced-gift-cards-for-woocommerce');
        } elseif (!isset($_REQUEST['value']) || !$_REQUEST['value']) {
            $error_msg = __('Please enter a proper gift card value.', 'advanced-gift-cards-for-woocommerce');
        } else {

            $args = array(
                'value'     => floatval($_REQUEST['value']),
                'design'    => isset($_REQUEST['design']) ? sanitize_text_field($_REQUEST['design']) : 'default',
                'custom_bg' => isset($_REQUEST['custom_bg']) ? intval($_REQUEST['custom_bg']) : null,
            );

            echo $this->_get_email_preview_content($args);
        }

        if ($error_msg) {
            include $this->_constants->VIEWS_ROOT_PATH . 'view-email-preview-error.php';
        }

        wp_die();
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin init.
     *
     * @since 1.0
     * @access public
     * @implements ACFWF\Interfaces\Initializable_Interface
     */
    public function initialize()
    {
        add_action('wp_ajax_agcfw_gift_card_preview_email', array($this, 'ajax_gift_card_email_preview'));
        add_action('wp_ajax_nopriv_agcfw_gift_card_preview_email', array($this, 'ajax_gift_card_email_preview'));
    }

    /**
     * Execute Emails class.
     *
     * @since 1.0
     * @access public
     * @inherit AGCFW\Interfaces\Model_Interface
     */
    public function run()
    {
        add_filter('woocommerce_email_classes', array($this, 'register_advanced_gift_card_email'));
        add_filter('woocommerce_locate_core_template', array($this, 'override_template_file_path_check'), 10, 4);
    }

}
