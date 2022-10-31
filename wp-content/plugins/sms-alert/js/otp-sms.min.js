var interval;
$sa  =jQuery;
$sa(document).on("input", ".otp_input",function(){
	enableValidateBtn(this);
});

$sa(document).on('keypress', '.otp_input,.otp-number', function (e) {
    if (e.which == 13) e.preventDefault();
	var maxlength = $sa(this).attr("data-max");
	
	if($sa(this).val().length==maxlength)
	{
		if(event.which){
			event.preventDefault();
		}
	}
});


if(
	(typeof sa_otp_settings  != 'undefined' && sa_otp_settings['is_checkout']) && 
	((typeof sa_otp_settings !=  'undefined' && sa_otp_settings['login_with_otp']) ||
	(typeof sa_otp_settings !=  'undefined' && sa_otp_settings['buyer_login_otp'])	
	)
)
{
	$sa(".showlogin").parents(".woocommerce-info").hide();
	var content 	= $sa(".showlogin").parents(".woocommerce-info").clone();
	
	var child_div 	= $sa(".showlogin").parents(".woocommerce-info").after(content);

	child_div.show();
	child_div.find('.showlogin').addClass("sa-showlogin").removeClass("showlogin");

	$sa(document).on("click",".sa-showlogin",function(){
		if(sa_otp_settings['hide_default_login_form'] == 'on')
		{
			$sa(".sa-lwo-form").first().toggle();
		}
		else
		{			
			if($sa(this).hasClass("lwo-opened"))
			{
				$sa(".sa-lwo-form").hide();
				$sa(".woocommerce-form-login").hide();
				$sa(this).removeClass("lwo-opened");
			}
			else
			{
				$sa(".woocommerce-form-login").not('.sa-lwo-form').toggle();
				$sa(this).addClass("lwo-opened");
			}
		}
	});
}

$sa(document).on("click",".sa_default_login_form,.sa_default_signup_form",function(){
	var parent_cls = $sa(this).attr("data-parentform");
	if($sa(this).parents('.smsalert-modal').length>0)
	{
		var id = $sa(this).parents('.smsalert-modal').attr('id');
		$sa("#"+id+" ."+parent_cls).fadeIn(1000,'linear');
	}
	else{
		$sa("form."+parent_cls).fadeIn(1000,'linear');
	}
	$sa(this).parents("form").attr("style","display:none!important");
	$sa(this).parents("form").find(".phone-valid").val("");
});	

$sa(document).on("click", ".sa_myaccount_btn",function(e){
	$sa('.loginwithotp').removeAttr('inert');
	var parentForm =  $sa(this).parents("form");
	if(parentForm.parents('.um-login').length > 0 || parentForm.parents('#tab-customlogin').length > 0 || parentForm.parents('.xoo-el-section-login').length > 0 || parentForm.hasClass('xoo-el-form-login') || parentForm.hasClass('checkoutwc'))
	{
		parentForm.addClass('login');
	}
	if(parentForm.parents('#tab-customregister').length > 0 || parentForm.parents('.xoo-el-section-register').length > 0 || parentForm.hasClass('xoo-el-form-register'))
	{
		parentForm.addClass('register');
	}
	if(parentForm.hasClass('login'))
	{
		$sa(".loginwithotp").parents("div").find('.sa_default_login_form').trigger("click");
		parentForm.after($sa(".loginwithotp").detach());
		parentForm.parents("div").find('.loginwithotp').not('.loginwithotp:first').remove();
	}
    if(parentForm.hasClass('register'))
	{
		parentForm.after($sa(".signupwithmobile").detach());
	}
	parentForm.attr("style", "display: none !important");
	parentForm.next().find(".sa-lwo-form").fadeIn(200,'linear',function(){
		var mob_field = $sa(this).find('.phone-valid');		
		$sa('html').animate({scrollTop: mob_field.offset().top-100}, 500, function() {			
		    mob_field.focus();	
		});	
	});
	parentForm.parent().find(".sa-lwo-form").addClass("lwo-opened");
});

$sa(document).on("click", ".close",function(){
	hideOtpModal($sa(this));
});

