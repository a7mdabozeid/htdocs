<?php

function getCustomAramesZones($lang = 'en') {
    
    if($lang == 'ar') {
        $zones = array(
            '' => '--- اختر منطقة ---',
            'Western' => 'الغربية',
            'Eastern' => 'الشرقية',
            'Central' => 'الوسطة'
        );
        
        $zones_2 = array(
            "" =>  " --- الرجاء الاختيار --- ",
            "Al Baha Area" => "منطقة الباحة",
            "Al Madinah Area" => "منطقة المدينة المنورة",
            "Al Qassim Area" => "منطقة القصيم",
            "Aljouf Area" => "منطقة الجوف",
            "Aser Area" => "منطقة عسير",
            "Gizan Area" => "منطقة جيزان",
            "Hail Area" => "منطقة حائل",
            "Makkah Area" => "منطقة مكة المكرمة",
            "Najran  Area" => "منطقة نجران",
            "Riyadh Area" => "منطقة الرياض",
            "Tabuk Area" => "منطقة تبوك",
            "The Eastern Area" => "المنطقة الشرقية",
            "The Northern border Area" => "منطقة الحدود الشمالية"
        );   
    } else {
        $zones = array(
            '' => '--- choose area ---',
            'Western' => 'Western',
            'Eastern' => 'Eastern',
            'Central' => 'Central'
        );
        
        $zones_2 = array(
                "" =>  "--- Please Select ---",
                "Al Baha Area" => "Al Baha Area",
                "Al Madinah Area" => "Al Madinah Area",
                "Al Qassim Area" => "Al Qassim Area",
                "Aljouf Area" => "Aljouf Area",
                "Aser Area" => "Aser Area",
                "Gizan Area" => "Gizan Area",
                "Hail Area" => "Hail Area",
                "Makkah Area" => "Makkah Area",
                "Najran  Area" => "Najran  Area",
                "Riyadh Area" => "Riyadh Area",
                "Tabuk Area" => "Tabuk Area",
                "The Eastern Area" => "The Eastern Area",
                "The Northern border Area" => "The Northern border Area"
        );
    }
    return $zones_2;
}

// billing_state

add_filter( 'woocommerce_default_address_fields', 'customize_extra_address_fields', 1000, 1 );
function customize_extra_address_fields( $address_fields ) {


    
    
    if(ICL_LANGUAGE_CODE == 'ar') {
        $regions = getCustomAramesZones('ar');
    } else {
        $regions = getCustomAramesZones();
    }
    
	$address_1_args = wp_parse_args( array(
	    'label' => __('Region', 'woocommerce'),
		'options' => $regions,
	), $address_fields['address_1'] );

	$address_fields['address_1'] = $address_1_args;

    $c_dir = ICL_LANGUAGE_CODE == 'ar' ? 'ar/' : '';
    
    
    

    $cities_file_en = file_get_contents(plugin_dir_url(__FILE__).'collection-cities-en.json');
	$cities_options_en = json_decode($cities_file_en, JSON_UNESCAPED_UNICODE);

    $cities_file = file_get_contents(plugin_dir_url(__FILE__).'collection-cities-'.ICL_LANGUAGE_CODE.'.json');
	
	$cities_options = json_decode($cities_file, JSON_UNESCAPED_UNICODE);

    if(ICL_LANGUAGE_CODE == 'ar') {
    	$new_cities_options = array(
            '' => '--- اختر مدينة ---',

        );
    } else {
        $new_cities_options = array(
            '' => '--- Choose city ---',
        );
    }
    
    foreach($cities_options['Al Baha Area'] as $key => $val) {

        $new_cities_options[$cities_options_en['Al Baha Area'][$key]]=$val;
    }

	$city_args = wp_parse_args( array(
		'options' => $new_cities_options
	), $address_fields['city'] );
	
	$address_fields['city'] = $city_args;


	$state_args = wp_parse_args( array(
		'required' => 0,
	), $address_fields['state'] );

	$address_fields['state'] = $state_args;

    
    return $address_fields;
}






// Copy from here

/**
 * Change the checkout city field to a dropdown field.
 */
