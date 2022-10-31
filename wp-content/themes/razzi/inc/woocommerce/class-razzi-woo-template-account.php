<?php
/**
 * Hooks of Account.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce\Template;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Account template.
 */
class Account {
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
		add_action( 'woocommerce_edit_account_form_start', array( $this, 'account_title' ), 20 );

		// Get login social
		if( class_exists('NextendSocialLogin') && method_exists('NextendSocialLogin', 'addLoginFormButtons') ) {
			add_action('woocommerce_login_form_end', 'NextendSocialLogin::addLoginFormButtons');
			add_action('woocommerce_register_form_end', 'NextendSocialLogin::addLoginFormButtons');
		}

	}

	/**
	 * Add title page account detail
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function account_title() {
		echo sprintf( '<h3 class="account-title title">%s</h3>', esc_html__( 'Edit Account', 'razzi' ) );
	}

}
