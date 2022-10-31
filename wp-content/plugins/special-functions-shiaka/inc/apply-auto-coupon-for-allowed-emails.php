<?php


// check_email_auto_applied

add_action( 'wp_ajax_check_email_auto_applied', 'check_email_auto_applied_function' );
add_action( 'wp_ajax_nopriv_check_email_auto_applied', 'check_email_auto_applied_function' );

function check_email_auto_applied_function() {
    
    global $wpdb, $woocommerce;

    $return = [];
    $email = filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL);
    
    if(!$email) {
        wp_die();
    }
    
    $domain = '*'.substr($email, strpos($email, '@'));
    
 $results = $wpdb->get_results( 
                    $wpdb->prepare(
                        "SELECT DISTINCT post_id FROM {$wpdb->prefix}postmeta WHERE post_id in (".
                        "SELECT DISTINCT post_id FROM {$wpdb->prefix}postmeta WHERE post_id in (SELECT post_id FROM {$wpdb->prefix}postmeta WHERE (meta_key ='customer_email' AND (meta_value LIKE '%{$domain}%' OR meta_value LIKE '%{$email}%')) ) "
                        ."AND (meta_key = '_acfw_schedule_start' AND ( meta_value ='' OR meta_value < '".date('Y-m-d h:i:s')."')) "
                        .")"
                        ."AND (meta_key = '_acfw_schedule_end' AND ( meta_value ='' OR meta_value > '".date('Y-m-d h:i:s')."')) "
                    ), ARRAY_N);

    if(!empty($results)):
        $query = new WP_Query(array(
            'post_type' => 'shop_coupon',
            'post__in' => $results[0],
            'order' => 'desc',
            'post_status' => 'publish'
        ));
        
        
        if($query->have_posts()):
            
            while($query->have_posts()):
                $query->the_post();
                $return['code'] = get_the_title();
            endwhile;
            wp_reset_postdata();
        endif;

    endif;
    
    
     
    echo json_encode($return);
    wp_die();
}

add_action('woocommerce_after_checkout_form', 'debounce_add_jscript_checkout');

function debounce_add_jscript_checkout() {
?>
<style>
    #ship-to-different-address {
        display: none;
    }
</style>
<script>

function checkEmailAjax(email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if ( email != null && email != '' && email != undefined && email.length >= 6 &&  emailReg.test( email ) && jQuery('body').attr('check-email') != true ) {
        jQuery('body').attr('check-email', true);
        
        jQuery.ajax({
            url: '<?= admin_url( 'admin-ajax.php' ); ?>',
            type: 'POST',
            dataType: 'json',
            data: {email, action: 'check_email_auto_applied'},
            async: true
        })
        .done(function ajaxDone(res) {
            if(res.code) {
                var textData = jQuery('#wc-checkout-js-extra').html(), obj = textData.slice(textData.indexOf('=')+1, textData.lastIndexOf('}')+1 );
                
                var jsonData = JSON.parse(obj),
                fromData = {
                    coupon_code: res.code,
                    security: jsonData.apply_coupon_nonce
                };
                
                jQuery.ajax({
                    url: '<?= site_url() ?>?wc-ajax=apply_coupon',
                    type: 'POST',
                    dataType: 'json',
                    data: fromData,
                    async: true
                })
                .always(function ajaxAlways() { 
                    jQuery('input#billing_phone').change();
                });
                
                // jQuery('input[name="coupon_code"]').val(res.code);
                // jQuery('input[name="coupon_code"]').closest('form').submit();
            }
        })
        .always(function ajaxDone(res) {
            jQuery('body').attr('check-email', false);
            // console.log('res', res);
        });
    }
            
}


    
    var checkoutCouponExtraJS = setInterval(function () {
        if( jQuery('#wc-checkout-js-extra').length > 0 ) {
            var _input = jQuery('#billing_email'),
            email = _input.val();
            checkEmailAjax(email);
            clearInterval(checkoutCouponExtraJS);
        }
    }, 100);

    
    var c = 0;
    jQuery('#billing_email').on('keyup', function () {
        clearTimeout(c);
        var _input = jQuery(this),
        email = _input.val();
        c = setTimeout(function () {
            checkEmailAjax(email);
        }, 2000, email);
        
    })
</script>
<?php
}
