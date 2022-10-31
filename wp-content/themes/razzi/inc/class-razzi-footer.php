<?php
/**
 * Footer functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Footer initial
 *
 */
class Footer {
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
		add_action( 'razzi_after_open_site_footer', array( $this, 'show_footer' ) );
		add_action( 'razzi_after_close_site_footer', array( $this, 'gotop_button' ) );
		add_action( 'wp_footer', array( $this, 'base_url' ), 100 );
	}

	/**
	 * Site footer
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function show_footer() {
		$show_footer = is_page() ? ! get_post_meta( \Razzi\Helper::get_post_ID(), 'rz_hide_footer_section', true ) : true;
		if ( ! apply_filters( 'razzi_get_footer', $show_footer ) ) {
			return;
		}

		$sections = apply_filters( 'razzi_get_footer_sections', Helper::get_option( 'footer_sections' ) );

		if ( empty( $sections ) ) {
			return;
		}

		foreach ( (array) $sections as $section ) {
			get_template_part( 'template-parts/footer/footer', $section );
		}
	}

	/**
	 * Display footer extra class
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function classes( $classes ) {
		$classes .= ' site-footer-' . Helper::get_option('footer_background_scheme');

		$footer_border = intval( Helper::get_option('footer_section_border_top'));
		$footer_border_custom = get_post_meta( \Razzi\Helper::get_post_ID(), 'rz_footer_section_border_top', true );
		if( is_page() && $footer_border_custom != 'default' ) {
			$footer_border = $footer_border_custom;
		}

		if ( $footer_border ) {
			$classes .= ' has-divider';
		}

		echo apply_filters( 'razzi_site_footer_class', $classes);
	}

	/**
	 * Options of footer items
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function footer_items_option() {
		return apply_filters( 'razzi_footer_items_option', array(
			'copyright' => esc_html__( 'Copyright', 'razzi' ),
			'menu'      => esc_html__( 'Menu', 'razzi' ),
			'text'      => esc_html__( 'Custom text', 'razzi' ),
			'payment'   => esc_html__( 'Payments', 'razzi' ),
			'social'    => esc_html__( 'Socials', 'razzi' ),
			'logo'      => esc_html__( 'Logo', 'razzi' ),
		) );
	}

	/**
	 * Custom template tags of footer
	 *
	 * @since 1.0.0
	 *
	 * @return  void
	 */
	public function footer_item( $item ) {
		switch ( $item ) {
			case 'copyright':
				echo '<div class="copyright">' . do_shortcode( wp_kses_post( Helper::get_option( 'footer_copyright' ) ) ) . '</div>';
				break;

			case 'menu':
				$menu_slug = Helper::get_option( 'footer_menu' );
				if( ! empty($menu_slug) ) {
					wp_nav_menu( array(
						'theme_location' => '__no_such_location',
						'menu'           => Helper::get_option( 'footer_menu' ),
						'container'      => 'nav',
						'menu_id'        => 'footer-menu',
						'menu_class'     => 'footer-menu nav-menu menu',
						'depth'          => 1,
					) );
				}

				break;

			case 'text':
				if ( $footer_custom_text = Helper::get_option( 'footer_main_text' ) ) {
					echo '<div class="custom-text">' . do_shortcode( wp_kses_post( $footer_custom_text ) ) . '</div>';
				}
				break;

			case 'payment':
				$this->footer_payments();
				break;

			case 'social':
				\Razzi\Helper::socials_menu();
				break;

			case 'logo':
				$this->footer_logo();
				break;

			default:
				do_action( 'razzi_footer_footer_main_item', $item );
				break;
		}
	}

	/**
	 * Display logo  in footer
	 *
	 * @since 1.0.0
	 *
	 * @return  void
	 */
	public function footer_logo() {
		$logo_type = Helper::get_option( 'footer_logo_type' );

		$style = $class = '';

		if ( 'svg' == $logo_type ) :
			$logo =  Helper::get_option( 'footer_logo_svg' );

        elseif ( 'text' == $logo_type ) :
			$logo  = Helper::get_option( 'footer_logo_text' );
		else:
			$logo = Helper::get_option( 'footer_logo' );

			if ( ! $logo ) {
				$logo = $logo ? $logo : get_theme_file_uri( '/images/logo.svg' );
			}

			$dimension = Helper::get_option( 'footer_logo_dimension' );
			$style     = ! empty( $dimension['width'] ) ? ' width="' . esc_attr( $dimension['width'] ) . '"' : '';
			$style     .= ! empty( $dimension['width'] ) ? ' height="' . esc_attr( $dimension['height'] ) . '"' : '';
		endif;

		?>
        <div class="footer-branding">
            <a href="<?php echo esc_url( home_url( '/' ) ) ?>" class="logo <?php echo esc_attr( $class ) ?>">
				<?php if ( 'svg' == $logo_type ) : ?>
					<?php echo \Razzi\Icon::sanitize_svg( $logo ); ?>
				<?php elseif ( 'text' == $logo_type ) : ?>
					<?php echo esc_html( $logo ); ?>
				<?php else : ?>
                    <img src="<?php echo esc_url( $logo ); ?>"
                         alt="<?php echo get_bloginfo( 'name' ); ?>" <?php echo wp_kses_post( $style ) ?>>
				<?php endif; ?>
            </a>
        </div>

		<?php
	}

	/**
	 * Display payment  in footer
	 *
	 * @since 1.0.0
	 *
	 * @return  void
	 */
	public function footer_payments() {
		$output = array();

		$images = (array) Helper::get_option( 'footer_main_payment_images' );
		if ( $images ) {

			$output[] = '<ul class="payments">';
			foreach ( $images as $image ) {

				if ( ! isset( $image['image'] ) && ! $image['image'] ) {
					continue;
				}

				$image_id = $image['image'];

				$img = wp_get_attachment_image( $image_id, 'full' );
				if ( isset( $image['link'] ) && ! empty( $image['link'] ) ) {
					if ( $img ) {
						$output[] = sprintf( '<li><a href="%s">%s</a></li>', esc_url( $image['link'] ), $img );
					}
				} else {
					if ( $img ) {
						$output[] = sprintf( '<li>%s</li>', $img );
					}
				}

			}
			$output[] = '</ul>';
		}

		if ( $output ) {
			printf( '<div class="footer-payments">%s</div>', implode( ' ', $output ) );
		}
	}

	/**
	 * Add this back-to-top button to footer
	 *
	 * @since 1.0.0
	 *
	 * @return  void
	 */
	public function gotop_button() {
		if ( apply_filters( 'razzi_get_back_to_top', Helper::get_option( 'general_backtotop' ) ) ) {
			echo '<a href="#page" id="gotop">' . \Razzi\Icon::get_svg( 'arrow-right' ) . '</a>';
		}
	}

	/**
	 * Add base url to footer
	 *
	 * @since 1.0.0
	 *
	 * @return  void
	 */
	public function base_url() {
		echo '<input type="hidden" id="rz-base-url" data-url="' . esc_url( home_url( '/' ) ) . '">';
	}

}
