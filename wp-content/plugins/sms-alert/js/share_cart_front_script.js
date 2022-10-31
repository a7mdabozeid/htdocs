$sa  =jQuery;
$sa(document).ready(function(){
	$sa('body').on('click','#smsalert_share_cart',function(e){	
	    e.preventDefault();
		$sa(this).addClass('button--loading');
		$sa.ajax({
			url:ajax_url.ajaxurl,
			type:'POST',
			data:'action=check_cart_data',
			success : function(response) {
				if(response === '0'){
					$sa('#smsalert_scp_ul').addClass('woocommerce-error').css({"padding":"1em 1.618em"});
					$sa('#smsalert_scp_ul').html('<li>Sorry, You cannot share your cart, Your cart is empty</li>');
				}
				$sa('body').addClass("smsalert_sharecart_popup_body");
				$sa("#smsalert_sharecart_popup").css("display","block");
				$sa('#smsalert_share_cart').removeClass('button--loading');
				$sa('#sc_umobile').trigger('keyup');
			},
			error: function() {
				alert('Error occured');
			}
		});
		return false;
   	});

	$sa(document).on('click','.close',function(){
		var modal_style = $sa('.smsalertModal').attr('data-modal-close');
		$sa('.smsalertModal').addClass(modal_style+'Out');
		$sa("#smsalert_sharecart_popup").css("display","none");
		$sa('body').removeClass("smsalert_sharecart_popup_body");
		setTimeout(function() {
			$sa('.smsalertModal').removeClass(modal_style+'Out');
		}, 500);
		$sa('#smsalert_scp_ul').removeClass('woocommerce-error').css({"padding":"0"});
	});

	$sa('body').on('click','#sc_btn',function(e){
		e.preventDefault();
		$sa('#sc_btn').attr("disabled",true);
		var uname 	= $sa("#sc_uname").val();
		var umobile = $sa("#sc_umobile").val();
		var fname 	= $sa("#sc_fname").val();
		var fmobile = $sa("#sc_fmobile").val();
		var intRegex = /^\d+$/;
		
		if((!intRegex.test(umobile) && umobile != '') || (!intRegex.test(fmobile) && fmobile != '')) {
			$sa('#sc_btn').before('<li class="sc_error" style="color:red">*Invalid Mobile Number</li>');
			setTimeout(function() {
				$sa('.sc_error').remove();
			}, 2000);
			$sa('#sc_btn').attr("disabled",false);
			return false;
		}
		
		if(uname != '' && umobile != '' && fname != '' && fmobile != '') {
			$sa(this).addClass('button--loading');
			var formdata = $sa(".sc_form").serialize();
			if(formdata.search("sc_uname") == -1){
				formdata = formdata+'&sc_uname='+encodeURI(uname);
			}
			$sa.ajax({
				url:ajax_url.ajaxurl,
				type:'POST',
				data:'action=save_cart_data&'+formdata,
				success : function(response) {
					$sa('#sc_btn').removeClass('button--loading');
					$sa('.sc_form').hide();
					$sa('#sc_response').html(response);
					setTimeout(function() {
						$sa("#smsalert_sharecart_popup").css("display","none"); 
						$sa('body').removeClass("smsalert_sharecart_popup_body");
						$sa('.sc_form').show();
						$sa('#sc_response').html('');
					}, 2000);
				},
				error: function(errorMessage) {
					$sa('#sc_btn').removeClass('button--loading');
					alert('Error occured');
				}
			});
		}
		else {
			$sa('#sc_btn').attr("disabled",false);
			$sa('#sc_btn').before('<li class="sc_error" style="color:red">*Please fill all fields</li>');
			setTimeout(function() {
				$sa('.sc_error').remove();
			}, 2000);
		}
		return false;
	});
});