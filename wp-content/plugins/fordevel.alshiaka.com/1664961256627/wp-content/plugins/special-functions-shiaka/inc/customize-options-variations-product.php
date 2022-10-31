<?php


add_action('wp_ajax_verify_product_swatches', 'customize_product_options_feedback');
add_action('wp_ajax_nopriv_verify_product_swatches', 'customize_product_options_feedback');
function customize_product_options_feedback() {
    
    // print_r($_REQUEST);
        $vs = is_array($_REQUEST['variation_swcv']) && !empty($_REQUEST['variation_swcv']) ? $_REQUEST['variation_swcv'] : false;
    
        $post_id = is_numeric($_REQUEST['post_id']) ? $_REQUEST['post_id'] : false;
        
        if(!$post_id || !$vs || !is_array($vs) || count($vs) < 1) {
            wp_die('error fields');
        }
    
        $product = wc_get_product($post_id);
        
        $available_variations = $product->get_available_variations();
    
            
        $swatches = array();
        foreach($available_variations as $available_variation) {
          
            foreach($available_variation['attributes'] as $variation_attr => $variation_slug):
                foreach($available_variation['attributes'] as $variation_attr_2 => $variation_slug_2):
                    if( $variation_attr != $variation_attr_2 ){
                        $slug_term = substr($variation_attr, strpos($variation_attr, 'pa'));
                        $slug_term_2 = substr($variation_attr_2, strpos($variation_attr_2, 'pa'));
                        
                        if( $available_variation['max_qty'] < 1 ) {
                            $swatches[$slug_term][$variation_slug][$slug_term_2][$variation_slug_2] = 'outofstock';
                        
                        } else {
                            $swatches[$slug_term][$variation_slug][$slug_term_2][$variation_slug_2] = 'in_stock_'.$available_variation['max_qty'];
                        
                        }
                    }

                endforeach;
            endforeach;
        }
        

        $new_res = array();
        if(!empty($swatches)) {
            foreach($vs as $attr => $options) {
                $new_res[$attr] = [];
                foreach($options as $option) {
                    $term_1 = get_term_by('name', $option, $attr);
                    foreach($vs as $attr_2 => $options_2 ){
                        if( $attr != $attr_2 ):
                             $new_res[$attr][$option][$attr_2] = array();
                            foreach($options_2 as $option_2) {
                                    $term_2 = get_term_by('name', $option_2, $attr_2);
            
                                    if(isset($swatches[$attr][$term_1->slug][$attr_2][$term_2->slug])):
                                        
                                        $var_status = $swatches[$attr][$term_1->slug][$attr_2][$term_2->slug];
                                    else:
                                        $var_status = 'outofvariation';
                                    endif;
                                    $new_res[$attr][$option][$attr_2][$term_2->name] = $var_status;
    
                                   
        
        //                      END
                             
                            }
                            
                        endif;
                    }
                }
            }

        }
        
    
    echo json_encode($new_res);
    wp_die();
    
}





add_action('wp_footer', 'customize_product_options_script'); 

