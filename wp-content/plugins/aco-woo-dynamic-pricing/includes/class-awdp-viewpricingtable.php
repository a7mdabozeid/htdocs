<?php

/*
* @@ Pricing Table View
* Last updated version 4.0.0
*/

class AWDP_viewPricingTable
{

    public function pricin_table ( $rules, $item, $price, $post_id, $prodLists, $variations, $discountedPrice, $discountProductMaxPrice, $discountProductMinPrice )
    { 

        $table = '';
        // Multi Lang
        $checkML        = call_user_func ( array ( new AWDP_ML(), 'is_default_lan' ), '' );
        $currentLang    = !$checkML ? call_user_func ( array ( new AWDP_ML(), 'current_language' ), '' ) : ''; 

        foreach ( $rules as $rule ) { 
            
            // Check if table is enabled
            if ( !$rule['pricing_table'] ) {
                continue;
            }

            // Exit if not quantity rule
            if ( 'cart_quantity' != $rule['type'] ) {
                continue;
            }

            // Get Product List
            $checkItem = call_user_func_array ( 
                array ( new AWDP_Discount(), 'get_items_to_apply_discount' ), 
                array ( $item, $rule ) 
            );
            if ( !$checkItem ) {
                continue;
            }

            // Check if User if Logged-In
            if ( intval ( $rule['discount_reg_customers'] ) === 1 && !is_user_logged_in() ) { 
                continue;
            }

            $quantity_rules             = $rule['quantity_rules'];
            $quantity_type              = $rule['quantity_type'];
            $table_layout               = $rule['table_layout'];
            $prod_ID                    = $item->get_data()['slug'];
            $wdp_item_ID                = $item->get_ID();
            $table_sort                 = get_option('awdp_table_sort') ? get_option('awdp_table_sort') : '';
            $variation_check            = $rule['variation_check'];
            $parent_id                  = $item->get_parent_id();
            $rules_to_validate          = ['cart_total_amount', 'cart_total_amount_all_prods', 'product_price'];
            $cart_total                 = 0;
            $updated_product_price      = 0;
            $act_qnty                   = 0;
            $discount_pt                = 0;
            $wdp_cart_totals            = 0;
            $wdp_cart_items             = 0;
            $wdp_cart_quantity          = 0;
            $discount_amt               = 0;
            $wdp_applicable_ids         = [];
            $discount = $table = $tr_qn = $tr_pr = '';
            
            $price                      = $discountedPrice ? $discountedPrice : $price;

            // Values
            $value_display              = get_option('awdp_table_value') ? get_option('awdp_table_value') : '';
            $value_display_text_hide    = get_option('awdp_table_value_notext') ? get_option('awdp_table_value_notext') : 0; 

            /*
            * WPML Label
            * Version 4.0.5
            */
            $langSettings               = get_option('awdp_settings_lang_options') ? get_option('awdp_settings_lang_options') : [];
            if ( !empty ($langSettings) && array_key_exists ( $currentLang, $langSettings ) ) {
                $awdp_pc_title              = array_key_exists ( 'pricing_title', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['pricing_title'] : ( get_option('awdp_pc_title') ? get_option('awdp_pc_title') : __("Quantity Discounts", "aco-woo-dynamic-pricing") );
                $awdp_pc_label              = array_key_exists ( 'pricing_price_label', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['pricing_price_label'] : ( get_option('awdp_pc_label') ? get_option('awdp_pc_label') : __("Price", "aco-woo-dynamic-pricing") );
                $awdp_qn_label              = array_key_exists ( 'pricing_quantity_label', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['pricing_quantity_label'] : ( get_option('awdp_qn_label') ? get_option('awdp_qn_label') : __("Quantity", "aco-woo-dynamic-pricing") );
                $awdp_nw_label              = array_key_exists ( 'pricing_new_label', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['pricing_new_label'] : ( get_option('awdp_new_label') ? get_option('awdp_new_label') : __("Price", "aco-woo-dynamic-pricing") );
                $value_display_text         = array_key_exists ( 'tablevaluetext', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['tablevaluetext'] : get_option('awdp_table_value_text');
                $discount_description       = array_key_exists ( 'discount_description', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['discount_description'] : get_option('awdp_discount_description');
                $discount_item_description  = array_key_exists ( 'discount_item_description', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['discount_item_description'] : get_option('awdp_discount_item_description');  
            } else  {
                $awdp_pc_title              = get_option('awdp_pc_title') ? get_option('awdp_pc_title') : __("Quantity Discounts", "aco-woo-dynamic-pricing");
                $awdp_pc_label              = get_option('awdp_pc_label') ? get_option('awdp_pc_label') : __("Price", "aco-woo-dynamic-pricing");
                $awdp_qn_label              = get_option('awdp_qn_label') ? get_option('awdp_qn_label') : __("Quantity", "aco-woo-dynamic-pricing");
                $awdp_nw_label              = get_option('awdp_new_label') ? get_option('awdp_new_label') : __("Price", "aco-woo-dynamic-pricing");
                $value_display_text         = get_option('awdp_table_value_text') ? get_option('awdp_table_value_text') : '';
                $discount_description       = get_option('awdp_discount_description') ? get_option('awdp_discount_description') : '';
                $discount_item_description  = get_option('awdp_discount_item_description') ? get_option('awdp_discount_item_description') : '';
            }
            /* End WPML */

            // Pricing table texts
            $prcn_text      = ( !$value_display_text_hide ) ? ( ( ( $value_display == 'discount_value' || $value_display == 'discount_both' ) && $value_display_text ) ? ' '.$value_display_text : __('% OFF', 'aco-woo-dynamic-pricing') ) : '';
            $fxd_text       = ( !$value_display_text_hide ) ? ( ( ( $value_display == 'discount_value' || $value_display == 'discount_both' ) && $value_display_text ) ? ' '.$value_display_text : __(' OFF on cart value', 'aco-woo-dynamic-pricing') ) : '';
            $fxd_text_two   = ( !$value_display_text_hide ) ? ( ( ( $value_display == 'discount_value' || $value_display == 'discount_both' ) && $value_display_text ) ? ' '.$value_display_text : __(' OFF', 'aco-woo-dynamic-pricing') ) : '';
            $cart_text      = ( !$value_display_text_hide ) ? ( ( ( $value_display == 'discount_value' || $value_display == 'discount_both' ) && $value_display_text ) ? ' '.$value_display_text : __(' will be deducted from cart', 'aco-woo-dynamic-pricing') ) : '';

            $list_id        = ( array_key_exists('product_list', $rule) && $rule['product_list'] ) ? $rule['product_list'] : '';

            // Check current product
            $prod_validate = call_user_func_array ( 
                array ( new AWDP_Discount(), 'validate_discount_rules' ), 
                array ( $item, $rule, $rules_to_validate, $item, true ) 
            );

            if ( !$prod_validate ) {
                return $table;
            } 
            // End Check

            // Checking if Product List is Active / Cart rules Validation
            if ( $list_id && $list_id != 'null' && $prodLists[$list_id] && isset ( WC()->cart ) && WC()->cart->get_cart_contents_count() > 0 ) {
                $applicable_products = $prodLists[$list_id];
                $cart_items = WC()->cart->get_cart();
                if ($cart_items) {
                    foreach ($cart_items as $cart_item) {
                        if (in_array($cart_item['product_id'], $applicable_products)) {
                            $validate = call_user_func_array ( 
                                array ( new AWDP_Discount(), 'validate_discount_rules' ), 
                                array ( $cart_item, $rule, $rules_to_validate, $cart_item, true ) 
                            );
                            if ( $validate ) {
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
                $cart_items = WC()->cart->get_cart();
                if ($cart_items) {
                    foreach ($cart_items as $cart_item) {
                        $validate = call_user_func_array ( 
                            array ( new AWDP_Discount(), 'validate_discount_rules' ), 
                            array ( $cart_item, $rule, $rules_to_validate, $cart_item, true ) 
                        );
                        if ( $validate ) {
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

            $wdp_applicable_ids     = array_values ( array_unique ( $wdp_applicable_ids ) );
            $last_key               = end($quantity_rules);
            $max_range              = $last_key["start_range"];
            $pricing_table_price    = wc_add_number_precision ( $price );

            // Sort
            if ( $table_sort == 'descending_order' ) {
                array_multisort ( array_column ( $quantity_rules, "start_range" ), SORT_DESC, $quantity_rules );
            } else {
                array_multisort ( array_column ( $quantity_rules, "start_range" ), SORT_ASC, $quantity_rules );
            }

            // Variable Product
            if ( $item->is_type('variable') ) {
                $variation_prices = [];
                if ( $discountProductMaxPrice && $discountProductMinPrice )  {
                    array_push( $variation_prices, wc_add_number_precision ( $discountProductMaxPrice ) );
                    array_push( $variation_prices, wc_add_number_precision ( $discountProductMinPrice ) );
                } else {
                    if ( array_key_exists ( $prod_ID, $variations ) ) {
                        $variation_ids = $variations[$prod_ID];
                        foreach ( $variation_ids as $variation_id ) {
                            array_push( $variation_prices, wc_add_number_precision ( $variation_id ) );
                        }
                    } else {
                        $variations = $item->get_available_variations();
                        foreach ( $variations as $variation ) {
                            array_push ( $variation_prices, wc_add_number_precision ( $variation['display_price'] ) );
                        }
                    }
                }
            }

            if ( $quantity_type == 'type_cart' ) { 

                /*
                * Individual Cart Quantity Rule
                */
                if ( $table_layout == 'horizontal' ) {
                    $table .= '<div class="wdp_table_outter"><h4>' . $awdp_pc_title . '</h4><table class="wdp_table lay_horzntl"><tbody class="wdp_table_body">';
                    $tr_qn = '<tr><td>' . $awdp_qn_label . '*' . '</td>';
                    $tr_pr = '<tr><td>' . $awdp_pc_label . '</td>';
                    if ( $value_display == 'discount_both' ) {
                        $tr_nw = '<tr><td>' . $awdp_nw_label . '</td>';
                    }
                } else {
                    if ( $value_display == 'discount_both' ) {
                        $table .= '<div class="wdp_table_outter"><h4>' . $awdp_pc_title . '</h4><table class="wdp_table"><thead><tr class="wdp_table_head"><td>' . $awdp_qn_label . '*' . '</td><td>' . $awdp_pc_label . '</td><td>' . $awdp_nw_label . '</td></tr></thead><tbody class="wdp_table_body">';
                    } else {
                        $table .= '<div class="wdp_table_outter"><h4>' . $awdp_pc_title . '</h4><table class="wdp_table"><thead><tr class="wdp_table_head"><td>' . $awdp_qn_label . '*' . '</td><td>' . $awdp_pc_label . '</td></tr></thead><tbody class="wdp_table_body">';
                    }
                }

                foreach ( $quantity_rules as $quantity_rule ) {

                    $discount_val = $quantity_rule['dis_value'];
                    $discount_typ = $quantity_rule['dis_type'];
                    $discounted_new_price = '';

                    // $converted_rate = $this->converted_rate;
                    $converted_rate = 1; // Removing coupon amount - get price changed to $price - calculate total

                    // Pricing Table
                    if ( $discount_typ == 'percentage' ) {
                        $discount_pt = $pricing_table_price * ((float)$discount_val / 100);
                        $discount_pt = min($pricing_table_price, $discount_pt);
                        // Updated Price
                        $discounted_new_price = (($pricing_table_price - $discount_pt) > 0) ? wc_price ( wc_remove_number_precision ( $pricing_table_price - $discount_pt ) * $converted_rate ) : 0;
                    } else if ( $discount_typ == 'fixed' ) {
                        $discount_pt = wc_add_number_precision($discount_val * $converted_rate);
                        // Discount
                        $discounted_new_price = wc_price($discount_val) . $cart_text;
                    }

                    // $discounted_new_price = (($pricing_table_price - $discount_pt) > 0) ? wc_price ( wc_remove_number_precision ( $pricing_table_price - $discount_pt ) * $converted_rate ) : 0;

                    $discounted_new_price_bt = $discounted_new_price;

                    if ( $value_display == 'discount_value' || $value_display == 'discount_both' ) {
                        if ($discount_typ == 'percentage') {
                            $discounted_new_price = (float)$discount_val . $prcn_text;
                        } else if ($discount_typ == 'fixed') {
                            $discounted_new_price = wc_price($discount_val) . $fxd_text;
                        }
                    }

                    // Pricing Table Calculations
                    if ( $item->is_type('variable') ) { 

                        // Variation Pricing Table
                        $price_to_discount_max = $variation_prices ? max($variation_prices) : 0;
                        $price_to_discount_min = $variation_prices ? min($variation_prices) : 0;

                        if ( $discount_typ == 'percentage' ) {
                            $discount_max_value = $price_to_discount_max * ((float)$discount_val / 100);
                            $discount_min_value = $price_to_discount_min * ((float)$discount_val / 100);
                            // $discount_max_value = min($price_to_discount, $discount_pt);
                        } else if ($discount_typ == 'fixed') {
                            $discount_max_value = wc_add_number_precision($discount_val);
                            $discount_min_value = wc_add_number_precision($discount_val);
                        }

                        $discounted_new_max_price = (($price_to_discount_max - $discount_max_value) > 0) ? wc_price ( wc_remove_number_precision ( $price_to_discount_max - $discount_max_value ) ) : 0;
                        $discounted_new_min_price = (($price_to_discount_min - $discount_min_value) > 0) ? wc_price ( wc_remove_number_precision ( $price_to_discount_min - $discount_min_value ) ) : 0;

                        if ( $table_layout == 'horizontal' ) {
                            if ( $quantity_rule['start_range'] == $quantity_rule['end_range'] ) {
                                $tr_qn .= '<td>' . $quantity_rule['start_range'] . '</td>';
                            } else if ($quantity_rule['end_range']) {
                                $tr_qn .= '<td>' . $quantity_rule['start_range'] . ' - ' . $quantity_rule['end_range'] . '</td>';
                            } else {
                                $tr_qn .= '<td>' . $quantity_rule['start_range'] . ' +</td>';
                            }
                            if ( $value_display == 'discount_value' ) {
                                if ($discount_typ == 'percentage') {
                                    $tr_pr .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                } else if ($discount_typ == 'fixed') {
                                    $tr_pr .= '<td>' . wc_price((float)$discount_val) . $fxd_text . '</td>';
                                }
                            } else if ( $value_display == 'discount_both' ) { // Display Both Price and Value
                                if ($discount_typ == 'percentage') {
                                    $tr_pr .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                } else if ($discount_typ == 'fixed') {
                                    $tr_pr .= '<td>' . wc_price((float)$discount_val) . $fxd_text . '</td>';
                                }
                                if ( $discounted_new_min_price == $discounted_new_max_price ) {
                                    $tr_nw .= '<td>' . $discounted_new_min_price . '</td>';
                                } else {
                                    $tr_nw .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                }
                            } else {
                                if ( $discounted_new_min_price == $discounted_new_max_price ) {
                                    $tr_pr .= '<td>' . $discounted_new_min_price . '</td>';
                                } else {
                                    $tr_pr .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                }
                            }
                        } else {
                            if ( $quantity_rule['start_range'] == $quantity_rule['end_range'] ) {
                                $table .= '<tr>';
                                $table .= '<td>' . $quantity_rule['start_range'] . '</td>';
                                if ( $value_display == 'discount_value' ) {
                                    if ($discount_typ == 'percentage') {
                                        $table .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                    } else if ($discount_typ == 'fixed') {
                                        $table .= '<td>' . wc_price((float)$discount_val) . $fxd_text . '</td>';
                                    }
                                } else if ( $value_display == 'discount_both' ) {
                                    if ( $discount_typ == 'percentage' ) {
                                        $table .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                    } else if ($discount_typ == 'fixed') {
                                        $table .= '<td>' . wc_price((float)$discount_val) . $fxd_text . '</td>';
                                    }
                                    if ( $discounted_new_min_price == $discounted_new_max_price ) {
                                        $table .= '<td>' . $discounted_new_min_price . '</td>';
                                    } else {
                                        $table .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                    }
                                } else {
                                    if ( $discounted_new_min_price == $discounted_new_max_price ) {
                                        $table .= '<td>' . $discounted_new_min_price . '</td>';
                                    } else {
                                        $table .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                    }
                                }
                                $table .= '</tr>';
                            } else if ( $quantity_rule['end_range'] ) {
                                $table .= '<tr>';
                                $table .= '<td>' . $quantity_rule['start_range'] . ' - ' . $quantity_rule['end_range'] . '</td>';
                                if ( $value_display == 'discount_value' ) {
                                    if ($discount_typ == 'percentage') {
                                        $table .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                    } else if ($discount_typ == 'fixed') {
                                        $table .= '<td>' . wc_price((float)$discount_val) . $fxd_text . '</td>';
                                    }
                                } else if ( $value_display == 'discount_both' ) {
                                    if ( $discount_typ == 'percentage' ) {
                                        $table .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                    } else if ($discount_typ == 'fixed') {
                                        $table .= '<td>' . wc_price((float)$discount_val) . $fxd_text . '</td>';
                                    }
                                    if ( $discounted_new_min_price == $discounted_new_max_price ) {
                                        $table .= '<td>' . $discounted_new_min_price . '</td>';
                                    } else {
                                        $table .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                    }
                                } else {
                                    if ( $discounted_new_min_price == $discounted_new_max_price ) {
                                        $table .= '<td>' . $discounted_new_min_price . '</td>';
                                    } else {
                                        $table .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                    }
                                }
                                $table .= '</tr>';
                            } else {
                                $table .= '<tr>';
                                $table .= '<td>' . $quantity_rule['start_range'] . ' +</td>';
                                if ( $value_display == 'discount_value' ) {
                                    if ($discount_typ == 'percentage') {
                                        $table .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                    } else if ($discount_typ == 'fixed') {
                                        $table .= '<td>' . wc_price((float)$discount_val) . $fxd_text . '</td>';
                                    }
                                } else if ( $value_display == 'discount_both' ) {
                                    if ($discount_typ == 'percentage') {
                                        $table .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                    } else if ($discount_typ == 'fixed') {
                                        $table .= '<td>' . wc_price((float)$discount_val) . $fxd_text . '</td>';
                                    }
                                    if ($discounted_new_min_price == $discounted_new_max_price) {
                                        $table .= '<td>' . $discounted_new_min_price . '</td>';
                                    } else {
                                        $table .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                    }
                                } else {
                                    if ($discounted_new_min_price == $discounted_new_max_price) {
                                        $table .= '<td>' . $discounted_new_min_price . '</td>';
                                    } else {
                                        $table .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                    }
                                }
                                $table .= '</tr>';
                            }
                        }

                    } else {

                        if ( $table_layout == 'horizontal' ) {
                            if ($quantity_rule['start_range'] == $quantity_rule['end_range']) {
                                $tr_qn .= '<td>' . $quantity_rule['start_range'] . '</td>';
                            } else if ($quantity_rule['end_range']) {
                                $tr_qn .= '<td>' . $quantity_rule['start_range'] . ' - ' . $quantity_rule['end_range'] . '</td>';
                            } else {
                                $tr_qn .= '<td>' . $quantity_rule['start_range'] . ' +</td>';
                            }
                            $tr_pr .= '<td>' . $discounted_new_price . '</td>';
                            if ( $value_display == 'discount_both' ) {
                                $tr_nw .= '<td>' . $discounted_new_price_bt . '</td>';
                            }
                        } else {
                            if ($quantity_rule['start_range'] == $quantity_rule['end_range']) {
                                if ( $value_display == 'discount_both' ) {
                                    $table .= '<tr><td>' . $quantity_rule['start_range'] . '</td><td>' . $discounted_new_price . '</td><td>' . $discounted_new_price_bt . '</td></tr>';
                                } else {
                                    $table .= '<tr><td>' . $quantity_rule['start_range'] . '</td><td>' . $discounted_new_price . '</td></tr>';
                                }
                            } else if ($quantity_rule['end_range']) {
                                if ( $value_display == 'discount_both' ) {
                                    $table .= '<tr><td>' . $quantity_rule['start_range'] . ' - ' . $quantity_rule['end_range'] . '</td><td>' . $discounted_new_price . '</td><td>' . $discounted_new_price_bt . '</td></tr>';
                                } else {
                                    $table .= '<tr><td>' . $quantity_rule['start_range'] . ' - ' . $quantity_rule['end_range'] . '</td><td>' . $discounted_new_price . '</td></tr>';
                                }
                            } else {
                                if ( $value_display == 'discount_both' ) {
                                    $table .= '<tr><td>' . $quantity_rule['start_range'] . ' +</td><td>' . $discounted_new_price . '</td><td>' . $discounted_new_price_bt . '</td></tr>';
                                } else {
                                    $table .= '<tr><td>' . $quantity_rule['start_range'] . ' +</td><td>' . $discounted_new_price . '</td></tr>';
                                }
                            }
                        }
                    }
                    // End PT

                } // End Foreach

                if ($table_layout == 'horizontal') {
                    $tr_qn .= '</tr>';
                    $tr_pr .= '</tr>';
                    if ( $value_display == 'discount_both' ) {
                        $tr_nw .= '</tr>';
                        $table .= $tr_qn . $tr_pr . $tr_nw . '</tbody></table>';
                    } else {
                        $table .= $tr_qn . $tr_pr . '</tbody></table>';
                    }
                } else {
                    $table .= '</tbody></table>';
                }

                $table .= $discount_description ? '<p class="wdp_helpText">*'.$discount_description.'</p>' : '<p class="wdp_helpText">*'.$awdp_qn_label.' refers to discounted items (products with discount) individual count on cart.</p>';

                // Pricinig Table
                if ($rule['pricing_table'] == 1 && $pricing_table_price > 0) $this->pricing_table[$item->get_id()][$rule['id']] = $table;

            } else if ( $quantity_type == 'type_item' ) {  
                
                /*
                * Total Cart Quantity Rule
                */
                if ($table_layout == 'horizontal') {
                    $table .= '<div class="wdp_table_outter"><h4>' . $awdp_pc_title . '</h4><table class="wdp_table lay_horzntl"><tbody class="wdp_table_body">';
                    $tr_qn = '<tr><td>' . $awdp_qn_label . '</td>';
                    $tr_pr = '<tr><td>' . $awdp_pc_label . '</td>';
                    if ( $value_display == 'discount_both' ) {
                        $tr_nw = '<tr><td>' . $awdp_nw_label . '</td>';
                    }
                } else {
                    if ( $value_display == 'discount_both' ) {
                        $table .= '<div class="wdp_table_outter"><h4>' . $awdp_pc_title . '</h4><table class="wdp_table"><thead><tr class="wdp_table_head"><td>' . $awdp_qn_label . '*' . '</td><td>' . $awdp_pc_label . '</td><td>' . $awdp_nw_label . '</td></tr></thead><tbody class="wdp_table_body">';
                    } else {
                        $table .= '<div class="wdp_table_outter"><h4>' . $awdp_pc_title . '</h4><table class="wdp_table"><thead><tr class="wdp_table_head"><td>' . $awdp_qn_label . '</td><td>' . $awdp_pc_label . '</td></tr></thead><tbody class="wdp_table_body">';
                    }
                }

                foreach ($quantity_rules as $quantity_rule) {

                    $discount_val = $quantity_rule['dis_value'];
                    $discount_typ = $quantity_rule['dis_type'];

                    // $converted_rate = $this->converted_rate;
                    $converted_rate = 1; // Removing coupon amount - get price changed to $price - calculate total

                    // Pricing Table
                    if ($discount_typ == 'percentage') {
                        $discount_pt = $pricing_table_price * ((float)$discount_val / 100);
                        $discount_pt = min($pricing_table_price, $discount_pt);
                        // Updated Price
                        $discounted_new_price = (($pricing_table_price - $discount_pt) > 0) ? wc_price ( wc_remove_number_precision ( $pricing_table_price - $discount_pt ) * $converted_rate ) : 0;
                    } else if ($discount_typ == 'fixed') {
                        $discount_pt = wc_add_number_precision($discount_val * $converted_rate);
                        // Discount
                        $discounted_new_price = wc_price($discount_val) . $cart_text;
                    }

                    $discounted_new_price_bt = $discounted_new_price;

                    // $discounted_new_price = (($pricing_table_price - $discount_pt) > 0) ? wc_price ( wc_remove_number_precision ( $pricing_table_price - $discount_pt ) * $converted_rate ) : 0;

                    if ( $value_display == 'discount_value' || $value_display == 'discount_both' ) {
                        if ($discount_typ == 'percentage') {
                            $discounted_new_price = (float)$discount_val . $prcn_text;
                        } else if ($discount_typ == 'fixed') {
                            $discounted_new_price = wc_price($discount_val) . $fxd_text_two;
                        }
                    }

                    // Pricing Table Calculations
                    if ($item->is_type('variable')) { 

                        // Variation Pricing Table
                        $price_to_discount_max = $variation_prices ? max($variation_prices) : 0;
                        $price_to_discount_min = $variation_prices ? min($variation_prices) : 0;

                        if ($discount_typ == 'percentage') {
                            $discount_max_value = $price_to_discount_max * ((float)$discount_val / 100);
                            $discount_min_value = $price_to_discount_min * ((float)$discount_val / 100);
                            // $discount_max_value = min($price_to_discount, $discount_pt);
                        } else if ($discount_typ == 'fixed') {
                            $discount_max_value = wc_add_number_precision($discount_val);
                            $discount_min_value = wc_add_number_precision($discount_val);
                        }

                        $discounted_new_max_price = (($price_to_discount_max - $discount_max_value) > 0) ? wc_price ( wc_remove_number_precision ( $price_to_discount_max - $discount_max_value ) ) : 0;
                        $discounted_new_min_price = (($price_to_discount_min - $discount_min_value) > 0) ? wc_price ( wc_remove_number_precision ( $price_to_discount_min - $discount_min_value ) ) : 0;

                        if ($table_layout == 'horizontal') {

                            if ( $quantity_rule['start_range'] == $quantity_rule['end_range'] ) {
                                $tr_qn .= '<td>' . $quantity_rule['start_range'] . '</td>';
                            } else if ($quantity_rule['end_range']) {
                                $tr_qn .= '<td>' . $quantity_rule['start_range'] . ' - ' . $quantity_rule['end_range'] . '</td>';
                            } else {
                                $tr_qn .= '<td>' . $quantity_rule['start_range'] . ' +</td>';
                            }
                            if ( $value_display == 'discount_value' ) {
                                if ($discount_typ == 'percentage') {
                                    $tr_pr .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                } else if ($discount_typ == 'fixed') {
                                    $tr_pr .= '<td>' . wc_price((float)$discount_val) . $fxd_text_two . '</td>';
                                }
                            } else if ( $value_display == 'discount_both' ) { // Display Both Price and Value
                                if ($discount_typ == 'percentage') {
                                    $tr_pr .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                } else if ($discount_typ == 'fixed') {
                                    $tr_pr .= '<td>' . wc_price((float)$discount_val) . $fxd_text_two . '</td>';
                                }
                                if ( $discounted_new_min_price == $discounted_new_max_price ) {
                                    $tr_nw .= '<td>' . $discounted_new_min_price . '</td>';
                                } else {
                                    $tr_nw .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                }
                            } else {
                                if ( $discounted_new_min_price == $discounted_new_max_price ) {
                                    $tr_pr .= '<td>' . $discounted_new_min_price . '</td>';
                                } else {
                                    $tr_pr .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                }
                            }

                        } else {

                            if ( $quantity_rule['start_range'] == $quantity_rule['end_range'] ) {
                                $table .= '<tr>';
                                $table .= '<td>' . $quantity_rule['start_range'] . '</td>';
                                if ( $value_display == 'discount_value' ) {
                                    if ($discount_typ == 'percentage') {
                                        $table .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                    } else if ($discount_typ == 'fixed') {
                                        $table .= '<td>' . wc_price((float)$discount_val) . $fxd_text_two . '</td>';
                                    }
                                } else if ( $value_display == 'discount_both' ) {
                                    if ( $discount_typ == 'percentage' ) {
                                        $table .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                    } else if ($discount_typ == 'fixed') {
                                        $table .= '<td>' . wc_price((float)$discount_val) . $fxd_text_two . '</td>';
                                    }
                                    if ( $discounted_new_min_price == $discounted_new_max_price ) {
                                        $table .= '<td>' . $discounted_new_min_price . '</td>';
                                    } else {
                                        $table .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                    }
                                } else {
                                    if ( $discounted_new_min_price == $discounted_new_max_price ) {
                                        $table .= '<td>' . $discounted_new_min_price . '</td>';
                                    } else {
                                        $table .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                    }
                                }
                                $table .= '</tr>';
                            } else if ( $quantity_rule['end_range'] ) {
                                $table .= '<tr>';
                                $table .= '<td>' . $quantity_rule['start_range'] . ' - ' . $quantity_rule['end_range'] . '</td>';
                                if ( $value_display == 'discount_value' ) {
                                    if ($discount_typ == 'percentage') {
                                        $table .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                    } else if ($discount_typ == 'fixed') {
                                        $table .= '<td>' . wc_price((float)$discount_val) . $fxd_text_two . '</td>';
                                    }
                                } else if ( $value_display == 'discount_both' ) {
                                    if ( $discount_typ == 'percentage' ) {
                                        $table .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                    } else if ($discount_typ == 'fixed') {
                                        $table .= '<td>' . wc_price((float)$discount_val) . $fxd_text_two . '</td>';
                                    }
                                    if ( $discounted_new_min_price == $discounted_new_max_price ) {
                                        $table .= '<td>' . $discounted_new_min_price . '</td>';
                                    } else {
                                        $table .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                    }
                                } else {
                                    if ( $discounted_new_min_price == $discounted_new_max_price ) {
                                        $table .= '<td>' . $discounted_new_min_price . '</td>';
                                    } else {
                                        $table .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                    }
                                }
                                $table .= '</tr>';
                            } else {
                                $table .= '<tr>';
                                $table .= '<td>' . $quantity_rule['start_range'] . ' +</td>';
                                if ( $value_display == 'discount_value' ) {
                                    if ($discount_typ == 'percentage') {
                                        $table .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                    } else if ($discount_typ == 'fixed') {
                                        $table .= '<td>' . wc_price((float)$discount_val) . $fxd_text_two . '</td>';
                                    }
                                } else if ( $value_display == 'discount_both' ) {
                                    if ($discount_typ == 'percentage') {
                                        $table .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                    } else if ($discount_typ == 'fixed') {
                                        $table .= '<td>' . wc_price((float)$discount_val) . $fxd_text_two . '</td>';
                                    }
                                    if ($discounted_new_min_price == $discounted_new_max_price) {
                                        $table .= '<td>' . $discounted_new_min_price . '</td>';
                                    } else {
                                        $table .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                    }
                                } else {
                                    if ($discounted_new_min_price == $discounted_new_max_price) {
                                        $table .= '<td>' . $discounted_new_min_price . '</td>';
                                    } else {
                                        $table .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                    }
                                }
                                $table .= '</tr>';
                            }

                        }

                    } else {

                        if ( $table_layout == 'horizontal' ) {
                            if ($quantity_rule['start_range'] == $quantity_rule['end_range']) {
                                $tr_qn .= '<td>' . $quantity_rule['start_range'] . '</td>';
                            } else if ($quantity_rule['end_range']) {
                                $tr_qn .= '<td>' . $quantity_rule['start_range'] . ' - ' . $quantity_rule['end_range'] . '</td>';
                            } else {
                                $tr_qn .= '<td>' . $quantity_rule['start_range'] . ' +</td>';
                            }
                            $tr_pr .= '<td>' . $discounted_new_price . '</td>';
                            if ( $value_display == 'discount_both' ) {
                                $tr_nw .= '<td>' . $discounted_new_price_bt . '</td>';
                            }
                        } else {
                            if ($quantity_rule['start_range'] == $quantity_rule['end_range']) {
                                if ( $value_display == 'discount_both' ) {
                                    $table .= '<tr><td>' . $quantity_rule['start_range'] . '</td><td>' . $discounted_new_price . '</td><td>' . $discounted_new_price_bt . '</td></tr>';
                                } else {
                                    $table .= '<tr><td>' . $quantity_rule['start_range'] . '</td><td>' . $discounted_new_price . '</td></tr>';
                                }
                            } else if ($quantity_rule['end_range']) {
                                if ( $value_display == 'discount_both' ) {
                                    $table .= '<tr><td>' . $quantity_rule['start_range'] . ' - ' . $quantity_rule['end_range'] . '</td><td>' . $discounted_new_price . '</td><td>' . $discounted_new_price_bt . '</td></tr>';
                                } else {
                                    $table .= '<tr><td>' . $quantity_rule['start_range'] . ' - ' . $quantity_rule['end_range'] . '</td><td>' . $discounted_new_price . '</td></tr>';
                                }
                            } else {
                                if ( $value_display == 'discount_both' ) {
                                    $table .= '<tr><td>' . $quantity_rule['start_range'] . ' +</td><td>' . $discounted_new_price . '</td><td>' . $discounted_new_price_bt . '</td></tr>';
                                } else {
                                    $table .= '<tr><td>' . $quantity_rule['start_range'] . ' +</td><td>' . $discounted_new_price . '</td></tr>';
                                }
                            }
                        }

                    }
                    // End PT

                } // End Foreach

                if ($table_layout == 'horizontal') {
                    $tr_qn .= '</tr>';
                    $tr_pr .= '</tr>';
                    if ( $value_display == 'discount_both' ) { 
                        $tr_nw .= '</tr>';
                        $table .= $tr_qn . $tr_pr . $tr_nw . '</tbody></table></div>';
                    } else {
                        $table .= $tr_qn . $tr_pr . '</tbody></table></div>';
                    }
                } else {
                    $table .= '</tbody></table></div>';
                }

                $table .= $discount_item_description ? '<p class="wdp_helpText">*'.$discount_item_description.'</p>' : '<p class="wdp_helpText">*'.$awdp_qn_label.' refers to discounted items (products with discount) total quantity on cart.</p>';

                // Pricinig Table
                if ($rule['pricing_table'] == 1 && $pricing_table_price > 0) $this->pricing_table[$item->get_id()][$rule['id']] = $table;
            
            } else if ( $quantity_type == 'type_product' ) { 

                /*
                * Product Quantity Discount *
                */ 

                $prod_QNT = [];
                $prod_QNTIDs = [];
                $item_id = $item->get_ID();
                $VCheckFlag = false;
                $VDisApplied = false;

                if ($table_layout == 'horizontal') {
                    $table .= '<div class="wdp_table_outter"><h4>' . $awdp_pc_title . '</h4><table class="wdp_table lay_horzntl"><tbody class="wdp_table_body">';
                    $tr_qn = '<tr><td>' . $awdp_qn_label . '</td>';
                    $tr_pr = '<tr><td>' . $awdp_pc_label . '</td>';
                    if ( $value_display == 'discount_both' ) {
                        $tr_nw = '<tr><td>' . $awdp_nw_label . '</td>';
                    }
                } else {
                    if ( $value_display == 'discount_both' ) {
                        $table .= '<div class="wdp_table_outter"><h4>' . $awdp_pc_title . '</h4><table class="wdp_table"><thead><tr class="wdp_table_head"><td>' . $awdp_qn_label . '*' . '</td><td>' . $awdp_pc_label . '</td><td>' . $awdp_nw_label . '</td></tr></thead><tbody class="wdp_table_body">';
                    } else {
                        $table .= '<div class="wdp_table_outter"><h4>' . $awdp_pc_title . '</h4><table class="wdp_table"><thead><tr class="wdp_table_head"><td>' . $awdp_qn_label . '</td><td>' . $awdp_pc_label . '</td></tr></thead><tbody class="wdp_table_body">';
                    }
                }
                
                foreach ($quantity_rules as $quantity_rule) {

                    $discount_val = $quantity_rule['dis_value'];
                    $discount_typ = $quantity_rule['dis_type'];
                    // $discount = '';

                    // $converted_rate = $this->converted_rate;
                    $converted_rate = 1; // Removing coupon amount - get price changed to $price - calculate total

                    // Pricing Table Calculations
                    if ($item->is_type('variable')) {

                        // Variation Pricing Table
                        $price_to_discount_max = $variation_prices ? max($variation_prices) : 0;
                        $price_to_discount_min = $variation_prices ? min($variation_prices) : 0;

                        if ($discount_typ == 'percentage') {
                            $discount_max_value = $price_to_discount_max * ((float)$discount_val / 100);
                            $discount_min_value = $price_to_discount_min * ((float)$discount_val / 100);
                            // $discount_max_value = min($price_to_discount, $discount_pt);
                        } else if ($discount_typ == 'fixed') {
                            $discount_max_value = wc_add_number_precision($discount_val);
                            $discount_min_value = wc_add_number_precision($discount_val);
                        }

                        $discounted_new_max_price = (($price_to_discount_max - $discount_max_value) > 0) ? wc_price ( wc_remove_number_precision ( $price_to_discount_max - $discount_max_value ) ) : 0;
                        $discounted_new_min_price = (($price_to_discount_min - $discount_min_value) > 0) ? wc_price ( wc_remove_number_precision ( $price_to_discount_min - $discount_min_value ) ) : 0;

                        if ($table_layout == 'horizontal') {

                            if ( $quantity_rule['start_range'] == $quantity_rule['end_range'] ) {
                                $tr_qn .= '<td>' . $quantity_rule['start_range'] . '</td>';
                            } else if ($quantity_rule['end_range']) {
                                $tr_qn .= '<td>' . $quantity_rule['start_range'] . ' - ' . $quantity_rule['end_range'] . '</td>';
                            } else {
                                $tr_qn .= '<td>' . $quantity_rule['start_range'] . ' +</td>';
                            }
                            if ( $value_display == 'discount_value' ) {
                                if ($discount_typ == 'percentage') {
                                    $tr_pr .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                } else if ($discount_typ == 'fixed') {
                                    $tr_pr .= '<td>' . wc_price((float)$discount_val) . $fxd_text_two . '</td>';
                                }
                            } else if ( $value_display == 'discount_both' ) { // Display Both Price and Value
                                if ($discount_typ == 'percentage') {
                                    $tr_pr .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                } else if ($discount_typ == 'fixed') {
                                    $tr_pr .= '<td>' . wc_price((float)$discount_val) . $fxd_text_two . '</td>';
                                }
                                if ( $discounted_new_min_price == $discounted_new_max_price ) {
                                    $tr_nw .= '<td>' . $discounted_new_min_price . '</td>';
                                } else {
                                    $tr_nw .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                }
                            } else {
                                if ( $discounted_new_min_price == $discounted_new_max_price ) {
                                    $tr_pr .= '<td>' . $discounted_new_min_price . '</td>';
                                } else {
                                    $tr_pr .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                }
                            }

                        } else {

                            if ( $quantity_rule['start_range'] == $quantity_rule['end_range'] ) {
                                $table .= '<tr>';
                                $table .= '<td>' . $quantity_rule['start_range'] . '</td>';
                                if ( $value_display == 'discount_value' ) {
                                    if ($discount_typ == 'percentage') {
                                        $table .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                    } else if ($discount_typ == 'fixed') {
                                        $table .= '<td>' . wc_price((float)$discount_val) . $fxd_text_two . '</td>';
                                    }
                                } else if ( $value_display == 'discount_both' ) {
                                    if ( $discount_typ == 'percentage' ) {
                                        $table .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                    } else if ($discount_typ == 'fixed') {
                                        $table .= '<td>' . wc_price((float)$discount_val) . $fxd_text_two . '</td>';
                                    }
                                    if ( $discounted_new_min_price == $discounted_new_max_price ) {
                                        $table .= '<td>' . $discounted_new_min_price . '</td>';
                                    } else {
                                        $table .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                    }
                                } else {
                                    if ( $discounted_new_min_price == $discounted_new_max_price ) {
                                        $table .= '<td>' . $discounted_new_min_price . '</td>';
                                    } else {
                                        $table .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                    }
                                }
                                $table .= '</tr>';
                            } else if ( $quantity_rule['end_range'] ) {
                                $table .= '<tr>';
                                $table .= '<td>' . $quantity_rule['start_range'] . ' - ' . $quantity_rule['end_range'] . '</td>';
                                if ( $value_display == 'discount_value' ) {
                                    if ($discount_typ == 'percentage') {
                                        $table .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                    } else if ($discount_typ == 'fixed') {
                                        $table .= '<td>' . wc_price((float)$discount_val) . $fxd_text_two . '</td>';
                                    }
                                } else if ( $value_display == 'discount_both' ) {
                                    if ( $discount_typ == 'percentage' ) {
                                        $table .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                    } else if ($discount_typ == 'fixed') {
                                        $table .= '<td>' . wc_price((float)$discount_val) . $fxd_text_two . '</td>';
                                    }
                                    if ( $discounted_new_min_price == $discounted_new_max_price ) {
                                        $table .= '<td>' . $discounted_new_min_price . '</td>';
                                    } else {
                                        $table .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                    }
                                } else {
                                    if ( $discounted_new_min_price == $discounted_new_max_price ) {
                                        $table .= '<td>' . $discounted_new_min_price . '</td>';
                                    } else {
                                        $table .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                    }
                                }
                                $table .= '</tr>';
                            } else {
                                $table .= '<tr>';
                                $table .= '<td>' . $quantity_rule['start_range'] . ' +</td>';
                                if ( $value_display == 'discount_value' ) {
                                    if ($discount_typ == 'percentage') {
                                        $table .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                    } else if ($discount_typ == 'fixed') {
                                        $table .= '<td>' . wc_price((float)$discount_val) . $fxd_text_two . '</td>';
                                    }
                                } else if ( $value_display == 'discount_both' ) {
                                    if ($discount_typ == 'percentage') {
                                        $table .= '<td>' .(float)$discount_val . $prcn_text . '</td>';
                                    } else if ($discount_typ == 'fixed') {
                                        $table .= '<td>' . wc_price((float)$discount_val) . $fxd_text_two . '</td>';
                                    }
                                    if ($discounted_new_min_price == $discounted_new_max_price) {
                                        $table .= '<td>' . $discounted_new_min_price . '</td>';
                                    } else {
                                        $table .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                    }
                                } else {
                                    if ($discounted_new_min_price == $discounted_new_max_price) {
                                        $table .= '<td>' . $discounted_new_min_price . '</td>';
                                    } else {
                                        $table .= '<td>' . $discounted_new_min_price . ' - ' . $discounted_new_max_price . '</td>';
                                    }
                                }
                                $table .= '</tr>';
                            }

                        }

                    } else {

                        if ($discount_typ == 'percentage') {
                            $discount_pt = $pricing_table_price * ((float)$discount_val / 100);
                            $discount_pt = min($pricing_table_price, $discount_pt);
                        } else if ($discount_typ == 'fixed') {
                            $discount_pt = wc_add_number_precision($discount_val * $converted_rate);
                        }

                        $discounted_new_price = (($pricing_table_price - $discount_pt) > 0) ? wc_price ( wc_remove_number_precision ( $pricing_table_price - $discount_pt ) * $converted_rate ) : 0;

                        $discounted_new_price_bt = $discounted_new_price;

                        if ( $value_display == 'discount_value' || $value_display == 'discount_both' ) {
                            if ($discount_typ == 'percentage') {
                                $discounted_new_price = (float)$discount_val . $prcn_text;
                            } else if ($discount_typ == 'fixed') {
                                $discounted_new_price =  wc_price((float)$discount_val) . $fxd_text_two;
                            }
                        }

                        if ( $table_layout == 'horizontal' ) {
                            if ($quantity_rule['start_range'] == $quantity_rule['end_range']) {
                                $tr_qn .= '<td>' . $quantity_rule['start_range'] . '</td>';
                            } else if ($quantity_rule['end_range']) {
                                $tr_qn .= '<td>' . $quantity_rule['start_range'] . ' - ' . $quantity_rule['end_range'] . '</td>';
                            } else {
                                $tr_qn .= '<td>' . $quantity_rule['start_range'] . ' +</td>';
                            }
                            $tr_pr .= '<td>' . $discounted_new_price . '</td>';
                            if ( $value_display == 'discount_both' ) {
                                $tr_nw .= '<td>' . $discounted_new_price_bt . '</td>';
                            }
                        } else {
                            if ($quantity_rule['start_range'] == $quantity_rule['end_range']) {
                                if ( $value_display == 'discount_both' ) {
                                    $table .= '<tr><td>' . $quantity_rule['start_range'] . '</td><td>' . $discounted_new_price . '</td><td>' . $discounted_new_price_bt . '</td></tr>';
                                } else {
                                    $table .= '<tr><td>' . $quantity_rule['start_range'] . '</td><td>' . $discounted_new_price . '</td></tr>';
                                }
                            } else if ($quantity_rule['end_range']) {
                                if ( $value_display == 'discount_both' ) {
                                    $table .= '<tr><td>' . $quantity_rule['start_range'] . ' - ' . $quantity_rule['end_range'] . '</td><td>' . $discounted_new_price . '</td><td>' . $discounted_new_price_bt . '</td></tr>';
                                } else {
                                    $table .= '<tr><td>' . $quantity_rule['start_range'] . ' - ' . $quantity_rule['end_range'] . '</td><td>' . $discounted_new_price . '</td></tr>';
                                }
                            } else {
                                if ( $value_display == 'discount_both' ) {
                                    $table .= '<tr><td>' . $quantity_rule['start_range'] . ' +</td><td>' . $discounted_new_price . '</td><td>' . $discounted_new_price_bt . '</td></tr>';
                                } else {
                                    $table .= '<tr><td>' . $quantity_rule['start_range'] . ' +</td><td>' . $discounted_new_price . '</td></tr>';
                                }
                            }
                        }

                    }

                } 

                if ($table_layout == 'horizontal') {
                    $tr_qn .= '</tr>';
                    $tr_pr .= '</tr>';
                    if ( $value_display == 'discount_both' ) { 
                        $tr_nw .= '</tr>';
                        $table .= $tr_qn . $tr_pr . $tr_nw . '</tbody></table></div>';
                    } else {
                        $table .= $tr_qn . $tr_pr . '</tbody></table></div>';
                    }
                } else {
                    $table .= '</tbody></table></div>';
                }

                // Pricinig Table
                if ( $rule['pricing_table'] == 1 && $pricing_table_price > 0) $this->pricing_table[$item->get_id()][$rule['id']] = $table;

            } 
                    
        } 

        return $table;

    }

}