<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Razzi
 */

get_header();
?>
<?php if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('single')) { ?>
    <div id="primary" class="content-area">
        <section class="error-404 not-found">
			<?php echo \Razzi\Icon::get_svg( 'error', 'error-404__svg' ); ?>
            <h1 class="page-title"><?php esc_html_e( '404. Page not found.', 'razzi' ); ?></h1>
            <div class="page-content">
				<?php esc_html_e( 'Sorry, we couldn&rsquo;t find the page you where looking for. We suggest that you return to homepage.', 'razzi' ); ?>
            </div><!-- .page-content -->
            <a href="<?php echo esc_url( get_home_url() ); ?>"
               class="razzi-button button-larger"><?php echo esc_html__( 'Go to homepage', 'razzi' ); ?></a>
        </section><!-- .error-404 -->

    </div><!-- #primary -->
<?php } ?>
<?php
get_footer();
