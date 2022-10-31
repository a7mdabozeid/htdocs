<?php
/**
 * Campaign functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Campaign initial
 *
 */
class Campaigns {
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
		if (  apply_filters( 'razzi_get_campaign_bar_position', Helper::get_option( 'campaign_bar_position' )) == 'after' ) {
			add_action( 'razzi_after_close_site_header', array( $this, 'display_campaign_bar' ), 10 );
		} else {
			add_action( 'razzi_before_open_site_header', array( $this, 'display_campaign_bar' ), 20 );
		}

	}

	/**
	 * Campaign bar
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function display_campaign_bar() {
		if ( ! apply_filters( 'razzi_get_campaign_bar', Helper::get_option( 'campaign_bar' ) ) ) {
			return;
		}

		$show_header = is_page() ? ! get_post_meta( \Razzi\Helper::get_post_ID(), 'rz_hide_header_section', true ) : true;
		if ( ! $show_header ) {
			return;
		}

		get_template_part( 'template-parts/headers/campaigns' );
	}

	/**
	 * Display campaign bar item.
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function campaign_item( $args ) {
		$args = wp_parse_args( $args, array(
			'text'    => '',
			'image'   => '',
			'bgcolor' => '',
			'color'   => 'dark',
			'height'  => '52',
		) );

		$css_class = array(
			'razzi-promotion',
		);

		$style = '';

		if ( ! empty( $args['bgcolor'] ) ) {
			$style .= 'background-color:' . $args['bgcolor'] . ';';
		}

		if ( ! empty( $args['image'] ) ) {
			$image = wp_get_attachment_image_url( $args['image'], 'full' );

			if ( $image ) {
				$style .= 'background-image: url("' . esc_url( $image ) . '");';
			}
		}

		if ( ! empty( $args['color'] ) ) {
			$style .= '--rz-color-dark:' . $args['color'] . ';';
		}

		?>
        <div class="<?php echo esc_attr( implode( ' ', $css_class ) ) ?>" style="<?php echo esc_attr( $style ) ?>">
			<?php echo wp_kses_post( $args['text'] ) ?>
        </div>
		<?php
	}
}
