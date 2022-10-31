<?php
/**
 * Plugin Name: Extended Rest API Endpoint | ShiakaApp
 * Plugin URI: http://Shiaka.com
 * Description: It was built for ShiakaApp.
 * Version: 1.1.8
 * Author: Alshiaka
 * Author URI: http://Shiaka.com/
 * License: GPL2+
 * Text Domain: Shiaka
 * Domain Path: /lang/
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

function test_endpoint() {
	register_rest_field( 'post', 'Abozeid', array(
		'get_callback' => function(){ return 'Amazing first api by abozeid';}
	));


echo	$currentLanguage = get_bloginfo('language');
return $hash  = md5('ahmed.fci22@gmail.com' . time() . wp_rand() );


	$to 			= 'ahmed.fci22@gmail.com';
	$subject  = 'The subject';
	$body 		= 'The email body content';
	$headers  = array('Content-Type: text/html; charset=UTF-8');

	wp_mail($to, $subject, $body, $headers);


	/*	$content = apply_filters( 'new_user_email_content', $email_text, $new_user_email );

        $content = str_replace( '###USERNAME###', $current_user->user_login, $content );
        $content = str_replace( '###ADMIN_URL###', esc_url( admin_url( 'profile.php?newuseremail=' . $hash ) ), $content );
        $content = str_replace( '###EMAIL###', $_POST['email'], $content );
        $content = str_replace( '###SITENAME###', $sitename, $content );
        $content = str_replace( '###SITEURL###', home_url(), $content );

        /* translators: New email address notification email subject. %s: Site title. */
    /*    wp_mail( ahmed.fci22@gmail.com, sprintf( __( '[%s] Email Change Request' ), $sitename ), $content );

        $_POST['email'] = $current_user->user_email;*/
}

add_action('rest_api_init',function(){

  register_rest_route('wc/v2/','test',[
    'method'=>'GET',
    'callback'=>'test_endpoint',
  ]);
});





//::::::::::::::::::::::::::: for rating avg ::::::::::::::::::::;;

/*
function get_star_rating() {
	$args_top_rating1 = array(
	    'post_type' => 'product',
	    'meta_key' => '_wc_average_rating',
	    'orderby' => 'meta_value',
	    'posts_per_page' => 8,
	    'status'=>'publish',
	    'catalog_visibility'=>'visible',
	    'stock_status'=>'instock'
	);

	$top_rating = new WP_Query( $args_top_rating1 );


	 while ( $top_rating->have_posts() ) : $top_rating->the_post();
	 global $product;

  $urltop_rating = get_permalink($top_rating->post->ID) ;

 	$rating_count = $product->get_rating_count();

 	$average_rating = $product->get_average_rating();

	 echo wc_get_rating_html( $average_rating, $rating_count);


	endwhile;



	// Get an instance of the WC_Product Object (from a product ID)
	$product = wc_get_product('41611');

	// The product average rating (or how many stars this product has)
	$average_rating = $product->get_average_rating();

	// The product stars average rating html formatted.
	$average_rating_html = wc_get_rating_html($average_rating);

	// Display stars average rating html
	echo $average_rating_html;



}

add_action('rest_api_init',function(){

  register_rest_route('wc/v2/','rating',[
    'method'=>'GET',
    'callback'=>'get_star_rating',
  ]);
});

*/
//::::::::::::::::::::::::::::::::::::::::::::::::::::

/*
add_action('rest_api_init',function(){

  register_rest_route('wc/v2/','product_rating',[
    'method'=>'GET',
    'callback'=>'display_the_product_rating',
  ]);
});


 function display_the_product_rating( $atts ) {
    // Shortcode attributes
    $atts = shortcode_atts( array(
        'id' => '',
    ), $atts, 'product_rating' );

    if ( isset($atts['id']) && $atts['id'] > 0 ):

    // Get an instance of the WC_Product Object
    $product = wc_get_product( $atts['id'] );

    // The product average rating (or how many stars this product has)
    $average = $product->get_average_rating();

    // HERE the average width
    $average_width = $average * 16 . 'px';

    endif;

    if ( isset($average) ) :

    return '<div class="starwrapper" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        <span class="star-rating" title="'.sprintf(__('Rated %s out of 5', 'woocommerce'), $average).'">
            <span style="width:'.$average_width.'">
                <span itemprop="ratingValue" class="rating">'.$average.'</span>
            </span>
        </span>
    </div><br clear="all">';

    endif;
}

*/
// ::::::::::: to get all subscripers :::::::::::::::::::::

add_action( 'rest_api_init', 'custom_api_endpoints' );
function custom_api_endpoints () {
  register_rest_route( 'wc/v2', 'get_subcription', array(
    'methods'  => 'POST',
    'callback' => 'custom_subscription_endpoint_handler'
  ) );
}
function custom_subscription_endpoint_handler () {
    return $woocommerce->get('subscriptions');
}

?>
