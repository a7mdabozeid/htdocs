// 

jQuery(function () {
    var checked = setInterval(function () {
        if( jQuery('#_weight').val().length <= 0 || jQuery('#_weight').val() <= 0 ) {
            jQuery('#_weight').val(0.5);
        }
    } , 500);
    clearInterval(checked);
}());