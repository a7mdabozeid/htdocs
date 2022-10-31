<?php
/**
 * Shortcode helper.
 *
 * @category Shortcode
 * @author   cozy vision technologies pvt ltd <support@cozyvision.com>
 * @package  Helper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	
/**
 * Shortcode class
 */
class Shortcode {

	/**
	 * Construct function.
	 */
	public function __construct() {
		$user_authorize = new smsalert_Setting_Options();
		if($user_authorize->is_user_authorised())
		{		   
		   add_shortcode( 'sa_loginwithotp', array( $this, 'add_sa_loginwithotp' ), 100 );
		   add_shortcode( 'sa_signupwithmobile', array( $this, 'add_sa_signupwithmobile' ), 100 );
		   add_shortcode('sa_subscribe', array($this, 'add_sa_subscribe'), 100);
		   add_action( 'wp_ajax_save_subscribe', array( $this, 'save_subscribe_data' ) );
		   add_action( 'wp_ajax_nopriv_save_subscribe', array( $this, 'save_subscribe_data' ) );
		}
	}
	
	/**
	 * add subscription form function.
	 *
	 * @return string
	 */
	function add_sa_subscribe($callback){						
		$grp_name = ( ! empty( $callback['group_name'] ) ) ? $callback['group_name'] : '';
	    return "<form id='sa-subscribe-form'>
			   <input type='hidden' name='grp_name' id='sa_grp_name' value='".$grp_name."'>
			   <p>
		       " . esc_html__('Name', 'sms-alert') . ": <input type='text' name='sa_name' id='sa_name' placeholder='" . esc_html__('Enter Name', 'sms-alert') . "'></br>
			   <p/><p>
			   " . esc_html__('Mobile', 'sms-alert') . ": <input type='text' name='sa_mobile' class='phone-valid' id='sa_mobile' placeholder='" . esc_html__('Enter Mobile Number', 'sms-alert') . "'></br>
			   <p/><p>
			   <input type='button' class='button' name='subscribe' id='sa_subscribe' value='" . esc_html__('Subscribe', 'sms-alert') . "'>
               </p>
                <div class='sasub_output'></div>			   
		</form>
		<script>jQuery('#sa_subscribe').click(function(){
		var name=jQuery('#sa_name').val();
		var mobile = jQuery('[name=sa_mobile]:hidden').val()?jQuery('[name=sa_mobile]:hidden').val():jQuery('[name=sa_mobile]').val();
		var grp_name=jQuery('#sa_grp_name').val();
		jQuery(this).val('Please Wait...').attr('disabled',true);
		jQuery.ajax({
			url: '".admin_url( 'admin-ajax.php' )."',
			type: 'POST',
			data:'action=save_subscribe&name='+name+'&mobile='+mobile+'&grp_name='+grp_name,
			success: function(data){			
					jQuery('.sasub_output').html(data);
					jQuery('#sa_subscribe').val('Subscribe').attr('disabled',false);
					jQuery('#sa-subscribe-form').trigger('reset');
			}
		})	
        });</script>";	   
	}
	
	/**
	 * save subscribe function.
	 *
	 */
	function save_subscribe_data(){
		   $grp_name = $_POST['grp_name'];
		   $datas[] = array('person_name'=>$_POST['name'],'number'=>$_POST['mobile']);
           $response = SmsAlertcURLOTP::create_contact($datas,$grp_name);
		   $response = json_decode($response,true);
		   if($response['status']=='success')
		   {
			    echo "<div class='sastock_output' style='color: rgb(255, 255, 255); background-color: green; padding: 10px; border-radius: 4px; margin-bottom: 10px;'>You have subscribed successfully.</div>";
		   }
		   else{
			    $error = !is_array($response['description'])?$response['description']:$response['description']['desc'];
				echo '<div class="sastock_output" style="color: rgb(255, 255, 255); background-color: red; padding: 10px; border-radius: 4px; margin-bottom: 10px;">'.$error.'</div>';
		   }
		   die();
	}

	/**
	 * loginwithotp function.
	 *
	 * @return string
	 */
	public function add_sa_loginwithotp() {
		$enabled_login_with_otp = smsalert_get_option( 'login_with_otp', 'smsalert_general' );
		$unique_class    = 'sa-lwo-'.mt_rand(1,100);
		if ( ('on' !== $enabled_login_with_otp) || (is_user_logged_in() && !current_user_can('administrator')) ) {
			return;
		}	
		ob_start();
		global $wp;
		echo '<form class="sa-lwo-form sa_loginwithotp-form '.$unique_class.'" method="post" action="' . home_url($wp->request) . '/?option=smsalert_verify_login_with_otp">';
		get_smsalert_template( 'template/login-with-otp-form.php', array() );
		echo wp_nonce_field('smsalert_wp_loginwithotp_nonce','smsalert_loginwithotp_nonce', true, false);
		echo '</form><style>.sa_default_login_form{display:none;}</style>';
		echo do_shortcode( '[sa_verify phone_selector=".sa_mobileno" submit_selector= ".'.$unique_class.' .smsalert_login_with_otp_btn"]' );
		  ?>
		<script>
		setTimeout(function() {
			if(jQuery(".modal.smsalertModal").length==0)	
			{			
			var popup = '<?php echo str_replace(array("\n","\r","\r\n"),'',(get_smsalert_template( "template/otp-popup.php", array(), true))); ?>';
			jQuery('body').append(popup);
			}
		}, 200);
		</script>
		 <?php	
		$content = ob_get_clean();
        return $content;
	}
	
	/**
	 * signupwithmobile function.
	 *
	 * @return string
	 */
	public function add_sa_signupwithmobile() {
		$enabled_signup_with_mobile = smsalert_get_option( 'signup_with_mobile', 'smsalert_general' );
		$unique_class    = 'sa-swm-'.mt_rand(1,100);
		if ( ('on' !== $enabled_signup_with_mobile) || (is_user_logged_in() && !current_user_can('administrator')) ) {
			return;
		}	
		ob_start();
		global $wp;
		echo '<form class="sa-lwo-form sa-signupwithotp-form '.$unique_class.'" method="post" action="' . home_url($wp->request) . '/?option=signwthmob">';
		get_smsalert_template( 'template/sign-with-mobile-form.php', array() );
		echo wp_nonce_field('smsalert_wp_signupwithmobile_nonce','smsalert_signupwithmobile_nonce', true, false);
		echo '</form><style>.sa_default_signup_form{display:none;}</style>';
		echo do_shortcode( '[sa_verify phone_selector="#reg_with_mob" submit_selector= ".'.$unique_class.' .smsalert_reg_with_otp_btn"]' );
		  ?>
		<script>
		setTimeout(function() {
			if(jQuery(".modal.smsalertModal").length==0)	
			{			
			var popup = '<?php echo str_replace(array("\n","\r","\r\n"),'',(get_smsalert_template( "template/otp-popup.php", array(), true))); ?>';
			jQuery('body').append(popup);
			}
		}, 200);
		</script>
		 <?php		
		$content = ob_get_clean();
        return $content;
	}

}
new Shortcode();
?>
