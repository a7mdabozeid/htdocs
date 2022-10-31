<?php



// Cart items in checkout

add_filter( 'woocommerce_cart_item_subtotal', 'show_coupon_item_subtotal_discount', 100, 3 );
add_filter( 'woocommerce_cart_item_price', 'show_coupon_item_subtotal_discount', 10, 3 );

function show_coupon_item_subtotal_discount( $subtotal, $cart_item, $cart_item_key ){
	
	//Check if sale price is not empty

	//Get product object
    $_product = $cart_item['data'];
	$line_subtotal_tax  = $cart_item['line_subtotal_tax'];
//     if( $cart_item['line_subtotal'] !== $cart_item['line_total'] ) {
// 	       $line_tax           = $cart_item['line_tax'];
// 		$regular_price = $_product->get_regular_price()* $cart_item['quantity'];
      
// 		$discountAmt=wc_price(($regular_price-$cart_item['line_subtotal']-$line_tax) + ($cart_item['line_subtotal']- $cart_item['line_total']));
 
//         $subtotal = sprintf( '<del>%s</del> <ins>%s</ins><p style="color:green;font-size:14px;"><span style="color:#ed8174;"><i class="fa fa-tags" aria-hidden="true"></i> '.__('Coupon applied', 'shiaka').'</span><br>'.__('You Saved', 'shiaka').': %s</p>',  wc_price($regular_price), wc_price($cart_item['line_total'] + $line_tax),$discountAmt );
//     }else{
		
//     		if( '' !== $_product->get_sale_price() ) {
             
//     		 $regular_price = $_product->get_regular_price() * $cart_item['quantity']; 
//     			$sale_price = $_product->get_sale_price() * $cart_item['quantity'];
//           $discountAmt=wc_price($regular_price-$sale_price);
    	
//      $subtotal = sprintf( '<del>%s</del> <ins style="background-color: none;">%s</ins><p style="color:green;font-size:14px;">'.__('You Saved', 'shiaka').': %s</p>',  wc_price($regular_price), wc_price($sale_price),$discountAmt );
//         }
        
        
// 	}
	
	if( $cart_item[ 'data' ]->get_regular_price() != $cart_item[ 'data' ]->get_price()  ) {
	    $discountAmt = ($cart_item[ 'data' ]->get_regular_price() - $cart_item[ 'data' ]->get_price()) * $cart_item['quantity'];
        $subtotal = sprintf( '<del>%s</del> <ins style="background-color: none;">%s</ins><p style="color:green;font-size:14px;">'.__('You Saved', 'shiaka').': %s</p>',  wc_price($cart_item[ 'data' ]->get_regular_price() * $cart_item['quantity']), wc_price($_product->get_price() * $cart_item['quantity']), $discountAmt );

	} else {
        $subtotal = $cart_item[ 'data' ]->get_price() * $cart_item['quantity'];
	    
	    $subtotal = wc_price($subtotal);
	}
    return $subtotal;
}
 



add_filter( 'woocommerce_cart_subtotal', 'wpq_9522_woocommerce_cart_subtotal', 99, 3 );
/**
* Show the cart subtotal before (striked out) and after coupon discount
* @uses wpq_9522_discount()
* @link http://www.wpquestions.com/question/showChronoLoggedIn/id/9522
*/
function wpq_9522_woocommerce_cart_subtotal( $cart_subtotal, $compound, $obj ){

    $t = 0;
    
    if(empty( WC()->cart->get_coupons() )) {
        return $cart_subtotal;
    }
            $quantities = WC()->cart->get_cart_item_quantities();

    //     echo '<pre style="display:none;text-align: left;
    // position: relative;
    // left: -900px;">';      
    // print_r($quantities);
    // echo '</pre>';
    
    foreach ( $obj->cart_contents as $key => $product ) : 
        $product_p = wc_get_product( $product['product_id'] );
          $product_price = $product['data']->get_regular_price() ;

    


        $t += $product_price * $quantities[$product['data']->get_id()];
    endforeach;
    
    $cart_total_num = str_replace('-', '', filter_var($cart_subtotal, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));

 
    
    
    if( $t >  $cart_total_num) {
        return sprintf( '<s> %s </s> %s', wc_price( $t ), $cart_subtotal ) ;

    }
    
    return $cart_subtotal;

}

