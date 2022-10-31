<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Razzi
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}

?>

<?php
\Razzi\Markup::instance()->open( 'comments_content', [
	'attr'    => [
		'id'    => 'comments',
		'class' => 'comments-area',
	],
	'actions' => true,
] );
?>

<?php
// You can start editing here -- including this comment!
if ( have_comments() ) :
	?>
	<?php do_action( 'razzi_comments_content' ); ?>
<?php

endif; // Check for have_comments().
?>

<?php \Razzi\Markup::instance()->close( 'comments_content' ); ?>
