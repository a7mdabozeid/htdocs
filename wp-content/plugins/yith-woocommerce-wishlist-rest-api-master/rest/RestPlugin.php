<?php

namespace YITH\Wishlist;

if( ! class_exists( '\YITH\Wishlist\RestPlugin' ) ) {
	class RestPlugin{
		public static function init(){
		    
           
// 			add_action( 'init', [ '\YITH\Wishlist\Rest', 'init' ] );
			add_action( 'rest_api_init', [ '\YITH\Wishlist\Rest', 'init' ] );
		}
	}
}
