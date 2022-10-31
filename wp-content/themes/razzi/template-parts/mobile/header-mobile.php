<?php
/**
 * Template file for displaying mobile header v1
 *
 * @package razzi
 */

?>

<?php
\Razzi\Markup::instance()->open('header_mobile',[
	'attr' => [
		'class' => 'header-mobile',
	],
	'actions' => false,
]);
?>

<?php do_action( 'razzi_header_mobile_content' ); ?>

<?php \Razzi\Markup::instance()->close('header_mobile');  ?>
