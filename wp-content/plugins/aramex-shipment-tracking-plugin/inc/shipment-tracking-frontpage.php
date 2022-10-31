<?php


function wp_demo_shortcode() { 

//Turn on output buffering
ob_start();
$code = '
<div class="woocommerce"><div class="woocommerce-notices-wrapper"><ul class="woocommerce-error" role="alert">
			<li class="message-error-tracking">
				</li>
	</ul>
	
</div>

</div>



<section class="elementor-section elementor-top-section elementor-element elementor-element-8d4e24d searchlocator elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="8d4e24d" data-element_type="section">
    <div class="elementor-container elementor-column-gap-default">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-57f6556" data-id="57f6556" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-6b41c56 elementor-search-form--button-type-text elementor-search-form--skin-classic elementor-widget elementor-widget-search-form" data-id="6b41c56" data-element_type="widget" data-settings="{&quot;skin&quot;:&quot;classic&quot;}" data-widget_type="search-form.default">
                    <div class="elementor-widget-container">
                        <form class="elementor-search-form" id="shiaka-search-tracking" role="search" action="https://shiaka-dev.com" method="POST">
                            <div class="elementor-search-form__container">
                            <input placeholder="47429215713" 
                            class="elementor-search-form__input" 
                            type="number" 
                            name="shipmenttracking" 
                            title="'.__('View details', 'woocommerce').'" 
                            value="" required>			
                            <button class="elementor-search-form__submit" 
                                type="submit" title="'.__('View details', 'woocommerce').'" 
                                aria-label="'.__('View details', 'woocommerce').'">
                                '.__('View details', 'woocommerce').'
                            </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<style>
 .elementor-widget-search-form  .elementor-search-form__submit {
    min-width: calc( 4.4 * 55px );
    font-family: "Roboto", Sans-serif;
    font-weight: 400;
    background-color: #FFAC52;
}

.elementor-search-form .elementor-search-form__submit, .elementor-search-form .elementor-search-form__submit:hover {
    color: var(--e-search-form-submit-text-color,#fff);
    border: none;
    border-radius: 0;
}

thead {
    background-color: #13304f;
    color: #f2f2f2;
}

.woocommerce-error {
    display: none;
}

table {
    overflow
}

@media (max-width: 991px) {
     .elementor-widget-search-form  .elementor-search-form__submit {
            min-width: initial;
            padding: 0 20px;
    }
    
    table.tt122 th {
        column-count: 2
    }
}
</style>
';




$code .= '


<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--additional_information panel entry-content wc-tab" id="tab-additional_information" role="tabpanel" aria-labelledby="tab-title-additional_information" style="padding: 0 1em;overflow-x:scroll;">
				

<table class="woocommerce-product-attributes shop_attributes tt122">
    <thead>
        <tr>
            <th>'.__('Location', 'aramex').'</th>
            <th>'.__('Action Date/Time', 'aramex').'</th>
            <th>'.__('Tracking Description', 'aramex').'</th>
            <th>'.__('Comments', 'aramex').'</th>
        </tr>
    </thead>
    <tbody id="tb-tracking">

    </tbody>
</table>
</div>


<script>

    jQuery("#shiaka-search-tracking input").on("keyup", function() {
        jQuery(".woocommerce-error").hide();    
    });

    jQuery("#shiaka-search-tracking input").on("focus", function () {
        jQuery(this).attr("data-placeholder", jQuery(this).attr("placeholder"));
        jQuery(this).attr("placeholder", "");
    });
    
    jQuery("#shiaka-search-tracking input").on("blur", function () {
        jQuery(this).attr("placeholder", jQuery(this).attr("data-placeholder"));
        jQuery(this).attr("data-placeholder", "");
    });
    jQuery("#shiaka-search-tracking").on("submit", function (e) {
        
        e.preventDefault();
        var _from = jQuery(this),
        formData = {
            action: "get_shipment_tracking_for_order",
            policy_id: jQuery("input[name=\"shipmenttracking\"]").val()
        };
        jQuery("#tb-tracking").html("").hide();
        jQuery("button", _from).attr("type", "button").data("text", jQuery("button", _from).text()).text("...");
        jQuery("#tb-tracking").html("").hide();
        jQuery(".woocommerce-error").hide();    

        jQuery.ajax({
            url: "'.admin_url('admin-ajax.php').'",
            type: "POST",
            data: formData,
            dataType: "json",
            async: true
        })
        .done(function ajaxDone(res) {
            if(res.error != undefined) {
                jQuery(".message-error-tracking").text(res.error);
                jQuery(".woocommerce-error").show();
            }
            
            if(res.length > 0) {
                  res.forEach(function (ele) {
                      jQuery(`
<tr class="woocommerce-product-attributes-item woocommerce-product-attributes-item--attribute_pa_color">
    <td class="woocommerce-product-attributes-item__value">
        <p>`+ele.Location+`</p>
    </td>
    <td class="woocommerce-product-attributes-item__value">
        <p>`+ele.DateTime+`	</p>
    </td>
    <td class="woocommerce-product-attributes-item__value">
        <p>`+ele.Description+`</p>
    </td>
    <td class="woocommerce-product-attributes-item__value">
        <p>
            `+ele.Comments+`
        </p>
    </td>
</tr>
                      `).appendTo(jQuery("#tb-tracking"));
                  });              
                  jQuery("#tb-tracking").fadeIn();
            } else {
                jQuery(".message-error-tracking").html("'.sprintf(__('No records were found for this order due to its data. please track your shipment on %s Aramex %s', 'shiaka'), '<a style=\'color: skyblue;\' rel=\'noreferrer\' target=\'_blank\' href=\'https://www.aramex.com/\'>', '</a>').'");
                jQuery(".woocommerce-error").show();
            }
            
            
        })
        .fail(function ajaxFailed(res) {
            jQuery(".message-error-tracking").text("'.__('Error Network Connection, please try again later', 'shiaka').'");
            jQuery(".woocommerce-error").show();
        })
        .always(function ajaxAlways() {
            jQuery("button", _from).attr("type", "submit").text(jQuery("button", _from).data("text"));
        });
        return false;
    });
</script>
';

// 

ob_get_clean();

 // Output needs to be return
return $code;
} 

// register shortcode
add_shortcode('shipmenttrackingshiaka', 'wp_demo_shortcode');


add_action( 'wp_loaded','check_page_tracking_shipment' );

function check_page_tracking_shipment() {
    
    $shortcode = '[shipmenttrackingshiaka]';
    
    $user = wp_get_current_user();
    
    $page = get_page_by_path( 'shipment-tracking' );
    
    // Check if the page already exists
    if(!isset($page->ID)) {
        $page_id = wp_insert_post(
            array(
            'comment_status' => 'close',
            'ping_status'    => 'close',
            'post_author'    => $user->ID,
            'post_title'     => __('Shipment Tracking', 'shiaka'),
            'post_name'      => __('Shipment Tracking', 'shiaka'),
            'post_status'    => 'publish',
            'post_content'   => $shortcode,
            'post_type'      => 'page',
            'lang' => 'en'
            )
        );
    } else {
        if($page->post_content != $shortcode) {
            $data = array(
              'ID' => $page->ID,
              'post_content' => $shortcode,
             );
             
            wp_update_post( $data );
        }

    }
}




add_action('wp_ajax_get_shipment_tracking_for_order', 'shipment_tracking_for_order_method');
add_action('wp_ajax_nopriv_get_shipment_tracking_for_order', 'shipment_tracking_for_order_method');

function shipment_tracking_for_order_method() {
    global $wpdb;
    
    $return = [];
    $params  = $_REQUEST;
    $policy_id = sanitize_text_field($params['policy_id']);
    
    if(!is_numeric($policy_id) || strlen($policy_id) < 5) {
        $return['error'] = sprintf(__('Policy number ( %s ) is not valid', 'shiaka'), $policy_id);
    }
    

    
    // echo $policy_id;
    $order_id = $wpdb->query("SELECT * FROM `wp_postmeta` WHERE meta_value = '$policy_id'");

    
    if( $order_id == false ) {
        $return['error'] = sprintf(__('Policy number ( %s ) is not exist', 'shiaka'), $policy_id);
    }
    
    
    if(empty($return)) {
        $results_call = array();
    	$soapClient = new SoapClient(plugin_dir_url(__FILE__).'shipments-tracking-api-wsdl.wsdl');
    	// echo '<pre>';
    	// shows the methods coming from the service 
    	// print_r($soapClient->__getFunctions());
    	
    	/*
    		parameters needed for the trackShipments method , client info, Transaction, and Shipments' Numbers.
    		Note: Shipments array can be more than one shipment.
    	*/
    
    	// Aramex default testing credential
    	
    	$woocommerce_aramex_settings = get_option('woocommerce_aramex_settings');
        // 	print_r($woocommerce_aramex_settings);
        	
    	// Aramex default testing credential
    	$params = array(
    		'ClientInfo'  			=> array(
    									'AccountCountryCode'	=> $woocommerce_aramex_settings['account_country_code'],
    									'AccountEntity'		 	=> $woocommerce_aramex_settings['account_entity'],
    									'AccountNumber'		 	=> $woocommerce_aramex_settings['account_number'],
    									'AccountPin'		 	=> $woocommerce_aramex_settings['account_pin'],
    									'UserName'			 	=> $woocommerce_aramex_settings['email_origin'] ,
    									'Password'			 	=> $woocommerce_aramex_settings['password'],
    									'Version'			 	=> 'v1.0'
    								),
    
    		'Transaction' 			=> array(
    									'Reference1'			=> '001' 
    								),
    		'Shipments'				=> array(
    									$policy_id // Replace with your Shipment number by looking in the Aramex dashboard
    								)
    	);
    	
    	
    	// calling the method and printing results
    	try {
    
    		$auth_call = $soapClient->TrackShipments($params);
            // echo '<pre>';
            // print_r($auth_call); 
            // echo '</pre>';
            $x = 0;
    		foreach($auth_call->TrackingResults as $result)
    		{
    
    				// var_dump($result->Value->TrackingResult);
    				$val = $result->Value->TrackingResult;
    			if($val->UpdateLocation != null):
                    // $results_call[$x][$key] 	= $val->WaybillNumber;
                    $results_call[$x]["DateTime"] 	= $val->UpdateDateTime;
                    $results_call[$x]["Location"] 	= $val->UpdateLocation;
                    // $results_call[$x]["UpdateCode"]		= $val->UpdateCode;
                    $results_call[$x]["Description"] = $val->UpdateDescription;
                    $results_call[$x]["Comments"] 			= $val->Comments;
    			    $x++;
    			else:
                    $return['error'] = __('No Data records exist yet for this shipment', 'shiaka');
                    break;
                endif;
    		}
    
    	} catch (SoapFault $fault) {
    		
    		// echo "TRY FAILED";
            $results_call['error'] = $fault->faultstring;
    // 		die('Error : ' . $fault->faultstring);
    	}
        $return = $results_call;
    }
    
    wp_die(json_encode($return, JSON_UNESCAPED_UNICODE));
}