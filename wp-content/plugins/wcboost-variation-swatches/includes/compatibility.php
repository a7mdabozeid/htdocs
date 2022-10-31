<?php
/**
 * Compatible with other plugins/themes
 */
namespace WCBoost\VariationSwatches;

defined( 'ABSPATH' ) || exit;

class Compatibility {
	/**
	 * The single instance of the class
	 *
	 * @var WCBoost\VariationSwatches\Compatibility
	 */
	protected static $_instance = null;

	/**
	 * Main instance
	 *
	 * @return WCBoost\VariationSwatches\Compatibility
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'check_compatible_hooks' ] );
	}

	/**
	 * Check compatibility with other plugins/themes and add hooks
	 */
	public function check_compatible_hooks() {
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			add_filter( 'wcboost_variation_swatches_translate_term_meta', array( $this, 'translate_term_meta' ), 10, 3 );
		}
	}

	/**
	 * Copy swatches metadata from the original term
	 *
	 * @return mixed
	 */
	public function translate_term_meta( $meta_value, $term_id, $meta_key ) {
		$term         = get_term( $term_id );
		$default_lang = apply_filters( 'wpml_default_language', null );

		$original_term_id = apply_filters( 'wpml_object_id', $term->term_id, $term->taxonomy, false, $default_lang );

		if ( $original_term_id ) {
			$meta_value = get_term_meta( $original_term_id, $meta_key, true );
		}

		return $meta_value;
	}
}

Compatibility::instance();