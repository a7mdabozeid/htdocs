<?php

// define the in_admin_footer callback 
function action_in_admin_footer() { 
    global $pagenow, $current_page, $post, $user;

    if( $pagenow == 'post.php' && !empty( $_GET['action'] ) && $_GET['action'] == 'edit' && $post->post_type == 'shop_order' ) {
        
        $user = wp_get_current_user();
        if ( !in_array( 'administrator', (array) $user->roles ) && !in_array( 'superadmin', (array) $user->roles ) ) {
            echo '
                <script>
                   setInterval(function () {
                    //  jQuery("#aramex_shipment").on("submit", function (e) {
                    //     e.preventDefault();
                    //     return false;
                    // });

                     if( jQuery("#aramex_return_shipment_creation_submit_id").length > 0 ) {
                         jQuery("#aramex_shipment_info_service_type").val("RTRN");
                     }
                     
                    jQuery("select, input, textarea, span.select2-selection, button.refund-items").each(function () {
                        var _in = jQuery(this);
                        _in.attr("data-value", _in.val());
                        _in.attr("readonly", "readonly");
                        _in.css({"pointerEvents": "none"});
                        _in.on("keyup", function (){
                            _in.val(_in.attr("data-value"));
                        });
                        _in.on("focus", function (e){
                            e.preventDefault();
                            return false;
                        });
                        _in.on("click", function (e){
                            e.preventDefault();
                            return false;
                        });
                        _in.on("keyup", function (e){
                            e.preventDefault();
                            return false;
                        });
                        _in.on("keydown", function (e){
                            e.preventDefault();
                            return false;
                        });
                        _in.on("change", function (e){
                            e.preventDefault();
                            return false;
                        });
                    });
                   }, 1000);
                </script>
            ';
        }
    }
}; 

// add the action 
add_action( 'in_admin_footer', 'action_in_admin_footer' ); 

