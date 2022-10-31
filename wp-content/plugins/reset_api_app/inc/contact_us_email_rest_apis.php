<?php

// custom route callback function
function sendContactMail(WP_REST_Request $request) {
    
    // Auth request
    $check_request = check_auth($request);
    if( $check_request !== true ) {
        return rest_ensure_response( $check_request );
    }
    
    
    $emailTo = get_option('shiaka__settings')['sh_contactus_mobile_email'];
    if(empty($emailTo)) {
        $emailTo = get_bloginfo('admin_email');
    }
    

  $response = array(
    'status' => 304,
    'message' => __('There was an error. email us at ', 'shiaka').$emailTo
  );

  $parameters = $request->get_params();

  $siteName = wp_strip_all_tags(trim(get_option('blogname')));
  $contactName = wp_strip_all_tags(trim($parameters['contact_name']));
  if(empty($contactName)) {
      $response['message'] = __('Name Field is required', 'shiaka');
  }


  $contactEmail = wp_strip_all_tags(trim($parameters['contact_email']));
  
  if( empty(filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) ) {
      $response['message'] = __('Valid email is required', 'shiaka');
  }
  
  $contactMessage = wp_strip_all_tags(trim($parameters['contact_message']));
  if(empty($contactMessage)) {
      $response['message'] = __('Message is required', 'shiaka');
  }

  if (!empty($contactName) && !empty($contactEmail) && !empty($contactMessage)) {
    $subject = "(New message sent from $siteName) By the Mobile Application form";
    $body = "<h3>$subject</h3><br/>";
    $body .= "<p><b>Name:</b> $contactName</p>";
    $body .= "<p><b>Email:</b> $contactEmail</p>";
    $body .= "<p><b>Message:</b> $contactMessage</p>";


    function wpse27856_set_content_type(){
        return "text/html";
    }
    add_filter( 'wp_mail_content_type','wpse27856_set_content_type' );

    if (wp_mail($emailTo, 'New message from '.$siteName.' Mobile contact form by '.$contactName.' <noreply@'.$_SERVER['HTTP_HOST'].'>', $body)) {
        $time = date('h:i a');
        $subject = "Mobile application contact form by ".$contactName . " <" . $contactEmail . ">";
        $post_content = '
'.$contactName.'
'.$contactEmail.'
'.$contactMessage.'
'.$contactName.
'\n
Mobile application contact form by '.$contactName.'
'.$_SERVER['REMOTE_ADDR'].'
'.$_SERVER['HTTP_USER_AGENT'].'
'.date('Y-m-d').'
'.$time.'
mobile-application Api
/wp-json/contact/mobileapp/send
ali-alanzan
ali.a@digitalspartners.com
AlShiaka Store
A company of great value and rooted heritage, envisioning more in the field of stylish men’s-wear
https://alshiaka.com
' . get_bloginfo('admin_email');

        $post_id_inbound = wp_insert_post(array(
            "post_type" => "flamingo_inbound",
            "post_status" => "publish",
            "post_title" => $subject,
            "post_content" => $post_content
        ));
        update_post_meta($post_id_inbound, '_submission_status', 'mail_sent');
        update_post_meta($post_id_inbound, '_subject', $subject);
        update_post_meta($post_id_inbound, '_from', $contactName." <".$contactEmail.">");
        update_post_meta($post_id_inbound, '_from_name', $contactName);
        update_post_meta($post_id_inbound, '_from_email', $contactEmail);
        $array_fields = array(
            "name" => $contactName,
            "email" => $contactEmail,
            "message" => $contactMessage
        );
        maybe_unserialize($array_fields);
        update_post_meta($post_id_inbound, '_fields', $array_fields);
        update_post_meta($post_id_inbound, '_field-name', $contactName);
        update_post_meta($post_id_inbound, '_field-email', $contactEmail);
        update_post_meta($post_id_inbound, '_field-message', $contactMessage);

        $meta_fields =  array(
            "remote_ip" => $_SERVER['REMOTE_ADDR'],
            "user_agent" => $_SERVER['HTTP_USER_AGENT'],
            "url" => "https://alshiaka.com/wp-json/contact/mobileapp/send",
            "date" => date('Y-m-d'),
            "time" => $time,
            "post_name" => "mobile-application",
            "post_title" => "Mobile Application",
            "post_url" => "https://alshiaka.com/wp-json/contact/mobileapp/send",
            "post_author" => "Ali Alanzan",
            "post_author_email" => "ali.a@digitalspartners.com"
        );
        maybe_unserialize($meta_fields);
        update_post_meta($post_id_inbound, '_meta', $meta_fields);

        
        $pid = $post_id_inbound; // post we will set it's categories
        $cat_name = 'Mobile Application Contact Form'; // category name we want to assign the post to 
        $taxonomy = 'flamingo_inbound_channel'; // category by default for posts for other custom post types like woo-commerce it is product_cat
        $append = false ;// true means it will add the cateogry beside already set categories. false will overwrite
        
        //get the category to check if exists
        $cat  = get_term_by('name', $cat_name , $taxonomy);
        
        //check existence
        if($cat == false){
        
            //cateogry not exist create it 
            $cat = wp_insert_term($cat_name, $taxonomy);
        
            //category id of inserted cat
            $cat_id = $cat['term_id'] ;
        
        }else{
        
            //category already exists let's get it's id
            $cat_id = $cat->term_id ;
        }
        
        //setting post category 
        $res=wp_set_post_terms($pid,array($cat_id),$taxonomy ,$append);
        
      $response['status'] = 200;
      $response['message'] = 'Form sent successfully.';
    }
  }
  return new WP_REST_Response($response);
}

// custom route declaration
add_action('rest_api_init', function () {
  register_rest_route( 'contact/mobileapp', 'send', array(
    'methods' => ['POST','PUT'],
    'callback' => 'sendContactMail'
  ));
});
