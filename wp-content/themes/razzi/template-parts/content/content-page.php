<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Razzi
 */

?>

<?php
\Razzi\Markup::instance()->open('page_content',[
	'tag' => 'article',
	'attr' => [
		'id'    => 'post-' . get_the_ID(),
		'class' => join( ' ', get_post_class( '', get_the_ID() ) ) ,
	],
	'actions' => 'before',
]);
?>

<?php the_content(); ?>

<?php \Razzi\Markup::instance()->close('page_content');  ?>
