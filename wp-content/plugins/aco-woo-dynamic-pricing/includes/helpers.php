<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Dynamic Discount Value
if ( !function_exists('awdp_dynamic_value') ) {

    function awdp_dynamic_value( $rule, $item, $price, $quantity, $prodLists, $disc_prod_ID = false )
    {

        if ( isset($rule['rules']) && is_array($rule['rules']) && !empty($rule['rules']) ) {

            $product_lists      = $prodLists ? $prodLists : [];  
            $list_id            = ( array_key_exists ( 'product_list', $rule ) && $rule['product_list'] ) ? $rule['product_list'] : '';
            $rulesArray         = $rule['rules'];

            // Initialise
            $wdp_cart_totals = $wdp_cart_items = $wdp_cart_quantity = $wdp_cart_quantity_pl = $wdp_cart_totals_pl = $wdp_cart_items_pl = 0;

            // Sort Based on discount
            usort ( $rulesArray, function($a, $b) {
                if ( $b['discount'] != '' && $a['discount'] != '' )
                    return $b['discount'] - $a['discount']; 
            } );

            $item               = $disc_prod_ID ? wc_get_product ( $disc_prod_ID ) : $item;
            // $evel_str           = '';

            // Checking if Product List is Active
            if ( isset ( WC()->cart ) && WC()->cart->get_cart_contents_count() > 0 ) {

                // Checkout page ajax loading fix 
                $cart_items = is_checkout() ? ( WC()->session->get('WDP_Cart') ? WC()->session->get('WDP_Cart') : WC()->cart->get_cart() ) : WC()->cart->get_cart(); 
    
                // Product List
                $applicable_products    = ( $list_id && $list_id != 'null' ) ? ( !empty ( $product_lists ) && array_key_exists ( $list_id, $product_lists ) ? $product_lists[$list_id] : [] ) : [];
    
                if ($cart_items) {
                    foreach ( $cart_items as $cart_item ) {
                        // $product_data       = $cart_item['data']->get_data();
                        // $wdp_cart_totals    = $wdp_cart_totals + $product_data['price'] * $cart_item['quantity'];
                        $wdp_cart_totals    = $wdp_cart_totals + $cart_item['data']->get_price() * $cart_item['quantity'];
                        $wdp_cart_items     = $wdp_cart_items + $cart_item['quantity'];
                        $wdp_cart_quantity  = $wdp_cart_quantity + 1;
                        // check Product List
                        if ( !empty ( $applicable_products ) && in_array ( $cart_item['product_id'], $applicable_products ) ) { 
                            $wdp_cart_totals_pl    = $wdp_cart_totals_pl + $cart_item['data']->get_price() * $cart_item['quantity'];
                            $wdp_cart_items_pl     = $wdp_cart_items_pl + $cart_item['quantity'];
                            $wdp_cart_quantity_pl  = $wdp_cart_quantity_pl + 1;
                        }
                    }
                }
    
            }

            foreach ( $rulesArray as $val ) {

                if ( !empty($val['rules']) && is_array($val['rules']) && count($val['rules']) ) {

                    $evel_str = '';

                    $val_rules  = array_values ( array_filter( $val['rules'] ) ); 
                    $dynmcDisc  = array_key_exists ( 'discount', $val ) ? $val['discount'] : ''; 

                    if ( !$dynmcDisc ) continue;

                    foreach ( $val_rules as $rul ) { 

                        $evel_str .= '(';

                        if ( $rul['rule']['value'] != '' ) {
                            if ( awdp_combinations ( $rul, $item, $list_id, $product_lists, $wdp_cart_totals, $wdp_cart_items, $wdp_cart_quantity, $wdp_cart_totals_pl, $wdp_cart_items_pl, $wdp_cart_quantity_pl ) ) { 
                                $evel_str .= ' true ';
                            } else { 
                                $evel_str .= ' false ';
                            }
                        } else {
                            $evel_str .= ' true ';
                        }

                        $evel_str .= ') ' . (($rul['operator'] !== false) ? $rul['operator'] : '') . ' ';
                        
                    }

                    if ( count($val['rules']) > 0 && !empty($val['rules']) ) {
                        preg_match_all('/\(.*\)/', $evel_str, $match);
                        $evel_str = $match[0][0] . ' ';
                    }

                    $evel_str = str_replace(['and', 'or'], ['&&', '||'], strtolower($evel_str));
            
                    if ( eval ( 'return ' . $evel_str . ';' ) ) {

                        return $dynmcDisc;

                    }

                }
                
            }

        }

    }

}

