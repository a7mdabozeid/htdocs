<?php

namespace YITH\Wishlist;

use WP_REST_Response;

// require_once(__DIR__.'/../../../../wp-load.php');


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


if( ! class_exists( '\YITH\Wishlist\Rest' ) ) {
	final class Rest {
		const REST_NAMESPACE = 'yith/wishlist';
		const REST_VERSION = 'v1';
		private static $logged = false;

		protected static $wishlist_routes = [
			'get' => [ // get list of wishlist for given user
				'route' => '/wishlists',
				'methods' => 'GET',
				'callback' => [ __CLASS__, 'get' ],
				'permission_callback' => [ __CLASS__, 'check_auth' ],
			],
			'post' => [ // create/update a whishlist
				'route' => '/wishlists',
				'methods' => 'POST',
				'callback' => [ __CLASS__, 'post' ],
				'permission_callback' => [ __CLASS__, 'check_write_cap' ],
			],
			'get_single' => [ // get single wishlist for given user
				'route' => '/wishlists/(?P<id>\d+)',
				'methods' => 'GET',
				'callback' => [ __CLASS__, 'get_single' ],
				'permission_callback' => [ __CLASS__, 'check_read_cap' ],
			],
			'update_single' => [ // update single wishlist for given user
				'route' => '/wishlists/(?P<id>\d+)',
				'methods' => 'PUT',
				'callback' => [ __CLASS__, 'update_single' ],
				'permission_callback' => [ __CLASS__, 'check_write_cap' ],
			],
			'delete' => [ // delete a wishlist
				'route' => '/wishlists/(?P<id>\d+)',
				'methods' => 'DELETE',
				'callback' => [ __CLASS__, 'delete' ],
				'permission_callback' => [ __CLASS__, 'check_write_cap' ],
			],
			'add_product' => [ // add a product to wishlist
				'route' => '/wishlists/(?P<id>\d+)/product/(?P<product_id>\d+)',
				'methods' => 'POST',
				'callback' => [ __CLASS__, 'add_product' ],
				'permission_callback' => [ __CLASS__, 'check_write_cap' ],
			],
			'remove_product' => [ // remove a product from wishlist
				'route' => '/wishlists/(?P<id>\d+)/product/(?P<product_id>\d+)',
				'methods' => 'DELETE',
				'callback' => [ __CLASS__, 'remove_product' ],
				'permission_callback' => [ __CLASS__, 'check_write_cap' ],
			],
		];

		public static function init() {
		
			self::register_routes();
		}

		protected static function register_routes(){
			do_action('yith_rest_wishlist_before_register_route');

			$wishlist_routes = apply_filters( 'yith_rest_wishlist_routes', self::$wishlist_routes );

			$prefix = self::REST_NAMESPACE . '/' . self::REST_VERSION;
			foreach( $wishlist_routes as $args ) {
				$route = $args['route'];
				unset( $args['route'] );
				register_rest_route( $prefix, $route, $args );
			}

			do_action('yith_rest_wishlist_after_register_route');
		}

		/**
		 * Get array of wishlists for current user
		 */
		public static function get($request) {
            // print_r($request);
			$user = get_user_by('email', $request['email']);
            $user_id = $user->ID;
            // echo 	$user_id ;
            
        
       
			try {
				$results = \WC_Data_Store::load( 'wishlist' )->query( [ 'user_id' => $user_id, 'session_id' => false ] );
			} catch( \Exception $e ){
				// return error response
				return new \WP_REST_Response(array('status' => 500, 'error' => $e->getMessage() ), 500);
			}

			if( empty( $results ) ) {
				return [];
			}

            global $wpdb;
            $wishlist_id = $results[0]->get_data()['id'];
            $wishlist_prs = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}yith_wcwl wls join  {$wpdb->prefix}posts wpps on (wls.prod_id = wpps.ID)  WHERE wls.wishlist_id = '".$wishlist_id."'", ARRAY_A );

            $woocommerce = new Client(
                'https://'.$_SERVER['HTTP_HOST'],
                $request['consumer_key'],
                $request['consumer_secret'],
                [
                    'wp_api' => true,
                    'version' => 'wc/v3',
                    'query_string_auth' => true
                ]
            );
            $products = $woocommerce->get('products'); 
            $ids = array_column($products, 'id');

        
        
        $prs = array();
     
        foreach($wishlist_prs as $pr) {
            
            $index_id = array_search($pr['ID'], $ids);

            $prs[] = $products[$index_id];
        }
        
        // $prs['wishlist_id'] = $wishlist_id;
        
			return new \WP_REST_Response( $prs );

		}

		/**
		 * Creates a wishlist for current user
		 */
		public static function post() {

		}

		/**
		 * Updates a wishlist for current user
		 */
		public static function update_single( $request ) {
			$id = isset( $request['id'] ) ? $request['id'] : 0;
			$product_ids = isset( $request['product_ids'] ) ? $request['product_ids'] : 0;

			$id = isset( $request['id'] ) ? $request['id'] : 0;

			if( ! $id || ! $product_ids ) {
				return new \WP_REST_Response( array( 'status' => 422, 'error' => 'Invalid id'), 422);
			}

			$wl = new \YITH_WCWL_Wishlist( $id );
		}

		/**
		 * Retrive a single wishlist item by id
		 * @return \WP_REST_Response
		 */
		public static function get_single( $request ) {

			$id = isset($request['id']) ? $request['id'] : 0;

			if( ! $id ) {
				return new \WP_REST_Response( array( 'status' => 422, 'error' => 'Invalid id'), 422);
			}

			$wl = new \YITH_WCWL_Wishlist( $id );

			if( ! $wl ) {
				return (new self)->err_404();
			}

			return new \WP_REST_Response( $wl->get_data() );
		}


		/**
		 * Deletes a wishlist
		 * @return \WP_REST_Response
		 */
		public static function delete( $request ){
			$id = $request['id'];
			return [ 'id' => $id ];
		}
		
	

		/**
		 * Adds a product to given wishlist
		 * @return \WP_REST_Response
		 */
		public static function add_product( $request ) {

			$wishlist_id = $request['id'] ? (int) $request['id'] : 0;
			$quantity = $request['quantity'] ? (int) $request['quantity'] : 1;
			$product_id = $request['product_id'] ? (int) $request['product_id'] : 0;
			$email = $request['email'] ? (string) $request['email'] : 0;
			$user = get_user_by('email', $request['email']);
            $user_id = $user->ID;
			if( ! $product_id || !$user_id ){
				return (new self)->err_404();
			}

			$args = [
				'add_to_wishlist' => $product_id,
				'user_id' => $user_id,
				'quantity' => $quantity,
				'wishlist_id' => $wishlist_id,
			];

			if( $wishlist_id ) {
				$args['wishlist_id'] = $wishlist_id;
			}



			try {
			    
                // print_r(session_id());
				
				YITH_WCWL()->add( $args );
				
                global $wpdb;
                $table_name='yith_wcwl_lists';
                $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}yith_wcwl_lists ORDER BY ID DESC LIMIT 1");
                // print_r($results);
                
                $execut= $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}yith_wcwl_lists SET user_id = '".$user_id."' WHERE ID = '".$results[0]->ID."'" ) );
                $execut= $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}yith_wcwl SET user_id = '".$user_id."' WHERE wishlist_id = '".$results[0]->ID."'" ) );

                // var_dump($execut);
                // yith_wcwl


				// $this->addProductToWishlist($args);
			} catch ( \YITH_WCWL_Exception $e ) {
				return new \WP_REST_Response(array('status' => 422, 'error' => $e->getMessage() ), 422);
			} catch ( \Exception $e ) {
				return new \WP_REST_Response(array('status' => 500, 'error' => $e->getMessage() ), 500);
			}

			// successful! return updated wishlist
			$wl = new \YITH_WCWL_Wishlist( $wishlist_id );
			return new \WP_REST_Response( $wl->get_data() );
		}

		/**
		 * Removes a product from given wishlist
		 * @return \WP_REST_Response
		 */
		public static function remove_product( $request ) {

			$wishlist_id = $request['id'] ? (int) $request['id'] : 0;
			$product_id = $request['product_id'] ? (int) $request['product_id'] : 0;

			if( ! $product_id || ! $wishlist_id ){
				return (new self)->err_404();
			}

			$args = [
				'remove_from_wishlist' => $product_id,
				'user_id' => get_current_user_id(),
				'wishlist_id' => $wishlist_id,
			];

			try {
				YITH_WCWL()->remove( $args );
			} catch ( \YITH_WCWL_Exception $e ) {
				return new \WP_REST_Response(array('status' => 422, 'error' => $e->getMessage() ), 422);
			} catch ( \Exception $e ) {
				return new \WP_REST_Response(array('status' => 500, 'error' => $e->getMessage() ), 500);
			}

			// successful! return updated wishlist
			$wl = new \YITH_WCWL_Wishlist( $wishlist_id );
			return new \WP_REST_Response( $wl->get_data() );
		}

		/**
		 * Checks if user is logged in
		 * Used in rest api permission check
		 * @return true|/WP_Error
		 */
		public static function check_auth( $request ){
        
        if(!isset($request['consumer_key']) || !isset($request['consumer_secret']) ) {
        	return new \WP_Error('unauthorized', 'Authentication Required', [
        				'code' => 401,
        				'message' => 'Authentication Required',
        				'data' => [],
        			]);
        }
        
        
        if(!isset($request['email']) ) {
        	return new \WP_Error('unauthorized', 'Authentication Required', [
        				'code' => 401,
        				'message' => 'User Email Required',
        				'data' => [],
        			]);
        }
          
        // 1st Method - Declaring $wpdb as global and using it to execute an SQL query statement that returns a PHP object
        global $wpdb;
       
        $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}woocommerce_api_keys WHERE truncated_key = '".$truncated_key."' AND consumer_secret = '".$request['consumer_secret']."' ", OBJECT );

       
        $truncated_key =  substr($request['consumer_key'], strlen($request['consumer_key'])-7, strlen($request['consumer_key'])); 
       
        $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}woocommerce_api_keys WHERE truncated_key = '".$truncated_key."' AND consumer_secret = '".$request['consumer_secret']."' ", OBJECT );
       
 

			if( !empty($results) && isset($results[0]->user_id) ) {
				return true;
			}

			return new \WP_Error('unauthorized', 'Authentication Required', [
				'code' => 401,
				'message' => 'Authentication Required',
				'data' => [],
			]);
		}

		/**
		 * Checks users read permssion for given wishlist
		 * Used in rest api permission check
		 */
		public static function check_read_cap( $request ){

			$res = self::check_auth( $request );
			if( is_wp_error( $res ) ){
				return $res;
			}

			$id = isset($request['id']) ? (int) $request['id'] : 0;

			$self = new self;
			if( ! $id ) {
				return $self->err_404();
			}

			$wl = new \YITH_WCWL_Wishlist( $id );
			if( ! $wl->current_user_can( 'view' ) ) {
				return $self->err_read_permission();
			}

			return true;
		}

		/**
		 * Checks users read permssion for given wishlist
		 * Used in rest api permission check
		 */
		public static function check_write_cap( $request ){
			$id = isset($request['id']) ? (int) $request['id'] : 0;

			$self = new self;
			if( ! $id ) {
				return $self->err_404();
			}

			$wl = new \YITH_WCWL_Wishlist( $id );
			if( ! $wl->current_user_can( 'write' ) ) {
				return $self->err_write_permission();
			}

			return true;
		}

		public function err_404(){
			return new \WP_REST_Response( [ 'status' => 404, 'error' => 'Wishlist not found!' ], 404);
		}

		protected function err_read_permission() {
			return new \WP_REST_Response( [ 'status' => 403, 'error' => 'You do not have permission to read.' ], 403);
		}

		protected function err_write_permission() {
			return new \WP_REST_Response( [ 'status' => 403, 'error' => 'You do not have permission to write.' ], 403);
		}

		protected function err_delete_permission() {
			return new \WP_REST_Response( [ 'status' => 403, 'error' => 'You do not have permission to delete.' ], 403);
		}
	}
}
