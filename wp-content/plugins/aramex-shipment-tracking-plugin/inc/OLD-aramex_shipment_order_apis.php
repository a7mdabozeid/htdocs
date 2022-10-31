<?php


// custom route callback function
function ShipmentTrackingOrderApis(WP_REST_Request $request) {
    
    $results_call = [];
    // Auth request
    $check_request = check_auth($request);
    if( $check_request !== true ) {
        return rest_ensure_response( $check_request );
    }

    $order_id = $request['order_id'];
    if(empty($order_id) || !is_numeric($order_id)) {

        return $results_call;
    }

    $policy_id = get_post_meta($order_id, 'order_tracking_policy_id', true);
    

    if(empty($policy_id) || !is_numeric($policy_id)) {
        return $results_call;
    }

	$soapClient = new SoapClient(plugin_dir_url(__FILE__).'shipments-tracking-api-wsdl.wsdl');
	// echo '<pre>';
	// shows the methods coming from the service 
	// print_r($soapClient->__getFunctions());
	
	/*
		parameters needed for the trackShipments method , client info, Transaction, and Shipments' Numbers.
		Note: Shipments array can be more than one shipment.
	*/

    	
    	$woocommerce_aramex_settings = get_option('woocommerce_aramex_settings', '');
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
                // $results_call[$x][$key] 	= $val->WaybillNumber;
                $results_call[$x]["DateTime"] 	= $val->UpdateDateTime;
                $results_call[$x]["Location"] 	= $val->UpdateLocation;
                // $results_call[$x]["UpdateCode"]		= $val->UpdateCode;
                $results_call[$x]["Description"] = $val->UpdateDescription;
                $results_call[$x]["Comments"] 			= $val->Comments;
			    $x++;

		}

	} catch (SoapFault $fault) {
		
		// echo "TRY FAILED";
        $results_call['error'] = $fault->faultstring;
// 		die('Error : ' . $fault->faultstring);
	}
    $response = $results_call;
    
    return new WP_REST_Response($response);
}


// custom route declaration
add_action('rest_api_init', function () {
  register_rest_route( 'order/shipment', 'tracking', array(
    'methods' => ['GET'],
    'callback' => 'ShipmentTrackingOrderApis'
  ));
});
