// Deactivation Form
jQuery(window).load(function() { 

    jQuery(document).on("click", function(e) {
        let popup = document.getElementById('plw-survey-form');
        let overlay = document.getElementById('plw-survey-form-wrap');
        let openButton = document.getElementById('deactivate-aco-product-labels-for-woocommerce'); 
        if(e.target.id == 'plw-survey-form-wrap'){
            plwClose();
        }
        if(e.target === openButton){ 
            e.preventDefault();
            popup.style.display = 'block';
            overlay.style.display = 'block';
        }
        if(e.target.id == 'plw_skip'){ 
            e.preventDefault();
            let urlRedirect = document.querySelector('a#deactivate-aco-product-labels-for-woocommerce').getAttribute('href');
            window.location = urlRedirect;
        }
        if(e.target.id == 'plw_cancel'){ 
            e.preventDefault();
            plwClose();
        }
    });

	function plwClose() {
		let popup = document.getElementById('plw-survey-form');
        let overlay = document.getElementById('plw-survey-form-wrap');
		popup.style.display = 'none';
		overlay.style.display = 'none';
		jQuery('#plw-survey-form form')[0].reset();
		jQuery("#plw-survey-form form .plw-comments").hide();
		jQuery('#plw-error').html('');
	}

    jQuery("#plw-survey-form form").on('submit', function(e) {
        e.preventDefault();
        jQuery('#plw_deactivate').prop('disabled', true);
        let valid = plwValidate();
		if (valid) {
            let urlRedirect = document.querySelector('a#deactivate-aco-product-labels-for-woocommerce').getAttribute('href');
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

    jQuery('#plw-survey-form .plw-comments textarea').on('keyup', function () {
		plwValidate();
	});

    jQuery("#plw-survey-form form input[type='radio']").on('change', function(){
        plwValidate();
        let val = jQuery(this).val();
        if ( val == 'I found a bug' || val == 'Plugin suddenly stopped working' || val == 'Plugin broke my site' || val == 'Other' || val == 'Plugin doesn\'t meets my requirement' ) {
            jQuery("#plw-survey-form form .plw-comments").show();
        } else {
            jQuery("#plw-survey-form form .plw-comments").hide();
        }
    });

    function plwValidate() {
		let error = '';
		let reason = jQuery("#plw-survey-form form input[name='Reason']:checked").val();
		if ( !reason ) {
			error += 'Please select your reason for deactivation';
		}
		if ( error === '' && ( reason == 'I found a bug' || reason == 'Plugin suddenly stopped working' || reason == 'Plugin broke my site' || reason == 'Other' || reason == 'Plugin doesn\'t meets my requirement' ) ) {
			let comments = jQuery('#plw-survey-form .plw-comments textarea').val();
			if (comments.length <= 0) {
				error += 'Please specify';
			}
		}
		if ( error !== '' ) {
			jQuery('#plw-error').html(error);
			return false;
		}
		jQuery('#plw-error').html('');
		return true;
	}

});