function hideOtpModal(obj)
{
	clearInterval(interval);
	$sa(".blockUI").hide();
	var modal_style = obj.parents().find('.smsalertModal').attr('data-modal-close');
	obj.parents().find('.smsalertModal').addClass(modal_style+'Out');
    obj.parents(".smsalertModal").not('.smsalert-modal').hide('slow');
	setTimeout(function() {
		$sa('.smsalertModal').removeClass(modal_style+'Out');
	}, 500);
}

let saGroup = function(ele){
 	ele.value = ele.value.replace(/[^0-9]/g,'');
	var cur_class = ele.className;
	var maxlength = $sa('.'+cur_class).attr("data-max");
	var next_input = ele.id.slice(6);
	if(ele.value.length > maxlength)
	{
		var cur_val = ele.value.slice(0, 1);
		$sa("."+cur_class).val(cur_val);
	}
}

let tabChange = function(val,modal_id){
	var modal_form_class = modal_id.parentElement.parentElement.parentElement.parentElement.getAttributeNode("data-form-id").value;

	let ele = '';
	if( modal_form_class == "" ){
		ele = document.querySelectorAll('.digit-group input');
	} else {
		ele = document.querySelectorAll('.'+modal_form_class+' .digit-group input');
	}
	
    if(ele[val-1].value != ''){
      ele[val].focus();
    }else if(ele[val-1].value == '' && event.currentTarget.id != 'digit-1'){
      ele[val-2].focus();
    } 	
}


$sa(document).on("keyup",".smsalertModal .otp-number",function(e) {
	var otp_length 	= $sa('#smsalert_customer_validation_otp_token').attr('data-max');
	var txtData 	= [];
	var parent 		= $sa(this).parents(".smsalertModal");
	parent.find(".otp-number").each(function() {
		var otp_number = $sa(this).val();
		txtData.push(otp_number);
	});
	parent.find(".otp_input").val(txtData.join(""));
	enableValidateBtn(parent.find(".otp_input"),otp_length);
	e.preventDefault();
	if(e.key === "Delete" && e.target.selectionStart==0) {
	var item 		= $sa(this);	
	item.val(item.next('.otp-number').val());
	item.nextAll(".otp-number").each(function() {
		item[0].setSelectionRange(0,0);
		$sa(this).val($sa(this).next('.otp-number').val());
	  });
	  e.preventDefault();
    }
	if(e.key === "ArrowLeft" || e.key === "Backspace") {
		$sa(this).prev('.otp-number').focus();
    }
	if(e.key === "ArrowRight") {
	  $sa(this).next('.otp-number').focus();
    } 
});


$sa(window).ready(function(){
	$sa(".sa_myaccount_btn").closest("form").find('#rememberme').closest('label').each(function () {
		var form = $sa(this).closest('form');
		form.find('.woocommerce-LostPassword').prepend($sa(this));
	});
if(sa_otp_settings['hide_default_login_form'] == 'on')
{
	if($sa(".sa_myaccount_btn[name=sa_myaccount_btn_login]").parents().find('.woocommerce-form-login-toggle').length==0)
	{
		$sa(".sa_myaccount_btn[name=sa_myaccount_btn_login]").trigger("click");
	}
	else{
		$sa(".woocommerce-form-login-toggle").next().find('.sa_myaccount_btn[name=sa_myaccount_btn_login]').trigger("click");
	}
	setTimeout(function() {$sa(".sa-showlogin").trigger("click")}, 10);
	$sa(".sa_default_login_form").hide();
	$sa(".sa_loginwithotp-form").show();
}	
var otp_field = $sa(".otp-number");
otp_field.on('paste',function(ev) { 
    var clip = ev.originalEvent.clipboardData.getData('text').trim();
    pasteOtp(clip);	 
	return ev.preventDefault();
});
});

function pasteOtp(otp)
{
	var maxlength = $sa(".otp_input").attr("data-max");
	otp_field = $sa(".otp-number");
	var sc = [...otp];
    otp_field.val(i => sc[i]).eq(maxlength-1).focus();
    $sa(".otp-number").trigger('keyup');
}

function getCountryByCode(code) {
	return window.intlTelInputGlobals.getCountryData().filter(
	function(data){ return (data.dialCode == code) ? data.iso2 : ''; }
	);
}

