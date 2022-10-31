<?php

if (!defined('ABSPATH'))
    exit;

class ACOPLW_Front_End
{

    static $cart_error = array();
    /**
     * The single instance of WordPress_Plugin_Template_Settings.
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static $_instance = null;
    public $products = false;
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
     * The plugin assets URL.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;
    /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;
    
    private $badge;

    /**
     * Check if price has to be display in cart and checkout
     * @var type
     * @var boolean
     * @access private
     * @since 3.4.2
     */
    private $show_price = false;
    private $hook_called = false;

    function __construct($badge, $file = '', $version = '1.0.0')
    {

        $this->_version     = $version;
        $this->_token       = ACOPLW_TOKEN;
        $this->badge        = $badge;
        $this->hookCount    = 0;
        add_action('init', array($this, 'register_acoplw_post_types'));

        if ( $this->acoplw_check_woocommerce_active() ) {

            // Enqueue Scripts
            add_action( 'wp_enqueue_scripts', array ( $this, 'enqueue_styles' ), 10 );
            add_action( 'wp_enqueue_scripts', array ( $this, 'enqueue_scripts' ), 10 );

            // Custom Styles
            add_action( 'wp_footer', array ( $this, 'customStyles' ), 10 );
            
            // Badge
            // add_filter( 'woocommerce_single_product_image_html', array( $this, 'acoplwBadge' ), 100000, 2 );
            // add_filter( 'post_thumbnail_html', array( $this, 'acoplwBadge' ), 100000, 2 );
            // add_filter( 'woocommerce_product_get_image', array( $this, 'acoplwBadge' ), 100000, 2 );
            // add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'acoplwBadge' ), 99, 2 );

            // Single Page Hook @ version 1.1.4
            add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'acoplwTitleHookSingle' ), 9999 );

            // Loop Hook (ELementor Listing Fix)
            $loop_hook = get_option('acoplw_enable_loop_hook') ? get_option('acoplw_enable_loop_hook') : 0;  

            if ( $loop_hook ) {
                add_action( 'woocommerce_after_shop_loop_item', array( $this, 'acoplwBadgeElem' ), 9999 );
            } else {
                // add_filter( 'woocommerce_product_get_image', array( $this, 'acoplwBadge'), 9999, 6 );
                add_filter( 'woocommerce_single_product_image_html', array( $this, 'acoplwBadge' ), 100000, 2 );
                add_filter( 'post_thumbnail_html', array( $this, 'acoplwBadge' ), 100000, 2 );
                add_filter( 'woocommerce_product_get_image', array( $this, 'acoplwBadge' ), 100000, 2 );
                // add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'acoplwBadgeThumbnail' ), 99, 2 );
            }

