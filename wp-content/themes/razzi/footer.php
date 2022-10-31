<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Razzi
 */

?>

<?php
\Razzi\Markup::instance()->close( 'site_content' );
?>

<?php do_action('razzi_before_open_site_footer'); ?>
<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {?>
	<footer id="site-footer" class="<?php \Razzi\Footer::classes('site-footer'); ?>">
		<?php do_action('razzi_after_open_site_footer'); ?>
		<?php do_action('razzi_before_close_site_footer'); ?>
	</footer>
<?php } ?>
<?php do_action('razzi_after_close_site_footer'); ?>

</div><!-- #page -->
<?php wp_footer(); ?>

</body>
</html>
