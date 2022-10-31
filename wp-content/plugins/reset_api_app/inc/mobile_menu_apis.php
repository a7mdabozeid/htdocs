<?php
require dirname(__FILE__) . '/../../woocommerce/vendor/autoload.php';

require_once( dirname(__FILE__).'/../../WooCommerceClientHelper/Client.php');
require_once( dirname(__FILE__).'/../../WooCommerceClientHelper/HttpClient/BasicAuth.php');
require_once( dirname(__FILE__).'/../../WooCommerceClientHelper/HttpClient/HttpClient.php');
require_once( dirname(__FILE__).'/../../WooCommerceClientHelper/HttpClient/HttpClientException.php');
require_once( dirname(__FILE__).'/../../WooCommerceClientHelper/HttpClient/OAuth.php');
require_once( dirname(__FILE__).'/../../WooCommerceClientHelper/HttpClient/Options.php');
require_once( dirname(__FILE__).'/../../WooCommerceClientHelper/HttpClient/Request.php');
require_once( dirname(__FILE__).'/../../WooCommerceClientHelper/HttpClient/Response.php');
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

add_action( 'in_admin_footer', 'action_in_admin_footer_for_menu_arabic' ); 
// define the in_admin_footer callback 
function action_in_admin_footer_for_menu_arabic() { 
    global $pagenow, $current_page, $post, $user;

    if( $pagenow == 'nav-menus.php' && !empty( $_GET['lang'] ) && $_GET['lang'] == 'ar' ) {
        
          echo '
                <script>
                   setInterval(function () {
                    if(jQuery("option[value=\""+jQuery("#icl_menu_translation_of").val()+"\"]", jQuery("#icl_menu_translation_of")).text() == "mobile_app_menu" ) {
                        jQuery("input#menu-name").val("mobile_app_menu_ar");
                    }
                   }, 500);
                </script>
            ';
    }
}; 

// add the action 





add_action( 'init', 'process_menu_in_dashboard' );
function process_menu_in_dashboard() {
    // This theme uses wp_nav_menu() in one location.
    register_nav_menu( 'mobileappmenu', __( 'Mobile App Menu', 'razzi' ) );
    
    // get 'your_custom_menu' id to assign it to the primary menu location created
    $menu_header = get_term_by('name', 'mobile_app_menu', 'nav_menu');
    $menu_header_id = $menu_header->term_id;
    
    // if menu not found, create a new one
    if($menu_header_id == 0) {
         $menu_header_id = wp_create_nav_menu('mobile_app_menu');
    }
    
    //Get all locations (including the one we just created above)
    $locations = get_theme_mod('nav_menu_locations');
    
    // set the menu to the new location and save into database
    if($locations['mobileappmenu'] != $menu_header_id) {
        $locations['mobileappmenu'] = $menu_header_id;
        set_theme_mod( 'nav_menu_locations', $locations );    
    }
    
}






/**
 * This is our callback function that embeds our phrase in a WP_REST_Response
 */
function mobile_menu_api_app( WP_REST_Request $request) {
    // rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
    $check_request = check_auth($request);
    
    if( $check_request !== true ) {
        return rest_ensure_response( $check_request );
    }

    $options = wp_get_nav_menu_items('mobile_app_menu');
    
    if( isset($_REQUEST['lang']) && $_REQUEST['lang'] == 'ar') {
        $options = wp_get_nav_menu_items('mobile_app_menu_ar');    
    }

    // echo $_SERVER['HTTP_HOST'];
    $woocommerce = new Client(
      'http://'.$_SERVER['HTTP_HOST'],
      $_REQUEST['consumer_key'],
      $_REQUEST['consumer_secret'],
      [
        'wp_api' => true, // Enable the WP REST API integration
        'version' => 'wc/v3',
        'verify_ssl' => false
      ]
    );
    
    $products = array();
    

    if(!empty($options)) {
        $k = 0;
        foreach($options  as $option){
            $products[$k] = array();
            if($option->object == 'product_cat') {
                $data = get_posts(array(
                    'post_type' => 'product',
                    'category' => array($option->object_id)
                ));
               
                $the_query = new WP_Query( array(
                    'post_type' => 'product',
                    'tax_query' => array(
                        array (
                            'taxonomy' => 'product_cat',
                            'field' => 'term_id',
                            'terms' => $option->object_id,
                        )
                    ),
                ) );
                $products_woo = [];
                while ( $the_query->have_posts() ) :
                    $the_query->the_post();
                    $pr = wc_get_product(get_the_ID());        
                    $prd_final = $pr->get_data();
                    $pr_atts = get_post_meta(get_the_ID(), '_product_attributes', true);
                    $prd_atts = array();
                    if(!empty($pr_atts)):
                        foreach($pr_atts as $p_key => $pr_attr) {
                            $pr_attr_new = array(); 
                            $tt_name = wc_attribute_label($pr_attr['name']);
                            $pr_attr_new['id'] = wc_attribute_taxonomy_id_by_name($pr_attr['name']);
                            $pr_attr_new['name'] = $tt_name;
                            $pr_attr_new['position'] = $pr_attr['position'];
                            $pr_attr_new['visible'] = (boolean) $pr_attr['is_visible'];
                            $pr_attr_new['variation'] = (boolean) $pr_attr['is_variation'];
                            $pr_attr_new['options'] = $pr->get_attribute($pr_attr['name']); 
                            $pr_attr_new['options'] =  explode(', ', $pr->get_attribute($pr_attr['name']));
                            $prd_atts[] = $pr_attr_new; 
                        }
                    endif;
           
                    $prd_final['attributes'] = $prd_atts;
                    $image = get_post($prd_final['image_id']);
                    $prd_final['images'] = [['src' => $image->guid]];
                    if( !$pr instanceof WC_Product_Simple ) {
                
                        $vars = $pr->get_available_variations();
                        $vars = array_column($vars, 'variation_id');
                        $prd_final['variations'] = $vars;
                    }
                    

                    $products_woo[] = $prd_final;
                          
                endwhile;
                
                
                wp_reset_postdata();

                // $product =  wc_get_product(88067);
                // $products_woo = $product->get_data();
                

                $products[$k]['products'] = $products_woo;
            }
            
            $products[$k]['link'] = $option;
            $k++;    
        }
    }
    

    return rest_ensure_response( $products );
}
 
/**
 * This function is where we register our routes for our example endpoint.
 */
function mobile_menu_api_app_routes() {
    // register_rest_route() handles more arguments but we are going to stick to the basics for now.
    register_rest_route( 'menu/mobile', '/app', array(
        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
        'methods'  => WP_REST_Server::READABLE,
        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => 'mobile_menu_api_app',
    ) );
}
 
add_action( 'rest_api_init', 'mobile_menu_api_app_routes' );