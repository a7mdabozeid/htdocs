
jQuery( document ).on( 'ready', function(){
	$ = jQuery;
	// Show refund subject field if other option is selected.
	var wps_rma_return_request_subject = $( '#wps_rma_return_request_subject' ).val();
	if (wps_rma_return_request_subject == null || wps_rma_return_request_subject == '') {
		$( '#wps_rma_return_request_subject_text' ).show();
	} else {
		$( '#wps_rma_return_request_subject_text' ).hide();
	}

	// onchange Show refund subject field if other option is selected.
	$( '#wps_rma_return_request_subject' ).on( 'click', function(){
		var reason = $( this ).val();
		if (reason == null || reason == '') {
			$( '#wps_rma_return_request_subject_text' ).show();
		} else {
			$( '#wps_rma_return_request_subject_text' ).hide();
		}
	});

	// Add more file field on the refund request form.
	$( '.wps_rma_return_request_morefiles' ).on( 'click', function(){		
		var count = $(this).data('count');
		var max  = $(this).data('max');
		var html = '<div class="add_field_input_div"><input type="file" class="wps_rma_return_request_files" name="wps_rma_return_request_files[]"><span class="wps_rma_delete_field">X</span><br></div>';

		if(count < max ){
			$( '#wps_rma_return_request_files' ).append( html );
			$(document).find('.wps_rma_return_request_morefiles').data('count', count+1);
		}
	});
	// delete file field on the refund request form.
	$( document ).on( 'click', '.wps_rma_delete_field', function(){
		var count = $(document).find('.wps_rma_return_request_morefiles').data( 'count' );
		$(document).find('.wps_rma_return_request_morefiles').data( 'count', count - 1 );
		$(this).parent( '.add_field_input_div' ).remove();
	});
	var check_refund_method = wrael_public_param.check_refund_method;
	var check_refund_manually = wrael_public_param.wps_refund_manually;

	// show the bank details field on selected refund method.
	var wps_wrma_refund_method = $('input[name=wps_wrma_refund_method]:checked').val();
	if ('' !== wps_wrma_refund_method && 'manual_method' === wps_wrma_refund_method ) {
		$( '#bank_details' ).show();
	} else if( ! wrael_public_param.check_pro_active ) {
		$( '#bank_details' ).show();
	} else if ( wrael_public_param.check_pro_active && 'on' != check_refund_method && 'on' == check_refund_manually ) {
		$( '#bank_details' ).show();
	} else {
		$( '#bank_details' ).hide();
	}
	// onchange show the bank details field on selected refund method.
	$( document ).on( 'click', 'input[name=wps_wrma_refund_method]', function() {
		if ('' !== $(this).val() && 'manual_method' === $(this).val() ) {
			$( '#bank_details' ).show();
		} else {
			$( '#bank_details' ).hide();
		}
	});
});