function enableValidateBtn(obj,otp_length=0)
{
	if($sa(obj).val().match(/^\d{4,8}$/)) {
		$sa("#sa_verify_otp,obj").removeAttr("style");
		$sa("#sa_verify_otp,obj").removeAttr("disabled");	
	}
	else
	{
		$sa("#sa_verify_otp,obj").css({"color":"grey","pointer-events":"none"});
	}
}

function saResendOTP(obj)
{
	$sa(obj).text('re-sending..');
	$sa(".sa-otp-initiated .sa-otp-btn-init").trigger("click");
	return false;
}

function sa_otp_timer(obj,otp_timer=15)
{
	initialiseAutoFillOtp();
	var timer = function(secs){
		var sec_num = parseInt(secs, 10)    
		var hours   = Math.floor(sec_num / 3600) % 24
		var minutes = Math.floor(sec_num / 60) % 60
		var seconds = sec_num % 60    
		hours = hours < 10 ? "0" + hours : hours;
		minutes = minutes < 10 ? "0" + minutes : minutes;
		seconds = seconds < 10 ? "0" + seconds : seconds;
		return [hours,minutes,seconds].join(":")
	};
	obj.find(".sa_timer").show().html(timer(otp_timer)+" sec");;
	obj.find(".sa_forgot").hide();
	obj.find(".sa_resend_btn").text("Resend");
	var counter = otp_timer;
	 interval = setInterval(function() {
		counter--;
		var places = (counter < 10 ? "0" : "");
		obj.find(".sa_timer").html(timer(counter)+" sec");
		if (counter == 0) 
		{
			counterRunning=false;
			obj.find(".sa_timer").hide();
			obj.find(".sa_forgot").show(); // 23/06												   
			var cssString = "pointer-events: auto; cursor: pointer; opacity: 1; float:right"; 
			obj.find(".sa_resend_btn").attr("style",cssString);
			clearInterval(interval);
			}
		else
		{
			var cssString = "pointer-events: none; cursor: default; opacity: 1; float:right";
			obj.find(".sa_resend_btn").attr("style",cssString);
		}
	}, 1000);
}

function saInitOTPProcess(obj,action_url, data_obj,otp_resend_timer=15,success_cb=null,failure_cb=null,submit_selector=null)
{
	var waiting_txt 	= (typeof sa_notices !=  'undefined' && sa_notices['waiting_txt']) ? sa_notices['waiting_txt'] : "Please Wait...";			
	var cur_btn 	   	= $sa(obj);
	var prev_btn_text 	= cur_btn.val();
	
	var wpml_lang 		= (typeof sa_otp_settings !=  'undefined' && sa_otp_settings['lang']) ? sa_otp_settings['lang'] : "";
	if(cur_btn.is("input")){
		cur_btn.val(waiting_txt).attr("disabled",true);
		
	}else{
		cur_btn.addClass('button--loading').attr("disabled",true);
	}
	$sa('form').removeClass('sa-otp-initiated');
	$sa.ajax({
		url:action_url+"&lang="+wpml_lang,
		type:"POST",
		data:data_obj,
		cache: false,
		crossDomain:!0,
		dataType:"json",
		success:function(o){
		   if("failure"==o.result)
		   {
			showError(o,cur_btn,failure_cb,prev_btn_text);
			return false;
		   }
		   showSAModal(o,cur_btn,submit_selector,prev_btn_text,success_cb,otp_resend_timer);
		},
		error:function(o,e,m)
		{
			showError(o,cur_btn,failure_cb,prev_btn_text);
		}
	});		   
	return false;
}