function customize_product_options_script() {

    // PRODUCT PAGE
    if(is_product()) {
        global $post;
        $product = wc_get_product( $post->ID );
        $price  = $product->get_price();

       echo "
       
       <script>
       
       setTimeout(function () {
           if( jQuery(`[name='quantity']`).attr('min') == '3'  ) {
               jQuery(`<div id='showPriceQty' class='woocommerce-variation-price-show-3p' data-one-slice='".$price."'> <span class='custom-qty-show-3p'>x 3: </span> ".wc_price((int)$price*3)."</div>`).insertBefore(jQuery('.woocommerce-variation-add-to-cart'));
           
            //   jQuery('.woocommerce-variation-add-to-cart').before('#showPriceQty');
           }
       }, 1000);
        
        function removeVarsCustomization(key = '', val) {
           console.log(val);
           
           
           
            if( jQuery('.wcboost-variation-swatches li.selected').length == 0 ) {
                jQuery('.wcboost-variation-swatches li').each(function () {
                    jQuery(this).removeAttr('style');
                });
                console.log(jQuery('.wcboost-variation-swatches li.selected').length);
            } else {
                var elementsToReset = jQuery('.variations_form select'),
                elementsToResetCount = elementsToReset.length,
                elementsToResetStart = 0;
                console.log(elementsToResetStart, elementsToResetCount);

                var resetTimeToCustomize = setInterval(function () {
                    if (  elementsToResetCount > elementsToResetStart ) {
                        clearInterval(resetTimeToCustomize);
                    }
                    
                    if( jQuery(elementsToReset[elementsToResetStart]).length > 0 ) {
                        jQuery(elementsToReset[elementsToResetStart]).change();
                    }
                }, 300);
                
            }
            
            


        }
        
        
        
        jQuery('.reset_variations').on('click', function () {
            removeVarsCustomization();
        });
        var options_av = {};
        var variation_swcv = {};

        setTimeout(function () {
            jQuery('.wcboost-variation-swatches').each(function () {
                var _swatche = jQuery(this),
                _attr = jQuery('select', _swatche),
                attr = _attr.attr('id'),
                values = [];
                jQuery('ul li', _swatche).each(function () {
                    var _li = jQuery(this);
                    values.push(_li.text().trim());
                });    
                variation_swcv[attr] = values;
            });
            function sendFormVars() {
                jQuery('table.variations').parent().css(".'{"position":"relative"}'.");
                jQuery('table.variations').css(".'{"filter": "opacity(0.5)", "cursor": "not-allowed", "pointer-events": "none"}'.");
                jQuery('table.variations').parent().append(jQuery(`".'<div class="blockUI blockOverlay addNewVars" style="z-index: 1000; border: none; margin: 0px; padding: 0px; width: 100%; height: 100%; top: 0px; left: 0px; background: rgb(255, 255, 255); opacity: 0.4; cursor: default; position: absolute;"></div>'."`));
                var form_data = {variation_swcv, action: 'verify_product_swatches', post_id: ".$post->ID."}
                jQuery.ajax({
                    url: '".admin_url('admin-ajax.php')."',
                    type: 'POST',
                    data: form_data,
                    dataType: 'json',
                    async: true
                })
                .done(function ajaxDone(res) {

                    options_av = res;
                    if(Object.keys(res).length > 0) {
                       for ( var key in res ) {
                           jQuery('#'+key).on('change', function () {
                               var _select = jQuery(this),
                               value = _select.val(),
                               key = _select.attr('id'),
                               selected = _select[0].options[_select[0].selectedIndex],
                               value = jQuery(selected).text();
                              
                              var others = options_av[key][value];
                              
                              
                              var optionsToSelect = jQuery('select[data-attribute_name]'),
                              optionsSelected = [];
                              
                              if( _select.val() == '' ) {
                                removeVarsCustomization(key, _select.val() );
                              } else {
                                for( var ko in others ) {
                                                                
                                 for(var ro in others[ko]) {
                                     if( others[ko][ro] == 'outofvariation' ) {
                                      jQuery('#'+ro).val('');
                                      jQuery('#'+ko).parent().find('[aria-label=\"'+ro+'\"]').removeClass('selected').removeClass('disabled').removeAttr('style').css(".'{"display": "none" }'.");
                                     } else if(  others[ko][ro] == 'outofstock'  ) {
                                      jQuery('#'+ro).val('');
                                      jQuery('#'+ko).parent().find('[aria-label=\"'+ro+'\"]').removeClass('selected').removeClass('disabled').removeAttr('style').css(".'{"filter": " contrast(0)", "textDecoration":"line-through", "pointerEvents": "none", "textDecorationThickness": "2.1px", "border": "1px solid #ddd", "boxShadow":"none" }'.");
                                     } else {
                                     
                                      jQuery('#'+ko).parent().find('[aria-label=\"'+ro+'\"]').removeAttr('style');
                                
                                     }
                                 }
                                
                                } 
                              }
                              
                                
                      

                                
                           });
                       }
                    }
                })
                .fail(function ajaxFailed(res){
                    
                })
                .always(function ajaxAlways(res) {
                    jQuery('.blockUI.blockOverlay.addNewVars', jQuery('table.variations').parent()).remove();
                    jQuery('table.variations').attr('style', '');

                });
            }
            
            if( Object.keys(variation_swcv).length > 0 ) {
               sendFormVars()
            } 
        }, 200);
        
        
        
        
        
        
        
        
        
        
       
            if( jQuery('.single-product').length > 0  ) {
                var scroll = window.scrollY;
                jQuery(window).scroll(function () {
                    if( window.scrollY > scroll ) {
                        if( window.scrollY >= 359 ) {
                            var style = '.single-product .site-content{padding-top: 126px;}';                        
                        } else {
                            var style = '';
                        }
                         jQuery('#fixed_interact').html('.header-sticky:not(.header-v6) .site-header.minimized{top: -300px;}'+style);
                    } else {
                        jQuery('#fixed_interact').html('');
                    }
                    scroll = window.scrollY;
                });   
            }

       </script>
        <style>
            @media(min-width: 991px) {
                div#site-header-minimized[style] { height: 200px !important; }
            }
            .header-sticky:not(.header-v6) .site-header.minimized{
                    transition: all .5s .1s ease-in-out;
            }
        </style>
        <style id='fixed_interact'>
            .header-sticky:not(.header-v6) .site-header.minimized{
                    position: relative;
            }
        </style>
       "; 
    }
    
}

