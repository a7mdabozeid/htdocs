<?php

namespace Razzi\Addons\Modules\Product_Tabs;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Product_Meta  {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Product Tab Ids
	 *
	 * @var $instance
	 */
	private $product_tab_ids;


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
	const TAXONOMY_TYPE   = 'razzi_product_tab_type';

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->product_tab_ids = $this->get_product_tabs();

		add_filter( 'woocommerce_product_data_tabs', array( $this, 'product_data_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'product_data_panel' ) );

		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
	}

	/**
	 * Add new product data tab for size guide
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function product_data_tab( $tabs ) {
		if( empty( $this->product_tab_ids ) ) {
			return $tabs ;
		}

		$tabs['razzi_product_tabs'] = array(
			'label'    => esc_html__( 'Product Tabs', 'razzi' ),
			'target'   => 'razzi-product-tabs',
			'class'    => array( 'razzi-product-tabs', ),
			'priority' => 62,
		);

		return $tabs;
	}

	/**
	 * Outputs the size guide panel
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_data_panel() {
		if( empty( $this->product_tab_ids ) ) {
			return;
		}
		global $post;
		?>
		<div id="razzi-product-tabs" class="panel wc-metaboxes-wrapper woocommerce_options_panel">
			<div class="wc-metaboxes">
				<?php foreach($this->product_tab_ids as $tab_id) : ?>
				<?php
					$tab_disable = get_post_meta( $post->ID, '_product_tab_disable_' . $tab_id, true );
					$tab_title = get_the_title($tab_id);
					$tab_title = $tab_disable ? $tab_title . '(' . esc_html__( 'Disabled', 'razzi' ) . ')' : $tab_title;
					$tab_class = $tab_disable ? 'tab-disabled' : '';
				?>
				<div class="wc-metabox postbox">
					<h3 class="<?php echo esc_attr($tab_class); ?>" style="border-bottom: 1px solid #ccc">
						<div class="handlediv" title="<?php esc_html_e('Click to toggle', 'razzi'); ?>"></div>
						<strong class="attribute_name"><?php echo $tab_title; ?></strong>
					</h3>
					<div class="wc-metabox-content" style="padding: 20px">
						<?php
						$tab_disable = get_post_meta( $post->ID, '_product_tab_disable_' . $tab_id, true );
						woocommerce_wp_checkbox(
							array(
								'id'          => '_product_tab_disable_' . $tab_id,
								'label'       => esc_html__('Tab Disable', 'razzi' ),
								'value'       => wc_bool_to_string( $tab_disable ),
							)
						);?>
						<fieldset class="form-field">
							<label><?php esc_html_e( 'Tab Content', 'razzi' ); ?></label>
							<?php
							$tab_content = get_post_meta( $post->ID, '_product_tab_content_' . $tab_id, true );
							wp_editor( $tab_content, '_product_tab_content_' . $tab_id );
							?>
						</fieldset>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Outputs the size guide panel
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_product_tabs() {
		if( ! empty( $this->product_tab_ids ) ) {
			return $this->product_tab_ids;
		}

		$products = new \WP_Query( array(
			'post_type'      => self::POST_TYPE,
			'posts_per_page' => '-1',
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'orderby' 		=> 'menu_order',
			'order' 		=> 'DESC',
			'suppress_filters'       => false,
			'tax_query' => array(
				array(
					'taxonomy' => self::TAXONOMY_TYPE,
					'field'    => 'name',
					'terms'    => 'custom',
				),
			),
		) );
		$post_ids = 0;
		if ( $products->have_posts() ) {
			$post_ids = $products->posts;
		}
		wp_reset_postdata();
		return $post_ids;
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
		//If not the flex post.
		if ( 'product' != $post->post_type ) {
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

		if(  $this->product_tab_ids ) {
			foreach( $this->product_tab_ids as $tab_id ) {
				if ( isset($_POST['_product_tab_content_' . $tab_id] ) && ! empty( $_POST['_product_tab_content_' . $tab_id] ) ) {
					update_post_meta( $post_id, '_product_tab_content_' . $tab_id, $_POST['_product_tab_content_' . $tab_id] );
				}

				if ( isset($_POST['_product_tab_disable_' . $tab_id] )  ) {
					update_post_meta( $post_id, '_product_tab_disable_' . $tab_id, 1);
				} else {
					update_post_meta( $post_id, '_product_tab_disable_' . $tab_id, 0);
				}
			}
		}

	}

}