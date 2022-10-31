<?php

/*
* @@ Cart Total
* Last updated version 4.0.0
* Added wc_add_number_precision for fixed values (individual product discounts)
*/

class AWDP_typeCartQuantity
{

    public function apply_discount_cart_quantity ( $rule, $item, $price, $quantity, $discVariable, $prodLists, $cartContents, $cartContent, $disc_prod_ID, $couponStatus = false, $cartView = false )
    { 

        $cart_total                 = 0;
        $updated_product_price      = '';
        $act_qnty                   = 0;
        $discount_pt                = 0;
        $wdp_cart_totals            = 0;
        $wdp_cart_items             = 0;
        $wdp_cart_quantity          = 0;
        $discount_amt               = 0;
        $wdp_applicable_ids         = [];
        $result                     = [];
        $quantity_rules             = $rule['quantity_rules'];
        $quantity_type              = $rule['quantity_type'];
        $table_layout               = $rule['table_layout'];
        $prod_ID                    = $cartContent['data']->get_slug();
        $cartKey                    = $cartView ? $prod_ID : $cartContent['key'];
        $wdp_item_ID                = $item->get_ID();
        $variation_check            = $rule['variation_check'];
        $parent_id                  = $item->get_parent_id();
        $discount = $table = $tr_qn = $tr_pr = '';
        $rules_to_validate          = ['cart_total_amount', 'cart_total_amount_all_prods', 'product_price'];
        $price                      = wc_add_number_precision($price);
        
        // $item = ( $parent_id == 0 ) ? $item : wc_get_product( $parent_id );

        $product_lists  = $prodLists;
        $list_id        = ( array_key_exists('product_list', $rule) && $rule['product_list'] ) ? $rule['product_list'] : '';

        // Check current product
        $prod_validate = call_user_func_array ( 
            array ( new AWDP_Discount(), 'validate_discount_rules' ), 
            array ( $item, $rule, $rules_to_validate, $item, true ) 
        );

        if ( !$prod_validate ) {
            return $result;
        } 
        // End Check

        // Checking if Product List is Active / Cart rules Validation
        /*
        * @4.1.1
        * $product_data['price'] to $cart_item['data']->get_price() as $product_data['price'] not getting addons price
        */
        if ( $list_id && $list_id != 'null' && $product_lists[$list_id] && isset ( WC()->cart ) && WC()->cart->get_cart_contents_count() > 0 ) {
            $applicable_products = $product_lists[$list_id];
            $cart_items = $cartContents;
            if ($cart_items) { 
                foreach ($cart_items as $cart_item) {
                    if ( in_array($cart_item['product_id'], $applicable_products) ) {
                        $validate = call_user_func_array ( 
                            array ( new AWDP_Discount(), 'validate_discount_rules' ), 
                            array ( $cart_item, $rule, $rules_to_validate, $cart_item, true ) 
                        );
                        $onSale = $rule['disable_on_sale'] ? ( $cart_item['data']->get_sale_price() == '' ) : true;
                        if ( $validate && $onSale ) {
                            $product_data           = $cart_item['data']->get_data();
                            // $wdp_cart_totals        = $wdp_cart_totals + $product_data['price'] * $cart_item['quantity'];
                            $wdp_cart_totals        = $wdp_cart_totals + $cart_item['data']->get_price() * $cart_item['quantity'];
                            $wdp_cart_items         = $wdp_cart_items + $cart_item['quantity'];
                            $wdp_cart_quantity      = $wdp_cart_quantity + 1;
                            $wdp_applicable_ids[]   = $cart_item['data']->get_slug();
                        }
                    }
                }
            }
        } else if ( isset ( WC()->cart ) && WC()->cart->get_cart_contents_count() > 0 ) { // Cart rules Validation
            $cart_items = $cartContents;
            if ($cart_items) {
                foreach ($cart_items as $cart_item) {
                    $validate = call_user_func_array ( 
                        array ( new AWDP_Discount(), 'validate_discount_rules' ), 
                        array ( $cart_item, $rule, $rules_to_validate, $cart_item, true ) 
                    );
                    $onSale = $rule['disable_on_sale'] ? ( $cart_item['data']->get_sale_price() == '' ) : true;
                    if ( $validate && $onSale ) {
                        $product_data           = $cart_item['data']->get_data();
                        // $wdp_cart_totals        = $wdp_cart_totals + $product_data['price'] * $cart_item['quantity'];
                        $wdp_cart_totals        = $wdp_cart_totals + $cart_item['data']->get_price() * $cart_item['quantity'];
                        $wdp_cart_items         = $wdp_cart_items + $cart_item['quantity']; 
                        $wdp_cart_quantity      = $wdp_cart_quantity + 1;
                        $wdp_applicable_ids[]   = $cart_item['data']->get_slug();
                    }
                }
            }
        } 

        /* Sort
        * Fix: max_range error when quantity values are added randomly
        * @ ver 4.1.3
        */ 
        array_multisort ( array_column ( $quantity_rules, "start_range" ), SORT_ASC, $quantity_rules );

        $wdp_applicable_ids = array_values ( array_unique ( $wdp_applicable_ids ) );
        $last_key           = end($quantity_rules);
        $max_range          = $last_key["start_range"]; 

        if ( $quantity_type == 'type_cart' ) { 
            
            /*
            * Individual Cart Quantity Rule
            */
            foreach ( $quantity_rules as $quantity_rule ) {

                $discount_val           = $quantity_rule['dis_value'];
                $discount_typ           = $quantity_rule['dis_type'];
                $discounted_new_price   = '';

                // @@ CHECK REMINDER - TODO
                if ( isset ( WC()->cart ) && WC()->cart->get_cart_contents_count() > 0 && empty ( $discVariable['discounts'] ) ) { 

                    if ( ( (int)$quantity_rule['start_range'] == (int)$quantity_rule['end_range'] ) && ( (int)$quantity_rule['start_range'] == $wdp_cart_quantity ) ) {

                        $discount_amt = 0;
                        if ($discount_typ == 'percentage') {
                            $discount_amt = $wdp_cart_totals * ($discount_val / 100);
                        } else if ($discount_typ == 'fixed') {
                            $discount_amt = $discount_val;
                        } else {
                            $discount_amt = 0;
                        }

                        $discVariable['taxable'] = $rule['inc_tax'];

                        $disc_act_val   = (float)$discount_val;
                        $disc_act_type  = $discount_typ;
                        $cart_total     = $wdp_cart_totals - $discount_amt;

                    } else if ( ( ( $wdp_cart_quantity >= (int)$quantity_rule['start_range'] ) && ( $wdp_cart_quantity <= (int)$quantity_rule['end_range'] ) ) || ( $wdp_cart_quantity >= $max_range && (int)$quantity_rule['start_range'] != (int)$quantity_rule['end_range'] )) {

                        $discount_amt = 0;
                        if ($discount_typ == 'percentage') {
                            $discount_amt = $wdp_cart_totals * ($discount_val / 100);
                        } else if ($discount_typ == 'fixed') {
                            $discount_amt = $discount_val;
                        } else {
                            $discount_amt = 0;
                        }

                        $discVariable['taxable'] = $rule['inc_tax'];

                        $disc_act_val           = (float)$discount_val;
                        $disc_act_type          = $discount_typ;
                        $cart_total             = $wdp_cart_totals - $discount_amt;
                        $updated_product_price  = '';

                    }

                } 

            } 

            if ($cart_total > 0) {

                $discVariable['discounts'][$cartKey]['discount']        = wc_add_number_precision ( (float)$discount_amt );
                $discVariable['discounts'][$cartKey]['quantity']        = $quantity;
                $discVariable['discounts'][$cartKey]['displayoncart']   = false;
                $discVariable['discounts'][$cartKey]['productid']       = $disc_prod_ID;
                $discVariable['taxable']                                = $rule['inc_tax'];
                
                $updated_product_price                                  = '';

            }

        } else if ( $quantity_type == 'type_item' ) { 

            /*
            * Total Cart Quantity Rule
            */
            foreach ( $quantity_rules as $quantity_rule ) {

                $discount_val = $quantity_rule['dis_value'];
                $discount_typ = $quantity_rule['dis_type'];

                if ( isset ( WC()->cart ) && WC()->cart->get_cart_contents_count() > 0 && empty ( $discVariable['discounts'] ) ) { 
    
                    if ( ( (int)$quantity_rule['start_range'] == (int)$quantity_rule['end_range'] ) && ( (int)$quantity_rule['start_range'] == $wdp_cart_items ) ) {  

                        // if ( $wdp_cart_items == (int)$quantity_rule['start_range'] ) { 

                            $discount_amt = 0;
                            if ($discount_typ == 'percentage') {
                                $discount_amt = $wdp_cart_totals * ($discount_val / 100);
                            } else if ($discount_typ == 'fixed') {
                                $discount_amt = $discount_val;
                            } else {
                                $discount_amt = 0;
                            }

                            $discVariable['taxable']    = $rule['inc_tax'];
                            $disc_act_val               = (float)$discount_val;
                            $disc_act_type              = $discount_typ;
                            $cart_total                 = $wdp_cart_totals - $discount_amt;

                        // } 

                    } else if ( ( $wdp_cart_items >= (int)$quantity_rule['start_range'] && $wdp_cart_items <= (int)$quantity_rule['end_range'] ) || ( $wdp_cart_items >= $max_range && ( (int)$quantity_rule['start_range'] != (int)$quantity_rule['end_range'] ) ) ) {

                        $discount_amt = 0;
                        if ($discount_typ == 'percentage') {
                            $discount_amt = $wdp_cart_totals * ($discount_val / 100);
                        } else if ($discount_typ == 'fixed') {
                            $discount_amt = $discount_val;
                        } else {
                            $discount_amt = 0;
                        }

                        $discVariable['taxable']    = $rule['inc_tax'];
                        $disc_act_val               = (float)$discount_val;
                        $disc_act_type              = $discount_typ;
                        $cart_total                 = $wdp_cart_totals - $discount_amt;

                    } 

                } 

            } 

            if ( $cart_total > 0 ) { 

                $discVariable['discounts'][$cartKey]['discount']        = wc_add_number_precision ( (float)$discount_amt );
                $discVariable['discounts'][$cartKey]['quantity']        = $quantity;
                $discVariable['discounts'][$cartKey]['displayoncart']   = false;
                $discVariable['discounts'][$cartKey]['productid']       = $disc_prod_ID;
                $discVariable['taxable']                                = $rule['inc_tax'];

                $updated_product_price                                  = '';

            }
        
        } else if ( $quantity_type == 'type_product' ) { 

            /*
            * Product Quantity Discount *
            */
            $cart_contents      = $cartContents;

            $prod_QNT           = [];
            $prod_QNTIDs        = [];
            $item_id            = $item->get_ID();
            $VCheckFlag         = false;
            $VDisApplied        = false;

            if ( $cart_contents ) {
                foreach ( $cart_contents as $cart_content ) { 
                    $cartData               = $cart_content['data']->get_data();
                    // $cartPSlug              = $cartData['slug'];
                    $cartPSlug              = $cartView ? $cartData['slug'] : $cart_content['key']; 
                    $cart_id                = $cart_content['data']->get_ID();
                    $prod_QNT[$cartPSlug]   = $cart_content['quantity'];
                    $prod_QNTIDs[$cart_id]  = $cart_content['quantity'];
                }
            }

            // todo - backedn variation check
            if ( $variation_check && array_key_exists ( $cartKey, $prod_QNT ) ) { 

                $newProd    = $disc_prod_ID ? wc_get_product ( $disc_prod_ID ) : $item;
                $var_pid    = wp_get_post_parent_id ( $disc_prod_ID );
                $act_qnty   = 0;

                if ( $newProd->is_type('variable') ) { 
                    $VCheckFlag = true;
                    $varIDs = call_user_func_array ( 
                        array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                        array ( $item_id, false  ) 
                    );
                    if ( $varIDs ) {
                        foreach ( $varIDs as $varID ) { 
                            if ( array_key_exists ( $varID, $prod_QNTIDs ) ) {
                                $act_qnty += $prod_QNTIDs[$varID]; 
                            }
                        }
                    }
                } else if ( $var_pid != 0 ) { 
                    $VCheckFlag = true;
                    $varIDs = call_user_func_array ( 
                        array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                        array ( $var_pid, false  ) 
                    );
                    if ( $varIDs ) {
                        foreach ( $varIDs as $varID ) { 
                            if ( array_key_exists ( $varID, $prod_QNTIDs ) ) {
                                $act_qnty += $prod_QNTIDs[$varID]; 
                            }
                        }
                    }
                } else {
                    $act_qnty = $prod_QNT[$cartKey];
                }
                
                $quantity = ( $act_qnty > 0 ) ? $act_qnty : $quantity;
            }
            // End Check

            $validate = call_user_func_array ( 
                array ( new AWDP_Discount(), 'validate_discount_rules' ), 
                array ( $item, $rule, $rules_to_validate, $item, true ) 
            );

            foreach ( $quantity_rules as $quantity_rule ) {

                $discount_val               = $quantity_rule['dis_value'];
                $discount_typ               = $quantity_rule['dis_type'];
                $discounted_new_price       = '';

                if ( ( (int)$quantity_rule['start_range'] == (int)$quantity_rule['end_range'] ) && ( (int)$quantity_rule['start_range'] == $quantity ) ) {  

                    /*
                    * Start and End Ranges are the same
                    */
                    // if ( $quantity == (int)$quantity_rule['start_range'] ) { 

                        $discount_amt = 0;
                        if ( $discount_typ == 'percentage' ) {
                            $discount_amt           = $price * ($discount_val / 100);
                            $updated_product_price  = $price - $discount_amt;
                        } else if ( $discount_typ == 'fixed' ) {
                            $discount_val = wc_add_number_precision ( $discount_val );
                            if ( $discount_val <= $price ) {
                                $discount_amt           = $discount_val;
                                $updated_product_price  = $price - $discount_amt;
                            } else {
                                $discount_amt           = $price;
                                $updated_product_price  = 0;
                            }
                        } else {
                            $discount_amt           = 0;
                            $updated_product_price  = $price;
                        }

                    // } 

                } else if ( ( $quantity >= (int)$quantity_rule['start_range'] && $quantity <= (int)$quantity_rule['end_range'] ) || ( $quantity >= $max_range && ( (int)$quantity_rule['start_range'] != (int)$quantity_rule['end_range'] ) ) ) { 

                    $discount_amt = 0;
                    if ( $discount_typ == 'percentage' ) {
                        $discount_amt           = $price * ($discount_val / 100);
                        $updated_product_price  = $price - $discount_amt;
                    } else if ( $discount_typ == 'fixed' ) {
                        $discount_val = wc_add_number_precision ( $discount_val );
                        if ( $discount_val <= $price ) { 
                            $discount_amt           = $discount_val;
                            $updated_product_price  = $price - $discount_amt;
                        } else {
                            $discount_amt           = $price;
                            $updated_product_price  = 0;
                        }
                    } else {
                        $discount_amt           = 0;
                        $updated_product_price  = $price;
                    }

                } 

            } 

            // $discVariable['discounts'][$cartKey]['discount']        = $discount_amt;
            // $discVariable['discounts'][$cartKey]['discount']        = wc_add_number_precision ( (float)$discount_amt );;
            $discVariable['discounts'][$cartKey]['discount']        = (float)$discount_amt;
            $discVariable['discounts'][$cartKey]['quantity']        = $quantity;
            $discVariable['discounts'][$cartKey]['displayoncart']   = true;
            $discVariable['discounts'][$cartKey]['productid']       = $disc_prod_ID;
            $discVariable['taxable']                                = $rule['inc_tax'];

            $updated_product_price                                  = '';

        }

        $result['productDiscount']              = $discVariable;
        $result['discountedprice']              = $updated_product_price;

        return $result;
        
    }

}