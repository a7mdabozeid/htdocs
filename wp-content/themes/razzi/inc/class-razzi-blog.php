<?php
/**
 * Blog functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Woocommerce initial
 *
 */
class Blog {
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
		add_action( 'wp', array( $this, 'add_actions' ), 0 );
	}

	/**
	 * Add actions
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_actions() {
		$this->get( 'posts' );
		$this->get( 'post_loop' );
		$this->get( 'post' );
		$this->get( 'related_posts' );
		$this->get( 'search' );
	}

	/**
	 * Get Razzi Page Template Class.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function get( $class ) {
		switch ( $class ) {
			case 'posts':
				if ( \Razzi\Helper::is_blog() ) {
					return \Razzi\Blog\Posts::instance();
				}
				break;
			case 'post_loop':
				return \Razzi\Blog\Post_Loop::instance();
				break;

			case 'post':
				if ( is_singular( 'post' ) ) {
					return \Razzi\Blog\Post::instance();
				}
				break;

			case 'related_posts':
				if ( is_singular( 'post' ) && Helper::get_option('related_posts') ) {
					return \Razzi\Blog\Related_Posts::instance();
				}
				break;

			case 'search':
				if ( is_search() ) {
					return \Razzi\Blog\Search::instance();
				}
				break;
		}

	}
}
