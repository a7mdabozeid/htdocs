<?php
/**
 * Topbar Mobile functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Razzi
 */

namespace Razzi\Mobile;
use Razzi\Helper;

class Footer {
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
		add_filter( 'razzi_get_footer_sections', array( $this, 'get_footer_sections' ) );

		add_filter( 'razzi_site_footer_class', array( $this, 'get_footer_classes' ) );
	}

	/**
	 * Get footer section
	 *
	 */
	function get_footer_sections($section) {
		if( ! intval(Helper::get_option( 'mobile_footer_newsletter' )) ) {
			$key = array_search( 'newsletter', $section);
			if ( $key !== false ) {
				unset($section[$key]);
			}
		}

		if( ! intval(Helper::get_option( 'mobile_footer_widget' ))) {
			$key = array_search( 'widgets', $section );
			if ( $key !== false ) {
				unset($section[$key]);
			}
		}

		if( ! intval(Helper::get_option( 'mobile_footer_main' ))) {
			$key = array_search( 'main', $section );
			if ( $key !== false ) {
				unset($section[$key]);
			}
		}

		if( ! intval(Helper::get_option( 'mobile_footer_extra' )) ) {
			$key = array_search( 'extra', $section );
			if ( $key !== false ) {
				unset($section[$key]);
			}
		}

		return $section;
	}

	/**
	 * Get footer classes
	 *
	 */
	function get_footer_classes( $classes ) {
		if ( ! intval( Helper::get_option( 'mobile_footer_newsletter' ) ) &&
			! intval( Helper::get_option( 'mobile_footer_widget' ) ) &&
			! intval( Helper::get_option( 'mobile_footer_main' ) ) &&
			! intval( Helper::get_option( 'mobile_footer_extra' ) )
		) {
			$classes .= ' site-footer__no-padding-top';
		}

		return $classes;
	}

}
