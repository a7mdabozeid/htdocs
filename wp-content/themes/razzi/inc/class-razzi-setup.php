<?php
/**
 * Razzi functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Razzi
 */

namespace Razzi;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Razzi after setup theme
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
		add_action( 'after_setup_theme', array( $this, 'setup_theme' ), 2 );
		add_action( 'after_setup_theme', array( $this, 'setup_content_width' ) );
	}

	/**
	 * Setup theme
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup_theme() {
		/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on razzi, use a find and replace
	 * to change  'razzi' to the name of your theme in all the template files.
	 */
		load_theme_textdomain( 'razzi', get_template_directory() . '/lang' );

		// Theme supports
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );
		add_theme_support( 'customize-selective-refresh-widgets' );

		add_editor_style( 'assets/css/editor-style.css' );

		// Load regular editor styles into the new block-based editor.
		add_theme_support( 'editor-styles' );

		// Load default block styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for responsive embeds.
		add_theme_support( 'responsive-embeds' );

		add_theme_support( 'align-wide' );

		add_theme_support( 'align-full' );

		add_image_size( 'razzi-blog-grid', 600, 398, true );
		add_image_size( 'razzi-post-full', 1170, 450, true );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'primary'    => esc_html__( 'Primary Menu', 'razzi' ),
			'secondary'  => esc_html__( 'Secondary Menu', 'razzi' ),
			'hamburger'  => esc_html__( 'Hamburger Menu', 'razzi' ),
			'socials'    => esc_html__( 'Social Menu', 'razzi' ),
			'department' => esc_html__( 'Department Menu', 'razzi' ),
			'mobile'     => esc_html__( 'Mobile Menu', 'razzi' ),
		) );

	}

	/**
	 * Set the $content_width global variable used by WordPress to set image dimennsions.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'razzi_content_width', 640 );
	}
}
