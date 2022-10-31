<?php


add_action('wp_ajax_verify_product_swatches', 'customize_product_options_feedback');
add_action('wp_ajax_nopriv_verify_product_swatches', 'customize_product_options_feedback');
function customize_product_options_feedback() {
    
    // print_r($_REQUEST);
    $vs = is_array($_REQUEST['variation_swcv']) && !empty($_REQUEST['variation_swcv']) ? $_REQUEST['variation_swcv'] : false;

    $post_id = is_numeric($_REQUEST['post_id']) ? $_REQUEST['post_id'] : false;
    
    if(!$post_id || !$vs) {
        wp_die('error fields');
    }
    
    global $wpdb;
    $new_prev = array();
    foreach($vs as $attr => $options) {
        $new_prev[$attr] = [];
        foreach($options as $option) {
            $term_1 = get_term_by('name', $option, $attr);

            foreach($vs as $attr_2 => $options_2 ){
                if( $attr != $attr_2 ):
                     $new_prev[$attr][$option][$attr_2] = array();
                    foreach($options_2 as $option_2) {
                        $term_2 = get_term_by('name', $option_2, $attr_2);
               
                        



                            $product = wc_get_product($post_id);
                            
                            $available_variations = $product->get_available_variations();
                            $new_prev[$attr][$option][$attr_2][$term_2->name] = 'outofvariation';
                            foreach($available_variations as $available_variation) {
                                // print_r($available_variation);
                                // echo '<br/>' . $attr . '<br/>';
                                if( (isset($available_variation['attributes']['attribute_'.$attr]) && $available_variation['attributes']['attribute_'.$attr] == $term_1->slug)
                                    &&
                                     (isset($available_variation['attributes']['attribute_'.$attr_2]) && $available_variation['attributes']['attribute_'.$attr_2] == $term_2->slug)
                                ) {
                                    
                                    if( $available_variation['max_qty'] < 1 ) {
                                        $new_prev[$attr][$option][$attr_2][$term_2->name] = 'outofstock';

                                    } else {
                                        $new_prev[$attr][$option][$attr_2][$term_2->name] = 'in_stock_'.$available_variation['max_qty'];

                                    }
                                    
                                }
                            }
                           

//                      END
                     
                    }
                    
                endif;
            }
        }
    }
    
    
    
    echo json_encode($new_prev);
    wp_die();
    
}





add_action('wp_footer', 'customize_product_options_script'); 

function customize_product_options_script() {

    // PRODUCT PAGE
    if(is_product()) {
        global $post;
       echo "
       
       <script>
        
        jQuery('.reset_variations').on('click', function () {
            var _a = jQuery(this);
            jQuery('.wcboost-variation-swatches li').each(function () {
                jQuery(this).removeAttr('style');
            });
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
                    jQuery('.blockUI.blockOverlay.addNewVars', jQuery('table.variations').parent()).remove();
                jQuery('table.variations').attr('style', '');

                    options_av = res;
                    if(Object.keys(res).length > 0) {
                       for ( var key in res ) {
                           console.log(key, res[key]);
                           console.log(jQuery('#'+key));
                           jQuery('#'+key).on('change', function () {
                               var _select = jQuery(this),
                               value = _select.val(),
                               key = _select.attr('id'),
                               selected = _select[0].options[_select[0].selectedIndex],
                               value = jQuery(selected).text();
                              
                              var others = options_av[key][value];
                              
                             console.log(_select[0].selectedIndex);
                              if( _select[0].selectedIndex == 0  ) {
                                  jQuery('.wcboost-variation-swatches__item').removeAttr('style');
                              } else {
                                  for( var ko in others ) {
                                    
                                     for(var ro in others[ko]) {
                                         if( others[ko][ro] == 'outofvariation' ) {
                                          jQuery('#'+ko).parent().find('[aria-label=\"'+ro+'\"]').removeAttr('style').css(".'{"display": "none" }'.");
                                         } else if(  others[ko][ro] == 'outofstock'  ) {
                                          jQuery('#'+ko).parent().find('[aria-label=\"'+ro+'\"]').removeAttr('style').css(".'{"filter": " grayscale(1)", "textDecoration":"line-through", "pointerEvents": "none", "opacity": "1" }'.");
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
                   console.log(res); 
                });
            }
            console.log(variation_swcv);
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

