<?php

add_action('wp_ajax_notify_customer_for_policy_id', 'generate_policyid_message_unifonic_api_send_data');
function generate_policyid_message_unifonic_api_send_data() {
    $return = array();
    $name = sanitize_text_field($_POST['name']);
    $phone = sanitize_text_field($_POST['phone']);
    $policy_id = intval($_POST['policy_id']);
    $order_id = intval($_POST['order_id']);
    
    $text_msg = 'مرحبا  ' . $name . 'تم شحن طلبك رقم ';
    $text_msg .= $order_id . ' بواسطة أرامكس  ';
    $text_msg .= '-  ورقم الشحنة ' . $policy_id;
    $text_msg .= '- بإمكانك تتبعها من خلال موقع أرامكس https://www.aramex.com/sa/ar -شكرا لثقتك بمنتجات الشياكة';

    if( isset($_POST['returnOrder']) ) {
        $text_msg = 'تم معالجة طلبك للاستبدال/الاسترجاع، ولإتمام الإجراءات يرجى تسليم القطع لأحد فروع ارامكس أو التواصل مع أرامكس على الرقم المجاني 8001000880 لتنسيق موعد وصىول المندوب لإستلامها - رقم بوليصة الإسترجاع ';  ;                                  

        $text_msg .= ' ' . $policy_id . ' '; 
        $text_msg .= '- مدة صلاحية البوليصة 7 أيام عمل - نأمل أن تحوز خدمات متجر الشياكة على رضاك'; 

    }

    if(!empty($phone) && strpos($phone, '966') !== false) {
        
        //   $ch1 = curl_init($new_url_return);
        //   curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
          
        //   if($this->config->get('tmdsms_ssl') == 1){
        //     curl_setopt($ch1,CURLOPT_SSL_VERIFYPEER, false);
        //   }
          
        //   $curl_scraped_page1 = curl_exec($ch1);
        //   curl_close($ch1);
        
        
        $response = wp_remote_get( 'https://el.cloud.unifonic.com/wrapper/sendSMS.php?appsid=axgj8b97GUHYzAdzik94tCjQlz0IEMdd51&msg='.urlencode($text_msg).'&to='.$phone.'&sender=Alshiaka&format=json&messageBodyEncoding=UTF8' );
        $body     = wp_remote_retrieve_body( $response );
        // if( isset($response['http_response']) ):
        //     unset($response['http_response']);
        // endif;
        // echo '<pre>' . print_r($response, 1) . '</pre>';
        // echo '<pre>' . print_r($body, 1) . '</pre>';

        $return['body'] = $body;
    }

    
    
    echo json_encode($return);
    wp_die();
}

// // For the users that are  logged in:  
// add_action( 'wp_ajax_register_order_aramex_policy_id', 'store_order_aramex_policy_id' );

// // ajax handler
// function store_order_aramex_policy_id()
// {
//     // code goes here
//     $order_id = $_REQUEST['order_id'];
//     $policy_id = $_REQUEST['policy_id'];
    
//     // Display as order meta
//     wc_add_order_item_meta($order_id, 'order_tracking_policy_id', $policy_id);

//     $result['success'] = true;
//     die(json_encode($result)); // last line
// }

// define the in_admin_footer callback 
function action_in_admin_footer_for_policy_id() { 
    global $pagenow, $current_page, $post, $user;
    
    if( $pagenow == 'post.php' && $post->post_type == 'shop_order' && strpos($_SERVER['REQUEST_URI'], 'aramexpopup')) {
        $policy_id = get_post_meta('order_tracking_policy_id', $_GET['post'], true);
        // var_dump($policy_id);
        

        if(empty($policy_id)) {
         echo '
            <script>
               var cShipmentTrackingInterval = setInterval(function () {
    
            
                var _element = jQuery("#aramex_shipment_creation .success");
                 
                 if(  _element.length > 0 && jQuery("body").attr("call-ajax-shipment-tracking-id") != "true" ) {
                     jQuery("body").attr("call-ajax-shipment-tracking-id", "true");
                     var _ele = jQuery("#aramex_shipment_creation .success").html();
                     var policyIdAramex = _ele.substr(_ele.search(": ")+2, _ele.substr(_ele.search(": ")+2).search(" "));
                     var dataForm = {
                        action: "notify_customer_for_policy_id",
                        policy_id: policyIdAramex,
                        order_id: jQuery("#post_ID").val(),
                        name: jQuery("#aramex_shipment_receiver_name").val(),
                        phone: jQuery("#aramex_shipment_receiver_phone").val().replaceAll(" ", "")
                     };
                     
                     console.log(dataForm);
                     jQuery("body").attr("call-ajax-shipment-tracking-id", "false");
                     if( _element.text().search("Return") == -1 ) {
                         var meta_name = "order_tracking_policy_id";
                         var newStatusOrder  = "wc-shipped";
                     } else {
                         var meta_name = "order_return_policy_id";
                         var newStatusOrder = "wc-refunded";
                         dataForm.returnOrder = 1;
                         dataForm.phone = jQuery("#aramex_shipment_shipper_phone").val();
                         dataForm.name = jQuery("#aramex_shipment_shipper_name").val();
                     }
             
                     jQuery("select#order_status").val(newStatusOrder);
                     
                     jQuery("#newmeta #metakeyinput").val(meta_name);
                     jQuery("#newmeta #metavalue").val(policyIdAramex);
                     jQuery("#newmeta-submit").click();

                    
                      clearInterval(cShipmentTrackingInterval);


                     jQuery.ajax({
                     
                         url: "'.admin_url( 'admin-ajax.php' ).'",
                         type: "POST",
                         data: dataForm,
                         dataType: "json",
                         async: true
                     })
                     .done(function ajaxDone(res) {
                         
                     })
                     .fail(function (e) {

                     })
                     .always(function ajaxFailed(res) {
                     });


                 }
                 
               }, 100, cShipmentTrackingInterval);
            </script>
        ';
        }

    }
}; 

// add the action 
add_action( 'in_admin_footer', 'action_in_admin_footer_for_policy_id' ); 

