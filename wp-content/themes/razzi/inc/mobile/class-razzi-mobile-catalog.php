<?php
/**
 * Mobile functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi\Mobile;

use Razzi\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Mobile initial
 *
 */
class Catalog {
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
		add_action( 'wp', array( $this, 'hooks' ), 0 );
	}

	/**
	 * Hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function hooks() {
		if ( ! \Razzi\Helper::is_catalog() ) {
			return;
		}

		add_filter('razzi_get_catalog_layout', array( $this, 'get_catalog_layout' ));

		add_filter('razzi_get_catalog_toolbar_layout', array( $this, 'get_catalog_toolbar_layout' ));

		add_filter('razzi_get_catalog_localize_data', array( $this, 'get_catalog_localize_data' ));

		// Remove page header class
		add_filter( 'razzi_page_header_class', '__return_empty_string' );

		add_filter('razzi_get_page_header', '__return_false');

		// remove sidebar
		add_filter( 'razzi_get_sidebar', '__return_false' );

		// Add products toolbar
		add_action( 'woocommerce_before_shop_loop', array( \Razzi\Theme::instance()->get( 'breadcrumbs' ), 'breadcrumbs' ) );
		add_action( 'woocommerce_before_shop_loop', array( $this, 'products_toolbar' ) );

		add_action( 'razzi_get_product_filters_modal_sidebar', array( $this, 'get_product_filters_modal_sidebar' ) );
		add_action( 'razzi_get_product_filters_modal_classes', array( $this, 'get_product_filters_modal_classes' ) );

		// remove banners
		remove_action( 'woocommerce_before_shop_loop', array(
			\Razzi\Theme::instance()->get( 'woocommerce' )->get_template( 'catalog' ),
			'products_banners_top'
		), 10 );

		// remove top categories
		remove_action( 'woocommerce_before_shop_loop', array(
			\Razzi\Theme::instance()->get( 'woocommerce' )->get_template( 'catalog' ),
			'products_top_categories'
		), 20 );

		// remove toolbar mobile
		remove_action( 'woocommerce_before_shop_loop', array(
			\Razzi\Theme::instance()->get( 'woocommerce' )->get_template( 'catalog' ),
			'products_toolbar'
		), 40 );

	}

	/**
	 * Catalog layout
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_catalog_layout() {
		return 'grid';
	}

	/**
	 * Catalog layout
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_catalog_toolbar_layout() {
		return 'v0';
	}

	/**
	 * Get catalog localize data
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_catalog_localize_data($razzi_catalog_data) {
		$razzi_catalog_data['catalog_filters_sidebar_collapse_content'] = 1;

		return $razzi_catalog_data;
	}

	/**
	 * Catalog products toolbar.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function products_toolbar() {
		if ( wc_get_loop_prop( 'is_shortcode' ) ) {
			return;
		}
		?>

        <div class="catalog-toolbar">
			<div class="product-toolbar-header clearfix">
				<?php
				$this->page_header();
				?>
        	</div>
			<?php $this->products_toolbar_filter(); ?>
        </div>

		<?php
	}

	/**
	 * Catalog page header.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function page_header() {
		if ( ! intval(Helper::get_option( 'mobile_catalog_page_header' ) )) {
			return;
		}

		$items = (array) Helper::get_option( 'mobile_catalog_page_header_els' );
		if( in_array( 'breadcrumb', $items )) {
			\Razzi\Theme::instance()->get( 'breadcrumbs' )->breadcrumbs();
		}
		if( in_array( 'title', $items )) {
			the_archive_title('<h1 class="page-title">', '</h1>');
		}
		?>
		<?php
	}

	/**
	 * Catalog products toolbar.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function products_toolbar_filter() {
		if ( wc_get_loop_prop( 'is_shortcode' ) ) {
			return;
		}

		$navigation_bar = Helper::get_option( 'mobile_navigation_bar' );
		if ( in_array( $navigation_bar, array( 'simple_adoptive', 'standard_adoptive' ) ) ) {
		    return;
		}

		\Razzi\Theme::instance()->set_prop( 'modals', 'filter' );
		?>
        <a href="#catalog-filters" class="toggle-filters catalog-toolbar-item__filter"
           data-toggle="modal"
           data-target="catalog-filters-modal">
            <?php echo \Razzi\Icon::get_svg( 'filter-2', '', 'mobile' ) ?>
        </a>
		<?php
	}

	/**
	 * Change to mobile sidebar filter
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_product_filters_modal_sidebar( $sidebar ) {
		if ( is_active_sidebar( 'catalog-filters-mobile' ) ) {
			$sidebar = 'catalog-filters-mobile';
		}

		return $sidebar;
	}

	/**
	 * Change class filter status
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_product_filters_modal_classes( ) {
		return 'has-collapse-hide';
	}
}
