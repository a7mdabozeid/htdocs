<?php
/**
 * Newsletter popup template hooks.
 *
 * @package Razzi
 */

namespace Razzi\Modules;
use Razzi\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Newsletter popup template.
 */
class Newsletter_Popup {
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
		add_action( 'wp_footer', array( $this, 'newsletter_popup' ), 40 );
	}

	/**
	 * Add the popup HTML to footer
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function newsletter_popup() {
		$newsletter = apply_filters( 'razzi_newsletter_popup', Helper::get_option( 'newsletter_popup_enable' ) );
		if ( ! $newsletter ) {
			return;
		}

		get_template_part( 'template-parts/popup/newsletter-popup' );
	}
}
