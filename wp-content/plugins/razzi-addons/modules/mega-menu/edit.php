<?php
/**
 * Customize and add more fields for mega menu
 */

namespace Razzi\Addons\Modules\Mega_Menu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Edit {
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
     *
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
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_footer-nav-menus.php', array( $this, 'modal' ) );
		add_action( 'admin_footer-nav-menus.php', array( $this, 'templates' ) );
		add_action( 'wp_ajax_tamm_save_menu_item_data', array( $this, 'save_menu_item_data' ) );
	}

	/**
	 * Load scripts on Menus page only
	 *
	 * @param string $hook
     *
     * @since 1.0.0
     *
	 * @return void
	 */
	public function scripts( $hook ) {
		if ( 'nav-menus.php' !== $hook ) {
			return;
		}

		wp_register_style( 'razzi-mega-menu', RAZZI_ADDONS_URL . 'modules/mega-menu/assets/mega-menu.css', array(
			'media-views',
			'wp-color-picker',
		), '20160530' );
		wp_enqueue_style( 'razzi-mega-menu' );

		wp_register_script( 'razzi-mega-menu', RAZZI_ADDONS_URL . 'modules/mega-menu/assets/mega-menu.js', array(
			'jquery',
			'jquery-ui-resizable',
			'wp-util',
			'backbone',
			'underscore',
			'wp-color-picker'
		), '20160530', true );
		wp_enqueue_media();
		wp_enqueue_script( 'razzi-mega-menu' );
	}

	/**
	 * Prints HTML of modal on footer
	 *
	 * @since 1.0.0
     *
     * @return void
	 */
	public function modal() {
		?>
        <div id="tamm-settings" tabindex="0" class="tamm-settings">
            <div class="tamm-modal media-modal wp-core-ui">
                <button type="button" class="button-link media-modal-close tamm-modal-close">
                    <span class="media-modal-icon"><span
                                class="screen-reader-text"><?php esc_html_e( 'Close', 'razzi' ) ?></span></span>
                </button>
                <div class="media-modal-content">
                    <div class="tamm-frame-menu media-frame-menu">
                        <div class="tamm-menu media-menu"></div>
                    </div>
                    <div class="tamm-frame-title media-frame-title"></div>
                    <div class="tamm-frame-content media-frame-content">
                        <div class="tamm-content">
                            <!--							<span class="spinner"></span>-->
                        </div>
                    </div>
                    <div class="tamm-frame-toolbar media-frame-toolbar">
                        <div class="tamm-toolbar media-toolbar">
                            <div class="tamm-toolbar-primary media-toolbar-primary search-form">
                                <button type="button"
                                        class="button tamm-button tamm-button-save media-button button-primary button-large"><?php esc_html_e( 'Save Changes', 'razzi' ) ?></button>
                                <button type="button"
                                        class="button tamm-button tamm-button-cancel media-button button-secondary button-large"><?php esc_html_e( 'Cancel', 'razzi' ) ?></button>
                                <span class="spinner"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="media-modal-backdrop tamm-modal-backdrop"></div>
        </div>
		<?php
	}

	/**
	 * Prints underscore template on footer
	 *
	 * @since 1.0.0
	 */
	public function templates() {
		$templates = apply_filters(
			'tamm_js_templates', array(
				'menus',
				'title',
				'mega',
				'background',
				'icon',
				'content',
				'general',
				'general_2'
			)
		);

		foreach ( $templates as $template ) {
			$file = apply_filters( 'tamm_js_template_path', RAZZI_ADDONS_DIR . 'modules/mega-menu/tmpl/' . $template . '.php', $template );
			?>
            <script type="text/template" id="tmpl-tamm-<?php echo esc_attr( $template ) ?>">
				<?php
				if ( file_exists( $file ) ) {
					include $file;
				}
				?>
            </script>
			<?php
		}
	}

	/**
	 * Ajax function to save menu item data
	 *
	 * @since 1.0.0
	 */
	public function save_menu_item_data() {
		$_POST['data'] = stripslashes_deep( $_POST['data'] );
		parse_str( $_POST['data'], $data );

		$i = 0;
		// Save menu item data
		foreach ( $data['menu-item'] as $id => $meta ) {

			// Update meta value for checkboxes
			$keys = array_keys( $meta );

			if ( $i == 0 ) {
				if ( in_array( 'mega', $keys ) ) {
					update_post_meta( $id, 'tamm_menu_item_mega', true );
				} else {
					delete_post_meta( $id, 'tamm_menu_item_mega' );
				}

				if ( in_array( 'hideText', $keys ) ) {
					update_post_meta( $id, 'tamm_menu_item_hide_text', true );
				} else {
					delete_post_meta( $id, 'tamm_menu_item_hide_text' );
				}

				if ( in_array( 'isLabel', $keys ) ) {
					update_post_meta( $id, 'tamm_menu_item_is_label', true );
				} else {
					delete_post_meta( $id, 'tamm_menu_item_is_label' );
				}
			}

			foreach ( $meta as $key => $value ) {
				$key = str_replace( '-', '_', $key );
				if( $key === 'tamm_menu_item_icon_svg' ) {
				    $value = \Razzi\Addons\Helper::sanitize_svg($value);
                }
				update_post_meta( $id, 'tamm_menu_item_' . $key, $value );
			}

			$i ++;
		}

		do_action( 'razzi_save_menu_item_data', $data );

		wp_send_json_success( $data );
	}
}
