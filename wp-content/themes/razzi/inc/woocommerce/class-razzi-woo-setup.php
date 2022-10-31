<?php
/**
 * Woocommerce Setup functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce;
use Razzi\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Woocommerce initial
 *
 */
class Setup {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'woocommerce_setup' ) );
		add_filter( 'woocommerce_get_image_size_gallery_thumbnail', array(
			$this,
			'get_gallery_thumbnail_size'
		) );
	}

	/**
	 * WooCommerce setup function.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function woocommerce_setup() {
		add_theme_support( 'woocommerce', array(
			'product_grid' => array(
				'default_rows'    => 4,
				'min_rows'        => 2,
				'max_rows'        => 20,
				'default_columns' => 4,
				'min_columns'     => 2,
				'max_columns'     => 7,
			),
		) );

		if ( apply_filters('razzi_product_gallery_zoom', intval( Helper::get_option( 'product_image_zoom' ) ) ) ) {
			add_theme_support( 'wc-product-gallery-zoom' );
		}
		if ( intval( Helper::get_option( 'product_image_lightbox' ) ) ) {
			add_theme_support( 'wc-product-gallery-lightbox' );
		}

		add_theme_support( 'wc-product-gallery-slider' );
	}

	/**
	 * Set the gallery thumbnail size.
	 *
	 * @since 1.0.0
	 *
	 * @param array $size Image size.
	 *
	 * @return array
	 */
	public function get_gallery_thumbnail_size( $size ) {
		$size['width']  = 130;
		$cropping      = get_option( 'woocommerce_thumbnail_cropping', '1:1' );

		if ( 'uncropped' === $cropping ) {
			$size['height'] = '';
			$size['crop']   = 0;
		} elseif ( 'custom' === $cropping ) {
			$width          = max( 1, get_option( 'woocommerce_thumbnail_cropping_custom_width', '4' ) );
			$height         = max( 1, get_option( 'woocommerce_thumbnail_cropping_custom_height', '3' ) );
			$size['height'] = absint( \Automattic\WooCommerce\Utilities\NumberUtil::round( ( $size['width'] / $width ) * $height ) );
			$size['crop']   = 1;
		} else {
			$cropping_split = explode( ':', $cropping );
			$width          = max( 1, current( $cropping_split ) );
			$height         = max( 1, end( $cropping_split ) );
			$size['height'] = absint( \Automattic\WooCommerce\Utilities\NumberUtil::round( ( $size['width'] / $width ) * $height ) );
			$size['crop']   = 1;
		}

		return $size;
	}

}
