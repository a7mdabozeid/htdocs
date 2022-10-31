<?php
/**
 * Register required, recommended plugins for theme
 *
 * @package Razzi
 */

namespace Razzi\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register required plugins
 *
 * @since  1.0
 */
class Plugin_Install {
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
		add_action( 'tgmpa_register', array( $this, 'register_required_plugins' ) );
	}


	/**
	 * Register required plugins
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_required_plugins() {
		$plugins = array(
			array(
				'name'               => esc_html__( 'Meta Box', 'razzi' ),
				'slug'               => 'meta-box',
				'required'           => true,
				'force_activation'   => false,
				'force_deactivation' => false,
			),
			array(
				'name'               => esc_html__( 'Kirki', 'razzi' ),
				'slug'               => 'kirki',
				'required'           => true,
				'force_activation'   => false,
				'force_deactivation' => false,
			),
			array(
				'name'               => esc_html__( 'WooCommerce', 'razzi' ),
				'slug'               => 'woocommerce',
				'required'           => true,
				'force_activation'   => false,
				'force_deactivation' => false,
			),
			array(
				'name'               => esc_html__( 'Elementor Page Builder', 'razzi' ),
				'slug'               => 'elementor',
				'required'           => true,
				'force_activation'   => false,
				'force_deactivation' => false,
			),
			array(
				'name'               => esc_html__( 'Razzi Addons', 'razzi' ),
				'slug'               => 'razzi-addons',
				'source'             => esc_url( 'http://demo4.drfuri.com/plugins/razzi-addons.zip?version=1.1.8' ),
				'required'           => true,
				'force_activation'   => false,
				'force_deactivation' => false,
				'version'            => '1.1.8',
			),
			array(
				'name'               => esc_html__( 'Contact Form 7', 'razzi' ),
				'slug'               => 'contact-form-7',
				'required'           => false,
				'force_activation'   => false,
				'force_deactivation' => false,
			),
			array(
				'name'               => esc_html__( 'MailChimp for WordPress', 'razzi' ),
				'slug'               => 'mailchimp-for-wp',
				'required'           => false,
				'force_activation'   => false,
				'force_deactivation' => false,
			),
			array(
				'name'               => esc_html__( 'YITH WooCommerce Wishlist', 'razzi' ),
				'slug'               => 'yith-woocommerce-wishlist',
				'required'           => false,
				'force_activation'   => false,
				'force_deactivation' => false,
			),
			array(
				'name'               => esc_html__( 'WCBoost – Variation Swatches', 'razzi' ),
				'slug'               => 'wcboost-variation-swatches',
				'required'           => false,
				'force_activation'   => false,
				'force_deactivation' => false,
			),
		);
		$config  = array(
			'domain'       => 'razzi',
			'default_path' => '',
			'menu'         => 'install-required-plugins',
			'has_notices'  => true,
			'is_automatic' => false,
			'message'      => '',
			'strings'      => array(
				'page_title'                      => esc_html__( 'Install Required Plugins', 'razzi' ),
				'menu_title'                      => esc_html__( 'Install Plugins', 'razzi' ),
				'installing'                      => esc_html__( 'Installing Plugin: %s', 'razzi' ),
				'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'razzi' ),
				'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'razzi' ),
				'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'razzi' ),
				'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'razzi' ),
				'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'razzi' ),
				'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'razzi' ),
				'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'razzi' ),
				'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'razzi' ),
				'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'razzi' ),
				'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'razzi' ),
				'activate_link'                   => _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'razzi' ),
				'return'                          => esc_html__( 'Return to Required Plugins Installer', 'razzi' ),
				'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'razzi' ),
				'complete'                        => esc_html__( 'All plugins installed and activated successfully. %s', 'razzi' ),
				'nag_type'                        => 'updated',
			),
		);
		tgmpa( $plugins, $config );
	}
}
