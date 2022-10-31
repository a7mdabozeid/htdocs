<?php

/*
* @@ Total Amount
* Last updated version 4.0.0
*/

class AWDP_typeTotalAmount
{

    public function apply_discount_percent_total_amount ( $rule, $item, $price, $quantity, $discVariable, $cartContent, $disc_prod_ID, $couponStatus = false, $cartView = false )
    {

        $total_discount     = 0;
        $cart_total         = 0;
        $result             = [];
        // $variationDiscounts = $discVariable['variationDiscounts'];

        $prod_ID            = $cartContent['data']->get_slug();
        $cartKey            = $cartView ? $prod_ID : $cartContent['key'];

        $price_to_discount  = wc_add_number_precision ( $price ); 

        $discount = $price_to_discount * ((float)$rule['discount'] / 100); 
        $discount = min($price_to_discount, $discount); 

        // Store code and discount amount per item.
        $cart_total     = $cart_total + $price_to_discount;
        $total_discount = $total_discount + $discount;

        $discVariable['discounts'][$cartKey]['discount']        = $discount * $quantity;
        $discVariable['discounts'][$cartKey]['quantity']        = $quantity;
        $discVariable['discounts'][$cartKey]['displayoncart']   = false;
        $discVariable['discounts'][$cartKey]['productid']       = $disc_prod_ID;
        $discVariable['taxable']                                = $rule['inc_tax'];

        $result['productDiscount']              = $discVariable;
        // $result['discountedprice']              = $variationDiscounts;

        return $result;

    }

    public function apply_discount_fixed_price_total_amount ( $rule, $item, $price, $quantity, $discVariable, $cartContent, $disc_prod_ID, $couponStatus = false, $cartView = false )
    {
        
        $total_discount     = 0;
        $cart_total         = 0;
        $result             = [];
        // $variationDiscounts = $discVariable['variationDiscounts'];

        $prod_ID            = $cartContent['data']->get_slug();
        $cartKey            = $cartView ? $prod_ID : $cartContent['key'];

        $price_to_discount  = wc_add_number_precision ( $price ) * $quantity;

        $discount = wc_add_number_precision($rule['discount']); 

        if ( $discVariable['discount_remainder'] >= 0 ) {
            $discount = $discVariable['discount_remainder'];
        }

        if ( !isset($discVariable['discounts'][$cartKey]) && $price_to_discount > 0 ) { 

            if ( intval($price_to_discount) >= $discount && $discount >= 0 ) {
                $product_discount                   = $discount;
                $discVariable['discount_remainder'] = 0;
            } else if ( intval($price_to_discount) < $discount && $discount >= 0 ) {
                $product_discount                   = $price_to_discount;
                $discVariable['discount_remainder'] = $discount - $price_to_discount;
            } else {
                $product_discount = 0;
            }

            $discVariable['discounts'][$cartKey]['discount']        = $product_discount;
            $discVariable['discounts'][$cartKey]['quantity']        = $quantity;
            $discVariable['discounts'][$cartKey]['displayoncart']   = false;
            $discVariable['discounts'][$cartKey]['productid']       = $disc_prod_ID;
            $discVariable['taxable']                                = $rule['inc_tax'];

        }

        $result['productDiscount']              = $discVariable;
        // $result['discountedprice']              = $variationDiscounts;

        return $result;

    }

}