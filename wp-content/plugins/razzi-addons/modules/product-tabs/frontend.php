<?php

namespace Razzi\Addons\Modules\Product_Tabs;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class FrontEnd {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;


	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	const POST_TYPE     = 'razzi_product_tab';
	const TAXONOMY_TYPE   = 'razzi_product_tab_type';


	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter('woocommerce_product_tabs', array( $this, 'product_tabs' ), 20);
	}

	/**
	 * Add default product tabs to product pages.
	 *
	 * @param array $tabs Array of tabs.
	 * @return array
	 */
	function product_tabs( $tabs = array() ) {
		global $product;
		if( empty( $product ) ) {
			return $tabs;
		}

		$tabs_ids = (array) $this->get_product_tabs();
		$index = 5;
		foreach( $tabs_ids as $tab_id) {
			$tab = get_post( $tab_id );
			if( get_post_meta( $tab_id, '_product_tab_disable', true) == 'yes' ) {
				if( isset( $tabs[$tab->post_name] ) ) {
					unset( $tabs[$tab->post_name] );
				} else {
					continue;
				}
			}
			$terms = get_the_terms( $tab_id, self::TAXONOMY_TYPE );
			$tab_type = ! is_wp_error( $terms ) && $terms ? $terms[0]->name : 'global';

			switch( $tab_type ) {
				case 'global':
					if( ! isset( $tabs[$tab->post_name] ) && $this->is_global_tab($tab_id) && ! empty($tab->post_content) ) {
						$tabs[$tab->post_name]['title'] = $tab->post_title;
						$tabs[$tab->post_name]['priority'] = $index;
						$tabs[$tab->post_name]['tab_id'] = $tab_id;
						$tabs[$tab->post_name]['callback'] = 'Razzi\Addons\Modules\Product_Tabs\FrontEnd::get_product_tab_content';
					}
					break;
				case 'product':
					if( ! isset( $tabs[$tab->post_name] ) && $this->is_product_tab($tab_id) && ! empty($tab->post_content) ) {
						$tabs[$tab->post_name]['title'] = $tab->post_title;
						$tabs[$tab->post_name]['priority'] = $index;
						$tabs[$tab->post_name]['tab_id'] = $tab_id;
						$tabs[$tab->post_name]['callback'] = 'Razzi\Addons\Modules\Product_Tabs\FrontEnd::get_product_tab_content';
					}
					break;
				case 'custom':
					$has_content = empty( $tab->post_content ) ? false : true;
					if( ! isset( $tabs[$tab->post_name] ) && $this->is_custom_tab($tab_id, $has_content) ) {
						$tabs[$tab->post_name]['title'] = $tab->post_title;
						$tabs[$tab->post_name]['priority'] = $index;
						$tabs[$tab->post_name]['tab_id'] = $tab_id;
						$tabs[$tab->post_name]['callback'] = 'Razzi\Addons\Modules\Product_Tabs\FrontEnd::get_product_custom_tab_content';
					}
					break;
				case 'default':
					if( isset( $tabs[$tab->post_name] ) ) {
						$tabs[$tab->post_name]['title'] = $tab->post_title;
						$tabs[$tab->post_name]['priority'] = $index;
					}
					break;
				default :
					break;
			}

			$index = $index + 5;
		}

		return $tabs;
	}

	/**
	 * Callback get product tab content
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function get_product_tab_content($key, $product_tab) {
		if( ! isset($product_tab['tab_id']) ) {
			return;
		}

		$post = get_post( $product_tab['tab_id']);
		echo $post->post_content;
	}

	/**
	 * Callback get product tab content
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function get_product_custom_tab_content($key, $product_tab) {
		if( ! isset($product_tab['tab_id']) ) {
			return;
		}
		global $product;
		$tab_content = get_post_meta( $product->get_id(), '_product_tab_content_' . $product_tab['tab_id'], true );
		if( empty( $tab_content ) ) {
			$post = get_post( $product_tab['tab_id']);
			$tab_content = $post->post_content;
		}

		echo $tab_content;
	}

	/**
	 * Check product is global tab
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function is_global_tab($tab_id) {
		global $product;
		$categories = $product->get_category_ids();
		if( empty( $categories ) ) {
			return false;
		}

		$terms = get_the_terms( $tab_id, 'product_cat' );
		if( is_wp_error( $terms ) ) {
			return false;
		}

		if( empty( $terms ) ) {
			return true;
		}

		foreach( $terms as $term ) {
			if( in_array( $term->term_id, $categories ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check product is product tab
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function is_product_tab($tab_id) {
		global $product;
		$product_ids = maybe_unserialize( get_post_meta( $tab_id, '_product_tab_product_ids', true ) );
		if( in_array( $product->get_id(), $product_ids ) ) {
			return true;
		}


		return false;
	}


	/**
	 * Check product is custom tab
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function is_custom_tab($tab_id, $has_content) {
		global $product;
		if( get_post_meta( $product->get_id(), '_product_tab_disable_' . $tab_id, true )) {
			return false;
		}

		$tab_content = get_post_meta( $product->get_id(), '_product_tab_content_' . $tab_id, true );
		if( empty( $tab_content ) && ! $has_content ) {
			return false;
		}

		return true;
	}


	/**
	 * Get product tab ids
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_product_tabs() {
		$transient_name = 'razzi_wc_product_tabs';
		$transient     = get_transient( $transient_name );
		$post_ids = $transient ? $transient : false;
		if ( false === $post_ids) {
			$product_tabs = new \WP_Query( array(
				'post_type'      => self::POST_TYPE,
				'posts_per_page' => '-1',
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'orderby' 		=> 'menu_order',
				'order' 		=> 'DESC',
				'suppress_filters'       => false,
			) );
			if ( $product_tabs->have_posts() ) {
				$post_ids = $product_tabs->posts;
				$transient = $post_ids;
				set_transient( $transient_name, $transient, DAY_IN_SECONDS );
			}
			wp_reset_postdata();

		}

		return $post_ids;
	}

}