function jeroen_sormani_change_city_to_dropdown( $fields ) {
    global $woocommerce;

    if(ICL_LANGUAGE_CODE == 'ar') {
        $regions = getCustomAramesZones('ar');
    } else {
        $regions = getCustomAramesZones();
    }
    
    
    
	$region_args = wp_parse_args( array(
	    'label' => __('Region', 'woocommerce'),
		'options' => $regions,
	), $fields['billing']['billing_address_1'] );

	$fields['shipping']['shipping_address_1'] = $region_args;
	$fields['billing']['billing_address_1'] = $region_args; // Also change for billing field
    $c_dir = ICL_LANGUAGE_CODE == 'ar' ? 'ar/' : '';
    
    if(!isset($_REQUEST['wc-ajax'])) {
        if(ICL_LANGUAGE_CODE != 'ar') {
            echo '
        <script>
            var c = setInterval(function () {
                jQuery(function () {
                   jQuery(\'label[for="billing_address_1"]\').html(\'Region &nbsp;<abbr class="required" title="required">*</abbr>\') 
                }());
            }, 100);
            setTimeout(function () {
                clearInterval(c);
            }, 3000);
        </script>
            ';
        
        } else {
                echo '
            <script>
                var c = setInterval(function () {
                    jQuery(function () {
                       jQuery(\'label[for="billing_address_1"]\').html(\'المنطقة &nbsp;<abbr class="required" title="required">*</abbr>\') 
                    }());
                }, 100);
                setTimeout(function () {
                    clearInterval(c);
                }, 3000);
            </script>
                ';
        }
        
        echo '
        
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
                        if(google.maps.places.Autocomplete == undefined) {
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
    }



    $cities_file_en = file_get_contents(plugin_dir_url(__FILE__).'collection-cities-en.json');
	$cities_options_en = json_decode($cities_file_en, JSON_UNESCAPED_UNICODE);

    $cities_file = file_get_contents(plugin_dir_url(__FILE__).'collection-cities-'.ICL_LANGUAGE_CODE.'.json');
	
	$cities_options = json_decode($cities_file, JSON_UNESCAPED_UNICODE);
	
// 	echo '<pre>';
//     print_r($cities_file);
//     echo '</pre>';
    if(ICL_LANGUAGE_CODE == 'ar') {
    	$new_cities_options = array(
            '' => '--- اختر مدينة ---',

        );
    } else {
        $new_cities_options = array(
            '' => '--- choose city ---',
        );
    }
    
    foreach($cities_options['Al Baha Area'] as $key => $val) {
        // $new_cities_options[] = $val;
	
// 	echo '<pre>';
//     print_r($cities_options_en['Al Baha Area'][$key]);
//     echo '</pre>';
        $new_cities_options[$cities_options_en['Al Baha Area'][$key]]=$val;
    }

	$city_args = wp_parse_args( array(
		'options' => $new_cities_options
	), $fields['shipping']['shipping_city'] );
	
	$fields['shipping']['shipping_city'] = $city_args;
	$fields['billing']['billing_city'] = $city_args; // Also change for billing field
    
	return $fields;

}
add_filter( 'woocommerce_checkout_fields', 'jeroen_sormani_change_city_to_dropdown' );



add_action("wp_loaded","after_wp_is_loaded");
function after_wp_is_loaded(){
    try {
        remove_all_actions('wp_ajax_nopriv_get_cities_of_state_ajax');
        remove_all_actions('wp_ajax_get_cities_of_state_ajax');
        
        
        add_action( 'wp_ajax_get_cities_of_state_ajax', 'my_cities_of_state_ajax' );
        add_action( 'wp_ajax_nopriv_get_cities_of_state_ajax', 'my_cities_of_state_ajax' );
    }
    
    //catch exception
    catch(Exception $e) {
    //   echo 'Message: ' .$e->getMessage();
    }
    // addNewResponse();
}






function my_cities_of_state_ajax($request) {

	if(!in_array($_REQUEST['state_code'], getCustomAramesZones())) {
	    die;
	}
	
    $c_dir = $_REQUEST['lang'] == 'ar' ? 'ar/' : '';
    

    $cities_file_en = file_get_contents(plugin_dir_url(__FILE__).'collection-cities-en.json');
    $cities_options_en = json_decode($cities_file_en, JSON_UNESCAPED_UNICODE);

    $cities_file = file_get_contents(plugin_dir_url(__FILE__).'collection-cities-'.ICL_LANGUAGE_CODE.'.json');
	
	$cities_options = json_decode($cities_file, JSON_UNESCAPED_UNICODE);
	    

    // print_r(plugin_dir_url(__FILE__).'cities/'.$c_dir.$_REQUEST['state_code'].'.json');
        if(ICL_LANGUAGE_CODE == 'ar') {
    	$new_cities_options = array(
            '--- اختر مدينة ---',

        );
    } else {
        $new_cities_options = array(
            '--- choose city ---',
        );
    }
    
    foreach($cities_options[$_REQUEST['state_code']] as $key => $val) {
        $new_cities_options[]=$val;
        // $new_cities_options[$_REQUEST['state_code'][$key]]=$val;
        // $new_cities_options[$cities_options_en[$_REQUEST['state_code']][$key]]=$val;


    }

  
    header('Content-Type: text/json; charset=utf-8');
    
    echo json_encode(array('success'=>true, "data" => $new_cities_options));
    wp_die();
}
// get_cities_of_state_ajax




add_action( 'wp_ajax_get_cities_of_state_ajax_customized', 'my_cities_of_state_ajax_customized' );
add_action( 'wp_ajax_nopriv_get_cities_of_state_ajax_customized', 'my_cities_of_state_ajax_customized' );

function my_cities_of_state_ajax_customized($request) {

	if(!in_array($_REQUEST['state_code'], getCustomAramesZones())) {
	    die;
	}
	
    $c_dir = $_REQUEST['lang'] == 'ar' ? 'ar/' : '';
    

    $cities_file_en = file_get_contents(plugin_dir_url(__FILE__).'collection-cities-en.json');
    $cities_options_en = json_decode($cities_file_en, JSON_UNESCAPED_UNICODE);

    $cities_file = file_get_contents(plugin_dir_url(__FILE__).'collection-cities-'.ICL_LANGUAGE_CODE.'.json');
	
	$cities_options = json_decode($cities_file, JSON_UNESCAPED_UNICODE);
	    

    // print_r(plugin_dir_url(__FILE__).'cities/'.$c_dir.$_REQUEST['state_code'].'.json');
        if(ICL_LANGUAGE_CODE == 'ar') {
    	$new_cities_options = array(
            '' => '--- اختر مدينة ---',

        );
    } else {
        $new_cities_options = array(
            '' => '--- choose city ---',
        );
    }
    
    foreach($cities_options[$_REQUEST['state_code']] as $key => $val) {
        // $new_cities_options[]=$val;
        // $new_cities_options[$_REQUEST['state_code'][$key]]=$val;
        $new_cities_options[$cities_options_en[$_REQUEST['state_code']][$key]]=$val;


    }

  
    header('Content-Type: text/json; charset=utf-8');
    
    echo json_encode(array('success'=>true, "data" => $new_cities_options));
    wp_die();
}
// get_cities_of_state_ajax


