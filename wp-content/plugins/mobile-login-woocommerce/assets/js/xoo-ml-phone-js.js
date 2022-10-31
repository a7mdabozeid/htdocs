jQuery(document).ready(function($){

	function parse_notice( message, type = 'error' ){
		return xoo_ml_phone_localize.notices[ type + '_placeholder' ].replace( '%s', message );
	}

	if( xoo_ml_phone_localize.operator === 'firebase' ){
		// Initialize Firebase
		firebase.initializeApp(xoo_ml_phone_localize.firebase.config);
	}
	


	var otpForm = function( phoneFormHandler ){

		var self 				= this;

		self.phoneFormHandler 	= phoneFormHandler;
		self.$phoneForm 		= self.phoneFormHandler.$phoneForm;
		self.displayType 		= self.$phoneForm.find('input[name="xoo-ml-otp-form-display"]').length ? self.$phoneForm.find('input[name="xoo-ml-otp-form-display"]').val() : 'inline_input';

		self.firebaseAuth 		= false;

		if( self.displayType === 'external_form' ){
			if( !self.$phoneForm.next( 'form.xoo-ml-otp-form' ).length ){
				$( xoo_ml_phone_localize.html.otp_form_external ).insertAfter(this.$phoneForm);
			}
			self.$otpForm 		= self.$phoneForm.next('form.xoo-ml-otp-form');
			self.$noticeCont 	= self.$otpForm.find('.xoo-ml-notice');
		}
		else{ //Inline Input
			if( !self.phoneFormHandler.$phoneInput.siblings('.xoo-ml-inline-otp-cont').length ){
				$insertAfter = self.phoneFormHandler.$phoneInput.parents('.xoo-aff-input-group').length ? self.phoneFormHandler.$phoneInput.parents('.xoo-aff-input-group') : self.phoneFormHandler.$phoneInput;
				$( xoo_ml_phone_localize.html.otp_form_inline ).insertAfter( $insertAfter );
			}
			self.$otpForm 		= self.$phoneForm.find('.xoo-ml-inline-otp-cont');
			self.$noticeCont 	= self.$otpForm.siblings('.xoo-ml-notice');
		}

		
	
		self.$submitBtn 	= self.$otpForm.find('.xoo-ml-otp-submit-btn');
		self.$inputs 		= self.$otpForm.find('.xoo-ml-otp-input');
		self.$resendLink 	= self.$otpForm.find('.xoo-ml-otp-resend-link');
		self.noticeTimout 	= self.resendTimer = false;
		self.activeNumber 	= self.activeCode = '';
		self.formType 		= self.$phoneForm.find('input[name="xoo-ml-form-type"]').length ? self.$phoneForm.find('input[name="xoo-ml-form-type"]').val() : '';

		//Methods
		self.validateInputs 	= self.validateInputs.bind(this);
		self.setPhoneData 		= self.setPhoneData.bind(this);
		self.onSuccess 			= self.onSuccess.bind(this);
		self.startResendTimer 	= self.startResendTimer.bind(this);
		self.showNotice 		= self.showNotice.bind(this);
		self.onOTPSent 			= self.onOTPSent.bind(this);
		self.verifyOTP 			= self.verifyOTP.bind(this);
		self.firebaseVerify 	= self.firebaseVerify.bind(this);

		
		self.$resendLink.on( 'click', { otpForm: self }, self.resendOTP );
		

		if( self.displayType === 'external_form' ){
			self.$otpForm.find('.xoo-ml-otp-no-change').on( 'click', { otpForm: self }, self.changeNumber );
			self.$otpForm.on( 'submit', { otpForm: self }, self.onSubmit );
			self.$inputs.on( 'keyup', { otpForm: self }, self.onInputChange );
		}
		else{
			self.$submitBtn.on( 'click', { otpForm: self }, self.onSubmit );
		}


		self.phoneFormHandler.$phoneInput.parent().css('position', 'relative');

	}


	otpForm.prototype.firebaseVerify = function(){

		var otpForm = this;


	}


	otpForm.prototype.onSubmit = function( event ){

		event.preventDefault();

		var otpForm = event.data.otpForm;

		if( !otpForm.validateInputs() || !otpForm.getOtpValue().length ) return false;

		otpForm.$submitBtn.addClass('xoo-ml-processing');

		if( xoo_ml_phone_localize.operator === 'firebase' ){

			otpForm.firebaseAuth.confirm( otpForm.getOtpValue() ).then(function (result) {

			firebase.auth().currentUser.getIdToken( false ).then(function(idToken) {
				otpForm.verifyOTP( { firebase_idToken: idToken } );
			})

			}).catch(function (error) {
				// User couldn't sign in (bad verification code?)
				otpForm.verifyOTP( { firebase_error: JSON.stringify( error ) } );
			});

		}else{
			otpForm.verifyOTP();
		}

	}

	otpForm.prototype.verifyOTP = function( data ){

		var otpForm = this;


		var form_data = $.extend( {
			'otp': otpForm.getOtpValue(),
			'token': otpForm.$phoneForm.find( 'input[name="xoo-ml-form-token"]' ).val(),
			'action': 'xoo_ml_otp_form_submit',
			'parentFormData': objectifyForm( otpForm.$phoneForm.serializeArray() ),
		}, data );

		$.ajax({
			url: xoo_ml_phone_localize.adminurl,
			type: 'POST',
			data: form_data,
			success: function(response){
				otpForm.$submitBtn.removeClass('xoo-ml-processing');

				if( response.notice ){
					otpForm.showNotice( response.notice );
				}

				if( response.error === 0 ){
					otpForm.onSuccess();
					otpForm.$otpForm.trigger( 'xoo_ml_on_otp_success', [response] );
				}
			}
		});
		
	}


	otpForm.prototype.showNotice = function( notice ){
		var _t = this;
		clearTimeout(this.noticeTimout);
		this.$noticeCont.html( notice ).show();
		this.noticeTimout = setTimeout(function(){
			_t.$noticeCont.hide();
		},4000)
	}

	otpForm.prototype.onSuccess = function(){
		this.$otpForm.hide();
		this.$inputs.val('');
		this.$phoneForm.show();
	}

	otpForm.prototype.onInputChange = function( event ){

		var otpForm = event.data.otpForm;

		//Switch Input
		if( $(this).val().length === parseInt( $(this).attr('maxlength') ) && $(this).next('input.xoo-ml-otp-input').length !== 0 ){
			$(this).next('input.xoo-ml-otp-input').focus();
		}

		//Backspace is pressed
		if( $(this).val().length === 0 && event.keyCode == 8 && $(this).prev('input.xoo-ml-otp-input').length !== 0 ){
			$(this).prev('input.xoo-ml-otp-input').focus().val('');
		}
		
	}

	otpForm.prototype.changeNumber = function( event ){
		var otpForm = event.data.otpForm;
		otpForm.$otpForm.hide();
		otpForm.$phoneForm.show();
		otpForm.$inputs.val('');
	}

	otpForm.prototype.validateInputs = function(){
		var	otpForm 			= this, 
			passedValidation 	= true;

		if( otpForm.displayType === 'inline_input' ){

		}
		else{
			this.$inputs.each( function( index, input ){
				var $input = $(input);
				if( !parseInt( $input.val() ) && parseInt( $input.val() ) !== 0 ){
					$input.focus();
					passedValidation = false;
					return false;
				}
			} );
		}	
		
		return passedValidation;
	}

	otpForm.prototype.getOtpValue = function(){
		var otp = '';
		this.$inputs.each( function( index, input ){
			otp += $(input).val();
		});
		return otp;
	}

	otpForm.prototype.setPhoneData = function( data ){
		this.$otpForm.find('.xoo-ml-otp-no-txt').html( data.otp_txt );
		this.phoneFormHandler.verifiedPHone = false;
		this.activeNumber 					= data.phone_no;
		this.activeCode   					= data.phone_code;
	}

	otpForm.prototype.startResendTimer = function(){

		var _t 				= this,
			$cont 			= this.$otpForm.find('.xoo-ml-otp-resend'),
			$resendLink 	= $cont.find('.xoo-ml-otp-resend-link'),
			$timer 			= $cont.find('.xoo-ml-otp-resend-timer'),
			resendTime 		= parseInt( xoo_ml_phone_localize.resend_wait );

		if( resendTime === 0 ) return;

		$resendLink.addClass('xoo-ml-disabled');

		clearInterval( this.resendTimer );

		this.resendTimer = setInterval(function(){
			$timer.html('('+resendTime+')');
			if( resendTime <= 0 ){
				clearInterval( _t.resendTimer );
				$resendLink.removeClass('xoo-ml-disabled');
				$timer.html('');
			}
			resendTime--;
		},1000) 
	}

	otpForm.prototype.resendOTP = function( event ){
		event.preventDefault();
		var otpForm = event.data.otpForm;

		otpForm.startResendTimer();

		var form_data = {
			action: 'xoo_ml_resend_otp'
		}

		otpForm.$resendLink.addClass('xoo-ml-processing');

		$.ajax({
			url: xoo_ml_phone_localize.adminurl,
			type: 'POST',
			data: form_data,
			success: function(response){

				if( xoo_ml_phone_localize.operator === 'firebase' && response.otp_sent && response.phone_code && response.phone_no ){
					otpForm.phoneFormHandler.sendOTPUsingFirebase( response );
					otpForm.$resendLink.removeClass('xoo-ml-processing');
				}
				else{
					otpForm.phoneFormHandler.onRequestOTP( response );
				}
				
			}
		});
	}

	otpForm.prototype.onOTPSent = function( response ){

		var otpFormHandler = this;
		otpFormHandler.$otpForm.show();
		otpFormHandler.startResendTimer();
		otpFormHandler.setPhoneData( response );

		if( otpFormHandler.displayType === 'inline_input' ){
			otpFormHandler.phoneFormHandler.$inlineVerifyBtn.hide();
		}
		else{
			otpFormHandler.$phoneForm.hide();
		}
		
	}

	var i = 0;
	var PhoneForm = function( $phoneForm ){
		var self = this;
		self.$phoneForm = $phoneForm;
		self.prepare();
		self.$phoneInput 	= self.$phoneForm.find( '.xoo-ml-phone-input' );
		self.$phoneCode 	= self.$phoneForm.find( '.xoo-ml-phone-cc' );
		self.formType 		= self.$phoneForm.find('input[name="xoo-ml-form-type"]').length ? self.$phoneForm.find('input[name="xoo-ml-form-type"]').val() : ''
		self.$submit_btn 	= self.$phoneForm.find('button[type="submit"]');

		self.otpFormHandler = new otpForm( this );

		//Methods
		self.sendFormData 				= self.sendFormData.bind(this);
		self.getPhoneNumber 			= self.getPhoneNumber.bind(this);
		self.getOTPFormPreviousState 	= self.getOTPFormPreviousState.bind(this);
		self.adjustPositions 			= self.adjustPositions.bind(this);
		self.sendOTPUsingFirebase		= self.sendOTPUsingFirebase.bind(this);
		self.onRequestOTP				= self.onRequestOTP.bind(this);

		if( self.otpFormHandler.displayType === 'inline_input' ){
			$( xoo_ml_phone_localize.inline_otp_verify_btn ).insertAfter( self.$phoneInput );
			self.$inlineVerifyBtn = self.$phoneInput.next();
			self.$inlineVerifyBtn.on( 'click', { phoneForm: self }, self.sendOTP );
			self.$phoneInput.add( self.$phoneCode ).on( 'keyup change', { phoneForm: self }, self.onNumberChange );
			self.$noticeCont 	= self.otpFormHandler.$noticeCont;
		}
		else{
			$( '<div class="xoo-ml-notice"></div>' ).insertBefore(this.$phoneForm); //Notice element
			self.$noticeCont = self.$phoneForm.siblings('.xoo-ml-notice');
		}

		self.adjustPositions();

		self.$phoneForm.on( 'submit', { phoneForm: self }, self.sendOTP );

		if( xoo_ml_phone_localize.operator === 'firebase' && !xoo_ml_phone_localize.firebase.api ){
			this.$noticeCont.html( xoo_ml_phone_localize.notices.firebase_api_error ).show();
		}

	}

	PhoneForm.prototype.prepare = function(){
		
	}

	PhoneForm.prototype.adjustPositions = function(){

		var self 		= this,
			inputHeight = this.$phoneInput.innerHeight();

		if( inputHeight <= 10 ) return;

		console.log(inputHeight);

		if( this.$inlineVerifyBtn ){
			this.$inlineVerifyBtn.css(
				'top',
				this.$phoneInput.position().top + ( this.$phoneInput.innerHeight() / 2 ) - ( this.$inlineVerifyBtn.innerHeight() ? this.$inlineVerifyBtn.innerHeight() / 2 : 10 )
			);
		}
		

		setTimeout(function(){
			
			if( self.$phoneForm.is(':hidden') ){
				self.$phoneForm.show();
				var hideForm = true;
			}

			if( self.$phoneCode.length && self.$phoneInput.innerHeight() ){

				var phoneCodeCSS = {
					'height': self.$phoneInput.innerHeight()+'px',
					'line-height': self.$phoneInput.innerHeight()+'px',
				}

				self.$phoneCode.css( phoneCodeCSS );

				if( self.$phoneCode.siblings('.select2').length ){
					self.$phoneCode.siblings('.select2').css( phoneCodeCSS );
				}

			}

			if( hideForm ){
				self.$phoneForm.hide();
			}

		},200)
		
	}


	PhoneForm.prototype.onNumberChange = function( event ){
		var phoneForm = event.data.phoneForm;
		if(  ( phoneForm.formType === 'update_user' && phoneForm.initialPhone === phoneForm.getPhoneNumber() ) || ( phoneForm.verifiedPHone && phoneForm.verifiedPHone === phoneForm.getPhoneNumber() ) ){
			phoneForm.$inlineVerifyBtn.html( xoo_ml_phone_localize.strings.verified ).addClass('verified').show();
			phoneForm.verifiedPHone = phoneForm.getPhoneNumber();
		}
		else{
			phoneForm.$inlineVerifyBtn.html( xoo_ml_phone_localize.strings.verify ).removeClass('verified').show();
		}
		phoneForm.otpFormHandler.$otpForm.hide();
		phoneForm.$noticeCont.hide();
	}

	PhoneForm.prototype.sendFormData = function(){

		var phoneForm 		= this,
			form_data		= this.$phoneForm.serialize()+'&action=xoo_ml_request_otp';

		if( phoneForm.$submit_btn.length && phoneForm.$submit_btn.attr('name') ){
			form_data = form_data + '&' + phoneForm.$submit_btn.attr('name') + '=' + phoneForm.$submit_btn.val();
		}

		phoneForm.$submit_btn.addClass('xoo-ml-processing');

		if( phoneForm.$inlineVerifyBtn ){
			phoneForm.$inlineVerifyBtn.addClass('xoo-ml-processing');
		}

		$.ajax({
			url: xoo_ml_phone_localize.adminurl,
			type: 'POST',
			data: form_data,
			success: function(response){
				if( xoo_ml_phone_localize.operator === 'firebase' && response.otp_sent && response.phone_code && response.phone_no ){
					phoneForm.sendOTPUsingFirebase( response );
				}
				else{
					phoneForm.onRequestOTP( response );
				}


			},
			complete: function(){
				
			}
		});
	}

	PhoneForm.prototype.onRequestOTP = function( response ){

		var phoneForm = this;

		if( response.notice ){
			phoneForm.$noticeCont.html( response.notice ).show();
		}
		//Display otp form
		if( response.otp_sent ){
			phoneForm.otpFormHandler.onOTPSent( response );
		}

		phoneForm.$phoneForm.trigger( 'xoo_ml_otp_requested', [ response ] );

		phoneForm.$submit_btn.removeClass('xoo-ml-processing');

		if( phoneForm.$inlineVerifyBtn ){
			phoneForm.$inlineVerifyBtn.removeClass('xoo-ml-processing');
		}
	}


	PhoneForm.prototype.sendOTPUsingFirebase = function(response){

		var phoneForm 	= this;

		if( xoo_ml_phone_localize.operator !== 'firebase'  ) return;

		if( !window.recaptchaVerifier ){
			$( '<div class="xoo-ml-recaptcha"></div>' ).insertBefore( phoneForm.$phoneForm );
			//Firebase
			window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier( phoneForm.$phoneForm.siblings('.xoo-ml-recaptcha').get(0), {
				'size': 'invisible',
				'callback': function(response) {

				}
			});
		}

		var phoneNumber = response.phone_code.toString() + response.phone_no.toString(),
			appVerifier = window.recaptchaVerifier;

		firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier)
			.then(function ( confirmationResult ) {
			// SMS sent. Prompt user to type the code from the message, then sign the
			// user in with confirmationResult.confirm(code).
			phoneForm.otpFormHandler.firebaseAuth = confirmationResult;
			phoneForm.onRequestOTP(response);
		}).catch(function (error) {

			// Error; SMS not sent
			response.otp_sent 	= 0;
			response.notice 	= error.message ? parse_notice( error.message ) : xoo_ml_phone_localize.notices.try_later;
			phoneForm.onRequestOTP(response);

			console.log(error);

		});

	}


	PhoneForm.prototype.getPhoneNumber = function( $only ){
		var phoneForm 		= this,
			phoneNumber 	= '';

		code 	= phoneForm.$phoneCode.length && phoneForm.$phoneCode.val() ? phoneForm.$phoneCode.val().trim().toString() : '';
		number 	= phoneForm.$phoneInput.val().toString().trim();

		if( $only === 'code' ){
			return code;
		}
		else if( $only === 'number' ){
			return number;
		}
		else{
			return code+number;
		}
	}


	PhoneForm.prototype.getOTPFormPreviousState = function(){
		var phoneFormHandler = this;
		//If requested for changing phone number & same number is put again.
 		if( ( !phoneFormHandler.$phoneCode.length || phoneFormHandler.otpFormHandler.activeCode ===  phoneFormHandler.getPhoneNumber('code') ) && phoneFormHandler.otpFormHandler.activeNumber ===  phoneFormHandler.getPhoneNumber('number') ){
 			phoneFormHandler.otpFormHandler.$otpForm.show();
 			if( phoneFormHandler.otpFormHandler.displayType === 'external_form' ){
 				phoneFormHandler.$phoneForm.hide();
 			}
 			else{
 				phoneFormHandler.$inlineVerifyBtn.hide();
 			}
 			
 			return true;
 		}

 		return false;
	}


	var RegisterForm = function( $phoneForm ){

		PhoneForm.call( this, $phoneForm );
		var self 			= this;

		self.$phoneForm 	= $phoneForm;
		self.$changePhone 	= self.$phoneForm.find('.xoo-ml-reg-phone-change');
		self.verifiedPHone 	= false;

		//Methods
		self.fieldsValidation = self.fieldsValidation.bind(this);
		//event
		self.otpFormHandler.$otpForm.on( 'xoo_ml_on_otp_success', { phoneForm: self }, self.onOtpSuccess );
		self.$changePhone.on( 'click', { phoneForm: self }, self.changePhone );


		//If this is an update form
		if( self.getPhoneNumber( 'number' ) && self.formType === 'update_user' ){
			self.verifiedPHone = self.initialPhone = self.getPhoneNumber();
			self.$phoneInput.trigger('change');
		}

	}


	RegisterForm.prototype = Object.create( PhoneForm.prototype );


	RegisterForm.prototype.fieldsValidation = function(){
		var phoneFormHandler = this,
			$phoneForm 		= phoneFormHandler.$phoneForm,
			error_string 		= ''; 

		if( phoneFormHandler.getPhoneNumber( 'number' ).length !== ( parseInt( phoneFormHandler.getPhoneNumber( 'number'  ) ) ).toString().length ){
			error_string 		= xoo_ml_phone_localize.notices.invalid_phone;
		}
			
		//Validate registration form fields [ wocommerce ]
		if( phoneFormHandler.otpFormHandler.displayType === 'external_form' && $phoneForm.find('input[name="woocommerce-register-nonce"]').length ){

			var $emailField 	= $phoneForm.find('input[name="email"]'),
				$passwordField 	= $phoneForm.find('input[name="password"]');

			//If email field is empty
			if( $emailField.length && !$emailField.val() ){
				error_string = xoo_ml_phone_localize.notices.empty_email;
			}

			if( $passwordField.length && !$passwordField.val() ){
				error_string = xoo_ml_phone_localize.notices.empty_password;
			}

		}


		if( error_string ){
			phoneFormHandler.$noticeCont.html( error_string ).show();
			return false;
		}

		return true;

	}

	RegisterForm.prototype.sendOTP = function( event ){
		var phoneFormHandler = event.data.phoneForm;
		phoneFormHandler.$noticeCont.hide();
		$('.xoo-el-notice').hide();

		//If number is optional
		if( phoneFormHandler.getPhoneNumber('number').length === 0 && xoo_ml_phone_localize.show_phone !== 'required' ){
			return;
		}

		//Check if OTP form exists & number is already verified 
		if( !phoneFormHandler.otpFormHandler || phoneFormHandler.verifiedPHone === phoneFormHandler.getPhoneNumber() ) return;

		event.preventDefault();
 		event.stopImmediatePropagation();

 		$(window).scrollTop( phoneFormHandler.$phoneInput.offset().top - 200 );

 		if( !phoneFormHandler.fieldsValidation() ) return;

 		//If requested for changing phone number & same number is not put again.
 		if( !phoneFormHandler.getOTPFormPreviousState() ) {
			phoneFormHandler.sendFormData();
		}
		else{
			if( phoneFormHandler.otpFormHandler.displayType === 'inline_input' ){
				phoneFormHandler.$noticeCont.html( xoo_ml_phone_localize.notices.verify_error ).show();
			}
		}

		
	}


	RegisterForm.prototype.onOtpSuccess = function( event, response ){
		var phoneForm 	= event.data.phoneForm,
			otpFormHandler 	= phoneForm.otpFormHandler;

		phoneForm.verifiedPHone = phoneForm.initialPhone = phoneForm.getPhoneNumber();

		if( otpFormHandler.displayType === "inline_input" ){
			phoneForm.$inlineVerifyBtn.html( xoo_ml_phone_localize.strings.verified ).show();
			phoneForm.$phoneInput.trigger('change');
		}
		else{
			phoneForm.$phoneInput
				.prop('readonly', true)
				.addClass( 'xoo-ml-disabled' );
			phoneForm.$changePhone.show();
			if( xoo_ml_phone_localize.auto_submit_reg === "yes" ){
				phoneForm.$phoneForm.find('[type="submit"]').trigger('click');
			}
		}


		if( response.notice ){
			phoneForm.$noticeCont.html( response.notice ).show();
		}

	}

	RegisterForm.prototype.changePhone = function( event ){
		$(this).hide();
		event.data.phoneForm.$phoneInput
			.prop( 'readonly', false )
			.focus();
	}


	$('input[name="xoo-ml-reg-phone"]').each( function( key, form ){
		var $formType = $(this).parents('form').find('input[name="xoo-ml-form-type"]');
		if( $formType.length && $formType.val() !== 'login_with_otp' ){
			new RegisterForm( $(this).closest('form') );
		}
	} );


	var LoginForm = function( $phoneForm, $parentForm ){

		var self 				= this;
		self.$parentForm 		= $parentForm;
		self.$phoneForm 		= $phoneForm;

		this.createFormHTML();

		PhoneForm.call( this, $phoneForm );

		self.$parentOTPLoginBtn = self.$parentForm.find('.xoo-ml-open-lwo-btn');
		self.$loginOTPBtn 		= self.$phoneForm.find( '.xoo-ml-login-otp-btn' );

		//event
		self.$parentOTPLoginBtn.on( 'click', { phoneForm: self }, self.openLoginForm );
		self.otpFormHandler.$otpForm.on( 'xoo_ml_on_otp_success', { phoneForm: self }, self.onOtpSuccess );

		//Back to parent form
		$('.xoo-ml-low-back').on('click',function(){
			self.$parentForm.show();
			self.$phoneForm.hide();
			self.$noticeCont.hide();
		})

	}


	LoginForm.prototype = Object.create( PhoneForm.prototype );

	LoginForm.prototype.createFormHTML = function(){
		
		var formHTMLPlaceholder = this.$parentForm.find('.xoo-ml-lwo-form-placeholder');
		//attach form elements
		this.$phoneForm.append( formHTMLPlaceholder.html() );
		formHTMLPlaceholder.remove();
		//If otp login form is displayed first
		if( xoo_ml_phone_localize.login_first === "yes" ){
			this.$parentForm.hide();
		}
		else{
			this.$phoneForm.hide();
		}
	}

	LoginForm.prototype.sendOTP = function( event ){

		event.preventDefault();
		event.stopImmediatePropagation();
		var phoneFormHandler = event.data.phoneForm;
		phoneFormHandler.$noticeCont.hide();
		$('.xoo-el-notice').hide();

		if( !phoneFormHandler.getOTPFormPreviousState()  ){
			phoneFormHandler.sendFormData();
		}
		else{
			phoneFormHandler.otpFormHandler.$submitBtn.trigger('click');
		}

	}


	LoginForm.prototype.onOtpSuccess = function( event, response ){
		var phoneFormHandler = event.data.phoneForm;

		if( response.notice ){
			phoneFormHandler.$noticeCont.html( response.notice ).show();
		}

		if( response.redirect !== undefined ){
			var redirect = phoneFormHandler.$parentForm.find('input[name="xoo_el_redirect"]').length ? phoneFormHandler.$parentForm.find('input[name="xoo_el_redirect"]').val() : response.redirect;
			window.location = redirect;
		}

	}
	LoginForm.prototype.openLoginForm = function( event, response ){
		var phoneFormHandler = event.data.phoneForm;
		phoneFormHandler.$phoneForm.show();
		phoneFormHandler.$parentForm.hide();
		$('.xoo-el-notice').hide();
	}


	$('.xoo-ml-open-lwo-btn').each( function( key, el ){
		var $parentForm = $(this).closest('form');
		//attach login with otp form
		$('<form class="xoo-lwo-form"></form>').insertAfter( $parentForm );
		var $loginForm = $parentForm.next('.xoo-lwo-form');
		new LoginForm( $loginForm, $parentForm );
	} );


	//converts serializeArray to json object
	function objectifyForm(formArray) {//serialize data function

	  var returnArray = {};
	  for (var i = 0; i < formArray.length; i++){
	    returnArray[formArray[i]['name']] = formArray[i]['value'];
	  }
	  return returnArray;
	}


	$( document.body ).on('xoo_el_form_toggled', function( e, formType, containerObj ){

		var $container = containerObj.$container;

		$container.find('.xoo-ml-notice').hide();

		var lwoForm 		= $container.find('.xoo-lwo-form'),
			parentLoginForm = $container.find('.xoo-el-form-login');

		//If login with OTP form is to be displayed first.
		if( parentLoginForm.length ){
			if( xoo_ml_phone_localize.login_first === "yes" && lwoForm.length ){
				lwoForm.show();
				parentLoginForm.hide();
			}
			else{
				lwoForm.hide();
				parentLoginForm.show();
			}
		}

		$otpForm = $container.find( '.xoo-ml-otp-form' ); 
		if( $otpForm.length ){
			$otpForm.hide();
		}
	})

	if( $.fn.select2 ){
		$('select.xoo-ml-phone-cc').each(function( key, el ){
			$(el).select2();
		});
	}

	$('.xoo-ml-inline-otp-cont').on('xoo_ml_on_otp_success', function( event, response ){
		var $form = $(this).parents('form');
		if( !$form.length || !$form.find('input[name="gform_submit"]').length ) return;
		window[ 'gf_submitting_'+$form.find('input[name="gform_submit"]').val() ] = 0;
	})
})