function showSAModal(o,cur_btn,submit_selector,prev_btn_text,success_cb,otp_resend_timer)
{
	$sa('[tabindex="-1"]').removeAttr('tabindex');
	var currentModel 	= $sa(".modal.smsalertModal"); 
	("success"==o.result)?(
		(cur_btn.is("input") ? cur_btn.val(prev_btn_text).attr("disabled",false) : cur_btn.removeClass('button--loading').attr("disabled",false)),
		cur_btn.parents("form").addClass('sa-otp-initiated'),
		currentModel.find(".sa-message").empty().removeClass("woocommerce-error").append(o.message).addClass("woocommerce-message"),
		currentModel.show(),
		currentModel.find(".otp_input,.otp-number").val(""),
		currentModel.find(".otp_input,.otp-number").not(".hide").first().val("").focus(),
		$sa( "#sa_verify_otp" ).off().on( "click",{btn_class: submit_selector}, validateOtp ),
		currentModel.find(".smsalert_validate_field").show(),
		sa_otp_timer(currentModel,otp_resend_timer),
		((typeof success_cb == "function") ? success_cb(o) : "" )
		):
		(
		currentModel.find(".smsalert_validate_field").hide(),
		currentModel.find(".sa-message").empty().removeClass("woocommerce-message").append(o.message).addClass("woocommerce-error"),
		currentModel.show(),
		(cur_btn.is("input") ? cur_btn.val(prev_btn_text).attr("disabled",false) : cur_btn.removeClass('button--loading').attr("disabled",false))
		);
	return false;
}

function showError(o,cur_btn,failure_cb,prev_btn_text)
{
	cur_btn.val(prev_btn_text).attr("disabled",false);
	cur_btn.removeClass('check').attr("disabled",false);
	cur_btn.removeClass('button--loading').attr("disabled",false);
	(typeof failure_cb == "function") ? failure_cb(o) : "" ;
}

function sa_validateOTP(obj,action_url,data_obj,callback)
{
	var current_btn = $sa('#sa_verify_otp');
	var waiting_txt 	= (typeof sa_notices !=  'undefined' && sa_notices['waiting_txt']) ? sa_notices['waiting_txt'] : "Please wait...";	
	var current_modal = $sa(".modal.smsalertModal");	
	var wpml_lang 		= (typeof smsalert_wpml !=  'undefined' && smsalert_wpml['lang']) ? smsalert_wpml['lang'] : "";	

	$sa.ajax({
		url:action_url+"&lang="+wpml_lang,
		type:"POST",
		data:data_obj,
		crossDomain:!0,
		dataType:"json",
		beforeSend: function( xhr ) {
   			current_modal.find(".sa-message").empty().addClass("woocommerce-message").text(waiting_txt);
   			if(current_btn.is("input")){
				current_btn.val(waiting_txt).attr("disabled",true);
			
			}else{
				current_btn.addClass('button--loading').attr("disabled",true).text(" ").css("height","4pc");
			}
		},
		success:function(o){
		  otpSuceess(o,current_btn,callback);
		},
		error:function(o,e,m)
		{
			alert("error found here");
		}
	});
}

function otpSuceess(o,current_btn,callback)
{
	var current_modal = $sa(".modal.smsalertModal");
	("success"==o.result && o.message=="OTP Validated Successfully.")?
	(
		current_btn.removeClass("button--loading"),
		current_btn.text("Validate OTP"),
		//current_modal.hide(),
		hideOtpModal(current_btn),
		((typeof callback == "function") ? callback() : "" )
	):
	(
	current_btn.attr("disabled",false),
	current_btn.removeClass("button--loading"),
	current_btn.text("Validate OTP"),
	current_modal.find(".sa-message").show().empty().addClass("woocommerce-error").append(o.message).removeClass("woocommerce-message"),
	current_modal.find(".otp_input").focus());
}

