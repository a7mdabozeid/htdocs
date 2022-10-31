<?php



// Cart items in checkout

add_filter( 'woocommerce_cart_item_subtotal', 'show_coupon_item_subtotal_discount', 100, 3 );
add_filter( 'woocommerce_cart_item_price', 'show_coupon_item_subtotal_discount', 10, 3 );

function show_coupon_item_subtotal_discount( $subtotal, $cart_item, $cart_item_key ){
	
	//Check if sale price is not empty

	//Get product object
    $_product = $cart_item['data'];
	$line_subtotal_tax  = $cart_item['line_subtotal_tax'];
    if( $cart_item['line_subtotal'] !== $cart_item['line_total'] ) {
	       $line_tax           = $cart_item['line_tax'];
		$regular_price = $_product->get_regular_price()* $cart_item['quantity'];
      
		$discountAmt=wc_price(($regular_price-$cart_item['line_subtotal']-$line_tax) + ($cart_item['line_subtotal']- $cart_item['line_total']));
 
        $subtotal = sprintf( '<del>%s</del> <ins>%s</ins><p style="color:green;font-size:14px;"><span style="color:#ed8174;"><i class="fa fa-tags" aria-hidden="true"></i> '.__('Coupon applied', 'shiaka').'</span><br>'.__('You Saved', 'shiaka').': %s</p>',  wc_price($regular_price), wc_price($cart_item['line_total'] + $line_tax),$discountAmt );
    }else{
		
    		if( '' !== $_product->get_sale_price() ) {
             
    		 $regular_price = $_product->get_regular_price() * $cart_item['quantity'];
    			$sale_price = $_product->get_sale_price() * $cart_item['quantity'];
          $discountAmt=wc_price($regular_price-$sale_price);
    	
     $subtotal = sprintf( '<del>%s</del> <ins style="background-color: none;">%s</ins><p style="color:green;font-size:14px;">'.__('You Saved', 'shiaka').': %s</p>',  wc_price($regular_price), wc_price($_product->get_sale_price()),$discountAmt );
        }
        
        
	}
	
	if( $cart_item[ 'data' ]->get_regular_price() != $cart_item[ 'data' ]->get_price()  ) {
	    $discountAmt = $cart_item[ 'data' ]->get_regular_price() - $cart_item[ 'data' ]->get_price();
        $subtotal = sprintf( '<del>%s</del> <ins style="background-color: none;">%s</ins><p style="color:green;font-size:14px;">'.__('You Saved', 'shiaka').': %s</p>',  wc_price($cart_item[ 'data' ]->get_regular_price()), wc_price($_product->get_price()), $discountAmt );

	}
    return $subtotal;
}
 

// No Use
/**
* Show the product price before (striked out) and after coupon discount
* @uses wpq_9522_discount()
* @link http://www.wpquestions.com/question/showChronoLoggedIn/id/9522
*/
// function wpq_9522_woocommerce_cart_item_subtotal( $subtotal, $cart_item, $cart_item_key ){
    
// foreach ( WC()->cart->get_coupons( 'order' ) as $code => $coupon ) :
// if( in_array( $cart_item['product_id'], $coupon->product_ids )
// || in_array( $coupon->discount_type, array( 'percent_cart', 'fixed_cart' ) ) ):
// $newsubtotal = wc_price( wpq_9522_discount( $cart_item['line_total'], $coupon->discount_type, $coupon->amount ) );


     
// return sprintf( '%s %s', $subtotal, $newsubtotal );
// endif;
// endforeach;

// return $subtotal;
// }

// add_filter( 'woocommerce_cart_item_subtotal', 'wpq_9522_woocommerce_cart_item_subtotal', 99, 3 );


// function wpq_9522_discount( $price, $type, $amount ){
//     switch( $type ){
//         case 'percent_product':
//             $newprice = $price * ( 1 - $amount/100 );
//         break;
//         case 'fixed_product':
//             $newprice = $price - $amount;
//         break;
//         case 'percent_cart':
//             $newprice = $price * ( 1 - $amount/100 );
//         break;
//         case 'fixed_cart':
//             $newprice = $price - $amount;
//         break;
//         default:
//         $newprice = $price;
//     }

//     return $newprice;
// }


add_filter( 'woocommerce_cart_subtotal', 'wpq_9522_woocommerce_cart_subtotal', 99, 3 );
/**
* Show the cart subtotal before (striked out) and after coupon discount
* @uses wpq_9522_discount()
* @link http://www.wpquestions.com/question/showChronoLoggedIn/id/9522
*/
function wpq_9522_woocommerce_cart_subtotal( $cart_subtotal, $compound, $obj ){

    $t = 0;
    $p=0;
    
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
        // $product_price = $product['line_total'] + $product['line_tax'];
          $product_price = $product['data']->get_regular_price() ;
    //     echo '<pre style="display:none;text-align: left;
    // position: relative;
    // left: -900px;">';
    //         print_r($product);
    //     echo '</pre>';
    
        // foreach ( WC()->cart->get_coupons( 'order' ) as $code => $coupon ) :
        //     if( in_array( $product['product_id'], $coupon )
        //     || in_array( $coupon->discount_type, array( 'percent_cart', 'fixed_cart' ) ) ):
        //     $product_price = wpq_9522_discount( $product['line_total'], $coupon->discount_type, $coupon->amount );
        //     endif;
        // endforeach;
        


        $t += $product_price * $quantities[$product['data']->get_id()];
        $p += $product_p->get_price() * $quantities[$product['product_id']];
    endforeach;
    
    $cart_total_num = str_replace('-', '', filter_var($cart_subtotal, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));

    //     echo '<pre style="display:none;text-align: left;
    // position: relative;
    // left: -900px;">';
    //         print_r($cart_total_num);
    //                 echo '<br>';

    //         print_r($t);
    //     echo '</pre>';
    
    
    if( $t >  $cart_total_num) {
        return sprintf( '<s> %s </s> %s', wc_price( $t ), $cart_subtotal ) ;

    }
    
    return $cart_subtotal;

}



//  No use
function my_custom_show_sale_price_at_cart( $old_display, $cart_item, $cart_item_key ) {
    
    
    // line_subtotal, line_total
  
    
    $product = $cart_item['data'];

    if ( $product ) {
        $return = $product->get_price_html();
    }

    $return = $old_display;
    
    // echo '<pre>';
    //     print_r($return);
    // echo '</pre>';
    
    $check_in_coupon = false;
        // print_r( WC()->cart->get_coupons() );
    foreach ( WC()->cart->get_coupons() as $code => $coupon ) :
   
        if( in_array( $product->product_id, $coupon )
        || in_array( $coupon->discount_type, array( 'percent_cart', 'fixed_cart' ) ) ):
        $check_in_coupon = true;
        // $product_price = wpq_9522_discount( $product['line_total'], $coupon->discount_type, $coupon->amount );
        // return $product->get_price_html();
        endif;
    endforeach;
    
    if($cart_item['line_subtotal'] > $cart_item['line_total'] && !$check_in_coupon) {
        $subtotal_item = '<br/> <span>'.$cart_item['quantity'].' '.($cart_item['quantity']>1?__('pieces', 'shiaka'):__('piece', 'shiaka')).' </span> <br/> <del>'.wc_price($cart_item['line_subtotal']).'</del> <span style="background-color: none;">'.wc_price($cart_item['line_total']).'</span><br/><span style="color:green;font-size:14px;">'.__('You Saved', 'shiaka').': '.(wc_price($cart_item['line_subtotal']-$cart_item['line_total'])).'</span>';

        $return .= $subtotal_item;
    }

    
    return $return;

}
