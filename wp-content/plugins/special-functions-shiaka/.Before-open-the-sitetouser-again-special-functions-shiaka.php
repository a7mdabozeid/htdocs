<?php
/*
 * Plugin Name:       Special functions shiaka
 * Description:       Default weight in adimin, Hide weight in prodcut page & auto apply coupons for emails, customize discount prices, custome zones and cities, hide flat shipping when free is available, operation and customer service roles cannot update products | orders 
 * Author:            Digital Partners Team. Ali Alanzan
 * 
*/


// if this file is called firectly, abort!
defined('ABSPATH') or die();




/**
 * The code that runs during plugin activation
 */
function activate_special_functions_shiaka_plugin()
{
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'activate_special_functions_shiaka_plugin' );
/**
 * The code that runs during plugin deactivation
 */
function deactivate_special_functions_shiaka_plugin()
{
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'deactivate_special_functions_shiaka_plugin' );




include_once 'inc/custom-zones-cities.php';

include_once 'inc/hide-flat-shipping-when-free-is-available.php';

include_once 'inc/set-product-weight.php';



include_once 'inc/cart-items-in-checkout-coupon-customizing.php';

include_once 'inc/apply-auto-coupon-for-allowed-emails.php';



include_once 'inc/orders-products-readonly-not-admin-roles.php';

include_once 'inc/customize-options-variations-product.php';



include_once 'inc/add_field_link_to_image_in_gallery.php';






 
add_filter( 'woocommerce_product_add_to_cart_text', 'bbloomer_change_select_options_button_text', 9999, 2 );
 
function bbloomer_change_select_options_button_text( $label, $product ) {
    return __('Add to cart', 'woocommerce');

   return $label;
}



add_action('wp_ajax_get_wishlist_count', 'get_wishlist_count_method');
add_action('wp_ajax_nopriv_get_wishlist_count', 'get_wishlist_count_method');
function get_wishlist_count_method() {
 if(function_exists('yith_wcwl_count_all_products')) {
    wp_send_json( array(
        'count' => yith_wcwl_count_all_products()
    ) );
 }
    wp_die();
}




// define the in_admin_footer callback 
function action_in_admin_footer_print_pdf_custom_file_load() { 
    global $pagenow, $current_page, $post, $user;

    if( $pagenow == 'post.php' && !empty( $_GET['action'] ) && $_GET['action'] == 'edit' && $post->post_type == 'shop_order' ) {
        

echo '
                <script>
                                
               
                  jQuery("#wpo_wcpdf-box .wpo_wcpdf-actions > li:first-of-type > a").on("click", async function (e) {
                                       e.preventDefault();

                  try {
 
                    let response = await fetch(jQuery(this).attr("href")); 

                    var printWindow = window.open("", "","height=900,width=900" )
                    printWindow.document.write("<html><head><title>Html to PDF</title>");
                    printWindow.document.write("</head><body >");
                    printWindow.document.write(await response.text());
                    printWindow.document.write("</body></html>");
                    printWindow.document.close();
                    printWindow.print();
                  } catch (err) {
                    console.log("Fetch error:" + err);
                  }
                        fetch(jQuery(this).attr("href")).then(function (response){
                        
                         console.log(response)
                        
                        })
                      return false;
                  });
                </script>
            ';
    
    }
}; 

// add the action 
add_action( 'in_admin_footer', 'action_in_admin_footer_print_pdf_custom_file_load' ); 