function add_smsalert_button(submit_selector,phone_selector,unique_id,button_text='')
{
	if(!$sa(submit_selector).hasClass("sa-default-btn-hide"))
	{
	$sa(phone_selector).parents("form").addClass("sas-form");
	var button = $sa(submit_selector);
	$sa(submit_selector).addClass("sa-default-btn-hide");
	$sa(button.clone()).insertAfter(submit_selector).addClass("sa-otp-btn-init smsalert_otp_btn_submit");
	$sa(submit_selector+".sa-otp-btn-init").attr("id","sa_verify_"+unique_id).attr("name","sa_verify_"+unique_id);
	$sa(".sa-otp-btn-init").removeClass("sa-default-btn-hide");
	$sa(phone_selector).addClass("phone-valid");
	if($sa(submit_selector).is("button")){
		var text = (button_text!='')?button_text:$sa(submit_selector+".sa-default-btn-hide").text();
		$sa("#sa_verify_"+unique_id).html("<span class=button__text>"+ text+"</span>");
	}
	}
}
function send_otp(obj,submit_selector,phone_selector,username_selector,password_selector)
{
	$sa(obj).parents(".smsalertModal").hide();
    var country_enable = sa_otp_settings['show_countrycode'];
    var site_url = sa_otp_settings['site_url'];
    var otp_resend_timer = sa_otp_settings['otp_time'];
	if ( "on" !== country_enable ) {
		var e = $sa(obj).parents("form").find(phone_selector).val();
	} else {
		var e = $sa(obj).parents("form").find(phone_selector).intlTelInput("getNumber");
	}
	var u 	= $sa(obj).parents("form").find(username_selector).val();
	var p			= $sa(obj).parents("form").find(password_selector).val();
	if(typeof u !== "undefined" && typeof p !== "undefined")
	{
		var data 	= {username:u,password:p};
	}
	else
	{
		var data 	= {user_phone:e};
	}
	$sa(obj).parents("form").find("[aria-required=true], [required],.ff-el-is-required,.validate-required").not(".otp_input").each(function(){
		$sa(this).removeClass("sa_field_error");
		if($sa(this).is(":hidden")){
			return true;
		}
		
		if(($sa(this).attr("aria-required") || $sa(this).attr("required")) && ($sa(this).val() === "")){
			$sa(this).addClass("sa_field_error");
		}
		
		if($sa(this).attr("type") === "radio" || $sa(this).attr("type") === "checkbox"){
			if(!$sa("[name='"+$sa(this).attr("name")+"']").is(":checked"))
			{
				$sa(this).addClass("sa_field_error");
			}
		}

        if($sa(this).hasClass("ff-el-is-required") && $sa(this).next().find("input,select").val() === ""){
			$sa(this).addClass("sa_field_error");
		}
        if($sa(this).hasClass("validate-required") && $sa(this).find("input,select").val() === ""){
			$sa(this).addClass("sa_field_error");
		}  		
		
		if(!$sa(this).hasClass("sa_field_error") && $sa(this).attr("minlength")){

			var char_length = $sa(this).val().length;

			if(char_length < $sa(this).attr("minlength")){
				$sa(this).addClass("sa_field_error");
			}
		}

		if(!$sa(this).hasClass("sa_field_error") && $sa(this).attr("maxlength")){

			var char_length = $sa(this).val().length;

			if(char_length > $sa(this).attr("maxlength")){
				$sa(this).addClass("sa_field_error");
			}
		}
	});
	if($sa(obj).parents("form").find(".sa_field_error").length === 0)
	{
		if(username_selector !="" && password_selector !="")
		{
		   var action_url 	= site_url+"/?option=smsalert_ajax_login_popup";
		}
		else if($sa(obj).parents("form").hasClass("sa_loginwithotp-form")){
			data = $sa(obj).parents("form").serialize();
			action_url 	= site_url+"/?option=smsalert_ajax_login_with_otp";
		}
		else if($sa(obj).parents("form").hasClass("sa-signupwithotp-form")){
			data = $sa(obj).parents("form").serialize();
			action_url 	= site_url+"/?option=smsalert-registration-with-mobile";
		}
		else if($sa(obj).parents("form").hasClass("woocommerce-checkout")){
			var cartflow_param = '';
			if(typeof cartflows  != 'undefined')
			{
				cartflow_param = '&wcf_checkout_id='+cartflows['control_step'];
			}
			data = $sa(obj).parents("form").serialize()+"&checkout=Checkout";
			action_url 	= site_url+"/?option=smsalert-woocommerce-checkout-process&wc-ajax=checkout"+cartflow_param;
		}
		else if($sa(obj).parents("form").hasClass("woocommerce-post-checkout-form")){
			action_url 	= site_url+"/?option=smsalert-woocommerce-post-checkout";
		}
		else if($sa(obj).parents("form").hasClass("register") || $sa(obj).parents("form").hasClass("pie_register_reg_form") || $sa(obj).parents("form").hasClass("uwp-registration-form") || $sa(obj).parents().find(".um-register").length > 0){
			data = $sa(obj).parents("form").serialize()+"&register=Register";
			action_url 	= site_url+"/?option=smsalert_register_with_otp";
		}
		else{
			action_url 	= site_url+"/?option=smsalert-shortcode-ajax-verify";
		}
		saInitOTPProcess(obj,action_url, data,otp_resend_timer,function(resp){},function(){
	$sa(obj).parents("form").find(".sa-default-btn-hide").not(".sa-otp-btn-init").trigger("click")},submit_selector);
		return false;
	}
	else
	{
		//$sa(obj).parents("form").find(".sa-default-btn-hide").not(".sa-otp-btn-init").trigger("click");
		clickSaFormButton($sa(obj).parents("form").find(".sa-default-btn-hide").not(".sa-otp-btn-init")[0]);
		setTimeout(function() {$sa(".wc-block-components-checkout-place-order-button").not(".sa-otp-btn-init").addClass("sa-default-btn-hide")}, 10);
		return false;
	}
}
function clickSaFormButton(elem)
{
  var evt = document.createEvent("MouseEvents");
  evt.initMouseEvent("click", true, true, window,
          0, 0, 0, 0, 0, false, false, false, false, 0, null);
  if (document.createEvent) {
     elem.dispatchEvent(evt);
  } else {
     elem.fireEvent("on" + evt.eventType, evt); // support for IE crap
  }
}
function validateOtp(event)
{
	var site_url = sa_otp_settings['site_url'];
	var submit_selector = event.data.btn_class;
	var c_form 	= $sa(".sa-otp-initiated");
	var otp = $sa("#smsalert_customer_validation_otp_token").val();
	var action_url 		= site_url+"/?option=smsalert-validate-otp-form";
	var data 			= c_form.serialize()+"&otp_type=phone&from_both=&smsalert_customer_validation_otp_token="+otp;
	sa_validateOTP(submit_selector,action_url,data,function(){
		//$sa(submit_selector).hasClass("sa-default-btn-hide")?c_form.find(".sa-default-btn-hide").not(".sa-otp-btn-init").trigger("click"):$sa("#order_verify").val(otp);
		$sa(submit_selector).hasClass("sa-default-btn-hide")?clickSaFormButton(c_form.find(".sa-default-btn-hide").not(".sa-otp-btn-init")[0]):$sa("#order_verify").val(otp);
		
	});
}

