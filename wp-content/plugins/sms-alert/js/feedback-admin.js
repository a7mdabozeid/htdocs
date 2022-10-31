$sa  =jQuery;
$sa(document).ready( function($) {

	// if device is mobile.
	if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
	    $sa('body').addClass( 'mobile-device' );
	}

	var deactivate_url = '';

	// Add Deactivation id to all deactivation links.
	embed_id_to_deactivation_urls();

	// On click of deactivate.
	if( 'plugins.php' == smsf.current_screen ) {

		add_deactivate_slugs_callback( smsf.current_supported_slug );

		$sa( document ).on( 'change','.on-boarding-radio-field' ,function(e){

			e.preventDefault();
			if ( 'other' == $sa( this ).attr( 'id' ) ) {
				$sa( '#deactivation-reason-text' ).removeClass( 'smsf-keep-hidden' );
			} else {
				$sa( '#deactivation-reason-text' ).addClass( 'smsf-keep-hidden' );
			}
		});
	}

	// Close Button Click.
	$sa( document ).on( 'click','.smsf-on-boarding-close-btn a',function(e){
		e.preventDefault();
		smsf_hide_onboard_popup();
	});

	// Skip and deactivate.
	$sa( document ).on( 'click','.smsf-deactivation-no_thanks',function(e){

		window.location.replace( deactivate_url );
		smsf_hide_onboard_popup();
	});

	// Submitting Form.
	$sa( document ).on( 'submit','form.smsf-on-boarding-form',function(e){

		$sa('.smsf-on-boarding-submit').addClass('button--loading').attr('disabled',true);
		e.preventDefault();
		var form_data = $sa( 'form.smsf-on-boarding-form' ).serializeArray(); 

		$sa.ajax({
            type: 'post',
            dataType: 'json',
            url: smsf.ajaxurl,
            data: {
                nonce : smsf.auth_nonce, 
                action: 'send_onboarding_data' ,
                form_data: form_data,  
            },
            success: function( msg ){
            	$sa( document ).find('#smsf_wgm_loader').hide();
        		if( 'plugins.php' == smsf.current_screen ) {
					window.location.replace( deactivate_url );
				}
                smsf_hide_onboard_popup();
				$sa('.smsf-on-boarding-submit').removeClass('button--loading').attr('disabled',false);
            }
        });
	});

	// Open Popup.
	function smsf_show_onboard_popup() {
		$sa( '.smsf-onboarding-section' ).show();
		$sa( '.smsf-on-boarding-wrapper-background' ).addClass( 'onboard-popup-show' );

	    if( ! $sa( 'body' ).hasClass( 'mobile-device' ) ) {
	    	$sa( 'body' ).addClass( 'smsf-on-boarding-wrapper-control' );
	    }
	}

	// Close Popup.
	function smsf_hide_onboard_popup() {
		$sa( '.smsf-on-boarding-wrapper-background' ).removeClass( 'onboard-popup-show' );
		$sa( '.smsf-onboarding-section' ).hide();
		if( ! $sa( 'body' ).hasClass( 'mobile-device' ) ) {
	    	$sa( 'body' ).removeClass( 'smsf-on-boarding-wrapper-control' );
	    }
	}

	// Apply deactivate in all the smsf plugins.
	function add_deactivate_slugs_callback( all_slugs ) {
		
		for ( var i = all_slugs.length - 1; i >= 0; i-- ) {

			$sa( document ).on( 'click', '#deactivate-' + all_slugs[i] ,function(e){
				e.preventDefault();
				deactivate_url = $sa( this ).attr( 'href' );
				plugin_name = $sa( this ).attr( 'aria-label' );
				$sa( '#plugin-name' ).val( plugin_name.replace( 'Deactivate ', '' ) );
				plugin_name = plugin_name.replace( 'Deactivate ', '' );
				$sa( '#plugin-name' ).val( plugin_name );
				$sa( '.smsf-on-boarding-heading' ).text( plugin_name + ' Feedback' );
				var placeholder = $sa( '#deactivation-reason-text' ).attr( 'placeholder' );
				$sa( '#deactivation-reason-text' ).attr( 'placeholder', placeholder.replace( '{plugin-name}', plugin_name ) );
				smsf_show_onboard_popup();
			});
		}
	}

	// Add deactivate id in all the plugins links.
	function embed_id_to_deactivation_urls() {
		$sa( 'a' ).each(function(){
		    if ( 'Deactivate' == $sa(this).text() && 0 < $sa(this).attr( 'href' ).search( 'action=deactivate' ) ) {
		    	if( 'undefined' == typeof $sa(this).attr( 'id' ) ) {
			    	var slug = $sa(this).closest( 'tr' ).attr( 'data-slug' );
			    	$sa(this).attr( 'id', 'deactivate-' + slug );
		    	}
		    }
		});	
	}
});