            $title_hook = get_option('acoplw_enable_title_hook') ? get_option('acoplw_enable_title_hook') : 0;               
            if ( $title_hook ) {
                add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'acoplwTitleHook' ), 9999 );
            }

            // Detail Page Badge
            $badgeDetail = ( false === get_option('acoplw_detail_page_badge') ) ? 1 : ( get_option('acoplw_detail_page_badge') ? get_option('acoplw_detail_page_badge') : 0 ); 

            if ($badgeDetail) { 
                add_action( 'wp_footer', array ( $this, 'acoplwBadgeDetail' ), 5 );
            }

            // Assign Variables
            // add_filter( 'wp_head', array ( $this, 'acoplwHead'), 10, 3 );

            // Woocmmerce Block Support
            add_filter( "woocommerce_blocks_product_grid_item_html", array ( $this, "acoplwBadgeWCBlock" ), 10, 3);

            /* WooBuilder Support
            * @ver 3.1.4
            */
            add_action( 'jet-woo-builder/shortcodes/jet-woo-products/loop-item-end', array( $this, 'acoplwBadgeElem' ), 10 );
            
        }

    }

    public function acoplwBadgeWCBlock ( $html, $data, $product ) {

        return $this->badge->acoplwBadgeWCBlock ( $html, $data, $product );

    }

    /**
     * Load frontend CSS.
     * @access  public
     * @since   1.0.0
     * @return void
     */
    public function enqueue_styles()
    {

        wp_register_style('acoplw-style', plugin_dir_url( __FILE__ ) . '../assets/css/frontend.css', array(), $this->_version);

        wp_enqueue_style('acoplw-style');

    }

    public function customStyles()
    {

        echo $this->badge->customStyles();

    }

    /**
     * ACOPLW Badges
     * @param $productThumb, $product
     */

    public function acoplwBadge ( $productThumb, $product = false )
    {

        /*
        * Ajax Loading Fix 
        */
        if ( ( !is_admin() || ( is_ajax() && is_admin() ) ) && !is_single() ) {

            return $this->badge->acoplwBadge( $productThumb, $product, false );

        } else {

            return $productThumb;
            
        }

    }

    
    public function acoplwBadgeElem ()
    { 

        /*
        * Ajax Loading Fix 
        * @ver 1.2.9 - removed !is_single() check
        */
        if ( ( !is_admin() || ( is_ajax() && is_admin() ) ) ) { 

            $this->badge->acoplwBadgeElem();

        }

    }
    /**
    * ACOPLW Badges Detail
    * @param $productThumb, $product
    */

    public function acoplwBadgeDetail () {
	
		// if ( $this->hook_called == false ) {

            $this->badge->acoplwBadgeDetail ();
			// $this->hook_called = true; 

		// }
		
	}

    /**
     * ACOPLW Badges
     * @param $productImageHTML, $thumbID 
     */

    public function acoplwTitleHook ()
    {

        if ( !is_admin() && !is_single() ) {

            global $product;
            $productThumb = '';
            echo $this->badge->acoplwBadge( $productThumb, $product, true );

        } 

    }

    /**
     * Single Page Related Products 
     * version 1.1.4
    **/
    public function acoplwTitleHookSingle ()
    {

        if ( is_single() ) {

            global $product;
            $productThumb = '';
            echo $this->badge->acoplwBadge( $productThumb, $product, true );

        } 

    }

    
    /**
     * Load frontend JS.
     * @access  public
     * @since   1.0.0
     * @return void
     */
    public function enqueue_scripts()
    {

        wp_register_script('acoplw-script', plugin_dir_url( __FILE__ ) . '../assets/js/frontend.js', array('jquery'), $this->_version);

         /* 
         * Localize Frontend Scripts
         * Ver 3.1.3
         * Cahing plugin blocking global variables loading on mobile devices
         */
		wp_localize_script('acoplw-script', 'acoplw_frontend_object', 
            array(
                'classname'     => get_option('acoplw_wrapper_class'),
                'enablejquery'  => ( get_option('acoplw_jquery_status') && get_option('acoplw_enable_loop_hook') ) ? 1 : 0,
            )
        );

        wp_enqueue_script('acoplw-script');

    }

    // /**
    //  * ACOPLW Badges
    //  * @param $productImageHTML, $thumbID 
    //  */

    // public function acoplwSaleBadge ( $badge )
    // {
    //     if ( !is_admin() && !is_single() ) {
    //         return $this->badge->acoplwSaleBadge( $badge );
    //     } else {
    //         return $badge;
    //     }
    // }

    /**
     * ACOPLW Badges
     * @param $productImageHTML, $thumbID 
     */

    public function acoplwBadgeThumbnail ( $productImageHTML, $thumbID )
    {

        return $productImageHTML;

    }

    /**
     * ACOPLW Badges
     * @param $productImageHTML, $thumbID 
     */

    public function acoplwSidebarBadge ( $productImageHTML, $thumbID )
    {

        return $productImageHTML;

    }

    /**
     * ACOPLW Badges
     * @param $productImageHTML, $thumbID 
     */

    public function acoplwMinicartBadge ( $productImageHTML, $thumbID )
    {

        return $productImageHTML;

    }

    /*public function acoplwHead () 
    { ?>
        <script>
            window.acoplw_frontend = '<?php echo get_option('acoplw_wrapper_class'); ?>';
        </script>
    <?php 
    }*/
    
    /**
     * Check if woocommerce plugin is active
     */
    public function acoplw_check_woocommerce_active()
    {

        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            return true;
        }
        if (is_multisite()) {
            $plugins = get_site_option('active_sitewide_plugins');
            if (isset($plugins['woocommerce/woocommerce.php']))
                return true;
        }
        return false;

    }

    /**
     * ACOPLW Register Post Types
     */
    public function register_acoplw_post_types()
    {

        $post_type = ACOPLW_POST_TYPE;
        $labels = array(
            'name' => __('Badges', 'aco-product-labels-for-woocommerce'),
            'singular_name' => __('Badge', 'aco-product-labels-for-woocommerce'),
            'name_admin_bar' => 'PLW_Badge',
            'add_new' => _x('Add New Product Badge', $post_type, 'aco-product-labels-for-woocommerce'),
            'add_new_item' => __('Add New Badge', 'aco-product-labels-for-woocommerce'),
            'edit_item' => __('Edit Badge', 'aco-product-labels-for-woocommerce'),
            'new_item' => __('New Badge', 'aco-product-labels-for-woocommerce'),
            'all_items' => __('Badges', 'aco-product-labels-for-woocommerce'),
            'view_item' => __('View Badge', 'aco-product-labels-for-woocommerce'),
            'search_items' => __('Search Badge', 'aco-product-labels-for-woocommerce'),
            'not_found' => __('No Badge Found', 'aco-product-labels-for-woocommerce'),
            'not_found_in_trash' => __('No Badge Found In Trash', 'aco-product-labels-for-woocommerce'),
            'parent_item_colon' => __('Parent Badge'),
            'menu_name' => 'Custom Product Options'
        );
        $args = array(
            'labels' => apply_filters($post_type . '_labels', $labels),
            'description' => '',
            'public' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_ui' => false,
            // 'show_in_menu' => 'edit.php?post_type=product',
            'show_in_nav_menus' => false,
            'query_var' => false,
            'can_export' => true,
            'rewrite' => false,
            'capability_type' => 'post',
            'has_archive' => false,
            'rest_base' => $post_type,
            'hierarchical' => false,
            'show_in_rest' => false,
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'supports' => array('title'),
            'menu_position' => 5,
            'menu_icon' => 'dashicons-admin-post'
        );
        register_post_type($post_type, apply_filters($post_type . '_register_args', $args, $post_type));

        // Product Lists
        $post_type = ACOPLW_PRODUCT_LIST;
        $labels = array(
            'name' => __('Product Lists', 'aco-product-labels-for-woocommerce'),
            'singular_name' => __('Product List', 'aco-product-labels-for-woocommerce'),
            'name_admin_bar' => 'PLW_Badge',
            'add_new' => _x('Add New Product List', $post_type, 'aco-product-labels-for-woocommerce'),
            'add_new_item' => __('Add New List', 'aco-product-labels-for-woocommerce'),
            'edit_item' => __('Edit List', 'aco-product-labels-for-woocommerce'),
            'new_item' => __('New List', 'aco-product-labels-for-woocommerce'),
            'all_items' => __('Product Lists', 'aco-product-labels-for-woocommerce'),
            'view_item' => __('View List', 'aco-product-labels-for-woocommerce'),
            'search_items' => __('Search List', 'aco-product-labels-for-woocommerce'),
            'not_found' => __('No List Found', 'aco-product-labels-for-woocommerce'),
            'not_found_in_trash' => __('No List Found In Trash', 'aco-product-labels-for-woocommerce'),
            'parent_item_colon' => __('Parent List'),
            'menu_name' => 'Custom Product Options'
        );
        $args = array(
            'labels' => apply_filters($post_type . '_labels', $labels),
            'description' => '',
            'public' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_ui' => false,
            // 'show_in_menu' => 'edit.php?post_type=product',
            'show_in_nav_menus' => false,
            'query_var' => false,
            'can_export' => true,
            'rewrite' => false,
            'capability_type' => 'post',
            'has_archive' => false,
            'rest_base' => $post_type,
            'hierarchical' => false,
            'show_in_rest' => false,
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'supports' => array('title'),
            'menu_position' => 5,
            'menu_icon' => 'dashicons-admin-post'
        );
        register_post_type($post_type, apply_filters($post_type . '_register_args', $args, $post_type));

    }

}