// define the in_admin_footer callback 
function action_in_admin_footer_slider_image_gallery() { 
    global $pagenow, $current_page, $post, $user;

    if( $pagenow == 'post.php' && !empty( $_GET['action'] ) && $_GET['action'] == 'elementor' && $post->post_type == 'page' ) {
        
echo '
                <script>
                   setInterval(function () {
                        if( jQuery(\'[aria-labelledby="menu-item-gallery-edit"]\').length > 0 ) {
                            var _label = jQuery("[for=\'attachment-details-alt-text\']");
                            _label.text("'.__('Banner Link', 'shiaka').'");
                            jQuery(".alt-text-description").remove();
                        }
                   }, 10);
                </script>
            ';
    }
    
    
    
}; 

// add the action 
add_action( 'elementor/editor/before_enqueue_scripts', 'action_in_admin_footer_slider_image_gallery' ); 







add_action('wp_footer', 'script_implement'); 

function script_implement() {
    
    echo '
 <style>
         body {
                    display:none !important;
                }
    
        body.home {
            display: block !important;
        }
        
        .razzi-map__content.mapboxgl-map .mapboxgl-canvas-container canvas {
            display: none !important;
        }
        .razzi-map__content.mapboxgl-map .mapboxgl-canvas-container iframe {
            height: 100% !important;
            width: 100%  !important;
        }
        .razzi-map__content.mapboxgl-map .mapboxgl-canvas-container {
            padding: 0 !important;
            width: 100%;
            height: 100%;
        }


    </style>
    ';
       if( is_user_logged_in()    ) {
        //   && current_user_can('superadmin') && current_user_can('administrator')
        $user = wp_get_current_user();
        $ttimetoset = '';
        if( in_array('administrator', $user->roles)   )  {
            $ttimetoset = session_id().time();
          echo '
          <style id="'.$ttimetoset.'">
                        body {
                            display:block !important;
                        }
                    </style>
                    
            ';    
        }
    
  
        } else {
            
            
echo '
                <script>
                if(  !jQuery("body").hasClass("home") ) {
                    window.location.replace("https://alshiaka.com/?redirect=none");
                    
                }
                </script>
            ';
            
        }

    $options = get_settings('theme_mods_razzi')['footer_main_payment_images'];
    $options_json = json_encode($options);
    
    // echo '<pre style="display:none">';
    //     print_r($options);
    // echo '</pre>';
    
    $script = '';
    
    
    if( is_checkout() ) {
            // Redirect the mini-cart icon to the cart page in the checkout page
        $script .= '
        
            <script>
                var cartInterval = setInterval(function () {
                    if( jQuery(".header-cart").length > 0 ) {
                        jQuery(".header-cart > a[data-target=\"cart-modal\"]").removeAttr("data-toggle").removeAttr("data-target");
                        clearInterval(cartInterval);
                    }
                }, 100);
            </script>
        ';
            
    }
    
    
    // Disable mini-cart to the cart page in the cart page
    if( is_page( 'cart' ) || is_cart() ) {
        $script .= '
        
            <script>
                var cartInterval = setInterval(function () {
                    if( jQuery(".header-cart").length > 0 ) {
                        jQuery(".header-cart").css({"pointerEvents": "none"});
                        clearInterval(cartInterval);
                    } 
                }, 100);
            </script>
        ';
    }
    
    // Display block for the captcha with the guest refund form
    $script .= '
    
        <script>
            var refundFormCaptchaInterval = setInterval(function () {
                if( jQuery("input[name*=\"refund-guest\"]").length > 0 ) {
                    jQuery(".grecaptcha-badge").show();
                    clearInterval(refundFormCaptchaInterval);
                } 
            }, 100);
        </script>
            
    
    ';
    
    
    
    // Refund form translation
    $script .= '
        <script>
        var textsRefundForm = {
            "First Name": "'.__('First Name', 'shiaka').'",
            "Last name": "'.__('Last Name', 'shiaka').'",
            "Last Name": "'.__('Last Name', 'shiaka').'",
            "Email": "'.__('Email', 'shiaka').'",
            "Address": "'.__('Address', 'shiaka').'",
            "Phone": "'.__('Phone', 'shiaka').'",
            "Reason of refund": "'.__('Reason of refund', 'shiaka').'"
        };
        var _formRefund = jQuery("form.refund-front-form");
        if( _formRefund.length > 0 ) {
            jQuery("label", _formRefund).each(function () {
                var _label = jQuery(this)
                _span = jQuery("span", _label).length > 0 ? jQuery("span", _label).clone(true) : "",
                textLabel = _label.text().replace("*", "").trim();
                _label.html(textsRefundForm[textLabel]);
                _label.append(_span);
            });
        }
        </script>
    ';
    
    
    

        $script .= '
        
            <script>
            var i = 0;

            function changeCitiesWithKeys(i) {
            
                var formData = {
                    action: "get_cities_of_state_ajax_customized",
                    lang: "'.ICL_LANGUAGE_CODE.'",
                    state_code: jQuery("select#billing_address_1").val()
                }
                i++;
                
                jQuery("#billing_city").prop("disabled", true);
                 jQuery("#billing_city").css({
                    "backgroundColor": "rgb(19 48 80 / 35%)",
                    "color":"white",
                    "opacity":".5",
                    "pointerEvents": "none"
                });

                jQuery.ajax({
                    url: "'.admin_url("admin-ajax.php").'",
                    data: formData,
                    dataType: "json",
                    type: "POST",
                    async: true
                })
                .done(function (res) {
                    if(res.success != undefined) {
                        jQuery("#billing_city").html("");
                        for ( var k in res.data) {
                           
                            jQuery("#billing_city").append(jQuery(`<option value="`+k+`">`+res.data[k]+`</option>`));
                        }
                    }
                })
                .fail(function () {
                    if(i < 5) {
                        changeCitiesWithKeys(i);
                    }
                    
                })
                .always(function () {
                    jQuery("#billing_city").prop("disabled", false);
                     jQuery("#billing_city").attr("style", "");
                });
            }
            jQuery(function () {

                setTimeout(function () {
                    jQuery("select#billing_address_1").on("change", function () {
                            var _in = jQuery(this);
                            var _address = jQuery("#billing_address_2");
                            if( _address.val().search(_in.val()) < 0 ) {
                                _address.val("");
                            }
                            
                           changeCitiesWithKeys(i);
                    });
                    jQuery("select#billing_city").on("change", function () {
                            var _in = jQuery(this);
                            var _address = jQuery("#billing_address_2");
                            if( _address.val().search(_in.val()) < 0 ) {
                                _address.val("");
                            }
                    });
                    var _inHTML = jQuery(".autocomplete-selector-class .woocommerce-input-wrapper").html();
                    jQuery(_inHTML).attr("readonly");
                   
                    function initialize(){
                        if(google == undefined || google.maps.places.Autocomplete == undefined) {
                            return false;
                        }
                        var input=document.querySelector(".autocomplete-selector-class input");
                        var autocomplete = new google.maps.places.Autocomplete(input);
                        autocomplete.setComponentRestrictions({"country": ["SA"]});
                    }
                    var ccInitialize = setInterval(function () {
                        initialize();
                    }, 500);
                    setTimeout(function () {
                        clearInterval(ccInitialize);
                        jQuery(".autocomplete-selector-class input").removeAttr("readonly");
                    }, 5000, ccInitialize);
                    
                    
      
jQuery("input[name=\"payment_method\"]").each(function () {              jQuery(document).on("change", "#"+jQuery(this).attr("id") , function () {  jQuery("#ship-to-different-address-checkbox").change();  }); });

                }, 1000);
                
          

            }());
                

            </script>
        ';
    
    
    // Customize flamingo messages
    $script .= '
    
   
        
    <script>
        var frames_gmaps = [];
        if( jQuery(".razzi-map__table > .box-item").length > 0 ) {
            jQuery.ajax({
                url: "'.plugin_dir_url(__FILE__).'locatins-iframe.json",
                dataType: "json",
                type: "GET",
                async: true
            }).done(function ajaxDone(res) {
                console.log(res);
                frames_gmaps = res;
                var i = 0;
                jQuery(".razzi-map__table > .box-item").each(function () {
                    jQuery(this).attr("data-id-num", i);
                    jQuery(this).on("click", function () {
                        var _btn = jQuery(this);
                        jQuery(".razzi-map__content.mapboxgl-map .mapboxgl-canvas-container").html(frames_gmaps[_btn.attr("data-id-num")]);
                        
                    });
                    i++;
                });
                setTimeout(function () {
                    jQuery(".razzi-map__content.mapboxgl-map .mapboxgl-canvas-container").html(frames_gmaps[0]);
                }, 1000);
            });
        }
    
    
    if( jQuery("[name=\"firstname-refund-guest\"]").length > 0 ) {
        jQuery("#wpcf7-f598-p584-o1 form")
        .append(jQuery(`<input type="hidden" id="your-name" name="your-name" />`))
        .append(jQuery(`<input type="hidden" id="your-email" name="your-email" />`))
        .append(jQuery(`<input type="hidden" value="New Message" id="your-subject" name="your-subject" />`));
        
        jQuery(`[name="name-928"]`).on("keyup", function () {
            jQuery("#your-name").val(jQuery(this).val());
            jQuery("#your-subject").val("New Message by " + jQuery(this).val() + "<" +  jQuery(`[name="email-845"]`).val() + ">");
        });
        jQuery(`[name="email-845"]`).on("keyup", function () {
            jQuery("#your-email").val(jQuery(this).val());
            jQuery("#your-subject").val("New Message by "+jQuery(`[name="name-928"]`).val()+" <" + jQuery(this).val() + ">");
        });        
    }
    
    if( jQuery("input[name=\"firstname-refund-guest\"]").length > 0 ) {
        var _RefundGuestform = jQuery("input[name=\"firstname-refund-guest\"]").closest("form");
        _RefundGuestform.append(jQuery(`<input type="hidden" id="your-name" name="your-name" />`));
        _RefundGuestform.append(jQuery(`<input type="hidden" id="your-email" name="your-email" />`));
        _RefundGuestform.append(jQuery(`<input type="hidden" value="Client Return Order" id="your-subject" name="your-subject" />`));
    
    
        jQuery("input[name=\"orderid-refund-guest\"]", _RefundGuestform).on("keyup", function () {
        
            jQuery("#your-subject", _RefundGuestform).val("Client Return Order #"+jQuery(this).val());
        });
        
        jQuery("input[name=\"firstname-refund-guest\"]", _RefundGuestform).on("keyup", function () {
            jQuery("#your-name", _RefundGuestform).val(jQuery("input[name=\"firstname-refund-guest\"]", _RefundGuestform).val() + " " + jQuery("input[name=\"lastname-refund-guest\"]", _RefundGuestform).val());
        });
        
        jQuery("input[name=\"lastname-refund-guest\"]", _RefundGuestform).on("keyup", function () {
            jQuery("#your-name", _RefundGuestform).val(jQuery("input[name=\"firstname-refund-guest\"]", _RefundGuestform).val() + " " + jQuery("input[name=\"lastname-refund-guest\"]", _RefundGuestform).val());
        });
    
    }

    </script>
    
    ';
    
    
    
    // trans word Coupon checkout
    $script .= ' 
    
        <script>

            function checkPhoneNumberValidation() {
                var _in = jQuery("#billing_phone");
                if( _in.length <= 0  ) { 
                    return;
                }
                
                jQuery(".error", _in.parent()).remove();
                jQuery("button[type=\"submit\"]", _in.closest("form")).prop("disabled", false);

                var loadedPhoneValue = _in.val();
                if(loadedPhoneValue.indexOf("966") >= 0) {
                    restOfPhone = loadedPhoneValue.substr(loadedPhoneValue.indexOf("966")+3);
                    _in.val(restOfPhone);
                } else {
                    restOfPhone = loadedPhoneValue;
                }



                if( restOfPhone.search(/[^0-9\ ]/) != -1 || isNaN(restOfPhone.replaceAll(" ", "")) || restOfPhone.replaceAll(" ", "").length != 9 ) {
                    jQuery("<span class=\"error\" style=\"color:red;\">'.__('Please enter your Saudi phone number that contains 9 digits number without 0', 'shiaka').'</span>")
                    .appendTo(_in.parent());
                    jQuery("button[type=\"submit\"]", _in.closest("form")).prop("disabled", true);
                    jQuery("#real_phone_to_checkout").val("");
                } else {
                    jQuery("#real_phone_to_checkout").val("+966"+_in.val());
                    jQuery(".error", _in.parent()).remove();
                }
            }
            
            
            if(jQuery("#billing_phone_field").length > 0) {
            
                var _nameAttrInput = jQuery("input#billing_phone").attr("name"),
                restOfPhone = "",
                loadedValue = jQuery("input#billing_phone").val();
                

                jQuery("input#billing_phone").val(loadedValue.replace("+966",""));
                jQuery("input#billing_phone").attr("name", _nameAttrInput+"_frontend_code");
                jQuery("input#billing_phone").parent().append(jQuery(`<input id="real_phone_to_checkout" type="hidden" name="`+_nameAttrInput+`" />`));
                
                
                jQuery("#billing_phone_field > span").prepend(jQuery("<span class=\"country-code\"> +966 </span>"));
                
                checkPhoneNumberValidation();
                var phoneInterval = setInterval(function () {
                    if( jQuery("input#billing_phone").length > 0 ) {
                        checkPhoneNumberValidation(); 
                        clearInterval(phoneInterval);
                    }
                }, 100);


                jQuery("#billing_phone").blur(function () {
                    checkPhoneNumberValidation();
                });
                
                jQuery("#billing_phone").keyup(function () {
                    checkPhoneNumberValidation();

                });
   
                jQuery("#billing_phone").change(function () {
                    checkPhoneNumberValidation();
                });
                
                
                
                
                
            
                
            }
            
            
            
            
            jQuery(".woocommerce-ordering__button-label").each(function () {
                jQuery(this).text("'.__('Sort by', 'shiaka').'");
            });
        
            var paymentsImagesJson = JSON.parse(`'.$options_json.'`);
            if( jQuery("li", jQuery(".footer-payments ul.payments")).length < 1  ) {
            
                for( var _paymentBrandIndex in paymentsImagesJson ) {
                    
                    var _paymentBrand =  paymentsImagesJson[_paymentBrandIndex];
                    jQuery(`
                        <li>
                            <a href="`+_paymentBrand.link+`">
                                <img src="`+_paymentBrand.image+`" />
                            </a>
                        </li>
                    `).appendTo(jQuery(".footer-payments ul.payments"));
                }
            }

            
            jQuery(".woocommerce-product-attributes-item--attribute_pa_color .woocommerce-product-attributes-item__label").text("'.__('Color', 'shiaka').' ");

            
            setInterval(function () {
                jQuery(".cart-discount").each(function () {
                    var _divDiscount = jQuery(this);
                    var _title = jQuery(jQuery("th", _divDiscount)[0]),
                    anotherWords = _title.text().substr(_title.text().search(": ")+2);
                    _title.text("'.__('Coupon:', 'shiaka').' "+anotherWords);
                });
                
                '."
                
                jQuery('.reset_variations').each(function () {
                    if( jQuery('.reset-clearall-text', jQuery(this)).length < 1 ) {
                          jQuery(this).prepend(jQuery('<span class=\'reset-clearall-text\'>".__('Clear all', 'shiaka')."</span>'));                         
                    }
                });
                
                ".'

                if(jQuery(jQuery(`select#billing_city option`)[0]).length > 0) {
                    jQuery(jQuery(`select#billing_city option`)[0]).attr("value", "");
                }
                

    
                
                
            }, 1000);
        </script>
    ';
    
    
    
    // banner image link
    $script .= '
        <script>
            if( jQuery(".home .swiper-slide-image").length > 0 ) {
                jQuery(".swiper-slide-image").each(function () {
                    var oldHtml = jQuery(this).parent().html();
                    jQuery(this).parent().html(
                    `
                        <a href="`+jQuery(this).attr("alt")+`" target="_blank">
                        `+oldHtml+`
                        </a>
                    `
                    ); 
                });
            }

            jQuery(" .site-footer .copyright ").text("'.__('© Alshiaka 2022. All Rights Reserved', 'shiaka').'");

        </script>
    ';
    

    // Social Icons
    
    $script .= '
        
        <script>
                    
        var fbSvg = jQuery(`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16"> <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/> </svg>`);
        jQuery("#site-footer a[href*=\'facebook\']").each(function () {
            jQuery(".razzi-svg-icon", jQuery(this)).html(fbSvg);
        });
        
        
        var twSvg = jQuery(`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter" viewBox="0 0 16 16"> <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"/> </svg>`);
        jQuery("#site-footer a[href*=\'twitter\']").each(function () {
            jQuery(".razzi-svg-icon", jQuery(this)).html(twSvg);
        });
        
        var instagramSvg = jQuery(`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16"> <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"/> </svg>`);
        
        jQuery("#site-footer a[href*=\'instagram\']").each(function () {
            jQuery(".razzi-svg-icon", jQuery(this)).html(instagramSvg);
        });
            

            
            setTimeout(function () {
                    
                var fbSvg = jQuery(`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16"> <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/> </svg>`);
                jQuery(".menu-mobile-panel-content a[href*=\'facebook\']").each(function () {
                    jQuery(".razzi-svg-icon", jQuery(this)).html(fbSvg);
                });
                
                
                var twSvg = jQuery(`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter" viewBox="0 0 16 16"> <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"/> </svg>`);
                jQuery(".menu-mobile-panel-content a[href*=\'twitter\']").each(function () {
                    jQuery(".razzi-svg-icon", jQuery(this)).html(twSvg);
                });
                
                var instagramSvg = jQuery(`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16"> <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"/> </svg>`);
                
                jQuery(".menu-mobile-panel-content a[href*=\'instagram\']").each(function () {
                    jQuery(".razzi-svg-icon", jQuery(this)).html(instagramSvg);
                });
                if(jQuery("input#billing_phone").length > 0) {
                   
                    
                    var loadedPhoneValue = jQuery("input#billing_phone").val();
                    if(loadedPhoneValue.indexOf("966") >= 0) {
                        restOfPhone = loadedPhoneValue.substr(loadedPhoneValue.indexOf("966")+3);
                        jQuery("input#billing_phone").val(restOfPhone);
                    }
                    
                }
                
            

            }, 3000);
            
            
        '."
        
            jQuery('.socials-menu a').each(function () {
                jQuery(this).attr('target', '_blank');
            });
        ".'            

        </script>
    
    ';



    // Double check on qty input min
    $script .= "<script>
            
             
             
             var ccQtyInputCheck = setInterval(function () {
                jQuery('input.qty').each(function () {
                    if( jQuery(this).attr('min') == undefined || isNaN(jQuery(this).attr('min')) || jQuery(this).attr('min') < 1 ) {
                        jQuery(this).attr('min', 1);
                    }
                });
                ".
                '
                    if(  jQuery("#billing_city").length > 0 && jQuery("#billing_address_1").attr("loaded-cities-v1") != "true" && jQuery("#billing_address_1").length > 0 && jQuery("#billing_city").val().length <= 0  ) {
                       jQuery("#billing_address_1").attr("loaded-cities-v1", true);
                       jQuery("#billing_address_1").change(); 
                    }
                '
                ."
             }, 1000);
             setTimeout(function () {
                 clearInterval(ccQtyInputCheck);
             }, 5000);
    </script>";
    
    
        
    // hide image on hover temportary
    $script .= "<script>

            jQuery('.product-thumbnails--hover').each(function () {   jQuery(this).removeClass('product-thumbnails--hover').addClass('product-thumbnails'); jQuery(this).find('img').last().remove();    });
             ;
             
             var ccQtyInputCheck = setInterval(function () {
                jQuery('input.qty').each(function () {
                    if( jQuery(this).attr('min') == undefined || isNaN(jQuery(this).attr('min')) || jQuery(this).attr('min') < 1 ) {
                        jQuery(this).attr('min', 1);
                    }
                });
             }, 1000);
             setTimeout(function () {
                 clearInterval(ccQtyInputCheck);
             }, 5000);
    </script>";
    
    // wishlist script
    $script .= "
    <style>
        .site-header .header-wishlist .wishlist-counter {
            position: absolute;
            top: -10px;
            right: -10px;
            font-size: 10px;
            font-weight: 500;
            color: var(--rz-background-text-color-primary);
            text-align: center;
            line-height: 11px;
            min-width: 18px;
            height: 18px;
            border-radius: 30px;
            padding: 4px;
            background-color: var(--rz-background-color-primary);
            display: inline-block;
        }
        
         .site-header .header-wishlist > a {
            position: relative;
            padding-left: unset !important;
            padding-right: 0 !important;
            position: relative;
         }
    </style>
    
    <script>
    
        function updateWishlistIcon() {
            form_data = {
                action: 'get_wishlist_count'
            };
            jQuery.ajax({
                url: '".admin_url('admin-ajax.php')."',
                type: 'POST',
                data: form_data,
                dataType: 'json',
                async: true
            })
            .done(function ajaxDone(res) {
                if(res.count != undefined) {
                    if( jQuery('.wishlist-counter').length < 1 ) {
                        jQuery('<span class=\"wishlist-counter\">'+res.count+'</span>').appendTo(jQuery('.header-wishlist > a'));
                    } else {
                        jQuery('.wishlist-counter').html(res.count);
                    }
                }
            });
        }
        
        updateWishlistIcon();
        jQuery( function( $ ) {
          $( document ).on( 'added_to_wishlist removed_from_wishlist', function() {
           updateWishlistIcon();
          } );
        } );
    </script>";
    
    
    
    
    $script .= "
        <script>

             jQuery('.language-dropdown li').each(function () {
                 var _li = jQuery(this),
                 selected = jQuery('.razzi-language.list-dropdown .current .selected').first();
                
                if( _li.text() == selected.text() ) {
                    selected.addClass('customize-flag-'+_li.attr('class'));
                }
            });

            jQuery('.rtl .related.products > h2').text('".__('Related Products', 'shiaka')."');
            

            jQuery('.rtl .thmaf-razzi-acnt h3').each(function () {  
                jQuery(this).text(\"".__('Additional billing addresses', 'shiaka')."\");
                
            });
            
        </script>

    ";

    // add name to logged in users
    if(is_user_logged_in()) {
        $user = wp_get_current_user();
        $script .= "
            <script>

                if( jQuery('.account-links').length > 0  ) {
                 if( window.innerWidth <= 991 ) {
                    jQuery('.account-icon .razzi-svg-icon').append(jQuery('<span style=\"margin-inline-end: 5px;margin-top: 5px;\">". $user->first_name ."</span>'));
                 } else {
                    jQuery('.account-icon .razzi-svg-icon').prepend(jQuery('<span style=\"margin-inline-end: 5px;margin-top: 10px;\">". $user->first_name ."</span>'));                     
                 }

                }
            </script>
        ";
    }    
    
    
    echo $script;
  
}



function prefix_add_discount_line( $cart ) {

    $selected_payment_method_id = WC()->session->get( 'chosen_payment_method' );

    if($selected_payment_method_id == 'cod') {
        $cart->add_fee( __( 'Cash on delivery fees', 'shiaka' ) , 5, true );
    }


}
add_action( 'woocommerce_cart_calculate_fees', 'prefix_add_discount_line' );








function display_custom_total() {
    // Get (sub)total
    $subtotal = WC()->cart->subtotal;
    $total = WC()->cart->total;
    
    // Calculate
    $total_to_pay = $total - $subtotal;
    
    // The Output
    echo ' <tr class="cart-total-to-pay">
        <th>' . __( 'TAX fees', 'shiaka' )  . '</th>
        <td data-title="total-to-pay">' . wc_price(WC()->cart->get_total_tax())
  . '</td>
    </tr>';
}
add_action( 'woocommerce_cart_totals_after_order_total', 'display_custom_total', 20 );
add_action( 'woocommerce_review_order_after_order_total', 'display_custom_total', 20 );





/**
 * Rename product data tabs
 */
add_filter( 'woocommerce_product_tabs', 'woo_rename_tabs', 98 );
function woo_rename_tabs( $tabs ) {
    
    // var_dump($tabs);
// 	$tabs['description']['title'] = __( 'More Information' );		// Rename the description tab

    $count =  substr($tabs['reviews']['title'], strpos($tabs['reviews']['title'], ' '));
    
	$tabs['reviews']['title'] = __( 'Reviews', 'woocommerce' ) . $count;
	
	// Rename the reviews tab
// 	$tabs['additional_information']['title'] = __( 'Product Data' );	// Rename the additional information tab

	return $tabs;

}


// Change WooCommerce "Related products" text
