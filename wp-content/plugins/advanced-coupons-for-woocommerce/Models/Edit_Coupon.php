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
class Edit_Coupon implements Model_Interface, Initiable_Interface
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
     * @var Edit_Coupon
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
     * @return Edit_Coupon
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
    | One click apply notifications data
    |--------------------------------------------------------------------------
     */

    /**
     * Add "Apply notification" data tab to woocommerce coupon admin data tabs.
     *
     * @since 2.0
     * @access public
     *
     * @param array $coupon_data_tabs Array of coupon admin data tabs.
     * @return array Modified array of coupon admin data tabs.
     */
    public function apply_notification_admin_data_tab($coupon_data_tabs)
    {

        $coupon_data_tabs['acfw_apply_notification'] = array(
            'label'  => __('One Click Apply', 'advanced-coupons-for-woocommerce'),
            'target' => 'acfw_apply_notification',
            'class'  => '',
        );

        return $coupon_data_tabs;
    }

    /**
     * Add "Apply notification" data panel to woocommerce coupon admin data panels.
     *
     * @since 2.0
     * @access public
     *
     * @param int $coupon_id WC_Coupon ID.
     */
    public function apply_notification_admin_data_panel($coupon_id)
    {

        $panel_id            = 'acfw_apply_notification';
        $help_slug           = 'one-click-apply';
        $descriptions        = array(__("Show a WooCommerce notification on the cart page with a button to apply the coupon when the customer is eligible to apply this coupon.", 'advanced-coupons-for-woocommerce'));
        $coupon              = \ACFWF()->Edit_Coupon->get_shared_advanced_coupon($coupon_id);
        $apply_notifications = \ACFWF()->Helper_Functions->get_option($this->_constants->APPLY_NOTIFICATION_CACHE, array());
        $additional_classes  = 'toggle-enable-fields';
        $title               = __('One Click Apply', 'advanced-coupons-for-woocommerce');
        $fields              = apply_filters('acfw_apply_notification_admin_data_panel_fields', array(

            array(
                'cb'   => 'woocommerce_wp_checkbox',
                'args' => array(
                    'id'          => $this->_constants->META_PREFIX . 'enable_apply_notification',
                    'class'       => 'toggle-trigger-field',
                    'label'       => __('Enable one click apply notifications', 'advanced-coupons-for-woocommerce'),
                    'description' => __('When checked, this will enable one click apply notifications for this coupon which will then show a notification for customers in the cart when the coupon can be applied.', 'advanced-coupons-for-woocommerce'),
                    'value'       => in_array($coupon_id, $apply_notifications) ? 'yes' : '',
                ),
            ),

            array(
                'cb'   => 'woocommerce_wp_textarea_input',
                'args' => array(
                    'id'          => $this->_constants->META_PREFIX . 'apply_notification_message',
                    'label'       => __('Notification message', 'advanced-coupons-for-woocommerce'),
                    'description' => __("The notification message that will be displayed after checking that the coupon is elegible in the customer's cart.", 'advanced-coupons-for-woocommerce'),
                    'desc_tip'    => true,
                    'type'        => 'text',
                    'placeholder' => __("Your current cart is eligible for a coupon.", 'advanced-coupons-for-woocommerce'),
                    'value'       => $coupon->get_advanced_prop('apply_notification_message'),
                ),
            ),

            array(
                'cb'   => 'woocommerce_wp_text_input',
                'args' => array(
                    'id'          => $this->_constants->META_PREFIX . 'apply_notification_btn_text',
                    'label'       => __('Apply Button Text', 'advanced-coupons-for-woocommerce'),
                    'description' => __("The text for the button to apply the coupon.", 'advanced-coupons-for-woocommerce'),
                    'desc_tip'    => true,
                    'type'        => 'text',
                    'placeholder' => __("Apply Coupon", 'advanced-coupons-for-woocommerce'),
                    'value'       => $coupon->get_advanced_prop('apply_notification_btn_text'),
                ),
            ),

            array(
                'cb'   => 'woocommerce_wp_select',
                'args' => array(
                    'id'          => $this->_constants->META_PREFIX . 'apply_notification_type',
                    'label'       => __('Notification type', 'advanced-coupons-for-woocommerce'),
                    'description' => __("The type of notification to display.", 'advanced-coupons-for-woocommerce'),
                    'desc_tip'    => true,
                    'type'        => 'text',
                    'placeholder' => __("Select...", 'advanced-coupons-for-woocommerce'),
                    'value'       => $coupon->get_advanced_prop('apply_notification_type'),
                    'options'     => array(
                        'notice'  => __('Info', 'advanced-coupons-for-woocommerce'),
                        'success' => __('Success', 'advanced-coupons-for-woocommerce'),
                        'error'   => __('Error', 'advanced-coupons-for-woocommerce'),
                    ),
                ),
            ),
        ));

        include $this->_constants->VIEWS_ROOT_PATH . 'coupons' . DIRECTORY_SEPARATOR . 'view-generic-admin-data-panel.php';
    }

    /*
    |--------------------------------------------------------------------------
    | Payment methods restriction data
    |--------------------------------------------------------------------------
     */

    /**
     * Add payment methods restriction data tab to woocommerce coupon admin data tabs.
     *
     * @since 2.5
     * @access public
     *
     * @param array $coupon_data_tabs Array of coupon admin data tabs.
     * @return array Modified array of coupon admin data tabs.
     */
    public function payment_methods_restrict_admin_data_tab($coupon_data_tabs)
    {

        $coupon_data_tabs['acfw_payment_methods_restrict'] = array(
            'label'  => __('Payment Methods Restriction', 'advanced-coupons-for-woocommerce'),
            'target' => 'acfw_payment_methods_restrict',
            'class'  => '',
        );

        return $coupon_data_tabs;
    }

    /**
     * Add payment methods restriction data panel to woocommerce coupon admin data panels.
     *
     * @since 1.0
     * @access public
     *
     * @param int $coupon_id WC_Coupon ID.
     */
    public function payment_methods_restrict_admin_data_panel($coupon_id)
    {
        $panel_id           = 'acfw_payment_methods_restrict';
        $help_slug          = 'payment-methods-restriction';
        $descriptions       = array(__("Restrict the payment methods that are selectable on the checkout if this coupon is applied. The payment methods shown to the user on the checkout will be filtered via the rules below.", 'advanced-coupons-for-woocommerce'));
        $coupon             = \ACFWF()->Edit_Coupon->get_shared_advanced_coupon($coupon_id);
        $additional_classes = 'toggle-enable-fields';
        $title              = __('Payment Methods Restriction', 'advanced-coupons-for-woocommerce');
        $payment_methods    = \ACFWP()->Payment_Methods_Restrict->get_payment_gateway_options();
        $fields             = apply_filters('acfw_payment_methods_restriction_admin_data_panel_fields', array(
            array(
                'cb'   => 'woocommerce_wp_checkbox',
                'args' => array(
                    'id'          => $this->_constants->META_PREFIX . 'enable_payment_methods_restrict',
                    'class'       => 'toggle-trigger-field',
                    'label'       => __('Enable payment methods restriction', 'advanced-coupons-for-woocommerce'),
                    'description' => __('When checked, will enable payment methods restriction check when coupon is applied', 'advanced-coupons-for-woocommerce'),
                    'value'       => $coupon->get_advanced_prop_edit('enable_payment_methods_restrict'),
                ),
            ),
            array(
                'cb'   => 'woocommerce_wp_select',
                'args' => array(
                    'id'          => $this->_constants->META_PREFIX . 'payment_methods_restrict_type',
                    'style'       => 'width:50%;',
                    'label'       => __('Type', 'advanced-coupons-for-woocommerce'),
                    'description' => __('The type of implementation for this restriction. Select "allowed" to only allow payment via the selected methods. Select "disallowed" to only payment that don\'t fall under the selected methods.', 'advanced-coupons-for-woocommerce'),
                    'desc_tip'    => true,
                    'type'        => 'select',
                    'options'     => array(
                        'allowed'    => __('Allowed', 'advanced-coupons-for-woocommerce'),
                        'disallowed' => __('Disallowed', 'advanced-coupons-for-woocommerce'),
                    ),
                    'value'       => $coupon->get_advanced_prop_edit('payment_methods_restrict_type'),
                ),
            ),
            array(
                'cb'   => array(\ACFWF()->Edit_Coupon, 'acfw_multiselect_field'),
                'args' => array(
                    'id'          => $this->_constants->META_PREFIX . 'payment_methods_restrict_selection',
                    'class'       => 'wc-enhanced-select',
                    'style'       => 'width:50%;',
                    'label'       => __('Payment Methods', 'advanced-coupons-for-woocommerce'),
                    'description' => __('The payment methods that should/shouldn\'t be used to process payment with this coupon.', 'advanced-coupons-for-woocommerce'),
                    'desc_tip'    => true,
                    'type'        => 'text',
                    'options'     => $payment_methods,
                    'value'       => $coupon->get_advanced_prop_edit('payment_methods_restrict_selection'),
                ),

            ),
        ));

        include $this->_constants->VIEWS_ROOT_PATH . 'coupons' . DIRECTORY_SEPARATOR . 'view-generic-admin-data-panel.php';
    }

    /*
    |--------------------------------------------------------------------------
    | Exclude Coupons
    |--------------------------------------------------------------------------
     */

    /**
     * Display exlcude coupons field inside "Usage restriction" tab.
     *
     * @since 2.0
     * @since 3.3 Display coupon categories in the field options.
     * @access public
     *
     * @param int $coupon_id WC_Coupon ID.
     */
    public function display_exclude_coupons_field($coupon_id)
    {

        $coupon   = \ACFWF()->Edit_Coupon->get_shared_advanced_coupon($coupon_id);
        $excluded = $coupon->get_advanced_prop('excluded_coupons', array());
        $options  = array();

        foreach ($excluded as $excluded_id) {
            if (strpos($excluded_id, 'cat_') !== false) {
                $category = get_term_by( 'slug', substr($excluded_id, 4), $this->_constants->COUPON_CAT_TAXONOMY);
                $options[$excluded_id] = sprintf(__('Category: %s', 'advanced-coupons-for-woocommerce'), $category->name);
            } else {
                $options[$excluded_id] = wc_get_coupon_code_by_id($excluded_id);
            }
        }

        include $this->_constants->VIEWS_ROOT_PATH . 'coupons' . DIRECTORY_SEPARATOR . 'view-exclude-coupon-field.php';

    }

    /**
     * Add help link in usage restrictions tab.
     *
     * @since 2.7
     * @access public
     */
    public function usage_restrictions_add_help_link()
    {
        echo '<div class="acfw-help-link" data-module="usage-restrictions"></div>';
    }

    /*
    |--------------------------------------------------------------------------
    | Add Products Data
    |--------------------------------------------------------------------------
     */

    /**
     * Add new "add free products" data tab to woocommerce coupon admin data tabs.
     *
     * @since 2.0
     * @access public
     *
     * @param array $coupon_data_tabs Array of coupon admin data tabs.
     * @return array Modified array of coupon admin data tabs.
     */
    public function add_products_admin_data_tab($coupon_data_tabs)
    {

        $coupon_data_tabs['acfw_add_products'] = array(
            'label'  => __('Add Products', 'advanced-coupons-for-woocommerce'),
            'target' => 'acfw_add_products',
            'class'  => '',
        );

        return $coupon_data_tabs;
    }

    /**
     * Add "add free products" data panel to woocommerce coupon admin data panels.
     *
     * @since 2.0
     * @access public
     *
     * @param int $coupon_id WC_Coupon ID.
     */
    public function add_products_admin_data_panel($coupon_id)
    {

        $panel_id        = 'acfw_add_products';
        $coupon          = \ACFWF()->Edit_Coupon->get_shared_advanced_coupon($coupon_id);
        $spinner_img     = $this->_constants->IMAGES_ROOT_URL . 'spinner-2x.gif';
        $add_products    = $coupon->get_add_products_data('edit');
        $exclude         = is_array($add_products) ? array_column($add_products, 'product_id') : array();
        $panel_data_atts = apply_filters('acfwp_add_products_panel_data_atts', array(), $add_products, $coupon);

        include $this->_constants->VIEWS_ROOT_PATH . 'coupons' . DIRECTORY_SEPARATOR . 'view-add-products-data-panel.php';
    }

    /*
    |--------------------------------------------------------------------------
    | Auto Apply Data
    |--------------------------------------------------------------------------
     */

    /**
     * Register auto apply coupon metabox.
     *
     * @since 2.0
     * @access public
     *
     * @param string  $post_type Post type.
     * @param WP_Post $post      Post object.
     */
    public function register_auto_apply_metabox($post_type, $post)
    {

        if ('shop_coupon' !== $post_type) {
            return;
        }

        $metabox = function ($post) {

            $auto_apply_coupons = \ACFWF()->Helper_Functions->get_option($this->_constants->AUTO_APPLY_COUPONS, array());
            $input_name         = $this->_constants->META_PREFIX . 'auto_apply_coupon';
            $checked            = checked(in_array($post->ID, $auto_apply_coupons), true, false);

            ?>
            <label>
                <input id="acfw_auto_apply_coupon_field" type="checkbox" name="<?php echo esc_attr($input_name); ?>" value="yes" <?php echo $checked ?>>
                <?php _e('Enable auto apply for this coupon.', 'advanced-coupons-for-woocommerce');?>
            </label>
            <div class="auto-apply-warning">
                <?php _e('<strong>Note:</strong> coupon cannot be auto applied when "Allowed emails", "Usage limit per coupon", "Usage limit per user" and/or "Virtual coupons" option is set.', 'advanced-coupons-for-woocommerce');?>
            </div>
            <?php
};

        add_meta_box('acfw-auto-apply-coupon', __('Auto Apply Coupon', 'advanced-coupons-for-woocommerce'), $metabox, 'shop_coupon', 'side');
    }

    /*
    |--------------------------------------------------------------------------
    | Shipping Overrides
    |--------------------------------------------------------------------------
     */

    /**
     * Add shipping ovverides data tab to woocommerce coupon admin data tabs.
     *
     * @since 2.0
     * @access public
     *
     * @param array $coupon_data_tabs Array of coupon admin data tabs.
     * @return array Modified array of coupon admin data tabs.
     */
    public function shipping_overrides_admin_data_tab($coupon_data_tabs)
    {

        $coupon_data_tabs['acfw_shipping_overrides'] = array(
            'label'  => __('Shipping Overrides', 'advanced-coupons-for-woocommerce'),
            'target' => 'acfw_shipping_overrides',
            'class'  => '',
        );

        return $coupon_data_tabs;
    }

    /**
     * Add "Shipping Overrides" data panel to woocommerce coupon admin data panels.
     *
     * @since 2.0
     * @access public
     *
     * @param int $coupon_id WC_Coupon ID.
     */
    public function shipping_overrides_admin_data_panel($coupon_id)
    {

        $panel_id     = 'acfw_shipping_overrides';
        $coupon       = \ACFWF()->Edit_Coupon->get_shared_advanced_coupon($coupon_id);
        $spinner_img  = $this->_constants->IMAGES_ROOT_URL . 'spinner-2x.gif';
        $zone_methods = apply_filters('acfw_shipping_override_selectable_options', array());
        $overrides    = $coupon->get_shipping_overrides_data_edit();
        $exclude      = array_map(function ($row) {
            return array('zone' => $row['shipping_zone'], 'method' => $row['shipping_method']);
        }, $overrides);

        include $this->_constants->VIEWS_ROOT_PATH . 'coupons' . DIRECTORY_SEPARATOR . 'view-shipping-overrides-data-panel.php';
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Usage Limits.
    |--------------------------------------------------------------------------
     */

    /**
     * Advanced usage limits fields.
     *
     * @since 2.0
     * @access public
     *
     * @param int       $coupon_id Coupon ID.
     * @param WC_Coupon $coupon    Coupon object.
     */
    public function advanced_usage_limits_fields($coupon_id, $coupon)
    {

        $period = get_post_meta($coupon_id, $this->_constants->META_PREFIX . 'reset_usage_limit_period', true);
        $reset  = get_post_meta($coupon_id, $this->_constants->META_PREFIX . 'usage_limit_reset_time', true);

        woocommerce_wp_select(array(
            'id'          => $this->_constants->META_PREFIX . 'reset_usage_limit_period',
            'label'       => __('Reset usage count every:', 'advanced-coupons-for-woocommerce'),
            'options'     => array(
                'none'    => __('Never reset', 'advanced-coupons-for-woocommerce'),
                'yearly'  => __('Every year', 'advanced-coupons-for-woocommerce'),
                'monthly' => __('Every month', 'advanced-coupons-for-woocommerce'),
                'weekly'  => __('Every week', 'advanced-coupons-for-woocommerce'),
                'daily'   => __('Every day', 'advanced-coupons-for-woocommerce'),
            ),
            'description' => __('Set the time period to reset the usage limit count. <strong>Yearly:</strong> resets at start of the year. <strong>Monthly:</strong> resets at start of the month. <strong>Weekly:</strong> resets at the start of every week (day depends on the <em>"Week Starts On"</em> setting). <strong>Daily:</strong> resets everyday. Time is always set at 12:00am of the local timezone settings.', 'advanced-coupons-for-woocommerce'),
            'desc_tip'    => true,
            'value'       => $period,
        ));

        if ($period && 'none' != $period && $reset):
            $timezone = new \DateTimeZone($this->_helper_functions->get_site_current_timezone());
            $dateobj  = new \DateTime();

            $dateobj->setTimestamp($reset);
            $dateobj->setTimezone($timezone);

            $next_reset_date = date_i18n($this->_helper_functions->get_datetime_format(), $dateobj->getTimestamp() + $dateobj->getOffset());

            ?>
	            <div class="acfw-usage-limit-info">
	                <p class="reset-time"><?php echo sprintf(__('Next reset for coupon usage count is on <code>%s %s</code>', 'advanced-coupons-for-woocommerce'), $next_reset_date, $timezone->getName()); ?></p>
	            </div>
	        <?php endif;
    }

    /**
     * Add help link in usage limits tab.
     *
     * @since 2.7
     * @access public
     */
    public function usage_limits_add_help_link()
    {
        echo '<div class="acfw-help-link" data-module="usage-limits"></div>';
    }

    /*
    |--------------------------------------------------------------------------
    | Loyalty Programs
    |--------------------------------------------------------------------------
     */

    /**
     * Register Loyalty Program metabox.
     *
     * @since 2.0
     * @access public
     * @deprecated 2.6.3
     *
     * @param string  $post_type Post type.
     * @param WP_Post $post      Post object.
     */
    public function register_loyalty_program_metabox($post_type, $post)
    {
        wc_deprecrated_function('Edit_Coupon::' . __FUNCTION__, '2.6.3');
    }

    /*
    |--------------------------------------------------------------------------
    | Sort Coupons
    |--------------------------------------------------------------------------
     */

    /**
     * Display sort coupon priority field under general tab.
     *
     * @since 2.5
     * @access public
     *
     * @param int       $coupon_id Coupon ID.
     * @param WC_Coupon $coupon    Coupon object.
     */
    public function display_coupon_sort_priority_field($coupon_id, $coupon)
    {

        $coupon_priority  = get_post_meta($coupon_id, $this->_constants->META_PREFIX . 'coupon_sort_priority', true);
        $coupon_priority  = $coupon_priority ? $coupon_priority : '30';
        $priority_options = array(
            '1'  => __('Very high (1)', 'advanced-coupons-for-woocommerce'),
            '10' => __('High (10)', 'advanced-coupons-for-woocommerce'),
            '30' => __('Normal (30)', 'advanced-coupons-for-woocommerce'),
            '50' => __('Low (50)', 'advanced-coupons-for-woocommerce'),
            '90' => __('Very Low (90)', 'advanced-coupons-for-woocommerce'),
        );
        $is_custom = !in_array($coupon_priority, array_keys($priority_options));

        include $this->_constants->VIEWS_ROOT_PATH . 'coupons' . DIRECTORY_SEPARATOR . 'view-coupon-sort-priority-field.php';
    }

    /*
    |--------------------------------------------------------------------------
    | Save coupon
    |--------------------------------------------------------------------------
     */

    /**
     * Save coupon data.
     *
     * @since 2.0
     * @since 2.1 Delete _acfw_schedule_expire meta on save.
     * @access public
     *
     * @param int             $coupon_id Coupon ID.
     * @param Advanced_Coupon $coupon    Advanced coupon object.
     */
    public function save_coupon_data($coupon_id, $coupon)
    {

        $allowed_html = wp_kses_allowed_html('post');

        // Exclude coupons
        $excluded_coupons = isset($_POST[$this->_constants->META_PREFIX . 'exclude_coupon_ids']) ? $_POST[$this->_constants->META_PREFIX . 'exclude_coupon_ids'] : array();
        $excluded_coupons = is_array($excluded_coupons) && !empty($excluded_coupons) ? array_map('sanitize_text_field', $excluded_coupons) : array();
        $coupon->set_advanced_prop('excluded_coupons', $excluded_coupons);

        // Scheduler module
        if (\ACFWF()->Helper_Functions->is_module(Plugin_Constants::SCHEDULER_MODULE)) {
        }

        // Set coupon as auto apply
        if (\ACFWF()->Helper_Functions->is_module(Plugin_Constants::AUTO_APPLY_MODULE)) {

            $auto_apply_coupon = isset($_POST[$this->_constants->META_PREFIX . 'auto_apply_coupon']) && 'yes' == $_POST[$this->_constants->META_PREFIX . 'auto_apply_coupon'];
            $coupon->set_advanced_prop('auto_apply_coupon', $auto_apply_coupon);
        }

        // apply notification module
        if (\ACFWF()->Helper_Functions->is_module(Plugin_Constants::APPLY_NOTIFICATION_MODULE)) {

            $enable_apply_notification   = isset($_POST[$this->_constants->META_PREFIX . 'enable_apply_notification']) && 'yes' == $_POST[$this->_constants->META_PREFIX . 'enable_apply_notification'];
            $apply_notification_message  = isset($_POST[$this->_constants->META_PREFIX . 'apply_notification_message']) ? wp_kses($_POST[$this->_constants->META_PREFIX . 'apply_notification_message'], $allowed_html) : '';
            $apply_notification_btn_text = isset($_POST[$this->_constants->META_PREFIX . 'apply_notification_btn_text']) ? sanitize_text_field($_POST[$this->_constants->META_PREFIX . 'apply_notification_btn_text']) : '';
            $apply_notification_btn_type = isset($_POST[$this->_constants->META_PREFIX . 'apply_notification_type']) ? sanitize_text_field($_POST[$this->_constants->META_PREFIX . 'apply_notification_type']) : '';

            $coupon->set_advanced_prop('enable_apply_notification', $enable_apply_notification);

            if ('yes' == $enable_apply_notification) {

                $coupon->set_advanced_prop('apply_notification_message', $apply_notification_message);
                $coupon->set_advanced_prop('apply_notification_btn_text', $apply_notification_btn_text);
                $coupon->set_advanced_prop('apply_notification_type', $apply_notification_btn_type);
            }
        }

        // Payment methods restriction
        if (\ACFWF()->Helper_Functions->is_module(Plugin_Constants::PAYMENT_METHODS_RESTRICT)) {

            $enable_payment_methods_restrict    = isset($_POST[$this->_constants->META_PREFIX . 'enable_payment_methods_restrict']) ? sanitize_text_field($_POST[$this->_constants->META_PREFIX . 'enable_payment_methods_restrict']) : '';
            $payment_methods_restrict_type      = isset($_POST[$this->_constants->META_PREFIX . 'payment_methods_restrict_type']) ? sanitize_text_field($_POST[$this->_constants->META_PREFIX . 'payment_methods_restrict_type']) : 'allowed';
            $payment_methods_restrict_selection = isset($_POST[$this->_constants->META_PREFIX . 'payment_methods_restrict_selection']) && is_array($_POST[$this->_constants->META_PREFIX . 'payment_methods_restrict_selection']) ? array_map('sanitize_text_field', $_POST[$this->_constants->META_PREFIX . 'payment_methods_restrict_selection']) : array();

            $coupon->set_advanced_prop('enable_payment_methods_restrict', $enable_payment_methods_restrict);

            if ('yes' == $enable_payment_methods_restrict) {
                $coupon->set_advanced_prop('payment_methods_restrict_type', $payment_methods_restrict_type);
                $coupon->set_advanced_prop('payment_methods_restrict_selection', $payment_methods_restrict_selection);
            }
        }

        // Usage limits module
        if (\ACFWF()->Helper_Functions->is_module(Plugin_Constants::USAGE_LIMITS_MODULE)) {
            $reset_usage_limit_period = isset($_POST[$this->_constants->META_PREFIX . 'reset_usage_limit_period']) && $_POST[$this->_constants->META_PREFIX . 'reset_usage_limit_period'] ? sanitize_text_field($_POST[$this->_constants->META_PREFIX . 'reset_usage_limit_period']) : 'none';
            $coupon->set_advanced_prop('reset_usage_limit_period', $reset_usage_limit_period);
        }

        // Sort coupons
        if (\ACFWF()->Helper_Functions->is_module(Plugin_Constants::SORT_COUPONS_MODULE)) {
            $coupon_sort = isset($_POST[$this->_constants->META_PREFIX . 'coupon_sort_priority']) && $_POST[$this->_constants->META_PREFIX . 'coupon_sort_priority'] ? absint($_POST[$this->_constants->META_PREFIX . 'coupon_sort_priority']) : -1;

            if ($coupon_sort > -1) {
                $coupon->set_advanced_prop('coupon_sort_priority', $coupon_sort);
            }

        }
    }

    /**
     * Remove coupon from apply notifications cache on delete.
     *
     * @since 2.0
     * @access public
     *
     * @param int $coupon_id Coupon ID.
     */
    public function remove_coupon_from_global_options_cache($coupon_id)
    {

        if (get_post_type($coupon_id) !== 'shop_coupon') {
            return;
        }

        remove_action('save_post', array(\ACFWF()->Edit_Coupon, 'save_url_coupons_data'), 10);

        foreach ($this->_constants->CACHE_OPTIONS() as $cache_option) {

            $cache = \ACFWF()->Helper_Functions->get_option($cache_option, array());
            $key   = array_search($coupon_id, $cache);

            if (false !== $key && in_array($coupon_id, $cache)) {

                if ($key >= 0) {
                    unset($cache[$key]);
                }

                update_option($cache_option, $cache);
            }

        }
    }

    /**
     * Register 'exists' and 'noexist' condition values.
     * 
     * @since 3.2.1
     * @access public
     * 
     * @param array $allowed_values List of allowed condition values.
     * @return array Filtered list of allowed condition values.
     */
    public function register_condition_exist_type_values($allowed_values)
    {
        $allowed_values[] = 'exists';
        $allowed_values[] = 'notexist';

        return $allowed_values;
    }

    /*
    |--------------------------------------------------------------------------
    | Help modal
    |--------------------------------------------------------------------------
     */

    /**
     * Replace utm souce parameter value of all links in help modal from 'acfwf' to 'acfwp'.
     *
     * @since 3.0.1
     * @access public
     *
     * @param string $content Help modal content (JSON).
     * @return string Filtered help modal content (JSON).
     */
    public function replace_utm_source_in_help_modal_content($content)
    {
        return str_replace('utm_source=acfwf', 'utm_source=acfwp', $content);
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
    }

    /**
     * Execute Edit_Coupon class.
     *
     * @since 2.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        add_action('acfw_before_save_coupon', array($this, 'save_coupon_data'), 10, 2);
        add_action('woocommerce_coupon_options_usage_restriction', array($this, 'display_exclude_coupons_field'));
        add_action('woocommerce_coupon_options_usage_restriction', array($this, 'usage_restrictions_add_help_link'));

        // Auto apply
        if (\ACFWF()->Helper_Functions->is_module(Plugin_Constants::AUTO_APPLY_MODULE)) {
            add_action('add_meta_boxes', array($this, 'register_auto_apply_metabox'), 10, 2);
        }

        // Shipping Overrides module
        if (\ACFWF()->Helper_Functions->is_module(Plugin_Constants::SHIPPING_OVERRIDES_MODULE)) {

            add_filter('woocommerce_coupon_data_tabs', array($this, 'shipping_overrides_admin_data_tab'), 70, 1);
            add_action('woocommerce_coupon_data_panels', array($this, 'shipping_overrides_admin_data_panel'));
        }

        // Usage limit
        if (\ACFWF()->Helper_Functions->is_module(Plugin_Constants::USAGE_LIMITS_MODULE)) {
            add_action('woocommerce_coupon_options_usage_limit', array($this, 'advanced_usage_limits_fields'), 10, 2);
            add_action('woocommerce_coupon_options_usage_limit', array($this, 'usage_limits_add_help_link'));
        }

        // Add products
        if (\ACFWF()->Helper_Functions->is_module(Plugin_Constants::ADD_PRODUCTS_MODULE)) {

            add_filter('woocommerce_coupon_data_tabs', array($this, 'add_products_admin_data_tab'), 30, 1);
            add_action('woocommerce_coupon_data_panels', array($this, 'add_products_admin_data_panel'));
        }

        // Apply notification module
        if (\ACFWF()->Helper_Functions->is_module(Plugin_Constants::APPLY_NOTIFICATION_MODULE)) {

            add_filter('woocommerce_coupon_data_tabs', array($this, 'apply_notification_admin_data_tab'), 70, 1);
            add_action('woocommerce_coupon_data_panels', array($this, 'apply_notification_admin_data_panel'));
        }

        if (\ACFWF()->Helper_Functions->is_module(Plugin_Constants::PAYMENT_METHODS_RESTRICT)) {
            add_filter('woocommerce_coupon_data_tabs', array($this, 'payment_methods_restrict_admin_data_tab'), 50, 1);
            add_action('woocommerce_coupon_data_panels', array($this, 'payment_methods_restrict_admin_data_panel'));
        }

        // Sort coupons
        if (\ACFWF()->Helper_Functions->is_module(Plugin_Constants::SORT_COUPONS_MODULE)) {

            add_action('woocommerce_coupon_options', array($this, 'display_coupon_sort_priority_field'), 10, 2);
        }

        // Remove coupon on global options cache when trashed or deleted.
        add_action('wp_trash_post', array($this, 'remove_coupon_from_global_options_cache'));
        add_action('before_delete_post', array($this, 'remove_coupon_from_global_options_cache'));
        add_action('untrashed_post', array($this, 'remove_coupon_from_global_options_cache'));

        // disable permission check for help links.
        add_filter('pre_option_' . $this->_constants->ALLOW_FETCH_CONTENT_REMOTE, function () {return "yes";});

        add_filter('acfwf_help_modal_content_response', array($this, 'replace_utm_source_in_help_modal_content'));

        add_filter('acfw_condition_select_allowed_values', array($this, 'register_condition_exist_type_values'));
    }

}
