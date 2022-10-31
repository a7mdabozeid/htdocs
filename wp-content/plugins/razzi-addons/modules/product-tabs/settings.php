<?php

namespace Razzi\Addons\Modules\Product_Tabs;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Settings  {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;


	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	const POST_TYPE     = 'razzi_product_tab';
	const OPTION_NAME   = 'razzi_product_tab';
	const TAXONOMY_TAB_TYPE     = 'razzi_product_tab_type';


	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'woocommerce_get_sections_products', array( $this, 'product_tabs_section' ), 30, 2 );
		add_filter( 'woocommerce_get_settings_products', array( $this, 'product_tabs_settings' ), 30, 2 );

		if ( get_option( 'razzi_product_tab' ) != 'yes' ) {
			return;
		}

		$this->create_terms();
		$this->create_post_type();

		// Handle post columns
		add_filter( sprintf( 'manage_%s_posts_columns', self::POST_TYPE ), array( $this, 'edit_admin_columns' ) );
		add_action( sprintf( 'manage_%s_posts_custom_column', self::POST_TYPE ), array( $this, 'manage_custom_columns' ), 10, 2 );

		// Add meta boxes.
		add_action( 'add_meta_boxes', array( $this, 'meta_boxes' ), 1 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		add_action( 'wp_trash_post', array( $this, 'clear_product_tabs_cache' ) );
		add_action( 'before_delete_post', array( $this, 'clear_product_tabs_cache' ) );
		add_action( 'razzi_after_product_tab_ordering', array( $this, 'clear_product_tabs_cache' ) );

		// Enqueue style and javascript
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		// Add custom post type to screen ids
		add_filter('woocommerce_screen_ids', array( $this, 'wc_screen_ids' ) );

		// Add custom fields to edit custom box
		add_action('quick_edit_custom_box', array( $this, 'edit_custom_box' ), 20, 2 );

		add_action( 'wp_ajax_razzi_product_tab_ordering', array( $this, 'product_tab_ordering' ) );

		add_action( 'pre_get_posts', array( $this, 'product_tab_column_orderby' ) );

		$this->product_tab_redirect();
	}

	/**
	 * Add Product Tabs settings section to the Products setting tab.
     *
	 * @since 1.0.0
	 *
	 * @param array $sections
	 * @return array
	 */
	public function product_tabs_section( $sections ) {
		$sections['razzi_addons_product_tabs'] = esc_html__( 'Product Tabs', 'razzi' );

		return $sections;
	}

	/**
	 * Adds a new setting field to products tab.
     *
	 * @since 1.0.0
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public function product_tabs_settings( $settings, $section ) {
		if ( 'razzi_addons_product_tabs' != $section ) {
			return $settings;
		}

		$settings_product_tabs = array(
			array(
				'name' => esc_html__( 'Product Tabs', 'razzi' ),
				'type' => 'title',
				'id'   => self::OPTION_NAME . '_options',
			),
			array(
				'name'    => esc_html__( 'Product Tabs', 'razzi' ),
				'desc'    => esc_html__( 'Enable product tabs manager', 'razzi' ),
				'id'      => self::OPTION_NAME,
				'default' => 'no',
				'type'    => 'checkbox',
				'checkboxgroup' => 'start',
			),
			array(
				'type' => 'sectionend',
				'id'   => self::OPTION_NAME . '_options',
			),
		);

		return $settings_product_tabs;
	}

	/**
	 * Add custom column to product tabss management screen
	 * Add Thumbnail column
     *
	 * @since 1.0.0
	 *
	 * @param  array $columns Default columns
	 *
	 * @return array
	 */
	public function edit_admin_columns( $columns ) {
		$columns = array_merge( $columns, array(
			'product_tab_disable' => esc_html__( 'Disabled', 'razzi' ),
			'product_tab_type' => esc_html__( 'Type', 'razzi' ),
			'product_tab_products' => esc_html__( 'Products', 'razzi' ),
			'product_tab_categories' => esc_html__( 'Categories', 'razzi' ),
		) );

		return $columns;
	}

	/**
	 * Handle custom column display
     *
	 * @since 1.0.0
	 *
	 * @param  string $column
	 * @param  int    $post_id
	 */
	public function manage_custom_columns( $column, $post_id ) {
		$type_name = 'global';
		switch ( $column ) {
			case 'product_tab_disable':
				$tab_disable = get_post_meta( $post_id, '_product_tab_disable', true);
				echo $tab_disable == 'yes' ? esc_html__( 'Yes', 'razzi' ) : esc_html__( 'No', 'razzi' );
				echo sprintf('<input type="hidden" id="product_tab_disable_%s" value="%s">', esc_attr($post_id), esc_attr($tab_disable));
				break;
			case 'product_tab_type':
				$types = get_the_terms( $post_id, self::TAXONOMY_TAB_TYPE );
				if ( ! is_wp_error( $types ) && $types && is_array( $types ) ) {
					$type_name = $types[0]->name;
					switch( $type_name ) {
						case 'global':
							esc_html_e('Global', 'razzi') ;
							break;
						case 'product':
							esc_html_e('Product', 'razzi') ;
							break;
						case 'custom':
							esc_html_e('Custom', 'razzi') ;
							break;
						default:
							esc_html_e('Default', 'razzi') ;
							break;
					}
				} else {
					esc_html_e( 'No Type', 'razzi' );
				}

				break;
			case 'product_tab_products':
				$links = array();
				$product_ids = maybe_unserialize( get_post_meta( $post_id, '_product_tab_product_ids', true ) );
				if (  $product_ids ) {
					foreach ( $product_ids as $product_id ) {
						$product = wc_get_product( $product_id );
						if ( is_object( $product ) ) {
							$links[] = sprintf( '<a href="%s">%s</a>', esc_url( get_edit_post_link( $product_id ) ), $product->get_title() );
						}
					}
				} else {
					$links[] = esc_html__( 'No Product', 'razzi' );
				}

				echo implode( ', ', $links );
				break;
			case 'product_tab_categories':
				$cats = get_the_terms( $post_id, 'product_cat' );
				$links = array();
				if ( ! is_wp_error( $cats ) && $cats && is_array( $cats ) ) {
					foreach ( $cats as $cat) {
						$links[] = sprintf( '<a href="%s">%s</a>', esc_url( get_edit_term_link( $cat->term_id, 'product_cat', 'product' ) ), $cat->name );

					}
				} else {
					$links[] = esc_html__( 'No Category', 'razzi' );
				}

				echo implode( ', ', $links );
				break;
		}
	}

	/**
	 * Get option of product tabs.
     *
	 * @since 1.0.0
	 *
	 * @param string $option
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get_option( $option = '', $default = false ) {
		if ( ! is_string( $option ) ) {
			return $default;
		}

		if ( empty( $option ) ) {
			return get_option( self::OPTION_NAME, $default );
		}

		return get_option( sprintf( '%s_%s', self::OPTION_NAME, $option ), $default );
	}

	/**
	 * Add meta boxes
	 *
	 * @param object $post
	 */
	public function meta_boxes( $post ) {
		add_meta_box( 'razzi-product-tabs', esc_html__( 'Tabs Settings', 'razzi' ), array( $this, 'tabs_meta_box' ), self::POST_TYPE, 'advanced', 'high' );
	}

	/**
	 * Tables meta box.
	 * Content will be filled by js.
     *
	 * @since 1.0.0
	 *
	 * @param object $post
	 */
	public function tabs_meta_box( $post ) {
		?>
		<div id="razzi-product-tabs-settings" class="razzi-product-tabs-settings">
			<p class="form-field">
				<label><?php esc_html_e('Tab Type', 'razzi'); ?></label>
				<span class="razzi-product-tabs--type">
					<?php
					$terms = get_the_terms( $post->ID, self::TAXONOMY_TAB_TYPE );
					$tab_type = ! is_wp_error( $terms ) && $terms ? $terms[0]->name : 'global';
					?>
					<input type="radio" id="razzi-product-tab-global" <?php echo $tab_type == 'global' ? 'checked' : ''; ?> class="razzi-product-tab--input" checked name="_product_tab_type" value="global">
					<label for="razzi-product-tab-global"><?php esc_html_e('Global', 'razzi'); ?></label>
					<input type="radio" id="razzi-product-tab-product" <?php echo $tab_type == 'product' ? 'checked' : ''; ?> class="razzi-product-tab--input" name="_product_tab_type" value="product">
					<label for="razzi-product-tab-product"><?php esc_html_e('Product', 'razzi'); ?></label>
					<input type="radio" id="razzi-product-tab-custom" <?php echo $tab_type == 'custom' ? 'checked' : ''; ?> class="razzi-product-tab--input" name="_product_tab_type" value="custom">
					<label for="razzi-product-tab-custom"><?php esc_html_e('Custom', 'razzi'); ?></label>
				</span>

			</p>
			<p class="form-field razzi-product-tabs--product">
				<label for="product-tab-id"><?php esc_html_e( 'Products', 'razzi' ); ?></label>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="product-tab-id" name="_product_tab_product_ids[]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'razzi' ); ?>" data-action="woocommerce_json_search_products">
					<?php
					$product_ids = maybe_unserialize( get_post_meta( $post->ID, '_product_tab_product_ids', true ) );
					if ( $product_ids ) {
						foreach ( $product_ids as $product_id ) {
							$product = wc_get_product( $product_id );
							if ( is_object( $product ) ) {
								echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
							}
						}
					}
					?>
				</select>
			</p>
			<p class="form-field razzi-product-tabs--categories">
				<label for="product_tab_categories"><?php esc_html_e( 'Categories', 'razzi' ); ?></label>
				<select class="wc-category-search" multiple="multiple" style="width: 50%;" id="product_tab_categories" name="_product_tab_cat_slugs[]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'razzi' ); ?>" data-action="woocommerce_json_search_categories">
					<?php
					$terms = get_the_terms( $post->ID, 'product_cat' );
					if ( ! is_wp_error($terms) && $terms && is_array( $terms ) ) {
						foreach ( $terms as $term ) {
							echo '<option value="' . esc_attr( $term->slug ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $term->name ) . '</option>';
						}
					}
					?>
				</select>
			</p>
		</div>

		<?php
	}

	/**
	 * Add the default terms for WC taxonomies - product types and order statuses. Modify this at your own risk.
	 */
	public function create_terms() {
		$terms = array(
			'global',
			'product',
			'custom',
			'default'
		);

		foreach ( $terms as $term ) {
			if ( ! get_term_by( 'name', $term, self::TAXONOMY_TAB_TYPE ) ) { // @codingStandardsIgnoreLine.
				wp_insert_term( $term, self::TAXONOMY_TAB_TYPE );
			}
		}

	}

	/**
	 * Add the default terms for WC taxonomies - product types and order statuses. Modify this at your own risk.
	 */
	public function create_post_type() {
		$posts = array(
			'description' 				=>	esc_html__('Description', 'razzi'),
			'additional_information' 	=>	esc_html__('Additional Information', 'razzi'),
			'reviews' 					=>	esc_html__('Reviews', 'razzi'),
		);
		$term = get_term_by('name', 'default', self::TAXONOMY_TAB_TYPE);
		if( is_wp_error( $term ) || empty( $term ) ) {
			return;
		}

		$args = array(
			'post_type'    => self::POST_TYPE,
			'post_content'  => '',
			'post_status'  => 'publish',
			'tax_input'    => array(
				self::TAXONOMY_TAB_TYPE => $term->term_id
			),
		);

		foreach ( $posts as $value => $title ) {
			if (! get_page_by_path($value, 'OBJECT', self::POST_TYPE ) ){
				$args['post_title'] = esc_html($title);
				$args['post_name'] = $value;
				wp_insert_post($args);
			}
		}

	}

	/**
	 * Save meta box content.
     *
	 * @since 1.0.0
	 *
	 * @param int $post_id
	 * @param object $post
     *
	 * @return void
	 */
	public function save_post( $post_id, $post ) {
		// If not the flex post.
		if ( self::POST_TYPE != $post->post_type ) {
			return;
		}

		// Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
		}

		// Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
		}

		$this->clear_product_tabs_cache();

		if ( isset($_POST['_product_tab_disable']) ) {
			update_post_meta( $post_id, '_product_tab_disable', 'yes' );
		} else {
			update_post_meta( $post_id, '_product_tab_disable', 'no' );
		}

		if ( isset($_POST['_product_tab_type']) ) {
			$tab_type = $_POST['_product_tab_type'];
			$term = get_term_by('name', $tab_type, self::TAXONOMY_TAB_TYPE);
			wp_set_post_terms( $post_id, $term->term_id, self::TAXONOMY_TAB_TYPE );

			$cat_ids = [];
			$product_ids = [];
			if( $tab_type == 'global' ) {
				if ( isset($_POST['_product_tab_cat_slugs']) ) {
					$cat_slugs = $_POST['_product_tab_cat_slugs'];
					foreach( $cat_slugs as $value => $slug ) {
						$term = get_term_by('slug', $slug, 'product_cat');
						if( ! is_wp_error( $term ) && $term ) {
							$cat_ids[] = $term->term_id;
						}
					}

				}
			}
			elseif( $tab_type == 'product' ) {
				if ( isset($_POST['_product_tab_product_ids']) ) {
					$product_ids = $_POST['_product_tab_product_ids'];
				}
			}

			wp_set_post_terms( $post_id, $cat_ids, 'product_cat');
			update_post_meta( $post_id, '_product_tab_product_ids', $product_ids );
		}

	}

	/**
	 * Get all WooCommerce screen ids.
	 *
	 * @return array
	 */
	public static function wc_screen_ids($screen_ids) {
		$screen_ids[] = 'razzi_product_tab';

		return $screen_ids;
	}

	/**
	 * Get all WooCommerce screen ids.
	 *
	 * @return array
	 */
	public static function edit_custom_box($column_name, $post_type) {
		if( $post_type != self::POST_TYPE ) {
			return;
		}

		if( $column_name != 'product_tab_disable' ) {
			return;
		}
		?>
		<fieldset class="inline-edit-col-left">
			<div class="inline-edit-col">
				<label>
					<span class="title"><?php esc_html_e('Disable', 'razzi'); ?></span>
					<span class="input-text-wrap"><input type="checkbox" name="_product_tab_disable" class="inline-edit-checkbox" value=""></span>
				</label>
			</div>
		</fieldset>
		<?php

	}

	/**
	 * Ajax request handling for product ordering.
	 *
	 * Based on Simple Page Ordering by 10up (https://wordpress.org/plugins/simple-page-ordering/).
	 */
	public static function product_tab_ordering() {
		global $wpdb;

		if ( empty( $_POST['id'] ) ) {
			wp_die( -1 );
		}

		$sorting_id  = absint( $_POST['id'] );
		$previd      = absint( isset( $_POST['previd'] ) ? $_POST['previd'] : 0 );
		$nextid      = absint( isset( $_POST['nextid'] ) ? $_POST['nextid'] : 0 );
		$menu_orders = wp_list_pluck( $wpdb->get_results( "SELECT ID, menu_order FROM {$wpdb->posts} WHERE post_type = 'razzi_product_tab' ORDER BY menu_order DESC" ), 'menu_order', 'ID' );
		$index       = count( $menu_orders ) + 1;

		foreach ( $menu_orders as $id => $menu_order ) {
			$id = absint( $id );

			if ( $sorting_id === $id ) {
				continue;
			}
			if ( $nextid === $id ) {
				$index --;
			}
			$index --;
			$menu_orders[ $id ] = $index;
			$wpdb->update( $wpdb->posts, array( 'menu_order' => $index ), array( 'ID' => $id ) );

			/**
			 * When a single product has gotten it's ordering updated.
			 * $id The product ID
			 * $index The new menu order
			*/
			do_action( 'razzi_after_single_product_tab_ordering', $id, $index );
		}

		if ( isset( $menu_orders[ $previd ] ) ) {
			$menu_orders[ $sorting_id ] = $menu_orders[ $previd ] - 1;
		} elseif ( isset( $menu_orders[ $nextid ] ) ) {
			$menu_orders[ $sorting_id ] = $menu_orders[ $nextid ] + 1;
		} else {
			$menu_orders[ $sorting_id ] = 0;
		}


		$wpdb->update( $wpdb->posts, array( 'menu_order' => $menu_orders[ $sorting_id ] ), array( 'ID' => $sorting_id ) );

		do_action( 'razzi_after_product_tab_ordering', $sorting_id, $menu_orders );
		wp_send_json( $menu_orders );
	}

	/**
	 * Orderby product tabs
	 */
	function product_tab_column_orderby( $query ) {
		if( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if( $query->get('post_type') != self::POST_TYPE ) {
			return;
		}
		$query->set( 'orderby', 'menu_order' );
		$query->set( 'order', 'DESC' );
	}


	/**
	 * Redirect to product tabs manager
	 */
	function product_tab_redirect() {
		global $pagenow;
		if($pagenow == 'post.php' && isset($_GET['post']) && get_post_type( $_GET['post'] ) == 'razzi_product_tab'){
			$terms = get_the_terms( $_GET['post'], self::TAXONOMY_TAB_TYPE );
			$tab_type = ! is_wp_error( $terms ) && $terms ? $terms[0]->name : '';
			if( $tab_type == 'default' ) {
				wp_redirect(admin_url('/edit.php?post_type=razzi_product_tab' ));
				exit;
			}

		}
	}

	/**
	 * Clear product tabs ids
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function clear_product_tabs_cache() {
		delete_transient( 'razzi_wc_product_tabs' );
	}

	/**
	 * Load scripts and style in admin area
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function admin_scripts( $hook ) {
		$screen = get_current_screen();

		if ( in_array( $hook, array('edit.php', 'post-new.php', 'post.php' ) ) && self::POST_TYPE == $screen->post_type ) {
			wp_enqueue_style( 'razzi-product-tabs', RAZZI_ADDONS_URL . 'modules/product-tabs/assets/product-tabs-admin.css' );
			wp_enqueue_script( 'razzi-product-tabs', RAZZI_ADDONS_URL . 'modules/product-tabs/assets/product-tabs-admin.js', array( 'jquery', 'jquery-ui-sortable' ),'1.0', true );

		}

	}

}