function initialiseCountrySelector(phoneSelector='.phone-valid')
{
	var default_cc = (typeof sa_country_settings !="undefined" && sa_country_settings["sa_default_countrycode"] && sa_country_settings["sa_default_countrycode"]!="") ? sa_country_settings["sa_default_countrycode"] : "";
	var show_default_cc = "";
	var mob_field = $sa(phoneSelector);
		mob_field.addClass("phone-valid");
		mob_field.intlTelInput("destroy");
	var mob_field_name = mob_field.attr("name");
	var object = $sa(this).saIntellinput({hiddenInput:false});
	var iti = mob_field.intlTelInput(object);
	mob_field.parents(".iti--separate-dial-code").append('<input type="hidden" name="'+mob_field_name+'">');
	if(default_cc!="")
	{
		var selected_cc = getCountryByCode(default_cc);
		var show_default_cc = selected_cc[0].iso2.toUpperCase();
		iti.intlTelInput("setCountry",show_default_cc);
	}
	$sa(phoneSelector).on("countrychange", function () {
					var default_cc = $sa(this).intlTelInput("getSelectedCountryData");
					var fullnumber =  $sa(this).intlTelInput("getNumber");
					var field_name = $sa(this).attr("name");
					$sa(this).parents("form").find('[name="'+field_name+'"]:hidden').val(fullnumber);
				});
}

if ('OTPCredential' in window) {
  window.addEventListener('DOMContentLoaded', e => {
    const input = document.querySelector('#smsalert_customer_validation_otp_token');
	const ac = new AbortController();
	setTimeout(() => {
	   ac.abort();
	}, 1 * 60 * 1000);
    if (!input) return;
   initialiseAutoFillOtp();
  });
}

function initialiseAutoFillOtp()
{
	navigator.credentials.get({
      otp: { transport:['sms'] }
    }).then(otp => {
	  if($sa(".otp-number").hasClass('hide'))
	  {
		$sa("#smsalert_customer_validation_otp_token").val(otp.code);  
	  }		  
      else{
		pasteOtp(otp.code);  
	  }
      $sa("#sa_verify_otp").trigger('click');
    }).catch(err => {
      //console.log(err);
    });
}