<?php
namespace WCBoost\VariationSwatches;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class
 */
final class Plugin {
	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public $version = '1.0.12';

	/**
	 * Options mapping object.
	 *
	 * @var WCBoost\VariationSwatches\Mapping
	 */
	public $mapping = null;

	/**
	 * Instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @static
	 *
	 * @var Plugin
	 */
	protected static $_instance = null;

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->load_files();
		$this->init_hooks();
		$this->set_mapping();
	}

	/**
	 * Load files
	 */
	public function load_files() {
		require_once dirname( __FILE__ ) . '/mapping.php';
		require_once dirname( __FILE__ ) . '/helper.php';
		require_once dirname( __FILE__ ) . '/swatches.php';
		require_once dirname( __FILE__ ) . '/compatibility.php';

		require_once dirname( __FILE__ ) . '/admin/backup.php';
		require_once dirname( __FILE__ ) . '/admin/settings.php';
		require_once dirname( __FILE__ ) . '/admin/term-meta.php';
		require_once dirname( __FILE__ ) . '/admin/product-data.php';

		require_once dirname( __FILE__ ) . '/customizer/customizer.php';
	}

	/**
	 * Initialize hooks
	 */
	public function init_hooks() {
		add_action( 'init', [ $this, 'load_textdomain' ] );
	}

	/**
	 * Load plugin text domain
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wcboost-variation-swatches', false, dirname( plugin_basename( WCBOOST_VARIATION_SWATCHES_FILE ) ) . '/languages/' );
	}

	/**
	 * Set the mapping object
	 */
	public function set_mapping() {
		$this->mapping = new Mapping();
	}

	/**
	 * Get the mapping object
	 *
	 * @return WCBoost\VariationSwatches\Mapping
	 */
	public function get_mapping() {
		return $this->mapping;
	}
}
