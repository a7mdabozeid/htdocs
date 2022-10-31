<?php

/*
* @@ Product Price 
* @@ Last updated version 4.0.5
*/

class AWDP_productGroup
{

    public function product_group ( $rules, $price, $item_id, $product, $prodLists, $cartRules, $item_price )
    {

        $result                         = [];

        if ( $rules != false && sizeof ( $rules ) >= 1 ) { 

            $prod_ID            = $product->get_data()['slug'];
            $variationCheck     = $product->is_type( 'variable' );
            $variations         = $variationCheck ? $product->get_children() : [];
            $converted_rate     = 1;
            $discountprice      = '';
            $mindiscountprice   = '';
            $maxdiscountprice   = '';
            $wdp_max_price      = 0;
            $wdp_min_price      = 0;
            $maxdiscount        = 0;
            $mindiscount        = 0;
            $discount           = 0;
            $ProductRuleActive  = false;

            foreach ( $rules as $rule ) {
               
                // Get Product List
                $checkItem = call_user_func_array ( 
                    array ( new AWDP_Discount(), 'get_items_to_apply_discount' ), 
                    array ( $product, $rule, false, true ) 
                );

                $validateRules = call_user_func_array ( 
                    array ( new AWDP_Discount(), 'validate_discount_rules' ), 
                    array ( $product, $rule, ['cart_total_amount', 'cart_total_amount_all_prods', 'cart_items', 'cart_items_all_prods', 'cart_products', 'cust_prev_order_count', 'cart_user_role', 'cart_user_selection', 'payment_method', 'shipment_method', 'number_orders', 'amount_spent', 'last_order', 'previous_order', 'product_in_cart'] ) 
                );

                if ( !$checkItem ) {
                    continue;
                }

                // Check if User if Logged-In
                if ( intval ( $rule['discount_reg_customers'] ) === 1 && !is_user_logged_in() ) { 
                    continue;
                }

                // Validate Rules
                if ( !$validateRules ) {
                    continue;
                }

                if ( ( $rule['type'] == 'percent_product_price' || $rule['type'] == 'fixed_product_price' ) ) {

                    $type = ( $rule['type'] == 'fixed_product_price' ) ? 'fixed' : 'percentage';
                    $result[] = array ( 'type' => $type, 'value' => $rule['discount'] );

                }

            }

            return $result;

        }

        return $result;

    }

}