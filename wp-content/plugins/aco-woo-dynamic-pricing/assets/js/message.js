// Deactivation Form
jQuery(window).load(function() { 

    jQuery(document).on("click", function(e) {
        let popup = document.getElementById('wdp-survey-form');
        let overlay = document.getElementById('wdp-survey-form-wrap');
        let openButton = document.getElementById('deactivate-aco-woo-dynamic-pricing'); 
        if(e.target.id == 'wdp-survey-form-wrap'){
            wdpClose();
        }
        if(e.target === openButton){ console.log(1111);
            e.preventDefault();
            popup.style.display = 'block';
            overlay.style.display = 'block';
        }
        if(e.target.id == 'wdp_skip'){ 
            e.preventDefault();
            let urlRedirect = document.querySelector('a#deactivate-aco-woo-dynamic-pricing').getAttribute('href');
            window.location = urlRedirect;
        }
        if(e.target.id == 'wdp_cancel'){ 
            e.preventDefault();
            wdpClose();
        }
    });

	function wdpClose() {
		let popup = document.getElementById('wdp-survey-form');
        let overlay = document.getElementById('wdp-survey-form-wrap');
		popup.style.display = 'none';
		overlay.style.display = 'none';
		jQuery('#wdp-survey-form form')[0].reset();
		jQuery("#wdp-survey-form form .wdp-comments").hide();
		jQuery('#wdp-error').html('');
	}

    jQuery("#wdp-survey-form form").on('submit', function(e) {
        e.preventDefault();
        jQuery('#wdp_deactivate').prop('disabled', true);
        let valid = wdpValidate();
		if (valid) {
            let urlRedirect = document.querySelector('a#deactivate-aco-woo-dynamic-pricing').getAttribute('href');
            let form = jQuery(this);
            let serializeArray = form.serializeArray();
            let actionUrl = 'https://feedback.acowebs.com/plugin.php';
            jQuery.ajax({
                type: "post",
                url: actionUrl,
                data: serializeArray,
                contentType: "application/javascript",
                dataType: 'jsonp',
                success: function(data)
                {
                    window.location = urlRedirect;
                },
                error: function (jqXHR, textStatus, errorThrown) { 
                    window.location = urlRedirect;
                }
            });
        }
    });

    jQuery('#wdp-survey-form .wdp-comments textarea').on('keyup', function () {
		wdpValidate();
	});

    jQuery("#wdp-survey-form form input[type='radio']").on('change', function(){
        wdpValidate();
        let val = jQuery(this).val();
        if ( val == 'I found a bug' || val == 'Plugin suddenly stopped working' || val == 'Plugin broke my site' || val == 'Other' || val == 'Plugin doesn\'t meets my requirement' ) {
            jQuery("#wdp-survey-form form .wdp-comments").show();
        } else {
            jQuery("#wdp-survey-form form .wdp-comments").hide();
        }
    });

    function wdpValidate() {
		let error = '';
		let reason = jQuery("#wdp-survey-form form input[name='Reason']:checked").val();
		if ( !reason ) {
			error += 'Please select your reason for deactivation';
		}
		if ( error === '' && ( reason == 'I found a bug' || reason == 'Plugin suddenly stopped working' || reason == 'Plugin broke my site' || reason == 'Other' || reason == 'Plugin doesn\'t meets my requirement' ) ) {
			let comments = jQuery('#wdp-survey-form .wdp-comments textarea').val();
			if (comments.length <= 0) {
				error += 'Please specify';
			}
		}
		if ( error !== '' ) {
			jQuery('#wdp-error').html(error);
			return false;
		}
		jQuery('#wdp-error').html('');
		return true;
	}

});