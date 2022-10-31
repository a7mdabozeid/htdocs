<?php

if (!defined('ABSPATH'))
    exit;

class AWDP_Backend
{

    /**
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static $_instance = null;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_version;

    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_token;

    /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * The main plugin directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $dir;

    /**
     * The plugin assets directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_dir;

    /**
     * Suffix for Javascripts.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $script_suffix;

    /**
     * The plugin assets URL.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;
    public $hook_suffix = array();
    public $plugin_slug;

    /**
     * Constructor function.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function __construct($file = '', $version = '1.0.0')
    {
        $this->_version = $version;
        $this->_token = AWDP_TOKEN;
        $this->file = $file;
        $this->dir = dirname($this->file);
        $this->assets_dir = trailingslashit($this->dir) . 'assets';
        $this->assets_url = esc_url(trailingslashit(plugins_url('/assets/', $this->file)));

        $this->plugin_slug = 'abc';

        $this->script_suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        // $currentScreen = get_current_screen();


        register_activation_hook($this->file, array($this, 'install'));
        // register_deactivation_hook($this->file, array($this, 'deactivation'));
        add_action('save_post', array($this, 'delete_transient'), 1);
        add_action('edited_term', array($this, 'delete_transient'));
        add_action('delete_term', array($this, 'delete_transient'));
        add_action('created_term', array($this, 'delete_transient'));

        add_action('admin_menu', array($this, 'register_root_page'));

        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 10, 1);
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_styles'), 10, 1);

        $plugin = plugin_basename($this->file);
        add_filter("plugin_action_links_$plugin", array($this, 'add_settings_link'));

        add_action('admin_footer', array($this, 'wdp_deactivation_form'));
        
    }

    /**
     *
     *
     * Ensures only one instance of WCPA is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see WordPress_Plugin_Template()
     * @return Main WCPA instance
     */
    public static function instance($file = '', $version = '1.0.0')
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($file, $version);
        }
        return self::$_instance;
    }

    public function register_root_page()
    {
        $this->hook_suffix[] = add_menu_page(
            __('Pricing Rules', 'aco-woo-dynamic-pricing'), __('Pricing Rules', 'aco-woo-dynamic-pricing'), 'edit_products', 'awdp_admin_ui', array($this, 'admin_ui'), esc_url($this->assets_url) . '/images/icon.png', 25);
        $this->hook_suffix[] = add_submenu_page(
            'awdp_admin_ui', __('Product Lists', 'aco-woo-dynamic-pricing'), __('Product Lists', 'aco-woo-dynamic-pricing'), 'edit_products', 'awdp_admin_product_lists', array($this, 'admin_ui_pro_lists'));
        $this->hook_suffix[] = add_submenu_page(
            'awdp_admin_ui', __('Settings', 'aco-woo-dynamic-pricing'), __('Settings', 'aco-woo-dynamic-pricing'), 'edit_products', 'awdp_ui_settings', array($this, 'admin_ui_settings'));
    }

    public function admin_ui()
    {
        AWDP_Backend::view('admin-root', []);
    }

    public function add_settings_link($links)
    {
        $settings   = '<a href="' . admin_url('admin.php?page=awdp_admin_ui#/') . '">' . __('Pricing Rules','aco-woo-dynamic-pricing') . '</a>';
        $products   = '<a href="' . admin_url('admin.php?page=awdp_admin_product_lists#/') . '">' . __('Product Lists','aco-woo-dynamic-pricing') . '</a>';
        $upgrade    = '<a href="https://acowebs.com/woocommerce-dynamic-pricing-with-discount-rules/" target="_blank" style="font-weight:600;color:#6D71F9;">' . __('Upgrade to PRO','aco-woo-dynamic-pricing') . '</a>';
        array_push($links, $settings);
        array_push($links, $products);
        array_push($links, $upgrade);
        return $links;
    }

    /**
     *    Create post type forms
     */

     static function view($view, $data = array())
    {
        extract($data);
        include(plugin_dir_path(__FILE__) . 'views/' . $view . '.php');
    }

    // End admin_enqueue_styles ()

    public function admin_ui_pro_lists()
    {
        AWDP_Backend::view('admin-lists', []);
    }

    public function admin_ui_settings()
    {
        AWDP_Backend::view('admin-settings', []);
    }

    /**
     * Load admin CSS.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function admin_enqueue_styles($hook = '')
    {
        $currentScreen = get_current_screen();
        $screenID = $currentScreen->id; //
        if (strpos($screenID, 'awdp_') !== false) {

            wp_register_style($this->_token . '-admin', esc_url($this->assets_url) . 'css/backend.css', array(), $this->_version);
            wp_enqueue_style($this->_token . '-admin');
            
        }
    }

    /**
     * Load admin Javascript.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function admin_enqueue_scripts($hook = '')
    {

        $currentScreen = get_current_screen();
        $screenID = $currentScreen->id; //
        if (strpos($screenID, 'awdp_') !== false) {

            if (!isset($this->hook_suffix) || empty($this->hook_suffix)) {
                return;
            }

            // All Categories
            $categories = get_terms('product_cat', ['taxonomy' => 'product_cat', 'hide_empty' => false, 'fields' => 'id=>name']);

            // Product List
            $awdpList = get_posts(array('fields' => 'ids', 'numberposts' => -1, 'post_type' => AWDP_PRODUCT_LIST, 'orderby' => 'title', 'order' => 'ASC'));
            $awdpList = array_map(function ($v) {
                return ['id' => $v, 'name' => get_the_title($v)];
            }, $awdpList);

            // Rules
            $awdpRules = get_posts ( array ( 'fields' => 'ids', 'numberposts' => -1, 'post_type' => AWDP_POST_TYPE, 'orderby' => 'title', 'order' => 'ASC', 'meta_query' => array ( array ( 'key' => 'discount_status', 'value' => 1 ) ) ) );
            $awdpRules = array_map(function ($v) {
                return ['id' => $v, 'name' => get_the_title($v)];
            }, $awdpRules);

            // Tags
            $taglist = get_terms(array('hide_empty' => false, 'taxonomy' => 'product_tag'));

            // $screen = get_current_screen();

            $defaultLabel = get_option('awdp_fee_label') ? get_option('awdp_fee_label') : __("Discount", "aco-woo-dynamic-pricing");

            wp_enqueue_script('jquery');

            if (in_array($currentScreen->id, $this->hook_suffix)) {

                if (!wp_script_is('wp-i18n', 'registered')) {
                    wp_register_script('wp-i18n', esc_url($this->assets_url) . 'js/i18n.min.js', array('jquery'), $this->_version, true);
                }

                wp_enqueue_script($this->_token . '-backend-script', esc_url($this->assets_url) . 'js/backend.js', array('jquery', 'wp-i18n'), $this->_version, true);
                wp_localize_script($this->_token . '-backend-script', 'awdp_object', array(
                        'api_nonce' => wp_create_nonce('wp_rest'),
                        'root' => rest_url('awdp/v1/'),
                        'cats' => (array)$categories,
                        'tags' => (array)$taglist,
                        'productlist' => (array)$awdpList,
                        'defaultlabel' => $defaultLabel,
                        'activerules' => (array)$awdpRules,
                    )
                );

                $plugin_rel_path = (dirname($this->file)) . '\languages'; /* Relative to WP_PLUGIN_DIR */

                if ( AWDP_Wordpress_Version >= 5 ) {
                    wp_set_script_translations(AWDP_TOKEN . '-backend-script', 'aco-woo-dynamic-pricing', $plugin_rel_path);
                }

            }

        }

        // Deactivation JS
        if ( $screenID == 'plugins' ) {
            wp_enqueue_script('awdp-deactivation-message', esc_url($this->assets_url).'js/message.js', array());
        }

    }

    // Deactivation Form
    public function wdp_deactivation_form() {
        $currentScreen = get_current_screen();
        $screenID = $currentScreen->id;
        if ( $screenID == 'plugins' ) {
            $view = '<div id="wdp-survey-form-wrap"><div id="wdp-survey-form">
            <p>If you have a moment, please let us know why you are deactivating this plugin. All submissions are anonymous and we only use this feedback for improving our plugin.</p>
            <form method="POST">
                <input name="Plugin" type="hidden" placeholder="Plugin" value="'.AWDP_TOKEN.'" required>
                <input name="Version" type="hidden" placeholder="Version" value="'.AWDP_VERSION.'" required>
                <input name="Date" type="hidden" placeholder="Date" value="'.date("m/d/Y").'" required>
                <input name="Website" type="hidden" placeholder="Website" value="'.get_site_url().'" required>
                <input name="Title" type="hidden" placeholder="Title" value="'.get_bloginfo( 'name' ).'" required>
                <input type="radio" id="wdp_temporarily" name="Reason" value="I\'m only deactivating temporarily">
                <label for="wdp_temporarily">I\'m only deactivating temporarily</label><br>
                <input type="radio" id="wdp_notneeded" name="Reason" value="I no longer need the plugin">
                <label for="wdp_notneeded">I no longer need the plugin</label><br>
                <input type="radio" id="wdp_short" name="Reason" value="I only needed the plugin for a short period">
                <label for="wdp_short">I only needed the plugin for a short period</label><br>
                <input type="radio" id="wdp_better" name="Reason" value="I found a better plugin">
                <label for="wdp_better">I found a better plugin</label><br>
                <input type="radio" id="wdp_upgrade" name="Reason" value="Upgrading to PRO version">
                <label for="wdp_upgrade">Upgrading to PRO version</label><br>
                <input type="radio" id="wdp_requirement" name="Reason" value="Plugin doesn\'t meets my requirement">
                <label for="wdp_requirement">Plugin doesn\'t meets my requirement</label><br>
                <input type="radio" id="wdp_broke" name="Reason" value="Plugin broke my site">
                <label for="wdp_broke">Plugin broke my site</label><br>
                <input type="radio" id="wdp_stopped" name="Reason" value="Plugin suddenly stopped working">
                <label for="wdp_stopped">Plugin suddenly stopped working</label><br>
                <input type="radio" id="wdp_bug" name="Reason" value="I found a bug">
                <label for="wdp_bug">I found a bug</label><br>
                <input type="radio" id="wdp_other" name="Reason" value="Other">
                <label for="wdp_other">Other</label><br>
                <p id="wdp-error"></p>
                <div class="wdp-comments" style="display:none;">
                    <textarea type="text" name="Comments" placeholder="Please specify" rows="2"></textarea>
                    <p>For support queries <a href="https://support.acowebs.com/portal/en/newticket?departmentId=361181000000006907&layoutId=361181000000074011" target="_blank">Submit Ticket</a></p>
                </div>
                <button type="submit" class="wdp_button" id="wdp_deactivate">Submit & Deactivate</button>
                <a href="#" class="wdp_button" id="wdp_cancel">Cancel</a>
                <a href="#" class="wdp_button" id="wdp_skip">Skip & Deactivate</a>
            </form></div></div>';
            echo $view;
        } ?>
        <style>
            #wdp-survey-form-wrap{ display: none;position: absolute;top: 0px;bottom: 0px;left: 0px;right: 0px;z-index: 10000;background: rgb(0 0 0 / 63%); } #wdp-survey-form{ display:none;margin-top: 15px;position: fixed;text-align: left;width: 40%;max-width: 600px;z-index: 100;top: 50%;left: 50%;transform: translate(-50%, -50%);background: rgba(255,255,255,1);padding: 35px;border-radius: 6px;border: 2px solid #fff;font-size: 14px;line-height: 24px;outline: none;}#wdp-survey-form p{font-size: 14px;line-height: 24px;padding-bottom:20px;margin: 0;} #wdp-survey-form .wdp_button { margin: 25px 5px 10px 0px; height: 42px;border-radius: 6px;background-color: #1eb5ff;border: none;padding: 0 36px;color: #fff;outline: none;cursor: pointer;font-size: 15px;font-weight: 600;letter-spacing: 0.1px;color: #ffffff;margin-left: 0 !important;position: relative;display: inline-block;text-decoration: none;line-height: 42px;} #wdp-survey-form .wdp_button#wdp_deactivate{background: #fff;border: solid 1px rgba(88,115,149,0.5);color: #a3b2c5;} .wdp_button#wdp_deactivate:disabled{opacity: .5; cursor: not-allowed;} #wdp-survey-form .wdp_button#wdp_skip{background: #fff;border: none;color: #a3b2c5;padding: 0px 15px;float:right;}#wdp-survey-form .wdp-comments{position: relative;}#wdp-survey-form .wdp-comments p{ position: absolute; top: -24px; right: 0px; font-size: 14px; padding: 0px; margin: 0px;} #wdp-survey-form .wdp-comments p a{text-decoration:none;}#wdp-survey-form .wdp-comments textarea{background: #fff;border: solid 1px rgba(88,115,149,0.5);width: 100%;line-height: 30px;resize:none;margin: 10px 0 0 0;} #wdp-survey-form p#wdp-error{margin-top: 10px;padding: 0px;font-size: 13px;color: #ea6464;}
        </style>
    <?php }

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

    /**
     * Installation. Runs on activation.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function install()
    {
        $this->_log_version_number();

    }

    /**
     * Log the plugin version number.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    private function _log_version_number()
    {
        update_option($this->_token . '_version', $this->_version);
    }

    public function delete_transient($arg = false)
    {
        if ($arg) {
            in_array(get_post_type($arg), ['product', AWDP_POST_TYPE, AWDP_PRODUCT_LIST]) && delete_transient(AWDP_PRODUCTS_TRANSIENT_KEY);
        } else {
            delete_transient(AWDP_PRODUCTS_TRANSIENT_KEY);
        }

    }

}
