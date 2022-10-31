<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://themehigh.com
 * @since      1.0.0
 *
 * @package    themehigh-multiple-addresses
 * @subpackage themehigh-multiple-addresses/admin
 */
if(!defined('WPINC')) { 
    die; 
}

if(!class_exists('THMAF_Admin')):
 
    /**
     * Admin class.
    */
    class THMAF_Admin {
        private $plugin_name;
        private $version;

        /**
         * Initialize the class and set its properties.
         *
         * @param      string    $plugin_name       The name of this plugin.
         * @param      string    $version    The version of this plugin.
         */
        public function __construct($plugin_name, $version) {
            $this->plugin_name = $plugin_name;
            $this->version = $version;
            $this->plugin_pages = array(
                'woocommerce_page_th_multiple_addresses_free', 'user-edit.php', 'profile.php', 'post.php',
            );
            $this->define_order_page_hook();
            add_action('admin_head', array( $this, 'review_request_banner_styles' ));
        }
        function review_request_banner_styles() {
            ?>

             <style>
                .thmaf-notice-action{padding: 8px 18px;background: #fff;color: #2a7cba;border-radius: 5px; border:1px solid #2a7cba;} 
                .thmaf-notice-action .thmaf-yes { background-color: #007cba; color: #fff; }
                .thmaf-notice-action:hover:not(.thmaf-yes) { background-color: #f2f5f6; }
                .thmaf-notice-action.thmaf-yes:hover { opacity: .9; }
                .thmaf-notice-action .dashicons{ display: none; }
                .thmaf-themehigh-logo { position: absolute; right: 20px; top: calc(50% - 13px); }
                .thmaf-notice-action { background-repeat: no-repeat; padding-left: 40px; background-position: 18px 8px; }
                .thmaf-themehigh-logo {  position: absolute; right: 20px; top: calc(50% - 13px); }
                a.thmaf-notice-action.thmaf-yes { background-color: #007cba; color: #fff; }
                .thmaf-review-wrapper { padding: 15px 28px 26px 10px; margin-top: 35px; }
                .thmaf-review-image { float: left; }
                .thmaf-review-content { padding-right: 180px; }
                .thmaf-yes { background-image: url('.THMAF_URL.'admin/assets/css/images/tick.svg); }
                .thmaf-done { background-image: url('.THMAF_URL.'admin/assets/css/images/done.svg); }
                .thmaf-remind { background-image: url('.THMAF_URL.'admin/assets/css/images/reminder.svg); }
                .thmaf-dismiss { background-image: url('.THMAF_URL.'admin/assets/css/images/close.svg); }
                .thmaf-review-content p{ padding-bottom: 14px; }
              </style>;
            <?php
            }

        /**
         * Enqueue style and script.
         *
         * @param string $hook The screen id
         *
         * @return void
         */
        public function enqueue_styles_and_scripts($hook) {
            if(!in_array($hook, $this->plugin_pages)) {
                return;
            }

            $screen = get_current_screen();
            $debug_mode = apply_filters('thmaf_debug_mode', false);
            $suffix = $debug_mode ? '' : '.min';        
            $this->enqueue_styles($suffix);
            $this->enqueue_scripts($suffix);
        }
        
        /**
         * Enqueue style.
         *
         * @param string $suffix The suffix of style sheets
         *
         * @return void
         */
        public function enqueue_styles($suffix) {
            //wp_enqueue_style('woocommerce_admin_styles', THMAF_WOO_ASSETS_URL.'css/admin.css');
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_style('thmaf-admin-style', THMAF_ASSETS_URL_ADMIN . 'css/thmaf-admin'. $suffix .'.css', $this->version);
        }

        /**
         * Enqueue script.
         *
         * @param string $suffix The suffix of js file
         *
         * @return void
         */
        public function enqueue_scripts($suffix) {
            $deps = array('jquery', 'jquery-ui-dialog', 'jquery-ui-sortable', 'jquery-tiptip', 'wc-enhanced-select', 'select2', 'wp-color-picker');
            
            wp_enqueue_script('thmaf-admin-script', THMAF_ASSETS_URL_ADMIN . 'js/thmaf-admin'. $suffix .'.js', $deps, $this->version, false);
            
            $script_var = array(
                'admin_url' => admin_url(),
                'ajaxurl'   => admin_url('admin-ajax.php'),
            );
            wp_localize_script('thmaf-admin-script', 'thmaf_var', $script_var);
        }

        /**
         * Function for set capability.
         *
         *
         * @return string
         */
        public function thmaf_capability() {
            $allowed = array('manage_woocommerce', 'manage_options');
            $capability = apply_filters('thmaf_required_capability', 'manage_options');

            if(!in_array($capability, $allowed)) {
                $capability = 'manage_woocommerce';
            }
            return $capability;
        }
        
        /**
         * Function for set admin menu.
         *
         *
         * @return void
         */
        public function admin_menu() {
            $capability = $this->thmaf_capability();
            $this->screen_id = add_submenu_page('woocommerce', esc_html__('WooCommerce Multiple Addresses', 'themehigh-multiple-addresses'), esc_html__('Manage Address', 'themehigh-multiple-addresses'), $capability, 'th_multiple_addresses_free', array($this, 'output_settings'));
        }
        
        /**
         * Function for setting screen id.
         *
         * @param string $ids The unique screen id
         *
         * @return string
         */
        public function add_screen_id($ids) {
            $ids[] = 'woocommerce_page_th_multiple_addresses_free';
            $ids[] = strtolower(THMAF_i18n::__t('WooCommerce')) .'_page_th_multiple_addresses_free';

            return $ids;
        }

        /**
         * function for setting link.
         *
         * @param string $links The plugin action link
         *
         * @return void
         */
        public function plugin_action_links($links) {
            $settings_link = '<a href="'.esc_url(admin_url('admin.php?&page=th_multiple_addresses_free')).'">'. esc_html__('Settings', 'themehigh-multiple-addresses') .'</a>';
            array_unshift($links, $settings_link);
            return $links;
        }
        
        /**
         * Function for premium version notice.
         *
         *
         * @return void
         */
        private function _output_premium_version_notice() { ?>
            <div id="message" class="wc-connect updated thpladmin-notice thmaf-admin-notice">
                <div class="squeezer">
                    <table>
                        <tr>
                            <td width="70%">
                                <!-- <p><strong><i>WooCommerce Multiple addresses Pro</i></strong> premium version provides more features to setup your checkout page and cart page.</p> -->
                                <p>
                                    <strong><i><a href="<?php echo esc_url('https://www.themehigh.com/product/woocommerce-multiple-addresses-pro/'); ?>">
                                        <?php echo esc_html__('WooCommerce Multiple addresses', 'themehigh-multiple-addresses');?>

                                    </a></i></strong><?php echo esc_html__('premium version provides more features to manage the users addresses', 'themehigh-multiple-addresses'); ?>
                                    <ul>
                                    <li>
                                    <?php echo esc_html__('Let Your Shoppers Choose from a List of Saved Addresses', 'themehigh-multiple-addresses'); ?>
                                    </li>
                                    <li>
                                    <?php echo esc_html__('Manage All Addresses from My Account Page', 'themehigh-multiple-addresses'); ?>
                                        
                                    </li>
                                    <li>
                                        <?php echo esc_html__('Save Time with Google Autocomplete Feature', 'themehigh-multiple-addresses'); ?>
                                    </li>
                                    <li>
                                        <?php echo esc_html__('Custom Address Formats through Overriding', 'themehigh-multiple-addresses'); ?>
                                    </li>
                                    <li>
                                        <?php echo esc_html__('Display Your Multiple Address Layouts in Style', 'themehigh-multiple-addresses'); ?> 
                                    </li>
                                    <li>
                                        <?php echo esc_html__('Highly Compatible with', 'themehigh-multiple-addresses'); ?> 
                                            <strong><i><a href="<?php echo esc_url('https://www.themehigh.com/product/woocommerce-checkout-field-editor-pro/'); ?>">
                                                <?php echo esc_html__('WooCommerce Checkout Field Editor', 'themehigh-multiple-addresses'); ?>
                                            </a></i></li>
                                    </ul>
                            </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        <?php }

        /**
         * function for output settings.
         *
         * @return void
         */
        public function output_settings() {
            //$this->_output_premium_version_notice();
            $tab  = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general_settings';
            if($tab ==='general_settings') {
                $general_settings = THMAF_Admin_Settings_General::instance();   
                $general_settings->render_page();
            } else if($tab ==='pro') {
                $general_settings = THMAF_Admin_Settings_Pro::instance();   
                $general_settings->render_page();
            }
        }

        /**
         * Function for define order page hook.
         */
        public function define_order_page_hook() {
            add_action( 'admin_init', array( $this, 'thmaf_notice_actions' ), 20 );
            add_action( 'admin_notices', array($this, 'output_review_request_link'));
            add_action('woocommerce_admin_order_data_after_shipping_address', array($this, 'thwma_update_woo_order_status'),10,1);
        }

        /**
         * Function for update woocommerce order status.
         * 
         * @param array $order The order details
         */
        function thwma_update_woo_order_status($order) {
            $settings = THMAF_Utils::get_setting_value('settings_multiple_shipping');
            $enable_multi_shipping = isset($settings['enable_multi_shipping']) ? $settings['enable_multi_shipping']:'';
            $user_id = get_current_user_id();
            if($enable_multi_shipping == 'yes') {
                $enable_multi_ship_data = '';
                if (is_user_logged_in()) {
                    $enable_multi_ship_data = get_user_meta($user_id, THMAF_Utils::USER_META_ENABLE_MULTI_SHIP, true);
                }

                if($enable_multi_ship_data == 'yes') {
                    echo '<input type="hidden" name="multi_ship_enabled" value="yes" class="multi_ship_enabled">';
                } else {
                    echo '<input type="hidden" name="multi_ship_enabled" value="" class="multi_ship_enabled">';
                }
            } else {
                echo '<input type="hidden" name="multi_ship_enabled" value="" class="multi_ship_enabled">';
            }
        }



        function thmaf_notice_actions(){
            if( !(isset($_GET['thmaf_remind']) || isset($_GET['thmaf_dissmis']) || isset($_GET['thmaf_reviewed'])) ) {
                return;
            }

            $nonse = isset($_GET['thmaf_review_nonce']) ? $_GET['thmaf_review_nonce'] : false;

            if(!wp_verify_nonce($nonse, 'thmaf_notice_security')){
                die();
            }

            $now = time();

            $thmaf_remind = isset($_GET['thmaf_remind']) ? sanitize_text_field( wp_unslash($_GET['thmaf_remind'])) : false;
            if($thmaf_remind){
                update_user_meta( get_current_user_id(), 'thmaf_review_skipped', true );
                update_user_meta( get_current_user_id(), 'thmaf_review_skipped_time', $now );
            }

            $thmaf_dissmis = isset($_GET['thmaf_dissmis']) ? sanitize_text_field( wp_unslash($_GET['thmaf_dissmis'])) : false;
            if($thmaf_dissmis){
                update_user_meta( get_current_user_id(), 'thmaf_review_dismissed', true );
                update_user_meta( get_current_user_id(), 'thmaf_review_dismissed_time', $now );
            }

            $thmaf_reviewed = isset($_GET['thmaf_reviewed']) ? sanitize_text_field( wp_unslash($_GET['thmaf_reviewed'])) : false;
            if($thmaf_reviewed){
                update_user_meta( get_current_user_id(), 'thmaf_reviewed', true );
                update_user_meta( get_current_user_id(), 'thmaf_reviewed_time', $now );
            }
        }


        function output_review_request_link(){
            if(!apply_filters('thmaf_show_dismissable_admin_notice', true)){
                return;
            }

            $thmaf_reviewed = get_user_meta( get_current_user_id(), 'thmaf_reviewed', true );
            if($thmaf_reviewed){
                return;
            }

            $now = time();
            $dismiss_life  = apply_filters('thmafof_dismissed_review_request_notice_lifespan', 3 * MONTH_IN_SECONDS);
            $reminder_life = apply_filters('thmafof_skip_review_request_notice_lifespan', 1 * DAY_IN_SECONDS);

            $is_dismissed   = get_user_meta( get_current_user_id(), 'thmaf_review_dismissed', true );
            $dismisal_time  = get_user_meta( get_current_user_id(), 'thmaf_review_dismissed_time', true );
            $dismisal_time  = $dismisal_time ? $dismisal_time : 0;
            $dismissed_time = $now - $dismisal_time;
            if( $is_dismissed && ($dismissed_time < $dismiss_life) ){
                return;

            }

            $is_skipped = get_user_meta( get_current_user_id(), 'thmaf_review_skipped', true );
            $skipping_time = get_user_meta( get_current_user_id(), 'thmaf_review_skipped_time', true );
            $skipping_time = $skipping_time ? $skipping_time : 0;
            $remind_time = $now - $skipping_time;

            if($is_skipped && ($remind_time < $reminder_life) ){
                return;
            }

            $thmafof_since = get_option('thmafof_since');

            if(!$thmafof_since){
                $now = time();
                update_option('thmafof_since', $now, 'no' );
            }

            $thmafof_since = $thmafof_since ? $thmafof_since : $now;
            $show_notice_time  = 15 * DAY_IN_SECONDS;

            $value_period = $thmafof_since + $show_notice_time;

            if ($value_period > $now) {
                return ;
            }

            $this->render_review_request_notice();
        }

        /**
         * Function for review request notice.
         * 
         */
        function render_review_request_notice(){
            $current_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general_settings';
            $current_section = isset( $_GET['section'] ) ? sanitize_key( $_GET['section'] ) : '';

            $admin_url   = $this->get_admin_url($current_tab, $current_section);

            $remind_url  = $admin_url . '&thmaf_remind=true&thmaf_review_nonce=' . wp_create_nonce( 'thmaf_notice_security');
            $dismiss_url = $admin_url . '&thmaf_dissmis=true&thmaf_review_nonce=' . wp_create_nonce( 'thmaf_notice_security');
            $reviewed_url= $admin_url . '&thmaf_reviewed=true&thmaf_review_nonce=' . wp_create_nonce( 'thmaf_notice_security');
            ?>

            <div class="notice notice-info thpladmin-notice is-dismissible thmaf-review-wrapper" data-nonce="<?php echo wp_create_nonce( 'thmaf_notice_security'); ?>">
                <div class="thmaf-review-image">
                    <img src="<?php echo esc_url(THMAF_URL .'admin/assets/css/images/review-left.png'); ?>" alt="themehigh">
                </div>
                <div class="thmaf-review-content">
                    <h3><?php _e('Tell us what you loved', 'woo-extra-product-options'); ?></h3>
                    <p><?php _e('We are waiting to know your experience using the plugin Multiple Shipping Address for Woocommerce. Tell us what you loved about the latest improvements. Also, drop in your suggestions, review and help us grow better.', 'woo-extra-product-options'); ?></p>
                    <div class="action-row">
                        <a class="thmaf-notice-action thmaf-yes" onclick="window.open('https://wordpress.org/plugins/themehigh-multiple-addresses/#reviews', '_blank')" style="margin-right:16px; text-decoration: none">
                            <?php _e("Yes, today", 'woo-extra-product-options'); ?>
                        </a>

                        <a class="thmaf-notice-action thmaf-done" href="<?php echo esc_url($reviewed_url); ?>" style="margin-right:16px; text-decoration: none">
                            <?php _e('Already, Did', 'woo-extra-product-options'); ?>
                        </a>

                        <a class="thmaf-notice-action thmaf-remind" href="<?php echo esc_url($remind_url); ?>" style="margin-right:16px; text-decoration: none">
                            <?php _e('Maybe later', 'woo-extra-product-options'); ?>
                        </a>

                        <a class="thmaf-notice-action thmaf-dismiss" href="<?php echo esc_url($dismiss_url); ?>" style="margin-right:16px; text-decoration: none">
                            <?php _e("Nah, Never", 'woo-extra-product-options'); ?>
                        </a>
                    </div>
                </div>
                <div class="thmaf-themehigh-logo">
                    <span class="logo" style="float: right">
                        <a target="_blank" href="https://www.themehigh.com">
                            <img src="<?php echo esc_url(THMAF_URL .'admin/assets/css/images/logo.svg'); ?>" style="height:19px;margin-top:4px;" alt="themehigh"/>
                        </a>
                    </span>
                </div>
            </div>

            <?php
        }

        /**
         * Get admin Url.
         *
         * @param string $tab the current tab name
         * @param string $section the current section name
         *
         * @return admin url
         */
        public function get_admin_url($tab = false, $section = false) {
            $url = 'admin.php?&page=th_multiple_addresses_free';
            if($tab && !empty($tab)) {
                $url .= '&tab='. $tab;
            }
            if($section && !empty($section)) {
                $url .= '&section='. $section;
            }
            return admin_url($url);
        }

        
    }
endif;