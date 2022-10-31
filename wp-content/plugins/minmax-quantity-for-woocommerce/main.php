<?php
define( "BeRocket_MM_Quantity_domain", 'minmax-quantity-for-woocommerce');
define( "MM_QUANTITY_TEMPLATE_PATH", plugin_dir_path( __FILE__ ) . "templates/" );
load_plugin_textdomain('minmax-quantity-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
require_once(plugin_dir_path( __FILE__ ).'berocket/framework.php');
foreach (glob(__DIR__ . "/includes/*.php") as $filename)
{
    include_once($filename);
}
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * Class BeRocket_MM_Quantity
 */
class BeRocket_MM_Quantity extends BeRocket_Framework {
    public static $settings_name = 'br_mm_quantity_options';
    public $info, $defaults, $values, $notice_array;
    protected static $instance;
    protected $disable_settings_for_admin = array();
    function __construct () {
        $this->info = array(
            'id'          => 9,
            'lic_id'      => 17,
            'version'     => BeRocket_MM_Quantity_version,
            'plugin'      => '',
            'slug'        => '',
            'key'         => '',
            'name'        => '',
            'plugin_name' => 'MM_Quantity',
            'full_name'   => 'WooCommerce Min and Max Quantities',
            'norm_name'   => 'Min/Max Quantities',
            'price'       => '',
            'domain'      => 'minmax-quantity-for-woocommerce',
            'templates'   => MM_QUANTITY_TEMPLATE_PATH,
            'plugin_file' => BeRocket_MM_Quantity_file,
            'plugin_dir'  => __DIR__,
        );
        $this->defaults = array(
            'hide_checkout'         => '',
            'checkout_class'        => '.checkout-button',
            'checkout_mini_class'   => '.checkout',
            'prevent_add_to_cart'   => '',
            'full_or_limitation'    => '',
            'cart_min_price'        => '',
            'cart_max_price'        => '',
            'cart_min_quantity'     => '',
            'cart_max_quantity'     => '',
            'display_limitations'   => '',
            'fix_duplicate'         => '1',
            'fix_duplicate_page'    => array(
                '1' => 'checkout',
                '2' => 'cart',
            ),
            'groups'                => array(),
            'min_qty_text'          => 'Quantity of products in cart must be <strong>%value%</strong> or more',
            'max_qty_text'          => 'Quantity of products in cart must be <strong>%value%</strong> or less',
            'min_price_text'        => 'Total cost of products in cart must be <strong>%value%</strong> or more',
            'max_price_text'        => 'Total cost of products in cart must be <strong>%value%</strong> or less',
            'custom_css'            => '',
            'addons'                => array(),
            'script'                => array(
                'js_page_load'      => '',
            ),
            'fontawesome_frontend_disable'    => '',
            'fontawesome_frontend_version'    => '',
            'input_max_qty_reached_text'      => "Maximum quantity for this product is reached please check your cart",
        );
        $this->values = array(
            'settings_name' => 'br_mm_quantity_options',
            'option_page'   => 'br-mm-quantity',
            'premium_slug'  => 'woocommerce-minmax-quantity',
            'free_slug'     => 'minmax-quantity-for-woocommerce'
        );

        // List of the features missed in free version of the plugin
        $this->feature_list = array(
            'Quantity and cost limits for products from category',
            'Quantity and cost limits for products from specific attribute',
            'Quantity and cost limits for a specific user role',
            'Quantity and cost limits for group of products',
            'Infinite groups of products',
            'Multiplicity for products in limitation',
            'Use limitation for each product or for products summary',
            'Prevent add to cart when limit is reached',
            'Exclude product from rules'
        );

        if( method_exists($this, 'include_once_files') ) {
            $this->include_once_files();
        }
        if ( $this->init_validation() ) {
            new BeRocket_minmax_custom_post();
        }
        do_action('BeRocket_MM_Quantity__construct');
        parent::__construct( $this );

        if ( $this->init_validation() ) {
            $options = parent::get_option();
            if( ! empty($options['addons']) && is_array($options['addons']) ) {
                foreach($options['addons'] as $addon) {
                    include_once(plugin_dir_path( __FILE__ ) . "includes/addons/{$addon}.php");
                }
            }
            add_action ( 'init', array( $this, 'init' ) );
            add_action ( 'wp_head', array( $this, 'set_styles' ) );
            add_action ( 'admin_init', array( $this, 'register_mm_quantity_options' ) );
            add_action ( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
            add_action ( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_action ( 'wp_ajax_mm_quantity_ajax', array( $this,'roles_ajax_choose' ) );    
            
            add_action( 'woocommerce_product_options_inventory_product_data', array( $this, 'wc_product_field' ) );
            add_action( 'save_post', array( $this, 'wc_save_product' ) );
            add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'variation_settings_fields' ), 10, 3 );
            add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation_settings_fields' ), 10, 2 );
            //add_action( 'woocommerce_after_calculate_totals', array( $this, 'cart_calculate_total' ), 10, 1 );
            if( ! isset($_GET['wc-ajax']) || ! in_array($_GET['wc-ajax'], array('apply_coupon')) ) {
                add_action( 'woocommerce_after_calculate_totals', array( $this, 'new_calculate_total' ), 10, 1 );
            }
            add_action( 'wp_head', array($this, 'fix_error_duplicate') );
            if( ! empty($options['prevent_add_to_cart']) ) {
                add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_to_cart' ), 10, 3 );
                add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'woocommerce_after_cart_item_quantity_update' ), 10, 3 );
                add_action( 'woocommerce_cart_item_restored', array( $this, 'woocommerce_cart_item_restored' ), 10, 2 );
            }
            add_action('woocommerce_before_shop_loop', array($this, 'rewrite_wc_print_notices'), 9);
            add_filter( 'woocommerce_add_to_cart_fragments', array( __CLASS__, 'woocommerce_add_to_cart_fragments' ), 900, 1 );
            add_filter ( 'BeRocket_updater_menu_order_custom_post', array($this, 'menu_order_custom_post') );
            add_filter ( 'berocket_update_qunatity_limitation_result_array', array($this, 'update_qunatity_limitation_result_array'), 10, 2 );
        }
    }

    public function rewrite_wc_print_notices() {
        if( $priority = has_action('woocommerce_before_shop_loop', 'wc_print_notices') ) {
            remove_action('woocommerce_before_shop_loop', 'wc_print_notices', $priority);
            echo '<div class="berocket_wc_print_notices">';
            wc_print_notices();
            echo '</div>';
        }
    }

    public static function woocommerce_add_to_cart_fragments( $fragments ) {
        ob_start();
        echo '<div class="berocket_wc_print_notices">';
        wc_print_notices();
        echo '</div>';
        $fragments['div.berocket_wc_print_notices'] = ob_get_clean();
        
        return $fragments;
    }

    /**
     * Framework class will use this function to check it plugin is activated. For example if we need
     * woocommerce installed to run the plugin we can check here and return false if we need to stop
     *
     * @return boolean
     */
    public function init_validation() {
        return ( ( is_plugin_active( 'woocommerce/woocommerce.php' ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) && 
            br_get_woocommerce_version() >= 2.1 );
    }

    /**
     * Function remove settings from database
     *
     * @return void
     */
    public static function deactivation() {
        if( ! empty(static::$settings_name) ) {
            do_action('brfr_deactivate_' . static::$settings_name);
            delete_option( static::$settings_name );
            delete_option( 'mm-Role-option' );
        }
    }

    /**
     * Function add options button to admin panel if parent will not do it self
     *
     * @access public
     *
     * @return void
     */
    public function admin_menu() {
        if ( parent::admin_menu() ) {
            add_submenu_page(
                'woocommerce',
                __( $this->info[ 'norm_name' ]. ' Settings', $this->info[ 'domain' ] ),
                __( $this->info[ 'norm_name' ], $this->info[ 'domain' ] ),
                'manage_options',
                $this->values[ 'option_page' ],
                array(
                    $this,
                    'option_form'
                )
            );
        }
    }
    public function admin_settings( $tabs_info = array(), $data = array() ) {
        parent::admin_settings(
            array(
                'General' => array(
                    'icon' => 'cog',
                ),
                'Text'     => array(
                    'icon' => 'font',
                ),
                'CSS'     => array(
                    'icon' => 'css3',
                ),
                'Products Limitation' => array(
                    'icon' => 'plus-square',
                    'link' => admin_url( 'edit.php?post_type=br_minmax_limitation' ),
                ),
                'License' => array(
                    'icon' => 'unlock-alt',
                    'link' => admin_url( 'admin.php?page=berocket_account' )
                ),
                'Addons'     => array(
                    'icon' => 'cubes',
                ),
            ),
            array(
            'General' => array(
                'hide_checkout' => array(
                    "label"     => __('Hide Checkout button', 'minmax-quantity-for-woocommerce'),
                    "type"      => "checkbox",
                    "name"      => "hide_checkout",
                    "value"     => "1",
                    "label_for" => __('if min/max requirements no passed hide checkout button.', 'minmax-quantity-for-woocommerce')
                ),
                'checkout_class' => array(
                    "label"         => '',
                    "type"          => "text",
                    "name"          => "checkout_class",
                    "label_be_for"  => __('Checkout cart button class: ', 'minmax-quantity-for-woocommerce')
                ),
                'checkout_mini_class' => array(
                    "label"         => '',
                    "type"          => "text",
                    "name"          => "checkout_mini_class",
                    "label_be_for"  => __('Checkout widget cart button class: ', 'minmax-quantity-for-woocommerce')
                ),
                'cart_quantity' => array(
                    "label"     => __( 'Cart products quantity', 'minmax-quantity-for-woocommerce' ),
                    "items"     => array(
                        array(
                            "type"          => "number",
                            "name"          => "cart_min_quantity",
                            "label_be_for"  => __('Minimum: ', 'minmax-quantity-for-woocommerce')
                        ),
                        array(
                            "type"      => "number",
                            "name"      => "cart_max_quantity",
                            "label_be_for" => __( 'Maximum: ' , "minmax-quantity-for-woocommerce" ),
                        ),
                    ),
                ),
                'cart_cost' => array(
                    "label"     => __( 'Cart products price', 'minmax-quantity-for-woocommerce' ),
                    "items"     => array(
                        'image' => array(
                            "type"          => "number",
                            "name"          => "cart_min_price",
                            "label_be_for"  => __('Minimum: ', 'minmax-quantity-for-woocommerce')
                        ),
                        array(
                            "type"      => "number",
                            "name"      => "cart_max_price",
                            "label_be_for" => __( 'Maximum: ' , "minmax-quantity-for-woocommerce" ),
                        ),
                    ),
                ),
                'prevent_add_to_cart' => array(
                    "label"     => __('Prevent add to cart', 'minmax-quantity-for-woocommerce'),
                    "type"      => "checkbox",
                    "name"      => "prevent_add_to_cart",
                    "value"     => "1",
                    "label_for" => __('Doesn\'t add products when limit reached', 'minmax-quantity-for-woocommerce')
                ),
                'full_or_limitation' => array(
                    "label"     => __('Full OR Limitation', 'minmax-quantity-for-woocommerce'),
                    "type"      => "checkbox",
                    "name"      => "full_or_limitation",
                    "value"     => "1"
                ),
                'fix_singular' => array(
                    "label"     => __('Display on product pages errors only for this product', 'minmax-quantity-for-woocommerce'),
                    "type"      => "checkbox",
                    "name"      => "fix_singular",
                    "value"     => "1",
                ),
                'fix_duplicate' => array(
                    "label"     => __('Fix error duplicate', 'minmax-quantity-for-woocommerce'),
                    "type"      => "checkbox",
                    "class"     => "br_minmax_fix_duplicate",
                    "name"      => "fix_duplicate",
                    "value"     => "1",
                    "label_for" => __('If on some pages errors displayed twice enable this option', 'minmax-quantity-for-woocommerce')
                ),
                'pages_to_fix' => array(
                    'label'     => __('Pages to fix duplicate', 'minmax-quantity-for-woocommerce'),
                    'tr_class'  => 'br_minmax_fix_duplicate_show',
                    "type"      => "checkbox",
                    "name"      => array("fix_duplicate_page", "1"),
                    "value"     => "checkout",
                    "label_for" => __('Chekcout page', 'minmax-quantity-for-woocommerce')
                ),
                'pages_to_fix_2' => array(
                    'label'     => '',
                    'tr_class'  => 'br_minmax_fix_duplicate_show',
                    "type"      => "checkbox",
                    "name"      => array("fix_duplicate_page", "2"),
                    "value"     => "cart",
                    "label_for" => __('Cart page', 'minmax-quantity-for-woocommerce')
                ),
                'pages_to_fix_3' => array(
                    'label'     => '',
                    'tr_class'  => 'br_minmax_fix_duplicate_show',
                    "type"      => "checkbox",
                    "name"      => array("fix_duplicate_page", "3"),
                    "value"     => "product",
                    "label_for" => __('Product page', 'minmax-quantity-for-woocommerce')
                ),
                'pages_to_fix_4' => array(
                    'label'     => '',
                    'tr_class'  => 'br_minmax_fix_duplicate_show',
                    "type"      => "checkbox",
                    "name"      => array("fix_duplicate_page", "4"),
                    "value"     => "archive",
                    "label_for" => __('Archive page', 'minmax-quantity-for-woocommerce')
                ),
                'pages_to_fix_5' => array(
                    'label'     => '',
                    'tr_class'  => 'br_minmax_fix_duplicate_show',
                    "type"      => "checkbox",
                    "name"      => array("fix_duplicate_page", "5"),
                    "value"     => "other",
                    "label_for" => __('Other pages', 'minmax-quantity-for-woocommerce')
                ),
            ),
            'Text'     => array(
                'min_qty_text' => array(
                    "type"     => "text",
                    "label"    => __('Minimum Quantity Message', 'minmax-quantity-for-woocommerce'),
                    "tr_class" => "berocket_text_input_message",
                    "name"     => "min_qty_text",
                ),
                'max_qty_text' => array(
                    "type"     => "text",
                    "label"    => __('Maximum Quantity Message', 'minmax-quantity-for-woocommerce'),
                    "tr_class" => "berocket_text_input_message",
                    "name"     => "max_qty_text",
                ),
                'min_price_text' => array(
                    "type"     => "text",
                    "label"    => __('Minimum Price Message', 'minmax-quantity-for-woocommerce'),
                    "tr_class" => "berocket_text_input_message",
                    "name"     => "min_price_text",
                ),
                'max_price_text' => array(
                    "type"     => "text",
                    "label"    => __('Maximum Price Message', 'minmax-quantity-for-woocommerce'),
                    "tr_class" => "berocket_text_input_message",
                    "name"     => "max_price_text",
                ),
            ),
            'CSS'     => array(
                array(
                    "type"  => "textarea",
                    "label" => "Custom CSS",
                    "name"  => "custom_css",
                ),
            ),
            'Addons' => array(
                'addon_input_limitation' => array(
                    "label"     => __('Input Limitation', 'minmax-quantity-for-woocommerce'),
                    "label_for" => __('Set correct limitation for product quantity input field on product page and cart page', 'minmax-quantity-for-woocommerce'),
                    "type"      => "checkbox",
                    "class"     => "berocket_addons",
                    "name"      => array("addons", "1"),
                    "value"     => "set_input_limitation",
                ),
                'addon_variation_limitation' => array(
                    "label"     => __('Variation Limitation on Product page', 'minmax-quantity-for-woocommerce'),
                    //"label_for" => __('Set correct limitation for product quantity input field on product page and cart page', 'minmax-quantity-for-woocommerce'),
                    "type"      => "checkbox",
                    "class"     => "berocket_addons",
                    "name"      => array("addons", "2"),
                    "value"     => "variation_text",
                ),
            ),
        ) );
    }
    /**
     * Function that use for WordPress init action
     *
     * @return void
     */
    public function init () {
        parent::init();
        $options = parent::get_option();
        wp_enqueue_script("jquery");
        wp_register_style( 'berocket_mm_quantity_style', plugins_url( 'css/shop.css', __FILE__ ), "", BeRocket_MM_Quantity_version );
        wp_enqueue_style( 'berocket_mm_quantity_style' );
        add_filter('berocket_minmax_group_limitations_on_product_check', array($this, 'group_limitations_on_product_check'), 10, 5);
        add_filter('berocket_minmax_group_limitations_before_error_check', array($this, 'group_limitations_before_error_check'), 10, 3);
        add_filter('berocket_minmax_group_limitations_filter', array($this, 'group_limitations_filter'), 10, 6);
        add_filter('berocket_minmax_check_product_error', array($this, 'check_product_error'), 10, 4);
        add_filter('berocket_minmax_group_limitation_settings_text', array($this, 'limitation_settings_text'), 10, 3);
        $version_option = get_option('BeRocket_MM_Quantity_version');
        if( empty($version_option) ) {
            update_option('BeRocket_MM_Quantity_version', BeRocket_MM_Quantity_version);
            $BeRocket_minmax_custom_post = BeRocket_minmax_custom_post::getInstance();
            $limitation_ids = $BeRocket_minmax_custom_post->get_custom_posts_frontend();
            foreach($limitation_ids as $limitation_id) {
                $settings_minmax = get_post_meta( $limitation_id, 'br_minmax_limitation', true );
                $settings_minmax['use_local_text'] = '1';
                update_post_meta($limitation_id, 'br_minmax_limitation', $settings_minmax);
            }
        }
    }

    public function group_limitations_on_product_check($group_limitations, $index, $values, $get_cart, $product_variables) {
        if( ! isset($group_limitations[$index]) ) {
            $group_limitations[$index] = array('qty' => 0, 'price' => 0, 'products' => array(), 'var_products_id' => array());
        }
        $group_limitations[$index]['qty'] += $values['quantity'];
        $group_limitations[$index]['price'] += $this->get_line_total_cart_item($values);
        $group_limitations[$index]['products'][] = $product_variables['product_post']->post_title;
        $group_limitations[$index]['products_id'][] = $product_variables['product_id'];
        $current_product_id = (empty($product_variables['var_product_id']) ? $product_variables['product_id'] : $product_variables['var_product_id']);
        if( ! isset($group_limitations[$index]['var_products_id'][$current_product_id]) ) {
            $group_limitations[$index]['var_products_id'][$current_product_id] = 0;
        }
        $group_limitations[$index]['var_products_id'][$current_product_id] += $values['quantity'];
        return $group_limitations;
    }
    public function get_line_total_cart_item($cart_item) {
		$line_total = 0;
		if( isset($cart_item['line_total_test']) ) {
			$line_total += $cart_item['line_total_test'];
		} else {
			$line_total += br_get_value_from_array($cart_item, 'line_total', 0);
			if( ($line_tax_data = br_get_value_from_array($cart_item, array('line_tax_data', 'total'))) && is_array($line_tax_data) ) {
				foreach($line_tax_data as $line_tax) {
					$line_total += $line_tax;
				}
			}
		}
		
		return $line_total;
	}
    public function group_limitations_before_error_check($group_limitations, $get_cart, $options) {
        if( ! empty($group_limitations[0]) ) {
            $group_limitations[0]['settings_minmax'] = array(
                'min_qty_text' => $options['min_qty_text'],
                'max_qty_text' => $options['max_qty_text'],
                'min_price_text' => $options['min_price_text'],
                'max_price_text' => $options['max_price_text'],
                'limitations' => array(
                    '1' => array(
                        'min_qty' => floatval($options['cart_min_quantity']),
                        'max_qty' => floatval($options['cart_max_quantity']),
                        'min_price' => floatval($options['cart_min_price']),
                        'max_price' => floatval($options['cart_max_price']),
                    )
                ),
            );
        }
        return $group_limitations;
    }
    public function group_limitations_filter($filter_array, $limitation_variables, $values, $get_cart, $product_variables, $options) {
        //ADD QUANTITY AND PRICE TO GROUPED LIMITATION
        $is_variable = $product_variables['product']->is_type('variable');
        $is_variation = ! empty($product_variables['var_product']);
        $check_condition = ! empty($limitation_variables['check_condition']);
        $var_check_condition = ! empty($limitation_variables['var_check_condition']);
        if( (! $is_variable && $check_condition ) || ( $is_variable && $is_variation && $var_check_condition ) ) {
            $filter_array['group_limitations'] = apply_filters('berocket_minmax_group_limitations_on_product_check', $filter_array['group_limitations'], $limitation_variables['limitation_id'], $values, $get_cart, $product_variables, $options);
        }
        return $filter_array;
    }
    public function check_product_error($error, $settings_limitation, $qty, $price) {
        if( ! empty($settings_limitation['min_qty']) && $qty < apply_filters('berocket_check_product_error_min_qty', $settings_limitation['min_qty']) ) {
            $error['min_qty'][] = apply_filters('berocket_check_product_error_min_qty', $settings_limitation['min_qty']);
            $error['cart_values']['min_qty'] = $qty;
        }
        if( ! empty($settings_limitation['max_qty']) && $qty > apply_filters('berocket_check_product_error_max_qty', $settings_limitation['max_qty']) ) {
            $error['max_qty'][] = apply_filters('berocket_check_product_error_max_qty', $settings_limitation['max_qty']);
            $error['cart_values']['max_qty'] = $qty;
        }
        if( ! empty($settings_limitation['min_price']) && $price < apply_filters('berocket_check_product_error_min_price', $settings_limitation['min_price']) ) {
            $error['min_price'][] = wc_price(apply_filters('berocket_check_product_error_min_price', $settings_limitation['min_price']));
            $error['cart_values']['min_price'] = wc_price($price);
        }
        if( ! empty($settings_limitation['max_price']) && $price > apply_filters('berocket_check_product_error_max_price', $settings_limitation['max_price']) ) {
            $error['max_price'][] = wc_price(apply_filters('berocket_check_product_error_max_price', $settings_limitation['max_price']));
            $error['cart_values']['max_price'] = wc_price($price);
        }
        return $error;
    }

    public function limitation_settings_text($settings_minmax, $limitation_id, $options) {
        if( empty($settings_minmax['use_local_text']) ) {
            $settings_minmax['min_qty_text'] = $options['min_qty_text'];
            $settings_minmax['max_qty_text'] = $options['max_qty_text'];
            $settings_minmax['min_price_text'] = $options['min_price_text'];
            $settings_minmax['max_price_text'] = $options['max_price_text'];
        }
        return $settings_minmax;
    }

    /**
     * Function set styles in wp_head WordPress action
     *
     * @return void
     */
    public function set_styles () {
        $options = parent::get_option();
        echo '<style>'.$options['custom_css'].'</style>';
    }
    
    /**
     * Function adding styles/scripts and settings to admin_init WordPress action
     *
     * @access public
     *
     * @return void
     */
    public function register_mm_quantity_options () {
        wp_enqueue_script( 'berocket_mm_quantity_admin', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), BeRocket_MM_Quantity_version );
        wp_register_style( 'berocket_mm_quantity_admin_style', plugins_url( 'css/admin.css', __FILE__ ), "", BeRocket_MM_Quantity_version );
        wp_enqueue_style( 'berocket_mm_quantity_admin_style' );
    }    
    
    public static function admin_enqueue_scripts() {
       parent::admin_enqueue_scripts();
    }
    
    public function enqueue_scripts() {
        wp_enqueue_script( 'berocket-front-cart-js', plugins_url( 'js/frontend.js', __FILE__ ), array('jquery') );
    }
    
    public function wc_product_field() {
        wp_nonce_field('berocket_minmax', 'product_edit');
        woocommerce_wp_text_input( 
            array( 
                'id' => 'min_quantity', 
                'class' => 'wc_input_stock short', 
                'label' => __( 'Minimum Quantity', 'minmax-quantity-for-woocommerce' ),
                'type' => 'number', 
                'custom_attributes' => array('min' => '1'),
                'wrapper_class'     => 'berocket_min_max_clear berocket_options_on_product_page'
            ) 
        );
        echo '<style>.berocket_min_max_clear{clear:both;}</style>';
        woocommerce_wp_text_input( 
            array( 
                'id' => 'max_quantity', 
                'class' => 'wc_input_stock short', 
                'label' => __( 'Maximum Quantity', 'minmax-quantity-for-woocommerce' ),
                'type' => 'number', 
                'custom_attributes' => array('min' => '1'),
                'wrapper_class'     => 'berocket_options_on_product_page'
            ) 
        );
        echo '<script>
            jQuery(document).ready(function() {
                if( jQuery("#product-type").length ) {
                    function berocket_hide_option_by_product_type() {
                        if( jQuery("#product-type").val() == "grouped" ) {
                            jQuery(".berocket_options_on_product_page").hide();
                        } else {
                            jQuery(".berocket_options_on_product_page").show();
                        }
                    }
                    berocket_hide_option_by_product_type();
                    jQuery("#product-type").on("change", berocket_hide_option_by_product_type);
                }
            });
        </script>';
    }
    
    public function wc_save_product( $product_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if( empty($_REQUEST['product_edit']) || ! wp_verify_nonce($_REQUEST['product_edit'], 'berocket_minmax') ) {
            return;
        }
        if ( isset( $_POST['min_quantity'] ) ) {
            update_post_meta( $product_id, 'min_quantity', $_POST['min_quantity'] );
        }
        if ( isset( $_POST['max_quantity'] ) ) {
            update_post_meta( $product_id, 'max_quantity', $_POST['max_quantity'] );
        }
    }
    
    public function variation_settings_fields( $loop, $variation_data, $variation ) {
        wp_nonce_field('berocket_minmax', 'variation_edit');
        woocommerce_wp_text_input( 
            array( 
                'id'                => 'min_quantity_var[' . $variation->ID . ']', 
                'label'             => __( 'Minimum Quantity', 'minmax-quantity-for-woocommerce' ),
                'value'             => get_post_meta( $variation->ID, 'min_quantity_var', true ),
                'custom_attributes' => array('min' => '1'),
                'wrapper_class'     => 'berocket_min_max_clear berocket_options_on_product_page'
            )
        );
        echo '<style>.berocket_min_max_clear{clear:both;}</style>';
        woocommerce_wp_text_input( 
            array( 
                'id'                => 'max_quantity_var[' . $variation->ID . ']', 
                'label'             => __( 'Maximum Quantity', 'minmax-quantity-for-woocommerce' ),
                'value'             => get_post_meta( $variation->ID, 'max_quantity_var', true ),
                'custom_attributes' => array('min' => '1'),
                'wrapper_class'     => 'berocket_options_on_product_page'
            )
        );
    }
    
    public function save_variation_settings_fields( $post_id ) {
        if( empty($_REQUEST['variation_edit']) || ! wp_verify_nonce($_REQUEST['variation_edit'], 'berocket_minmax') ) {
            return;
        }
        if( isset( $_POST['min_quantity_var'][ $post_id ] ) ) {
            update_post_meta( $post_id, 'min_quantity_var', $_POST['min_quantity_var'][ $post_id ] );
        }
        if( isset( $_POST['max_quantity_var'][ $post_id ] ) ) {
            update_post_meta( $post_id, 'max_quantity_var', $_POST['max_quantity_var'][ $post_id ] );
        }
    }

    public function fix_error_duplicate() {
        if( !function_exists('wc_get_notices') || empty(WC()->cart) ) return false;
        $this->new_calculate_total(WC()->cart);
        $options = $this->get_option();
        WC()->cart->get_cart();
        if( ! empty($options['fix_duplicate']) && isset($options['fix_duplicate_page']) && is_array($options['fix_duplicate_page']) && count($options['fix_duplicate_page']) ) {
            $fix_page = ( 
                is_checkout() && in_array('checkout', $options['fix_duplicate_page'])
                || is_cart() && in_array('cart', $options['fix_duplicate_page'])
                || is_product() && in_array('product', $options['fix_duplicate_page'])
                || (is_shop() || is_product_taxonomy()) && in_array('archive', $options['fix_duplicate_page'])
                || (
                    in_array('other', $options['fix_duplicate_page'])
                    && ! is_checkout() && ! is_cart() && ! is_product() && ! is_shop() && ! is_product_taxonomy()
                )
            );
            if( $fix_page ) {
                $notices_old = wc_get_notices();
                foreach($notices_old as $error_type => $errors) {
                    if( 'notice' != $error_type && isset($notices_old[$error_type]) && is_array($notices_old[$error_type]) ) {
                        foreach($errors as $error_i => $error_text) {
                            if( is_array($error_text) ) {
                                $error_text = ( isset($error_text['notice']) ? $error_text['notice'] : "" );
                            }
                            if( strpos($error_text, '<span class="berocket_minmax"') !== FALSE ) {
                                unset($notices_old[$error_type][$error_i]);
                            }
                        }
                    }
                }
                wc_set_notices($notices_old);
            }
        }
        if( ! empty($options['fix_singular']) && is_product() ) {
            $only_err_product_id = get_queried_object_id();
            $this->new_calculate_total(WC()->cart, false, true, $only_err_product_id);
        }
    }
    public function get_line_total( $item, $inc_tax = false, $round = true ) {
        $total = 0;

        if ( is_callable( array( $item, 'get_total' ) ) ) {
            // Check if we need to add line tax to the line total.
            $total = $inc_tax ? $item->get_total() + $item->get_total_tax() : $item->get_total();

            // Check if we need to round.
            $total = $round ? round( $total, wc_get_price_decimals() ) : $total;
        }

        return apply_filters( 'woocommerce_order_amount_line_total', $total, $this, $item, $inc_tax, $round );
    }
    public function update_qunatity_limitation_result_array($qunatity_limitation_result_array, $limitation) {
        $min = (empty($limitation['min_qty']) ? 0 : (int)$limitation['min_qty']);
        $max = (empty($limitation['max_qty']) ? 0 : (int)$limitation['max_qty']);
        $step = (empty($limitation['multiplicity']) ? 0 : (int)$limitation['multiplicity']);
        if( $min != 0 ) {
            $qunatity_limitation_result_array['min'] = ( isset($qunatity_limitation_result_array['min'])
                ?   ( $qunatity_limitation_result_array['min'] < $min ? $min : $qunatity_limitation_result_array['min'] )
                :   ( $min )
            );
        }
        if( $max != 0 ) {
            $qunatity_limitation_result_array['max'] = ( isset($qunatity_limitation_result_array['max'])
                ?   ( $qunatity_limitation_result_array['max'] > $max ? $max : $qunatity_limitation_result_array['max'] )
                :   ( $max )
            );
        }
        if( $step != 0 ) {
            $qunatity_limitation_result_array['step'] = ( isset($qunatity_limitation_result_array['step'])
                ?   ( $step == 1
                    ?   $qunatity_limitation_result_array['step']
                    :   ($qunatity_limitation_result_array['step'] == 1 ? $step : 0 )
                )
                :   ( $step )
            );
        }
        return $qunatity_limitation_result_array;
    }
    public function new_calculate_total($cart, $additional_product = false, $display_error = true, $only_err_product_id = false, $only_err_variation_id = false) {
        $check_product_variations = array();
        $options = $this->get_option();
        //GET OPTIONS AND FILTER IT
        if( $display_error === 'check_product_variations' ) {
            $br_minmax_notices = array('notice' => array(), 'error' => array());
        } else {
            global $br_minmax_notices;
            //REMOVE ERRORS/NOTICES IF ALREADY ADDED
            $br_minmax_notices = array('notice' => array(), 'error' => array());
            $notices_old = wc_get_notices();
            foreach($notices_old as $error_type => $errors) {
                if( 'notice' != $error_type && isset($notices_old[$error_type]) && is_array($notices_old[$error_type]) ) {
                    foreach($errors as $error_i => $error_text) {
                        if( is_array($error_text) ) {
                            $error_text = ( isset($error_text['notice']) ? $error_text['notice'] : "" );
                        }
                        if( strpos($error_text, '<span class="berocket_minmax"') !== FALSE ) {
                            unset($notices_old[$error_type][$error_i]);
                        }
                    }
                }
            }
            wc_set_notices($notices_old);
        }

        //INIT VARIABLES
        $return_result = true;
        $prevent_add_type = ( empty($options['prevent_add_to_cart']) ? 'error' : 'notice' );

        $BeRocket_minmax_custom_post = BeRocket_minmax_custom_post::getInstance();
        $limitation_ids = $BeRocket_minmax_custom_post->get_custom_posts_frontend();
        $product_limitations = array();
        $group_limitations = array();
        $get_cart = $cart->get_cart();
        $product_qty_in_cart = $cart->get_cart_item_quantities();

        //TEXT FOR PRODUCT LIMITATIONS
        $product_text_errors = array(
            'product' => array(
                'min_qty_text' => __('Quantity of product %products% can not be less than <strong>%value%</strong>', 'minmax-quantity-for-woocommerce'),
                'max_qty_text' => __('Quantity of product %products% can not be more than <strong>%value%</strong>', 'minmax-quantity-for-woocommerce'),
                'min_price_text' => '',
                'max_price_text' => '',
            ),
            'variation' => array(
                'min_qty_text' => __('Quantity of one variation of a %products% product can not be less than <strong>%value%</strong>', 'minmax-quantity-for-woocommerce'),
                'max_qty_text' => __('Quantity of one variation of a %products% product can not be more than <strong>%value%</strong>', 'minmax-quantity-for-woocommerce'),
                'min_price_text' => '',
                'max_price_text' => '',
            )
        );
        $product_text_errors = apply_filters('berocket_minmax_product_text_errors', $product_text_errors);

        //CHECK AND ADD ADDITIONAL PRODUCT
        if( $additional_product !== false ) {
            wc_clear_notices();
            $additional_product_id = ! empty( $additional_product['variation_id'] ) ? absint( $additional_product['variation_id'] ) : absint( $additional_product['product_id'] );
            if( empty( $product_qty_in_cart[ $additional_product_id ] ) ) {
                $product_qty_in_cart[ $additional_product_id ] = $additional_product['quantity'];
            } else {
                $product_qty_in_cart[ $additional_product_id ] += $additional_product['quantity'];
            }
            $additional_product_exist = 0;
            foreach ( $get_cart as $cart_item_key => $values ) {
                $_product = $values['data'];
                if( $additional_product['product_id'] == $values['product_id'] ) {
                    if( $additional_product['data']->is_type( 'variation' ) || $_product->is_type( 'variation' ) ) {
                        if( @ $values['variation_id'] == @ $additional_product['variation_id'] ) {
                            $additional_product_exist = $cart_item_key;
                        }
                    } else {
                        $additional_product_exist = $cart_item_key;
                    }
                }
            }
            if( $additional_product_exist === 0 ) {
                $get_cart['additional_product'] = $additional_product;
            } else {
                $get_cart[$additional_product_exist]['quantity'] += $additional_product['quantity'];
                if( ! isset($get_cart[$additional_product_exist]['line_total']) ) {
                    $get_cart[$additional_product_exist]['line_total'] = 0;
                }
                $get_cart[$additional_product_exist]['line_total'] += $additional_product['line_total'];
            }
        }
        
        $product_qty_in_cart_var_fix = array();
        $product_in_cart_line_price = array();
        foreach ( $get_cart as $cart_item_key => $values ) {
            $_product = $values['data'];
            if( $_product->is_type( 'variation' ) ) {
                $_product_id = wp_get_post_parent_id($values['variation_id']);
                if( ! isset($product_qty_in_cart_var_fix[$_product_id]) ) {
                    $product_qty_in_cart_var_fix[$_product_id] = 0;
                }
                if( ! isset($product_in_cart_line_price[$values['variation_id']]) ) {
                    $product_in_cart_line_price[$values['variation_id']] = 0;
                }
                $product_in_cart_line_price[$values['variation_id']] += $this->get_line_total_cart_item($values);
                $product_qty_in_cart_var_fix[$_product_id] += $values['quantity'];
            } else {
                $_product = $values['data'];
                $_product_id = br_wc_get_product_id($_product);
            }
            if( ! isset($product_in_cart_line_price[$_product_id]) ) {
                $product_in_cart_line_price[$_product_id] = 0;
            }
            $product_in_cart_line_price[$_product_id] += $this->get_line_total_cart_item($values);
        }
        $product_qty_in_cart = $product_qty_in_cart_var_fix + $product_qty_in_cart;
        //CHECK EVERY ITEM IN CART
        
        foreach ( $get_cart as $cart_item_key => $values ) {
            //INIT PRODUCT VARIABLES
            $_product = $values['data'];
            $_product_post = br_wc_get_product_post($_product);
            $_product_id = br_wc_get_product_id($_product);

            if( apply_filters('berocket_minmax_limitation_not_check_for_product', false, $values, $get_cart, $_product, $_product_post, $_product_id) ) {
                continue;
            }

            //GET PRODUCT LIMITATIONS
            $product_limitation = array(
                'min_qty' => get_post_meta( $values['product_id'], 'min_quantity', true ),
                'max_qty' => get_post_meta( $values['product_id'], 'max_quantity', true ),
            );

            $product_limitation = apply_filters('berocket_minmax_product_limitation', $product_limitation, $values['product_id'], false);

            $qty_prod = (empty($product_qty_in_cart[ $values['product_id'] ]) ? 0 : $product_qty_in_cart[ $values['product_id'] ]);
            $price_prod = (empty($product_in_cart_line_price[ $values['product_id'] ]) ? 0 : $product_in_cart_line_price[ $values['product_id'] ]);
            $has_error = false;
            //IS PRODUCT VARIATION
            if ( $_product->is_type( 'variation' ) ) {
                $qty_variation = $values['quantity'];
                $price_variation = (empty($product_in_cart_line_price[ $values['variation_id'] ]) ? 0 : $product_in_cart_line_price[ $values['variation_id'] ]);

                //GET VARIATION LIMITATIONS
                $variation_limitation = array(
                    'min_qty' => get_post_meta( $values['variation_id'], 'min_quantity_var', true ),
                    'max_qty' => get_post_meta( $values['variation_id'], 'max_quantity_var', true ),
                );
                $variation_limitation = apply_filters('berocket_minmax_product_limitation', $variation_limitation, $values['variation_id'], true);

                //INIT VARIATION VARIABLES AND REINIT PRODUCT VARIABLES
                $_product_id = wp_get_post_parent_id($values['variation_id']);
                $_product = wc_get_product($_product_id);
                $_product_post = br_wc_get_product_post($_product);
                $_var_product = wc_get_product($values['variation_id']);
                $_var_product_post = br_wc_get_product_post($_var_product);
                $_var_product_id = br_wc_get_product_id($_var_product);

                //CHECK FOR LIMITATION ERRORS AND ADD ERRORS TO LIST
                if( ($only_err_variation_id === FALSE || $only_err_variation_id == $_var_product_id) && ($only_err_product_id === FALSE || $only_err_product_id == $_product_id) && ! isset($product_limitations[$_var_product_id]) ) {
                    $check_product_variations[] = apply_filters('berocket_minmax_check_product_variation', array(), array($variation_limitation), $qty_variation, $price_variation);
                    $check_result = $this->check_product(array($variation_limitation), $qty_variation, $price_variation);
                    $check_result = apply_filters('berocket_check_product_for_each_product_var', $check_result, array($variation_limitation), $qty_variation, $price_variation);
                    $new_errors = $this->add_correct_error($product_text_errors['variation'], $check_result, array($_product_post->post_title));
                    if( count($new_errors) ) {
                        $has_error = true;
                        $return_result = false;
                    }
                    $br_minmax_notices[$prevent_add_type] = array_merge($br_minmax_notices[$prevent_add_type], $new_errors);
                    $product_limitations[$_var_product_id] = array();
                }
            } else {
                $_var_product = false;
                $_var_product_post = false;
                $_var_product_id = false;
            }

            $product_variables = array(
                'product_id'            => $_product_id,
                'product_post'          => $_product_post,
                'product'               => $_product,
                'var_product_id'        => $_var_product_id,
                'var_product_post'      => $_var_product_post,
                'var_product'           => $_var_product,
                'variation_selected'    => ( isset($values['variation']) ? $values['variation'] : array() ),
                'product_limitation'    => $product_limitation,
                'qty_prod'              => $qty_prod,
                'price_prod'            => $price_prod,
            );
            if( ! empty($_var_product_id) ) {
                $product_variables['variation_limitation'] = $variation_limitation;
                $product_variables['qty_variation'] = $qty_variation;
                $product_variables['price_variation'] = $price_variation;
            }
            $product_variables = apply_filters('berocket_minmax_product_variables', $product_variables);

            //CHECK FOR PRODUCT ERRORS AND ADD ERRORS TO LIST
            if( ($only_err_product_id === FALSE || $only_err_product_id == $_product_id) && ! $has_error && ! isset($product_limitations[$_product_id]) ) {
                $check_product_variations[] = apply_filters('berocket_minmax_check_product_variation', array(), array($product_limitation), $qty_prod, $price_prod, $_product->is_type( 'simple' ));
                $check_result = $this->check_product(array($product_limitation), $qty_prod, $price_prod);
                if ( empty($_var_product_id) ) {
                    $check_result = apply_filters('berocket_check_product_for_each_product', $check_result, array($product_limitation), $qty_prod, $price_prod);
                }
                $new_errors = $this->add_correct_error($product_text_errors['product'], $check_result, array($_product_post->post_title));
                if ( ! empty($_var_product_id) ) {
                    $br_minmax_notices['error'] = array_merge($br_minmax_notices['error'], $new_errors);
                } else {
                    $br_minmax_notices[$prevent_add_type] = array_merge($br_minmax_notices[$prevent_add_type], $new_errors);
                    if( count($new_errors) ) {
                        $return_result = false;
                    }
                }
                $product_limitations[$_product_id] = array();
            }

            //EXCLUDE PRODUCT
            if( apply_filters('berocket_minmax_limitation_not_check_for_product_limitations', false, $values, $get_cart, $product_variables, $options) ) {
                continue;
            }

            //CART LIMITATION
            $group_limitations = apply_filters('berocket_minmax_group_limitations_on_product_check', $group_limitations, 0, $values, $get_cart, $product_variables, $options);

            //CHECK ALL LIMITATIONS
            foreach($limitation_ids as $limitation_id) {
                $settings_minmax = get_post_meta( $limitation_id, 'br_minmax_limitation', true );
                //CHECK CONDITION FOR PRODUCT
                $check_condition = BeRocket_conditions_minmax::check(
                    $settings_minmax['condition'], 
                    'berocket_minmax_custom_post', 
                    array(
                        'product'           => $_product,
                        'product_post'      => $_product_post,
                        'product_id'        => $_product_id,
                        'var_product'       => false,
                        'var_product_post'  => false,
                        'var_product_id'    => false,
                        'product_variables' => $product_variables,
                    )
                );
                //CHECK CONDITION FOR VARIATION
                $var_check_condition = false;
                if( ! empty($_var_product_id) ) {
                    $var_check_condition = BeRocket_conditions_minmax::check(
                        $settings_minmax['condition'], 
                        'berocket_minmax_custom_post', 
                        array(
                            'product'           => $_product,
                            'product_post'      => $_product_post,
                            'product_id'        => $_product_id,
                            'var_product'       => $_var_product,
                            'var_product_post'  => $_var_product_post,
                            'var_product_id'    => $_var_product_id,
                            'product_variables' => $product_variables,
                        )
                    );
                }

                $limitation_variables = array(
                    'limitation_id' => $limitation_id,
                    'settings_minmax' => $settings_minmax,
                    'check_condition' => $check_condition,
                    'var_check_condition' => $var_check_condition,
                    'only_err_product_id' => $only_err_product_id,
                    'only_err_variation_id' => $only_err_variation_id,
                );
                $filter_elements = array('group_limitations', 'br_minmax_notices', 'return_result', 'product_limitations', 'check_product_variations');
                $filter_array = array();
                foreach($filter_elements as $filter_element) {
                    $filter_array[$filter_element] = $$filter_element;
                }
                $filter_array = apply_filters('berocket_minmax_group_limitations_filter', $filter_array, $limitation_variables, $values, $get_cart, $product_variables, $options);
                extract($filter_array, EXTR_OVERWRITE);
            }
        }
        $group_limitations = apply_filters('berocket_minmax_group_limitations_before_error_check', $group_limitations, $get_cart, $options);

        foreach($group_limitations as $limitation_id => $limitation_data) {
            if($only_err_product_id !== FALSE && ! in_array($only_err_product_id, $limitation_data['products_id'])) continue;
            if( $limitation_id <= 0 ) {
                $settings_minmax = $limitation_data['settings_minmax'];
            } else {
                $settings_minmax = get_post_meta( $limitation_id, 'br_minmax_limitation', true );
                $settings_minmax = apply_filters('berocket_minmax_group_limitation_settings_text', $settings_minmax, $limitation_id, $options);
            }
            $check_product_variations[] = apply_filters('berocket_minmax_check_product_variation', array(), $settings_minmax['limitations'], $limitation_data['qty'], $limitation_data['price'], false);
            $check_result = $this->check_product($settings_minmax['limitations'], $limitation_data['qty'], $limitation_data['price'], $limitation_data);
            $new_errors = $this->add_correct_error($settings_minmax, $check_result, $limitation_data['products']);
            $check_errors = $this->add_correct_error($settings_minmax, $check_result, $limitation_data['products'], true);
            if( count($check_errors) ) {
                $return_result = false;
                $br_minmax_notices[$prevent_add_type] = array_merge($br_minmax_notices[$prevent_add_type], $new_errors);
            } else {
                $br_minmax_notices['error'] = array_merge($br_minmax_notices['error'], $new_errors);
            }
        }
        if( $display_error === 'check_product_variations' ) {
            return $check_product_variations;
        }
        if( $display_error ) {
            foreach($br_minmax_notices as $error_type => $errors) {
                foreach($errors as $error) {
                    wc_add_notice( '<span class="berocket_minmax" style="display:none;"></span>'.apply_filters('berocket_minmax_wc_add_notice_text', $error), $error_type );
                }
            }
        }
        if ( wc_notice_count( 'error' ) == 0 ) {
            $this->show_checkout_button();
        } else {
            $this->hide_checkout_button();
        }
        return $return_result;
    }
    public function check_product($settings_limitations, $qty, $price, $limitation_data = false) {
        $error = array('limitation_qty' => count($settings_limitations), 'min_qty' => array(), 'max_qty' => array(), 'min_price' => array(), 'max_price' => array(), 'cart_values' => array());
        foreach($settings_limitations as $settings_i => $settings_limitation) {
            $error = apply_filters('berocket_minmax_check_product_error', $error, $settings_limitation, $qty, $price, $limitation_data);
        }
        return $error;
    }
    public function add_correct_error($settings_minmax, $error, $products_text, $return_is_prevent = false) {
        $options = $this->get_option();
        $errors_text = array();
        $products_text = array_unique($products_text);
        if( $return_is_prevent ) {
            $check_error = array('max_qty' => 'max_qty_text', 'max_price' => 'max_price_text');
        } else {
            $check_error = array('max_qty' => 'max_qty_text', 'min_qty' => 'min_qty_text', 'min_price' => 'min_price_text', 'max_price' => 'max_price_text');
        }
        $prevent_condition = apply_filters( 'berocket_minmax_check_products_condition', false, $settings_minmax, $error, $products_text, $return_is_prevent );

        if ( $prevent_condition ) {
            return array();
        }
        $check_error = apply_filters('berocket_minmax_add_correct_error', $check_error, $return_is_prevent, $settings_minmax, $error, $products_text);
        if( empty($options['full_or_limitation']) ) {
            $companion_errors = array(
                'min_qty'   => array('max_qty'),
                'max_qty'   => array('min_qty'),
                'min_price' => array('max_price'),
                'max_price' => array('min_price')
            );
        } else {
            $companion_errors = array(
                'min_qty'   => array('max_qty', 'min_price', 'max_price'),
                'max_qty'   => array('min_qty', 'min_price', 'max_price'),
                'min_price' => array('max_price', 'min_qty', 'max_qty'),
                'max_price' => array('min_price', 'min_qty', 'max_qty')
            );
        }
        foreach($check_error as $error_type => $error_type_text) {
            $error_count = count($error[$error_type]);
            if( isset($companion_errors[$error_type]) && is_array($companion_errors[$error_type]) ) {
                foreach($companion_errors[$error_type] as $companion_error_type) {
                    if( isset($error[$companion_error_type]) && is_array($error[$companion_error_type]) ) {
                        $error_count += count($error[$companion_error_type]);
                    }
                }
            }
            if( $error_count >= $error['limitation_qty'] && count($error[$error_type]) ) {
                $error_text = $settings_minmax[$error_type_text];
                $error[$error_type] = array_unique($error[$error_type]);
                $error_text = str_replace( '%value%', implode(', ', $error[$error_type]), $error_text);
                $error_text = str_replace( '%products%', implode(', ', $products_text), $error_text);
                if( isset($error['cart_values'][$error_type]) ) {
                    $error_text = str_replace( '%value_cart%', $error['cart_values'][$error_type], $error_text);
                }
                $errors_text[] = $error_text;
            }
        }
        $errors_text = array_unique($errors_text);
        return $errors_text;
    }
    public function show_checkout_button() {
        $options = parent::get_option();
        if( $options['hide_checkout'] ) {
           remove_action( 'woocommerce_after_cart_table', array($this, 'wp_footer_hide') );
           remove_action( 'woocommerce_after_mini_cart', array($this, 'wp_footer_hide') );
        }
    }
    public function hide_checkout_button() {
        $options = parent::get_option();
        if( $options['hide_checkout'] ) {
            add_action( 'woocommerce_after_cart_table', array($this, 'wp_footer_hide') );
            add_action( 'woocommerce_after_mini_cart', array($this, 'wp_footer_hide') );
        }
    }
    public function wp_footer_hide() {
        $options = parent::get_option();
        echo '<style>
        ', $options['checkout_class'], '{display:none!important;}
        ', $options['checkout_mini_class'], '{display:none!important;}
        </style>';
    }
    public function validate_add_to_cart($valid, $product_id, $quantity) {
        if( $valid ) {
            $origin_prod_id = $product_id;
            if( !empty( $_POST['variation_id'] ) ) {
                $product_id = $_POST['variation_id'];
            }
            $product = wc_get_product($product_id);
            $line_total = $product->get_price($quantity);
            $additional_product = array(
                'data'              => $product,
                'product_id'        => $origin_prod_id,
                'line_total'        => $line_total,
                'line_total_test'   => wc_get_price_including_tax($product, array('qty' => $quantity)),
                'quantity'          => $quantity,
                'additional_prod'   => true,
            );
            if ( $product->is_type( 'variation' ) ) {
                if( br_woocommerce_version_check() ) {
                    $additional_product['variation_id'] = $product->get_id(); // for WooCommerce 2.7 <
                } else { 
                    $additional_product['variation_id'] = $product->get_variation_id(); // for WooCommerce  > 2.7
                }
            }
            remove_action( 'woocommerce_after_calculate_totals', array( $this, 'new_calculate_total' ), 10, 1 );
            new WC_Cart_Totals( WC()->cart );
            add_action( 'woocommerce_after_calculate_totals', array( $this, 'new_calculate_total' ), 10, 1 );
            $valid = $this->new_calculate_total(WC()->cart, $additional_product, true );
        }
        return $valid;
    }
    
    public function woocommerce_after_cart_item_quantity_update($key, $quantity, $old_quantity) {
        WC()->cart->calculate_totals();
        $valid = $this->new_calculate_total(WC()->cart, false, false);
        if( ! $valid ) {
            WC()->cart->cart_contents[ $key ]['quantity'] = $old_quantity;
            $all_notices  = WC()->session->get( 'wc_notices', array() );
            unset( $all_notices['error'] );
            wc_set_notices( $all_notices );
        }
    }
    
    public function woocommerce_cart_item_restored($cart_item_key, $cart) {
        $valid = $this->new_calculate_total($cart, false, false);
        if( ! $valid ) {
            $cart->remove_cart_item($cart_item_key);
        }
    }

    public function save_settings_callback($settings) {
        if( isset($settings['groups']) && is_array($settings['groups']) ) {
            foreach($settings['groups'] as $i => $group_data) {
                $group_data['slug'] = sanitize_title($group_data['name']);
                $group_data['slug'] = sanitize_title_with_dashes($group_data['slug']);
                $settings['groups'][$i] = $group_data;
            }
        }
        // standart
        $settings = parent::save_settings_callback($settings);
        return $settings;
    }
    public function menu_order_custom_post($compatibility) {
        $compatibility['br_minmax_limitation'] = 'br-mm-quantity';
        return $compatibility;
    }
}

new BeRocket_MM_Quantity;
