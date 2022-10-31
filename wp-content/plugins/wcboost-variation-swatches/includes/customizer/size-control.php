<?php
namespace WCBoost\VariationSwatches\Customize;

defined( 'ABSPATH' ) || exit;

class Size_Control extends \WP_Customize_Control {
	/**
	 * Declare the control type.
	 *
	 * @var string
	 */
	public $type = 'wcboost-variation-swatches-size';

	/**
	 * Render control.
	 */
	public function render_content() {
		$size = $this->value();
		$size = wp_parse_args( $size, $this->setting->default );
		?>

		<?php if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif; ?>

		<?php if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif; ?>

		<span class="customize-control-inside">
			<input type="text" size="3" pattern="\d*" value="<?php echo esc_attr( $size['width'] ) ?>" data-name="width"/>
			x
			<input type="text" size="3" pattern="\d*" value="<?php echo esc_attr( $size['height'] ) ?>" data-name="height"/>
		</span>
		<?php
	}
}
