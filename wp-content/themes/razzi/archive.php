<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Razzi
 */

get_header();
?>
<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) { ?>
	<?php
	\Razzi\Markup::instance()->open('primary_content',[
		'attr' => [
			'id'    => 'primary',
			'class' => 'content-area',
		],
		'actions' => false,
	]);
	?>
	<?php if ( have_posts() ) : ?>
		<?php
		\Razzi\Markup::instance()->open('posts_content',[
			'attr' => [
				'class' => 'razzi-posts__wrapper',
			],
			'actions' => true,
		]);
		?>
		<?php
		/* Start the Loop */
		while ( have_posts() ) :
			the_post();

			/*
				* Include the Post-Type-specific template for the content.
				* If you want to override this in a child theme, then include a file
				* called content-___.php (where ___ is the Post Type name) and that will be used instead.
				*/
			get_template_part( 'template-parts/content/content', get_post_type() );

		endwhile;?>
	<?php
	else :
		get_template_part( 'template-parts/content/content', 'none' );
	endif;
	?>

	<?php \Razzi\Markup::instance()->close('posts_content');  ?>

	<?php \Razzi\Markup::instance()->close('primary_content');  ?>

	<?php get_sidebar(); ?>
<?php } ?>
<?php get_footer(); ?>