// Dynamic Discount Combinations
if ( !function_exists('awdp_combinations') ) {

    function awdp_combinations ( $rul, $item, $list_id, $product_lists, $wdp_cart_totals, $wdp_cart_items, $wdp_cart_quantity, $wdp_cart_totals_pl, $wdp_cart_items_pl, $wdp_cart_quantity_pl )
    {

        $ruleItem   = $rul['rule']['item'];
        $ruleVal    = $rul['rule']['value'];
        $rulCond    = $rul['rule']['condition'];

        // $operator = $rul["operator"];

        if ( 'cart_total_amount' == $ruleItem ) { 

            // Check if cart is empty
            if ( !isset (WC()->cart) || $wdp_cart_quantity_pl == 0 || !did_action('woocommerce_before_calculate_totals') ) 
                return false;

            $item_val   = $wdp_cart_totals_pl;
            $rel_val    = (float)$ruleVal;

        } else if ( 'cart_total_amount_all_prods' == $ruleItem ) { 

            // Check if cart is empty
            if ( !isset (WC()->cart) || $wdp_cart_totals == 0 || !did_action('woocommerce_before_calculate_totals') ) 
                return false;

            $item_val   = $wdp_cart_totals;
            $rel_val    = (float)$ruleVal;

        } else if ( 'product_price' == $ruleItem ) {

            $item_val   = (float)$item->get_price();  
            $rel_val    = (float)$ruleVal;

        } else if ( 'cart_items' == $ruleItem ) {

            // Check if cart is empty
            if ( !isset ( WC()->cart ) || $wdp_cart_quantity_pl == 0 || !did_action('woocommerce_before_calculate_totals') ) return false;

            $item_val   = $wdp_cart_items_pl;
            $rel_val    = (float)$ruleVal;

        } else if ( 'cart_items_all_prods' == $ruleItem ) {

            // Check if cart is empty
            if ( !isset ( WC()->cart ) || $wdp_cart_quantity == 0 || !did_action('woocommerce_before_calculate_totals') ) return false;

            $item_val   = $wdp_cart_items;
            $rel_val    = (float)$ruleVal;

        } else if ( 'cart_products' == $ruleItem ) {

            // Check if cart is empty
            if ( !isset ( WC()->cart ) || $wdp_cart_quantity == 0 || !did_action('woocommerce_before_calculate_totals') ) return false;

            $item_val   = $wdp_cart_quantity;
            $rel_val    = (float)$ruleVal;

        } else if ( 'cart_products_list' == $ruleItem ) {

            // Check if cart is empty
            if ( !isset ( WC()->cart ) || $wdp_cart_quantity_pl == 0 || !did_action('woocommerce_before_calculate_totals') ) return false;

            $item_val   = $wdp_cart_quantity_pl;
            $rel_val    = (float)$ruleVal;

        } else {

            return false;

        }

        switch ($rulCond) {
            case 'equal_to':
                if (@abs(($item_val - $rel_val) / $item_val) < 0.00001) {
                    return true;
                }
                break;
            case 'less_than':
                if ($item_val < $rel_val) {
                    return true;
                }
                break;
            case 'less_than_eq':
                if ($item_val < $rel_val || abs(($item_val - $rel_val) / $item_val) < 0.0001) {
                    return true;
                }
                break;
            case 'greater_than': 
                if ($item_val > $rel_val) { 
                    return true;
                }
                break;
            case 'greater_than_eq':
                if ($item_val > $rel_val || abs(($item_val - $rel_val) / $item_val) < 0.0001) {
                    return true;
                }
                break;
        }

    }

}
