<?php

if (!defined('ABSPATH'))
    exit;

class ACOPLW_Backend
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
        $this->_token = ACOPLW_TOKEN;
        $this->file = $file;
        $this->dir = dirname($this->file);
        $this->assets_dir = trailingslashit($this->dir) . 'assets';
        $this->assets_url = esc_url(trailingslashit(plugins_url('/assets/', $this->file)));

        $this->plugin_slug = 'abc';

        $this->script_suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';


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

        // Deactivation
        add_action('admin_footer', array($this, 'plw_deactivation_form'));

    }

    /*
    * Deactivation form
    * ver 3.1.8
    */
    public function plw_deactivation_form() {
        $currentScreen = get_current_screen();
        $screenID = $currentScreen->id;
        if ( $screenID == 'plugins' ) {
            $view = '<div id="plw-survey-form-wrap"><div id="plw-survey-form">
            <p>If you have a moment, please let us know why you are deactivating this plugin. All submissions are anonymous and we only use this feedback for improving our plugin.</p>
            <form method="POST">
                <input name="Plugin" type="hidden" placeholder="Plugin" value="'.ACOPLW_TOKEN.'" required>
                <input name="Version" type="hidden" placeholder="Version" value="'.ACOPLW_VERSION.'" required>
                <input name="Date" type="hidden" placeholder="Date" value="'.date("m/d/Y").'" required>
                <input name="Website" type="hidden" placeholder="Website" value="'.get_site_url().'" required>
                <input name="Title" type="hidden" placeholder="Title" value="'.get_bloginfo( 'name' ).'" required>
                <input type="radio" id="plw_temporarily" name="Reason" value="I\'m only deactivating temporarily">
                <label for="plw_temporarily">I\'m only deactivating temporarily</label><br>
                <input type="radio" id="plw_notneeded" name="Reason" value="I no longer need the plugin">
                <label for="plw_notneeded">I no longer need the plugin</label><br>
                <input type="radio" id="plw_short" name="Reason" value="I only needed the plugin for a short period">
                <label for="plw_short">I only needed the plugin for a short period</label><br>
                <input type="radio" id="plw_better" name="Reason" value="I found a better plugin">
                <label for="plw_better">I found a better plugin</label><br>
                <input type="radio" id="plw_upgrade" name="Reason" value="Upgrading to PRO version">
                <label for="plw_upgrade">Upgrading to PRO version</label><br>
                <input type="radio" id="plw_requirement" name="Reason" value="Plugin doesn\'t meets my requirement">
                <label for="plw_requirement">Plugin doesn\'t meets my requirement</label><br>
                <input type="radio" id="plw_broke" name="Reason" value="Plugin broke my site">
                <label for="plw_broke">Plugin broke my site</label><br>
                <input type="radio" id="plw_stopped" name="Reason" value="Plugin suddenly stopped working">
                <label for="plw_stopped">Plugin suddenly stopped working</label><br>
                <input type="radio" id="plw_bug" name="Reason" value="I found a bug">
                <label for="plw_bug">I found a bug</label><br>
                <input type="radio" id="plw_other" name="Reason" value="Other">
                <label for="plw_other">Other</label><br>
                <p id="plw-error"></p>
                <div class="plw-comments" style="display:none;">
                    <textarea type="text" name="Comments" placeholder="Please specify" rows="2"></textarea>
                    <p>For support queries <a href="https://support.acowebs.com/portal/en/newticket?departmentId=361181000000006907&layoutId=361181000000074011" target="_blank">Submit Ticket</a></p>
                </div>
                <button type="submit" class="plw_button" id="plw_deactivate">Submit & Deactivate</button>
                <a href="#" class="plw_button" id="plw_cancel">Cancel</a>
                <a href="#" class="plw_button" id="plw_skip">Skip & Deactivate</a>
            </form></div></div>';
            echo $view;
        } ?>
        <style>
            #plw-survey-form-wrap{ display: none;position: absolute;top: 0px;bottom: 0px;left: 0px;right: 0px;z-index: 10000;background: rgb(0 0 0 / 63%); } #plw-survey-form{ display:none;margin-top: 15px;position: fixed;text-align: left;width: 40%;max-width: 600px;z-index: 100;top: 50%;left: 50%;transform: translate(-50%, -50%);background: rgba(255,255,255,1);padding: 35px;border-radius: 6px;border: 2px solid #fff;font-size: 14px;line-height: 24px;outline: none;}#plw-survey-form p{font-size: 14px;line-height: 24px;padding-bottom:20px;margin: 0;} #plw-survey-form .plw_button { margin: 25px 5px 10px 0px; height: 42px;border-radius: 6px;background-color: #1eb5ff;border: none;padding: 0 36px;color: #fff;outline: none;cursor: pointer;font-size: 15px;font-weight: 600;letter-spacing: 0.1px;color: #ffffff;margin-left: 0 !important;position: relative;display: inline-block;text-decoration: none;line-height: 42px;} #plw-survey-form .plw_button#plw_deactivate{background: #fff;border: solid 1px rgba(88,115,149,0.5);color: #a3b2c5;} .plw_button#plw_deactivate:disabled{opacity: .5; cursor: not-allowed;} #plw-survey-form .plw_button#plw_skip{background: #fff;border: none;color: #a3b2c5;padding: 0px 15px;float:right;}#plw-survey-form .plw-comments{position: relative;}#plw-survey-form .plw-comments p{ position: absolute; top: -24px; right: 0px; font-size: 14px; padding: 0px; margin: 0px;} #plw-survey-form .plw-comments p a{text-decoration:none;}#plw-survey-form .plw-comments textarea{background: #fff;border: solid 1px rgba(88,115,149,0.5);width: 100%;line-height: 30px;resize:none;margin: 10px 0 0 0;} #plw-survey-form p#plw-error{margin-top: 10px;padding: 0px;font-size: 13px;color: #ea6464;}
        </style>
    <?php }

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
            __('Badges', 'aco-product-labels-for-woocommerce'), __('Badges', 'aco-product-labels-for-woocommerce'), 'edit_products', 'acoplw_badges_ui', array($this, 'admin_ui'), esc_url($this->assets_url) . '/images/icon.png', 25);
        $this->hook_suffix[] = add_submenu_page(
            'acoplw_badges_ui', __('Product Lists', 'aco-product-labels-for-woocommerce'), __('Product Lists', 'aco-product-labels-for-woocommerce'), 'edit_products', 'acoplw_product_lists_ui', array($this, 'admin_ui_pro_lists'));
        $this->hook_suffix[] = add_submenu_page(
            'acoplw_badges_ui', __('Settings', 'aco-product-labels-for-woocommerce'), __('Settings', 'aco-product-labels-for-woocommerce'), 'edit_products', 'acoplw_settings_ui', array($this, 'admin_ui_settings'));
    }

    public function admin_ui()
    {
        ACOPLW_Backend::view('admin-root', []);
    }

    public function add_settings_link($links)
    {
        $settings = '<a href="' . admin_url('admin.php?page=acoplw_badges_ui#/') . '">' . __('Badges','aco-product-labels-for-woocommerce') . '</a>';
        $products = '<a href="' . admin_url('admin.php?page=acoplw_product_lists_ui#/') . '">' . __('Product Lists','aco-product-labels-for-woocommerce') . '</a>';
        $upgrade = '<a href="https://acowebs.com/woocommerce-product-labels/" target="_blank" style="font-weight:600;color:#6D71F9;">' . __('Upgrade to PRO','aco-product-labels-for-woocommerce') . '</a>';
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
        ACOPLW_Backend::view('admin-lists', []);
    }

    public function admin_ui_settings()
    {
        ACOPLW_Backend::view('admin-settings', []);
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
        if (strpos($screenID, 'acoplw_') !== false) {

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

        $currentScreen  = get_current_screen();
        $screenID       = $currentScreen->id;
        $defaultLang    = ''; 
        $currentLang    = ''; 

        if (strpos($screenID, 'acoplw_') !== false) {

            if (!isset($this->hook_suffix) || empty($this->hook_suffix)) {
                return;
            }

            // All Categories
            $categories = get_terms ( 'product_cat', ['taxonomy' => 'product_cat', 'hide_empty' => false, 'fields' => 'id=>name'] );

            // Product List
            if ( get_option ('acoplw_dp_list_status') && get_option ('acoplw_dp_list_status') == 1 && function_exists('AWDP') ) {
                $acoplwList = get_posts ( array ( 'fields' => 'ids', 'numberposts' => -1, 'post_type' => array ( ACOPLW_PRODUCT_LIST, ACOPLW_DP_PRODUCT_LIST ), 'orderby' => 'title', 'order' => 'ASC' ) );
            } else {
                $acoplwList = get_posts ( array ( 'fields' => 'ids', 'numberposts' => -1, 'post_type' => ACOPLW_PRODUCT_LIST, 'orderby' => 'title', 'order' => 'ASC') );
            }

            $acoplwList     = array_map ( function ($v) {
                                return ['id' => $v, 'name' => get_the_title($v)];
                            }, $acoplwList ); 

            $previewImage   = plugin_dir_url(__FILE__). '../assets/images/preview-product.jpg';
            $taglist        = get_terms(array('hide_empty' => false, 'taxonomy' => 'product_tag'));
            $screen         = get_current_screen();

            // Language Parameters
            $defaultLang    = call_user_func ( array ( new ACOPLW_ML(), 'default_language' ), '' );
            $currentLang    = call_user_func ( array ( new ACOPLW_ML(), 'current_language' ), '' );

            $acoplwBaseurl  = ACOPLW_URL;

            wp_enqueue_script('jquery');

            if ( in_array ( $screen->id, $this->hook_suffix ) ) {

                if ( !wp_script_is ( 'wp-i18n', 'registered' ) ) {
                    wp_register_script ( 'wp-i18n', esc_url($this->assets_url) . 'js/i18n.min.js', array('jquery'), $this->_version, true );
                }

                wp_enqueue_script ( $this->_token . '-backend-script', esc_url($this->assets_url) . 'js/backend.js', array('jquery', 'wp-i18n'), $this->_version, true );
                wp_localize_script ( $this->_token . '-backend-script', 'acoplw_object', array(
                        'api_nonce'     => wp_create_nonce('wp_rest'),
                        'root'          => rest_url('acoplw/v1/'),
                        'cats'          => (array)$categories,
                        'tags'          => (array)$taglist,
                        'productlist'   => (array)$acoplwList,
                        'previewImage'  => $previewImage,
                        'defaultLang'   => $defaultLang,
                        'currentLang'   => $currentLang,
                        'acoplwBaseurl' => $acoplwBaseurl
                    )
                );

                $plugin_rel_path = ( dirname ( $this->file ) ) . '/../../languages'; /* Relative to WP_PLUGIN_DIR */
                if ( ACOPLW_Wordpress_Version >= 5 ) {
                    wp_set_script_translations ( ACOPLW_TOKEN . '-backend-script', 'aco-product-labels-for-woocommerce', $plugin_rel_path );
                }

            }

        }

        // Deactivation JS
        if ( $screenID == 'plugins' ) {
            wp_enqueue_script('acoplw-deactivation-message', esc_url($this->assets_url).'js/message.js', array());
        }

    }


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
            in_array(get_post_type($arg), ['product', ACOPLW_POST_TYPE, ACOPLW_PRODUCT_LIST]) && delete_transient(ACOPLW_PRODUCTS_TRANSIENT_KEY);
        } else {
            delete_transient(ACOPLW_PRODUCTS_TRANSIENT_KEY);
        }

    }

}
