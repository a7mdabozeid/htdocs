(function ($){
    $(document).ready( function () {
        $('.advanced-labels-font-family').on( 'change', function() {
            var fontFamily = $(this).val();

            if ( fontFamily.length ) {
                WebFont.load({
                    google: {
                        families: [fontFamily]
                    }
                });
            }

            $(this).css('font-family', fontFamily );
        }).trigger('change');
    });
})(jQuery);