<?php

/*
* @@ Product Price
* Last updated version 4.0.0
*/

class AWDP_typeProductPrice
{

    public function apply_discount_percent_product_price ( $rule, $item, $price, $quantity, $discVariable, $cartContent, $disc_prod_ID, $prodLists, $couponStatus = false, $cartView = false )
    {
        
        $prod_ID            = $cartContent['data']->get_slug(); 
        $cartKey            = $cartView ? $prod_ID : $cartContent['key'];
        $result             = [];
        $total_discount     = 0;
        $cart_total         = 0;
        $discount           = 0;
        $product_price      = wc_add_number_precision ( $price ); 

        // Checking for restriction discount
        $dynmValue          = array_key_exists ( 'dynamic_value', $rule ) ? $rule['dynamic_value'] : false;
        $dynmDisc           = $dynmValue ? awdp_dynamic_value ( $rule, $item, $price, $quantity, $prodLists, $disc_prod_ID ) : '';
        $discount           = $dynmValue ? ( $dynmDisc ? $dynmDisc : '' ) : $rule['discount'];

        // Actual Discount
        if ( $discount == '' || $discount <= 0 ) {
            $discount       = 0;
        } else {
            $discount       = $product_price * ( (float)$discount / 100 ); 
        }

        // Discount Calculation
        if ( $product_price >= $discount )
            $updated_product_price = $product_price - $discount;
        else
            $updated_product_price = 0;

        $discVariable['discounts'][$cartKey]['discount']            = $discount; 
        $discVariable['discounts'][$cartKey]['quantity']            = $quantity;
        $discVariable['discounts'][$cartKey]['displayoncart']       = true;
        $discVariable['discounts'][$cartKey]['productid']           = $disc_prod_ID;
        $discVariable['taxable']                                    = $rule['inc_tax'];
        
        $result['discountedprice']              = $updated_product_price;
        $result['productDiscount']              = $discVariable;

        return $result;

    }

    public function apply_discount_fixed_product_price ( $rule, $item, $price, $quantity, $discVariable, $cartContent, $disc_prod_ID, $prodLists, $couponStatus = false, $cartView = false )
    {

        $prod_ID            = $cartContent['data']->get_slug();
        $cartKey            = $cartView ? $prod_ID : $cartContent['key'];
        $result             = [];
        $discount           = 0;
        $product_price      = wc_add_number_precision ( $price );
        $discount_amount    = wc_add_number_precision ( $rule['discount'] ); 

        // Checking for restriction discount
        $dynmValue          = array_key_exists ( 'dynamic_value', $rule ) ? $rule['dynamic_value'] : false;
        $dynmDisc           = $dynmValue ? awdp_dynamic_value ( $rule, $item, $price, $quantity, $prodLists, $disc_prod_ID ) : '';
        $discount_amount    = $dynmValue ? ( $dynmDisc ? wc_add_number_precision ( $dynmDisc ) : '' ) : $discount_amount;

        if ( $discount_amount == '' || $discount_amount <= 0 ) {
            $discount_amount    = 0;
        }

        // Discount Calculation
        if ( $product_price >= $discount_amount ) {
            $updated_product_price  = $product_price - $discount_amount;
            $discount               = $discount_amount;
        } else {
            $updated_product_price  = 0;
            $discount               = $product_price;
        }
   
        $discVariable['discounts'][$cartKey]['discount']            = $discount;
        $discVariable['discounts'][$cartKey]['quantity']            = $quantity;
        $discVariable['discounts'][$cartKey]['displayoncart']       = true;
        $discVariable['discounts'][$cartKey]['productid']           = $disc_prod_ID;
        $discVariable['taxable']                                    = $rule['inc_tax'];
        
        $result['discountedprice']              = $updated_product_price;
        $result['productDiscount']              = $discVariable;

        return $result;

    }

}
