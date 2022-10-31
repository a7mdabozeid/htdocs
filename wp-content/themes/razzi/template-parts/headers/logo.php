<?php
/**
 * Template part for displaying the logo
 *
 * @package razzi
 */

use Razzi\Helper;

$pre =
$logo_type        = Helper::get_option( 'logo_type' );

$style            = $class = '';

if ( 'svg' == $logo_type ) :
	$logo = Helper::get_option( 'logo_svg' );
	$logo_light        = Helper::get_option( 'logo_svg_light' );
elseif ( 'text' == $logo_type ) :
	$logo = Helper::get_option( 'logo_text' );
	$class = 'logo-text';
else:
	$logo = Helper::get_option( 'logo' );
	$logo_light        = Helper::get_option( 'logo_light' );

	if ( ! $logo ) {
		$logo = $logo ? $logo : get_theme_file_uri( '/images/logo.svg' );
	}

	$dimension = Helper::get_option( 'logo_dimension' );
	$style     = ! empty( $dimension['width'] ) ? ' width="' . esc_attr( $dimension['width'] ) . '"' : '';
	$style     .= ! empty( $dimension['width'] ) ? ' height="' . esc_attr( $dimension['height'] ) . '"' : '';
endif;

$header_background = get_post_meta( \Razzi\Helper::get_post_ID(), 'rz_header_background', true );
$header_background_text = get_post_meta( \Razzi\Helper::get_post_ID(), 'rz_header_text_color', true );

?>
<div class="site-branding">
    <a href="<?php echo esc_url( home_url( '/' ) ) ?>" class="logo <?php echo esc_attr( $class ) ?>">
		<?php if ( 'svg' == $logo_type ) : ?>
            <span class="logo-dark"><?php echo apply_filters('razzi_get_theme_logo', \Razzi\Icon::sanitize_svg( $logo )); ?></span>
		<?php elseif ( 'text' == $logo_type ) : ?>
            <span class="logo-dark"><?php echo esc_html( $logo ); ?></span>
		<?php else : ?>
            <img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo get_bloginfo( 'name' ); ?>"
				 class="logo-dark" <?php echo wp_kses_post( $style ) ?>>
		<?php endif; ?>
		<?php if ( is_page() && $header_background == 'transparent' && $header_background_text == 'light' ) : ?>
			<?php if ( 'svg' == $logo_type ) : ?>
				<span class="logo-light"><?php echo apply_filters('razzi_get_theme_logo_light',\Razzi\Icon::sanitize_svg( $logo_light )); ?></span>
			<?php elseif ( 'text' == $logo_type ) : ?>
            	<span class="logo-light"><?php echo esc_html( $logo ); ?></span>
			<?php else : ?>
				<img src="<?php echo esc_url( $logo_light ); ?>" alt="<?php echo get_bloginfo( 'name' ); ?>"
					class="logo-light" <?php echo wp_kses_post( $style ) ?>>
			<?php endif; ?>
		<?php endif; ?>
    </a>

	<?php if ( is_front_page() && is_home() ) : ?>
        <h1 class="site-title">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
        </h1>
	<?php else : ?>
        <p class="site-title">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
        </p>
	<?php endif; ?>

	<?php if ( ( $description = get_bloginfo( 'description', 'display' ) ) || is_customize_preview() ) : ?>
        <p class="site-description"><?php echo wp_kses_post( $description ); /* WPCS: xss ok. */ ?></p>
	<?php endif; ?>
</div>