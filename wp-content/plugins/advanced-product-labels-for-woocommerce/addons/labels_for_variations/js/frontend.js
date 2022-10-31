(function ($){
    $(document).ready( function () {
        var default_labels = [];
        $('.brapl_variation_replace').closest('.berocket_better_labels').each(function() {
            default_labels.push($(this).clone());
        });
        $( 'form.variations_form' ).on( 'check_variations', function( event, variation ){
            $('.brapl_variation_replace').closest('.berocket_better_labels').addClass('berocket_hide_variations_load');
        });
        $( 'form.variations_form' ).on( 'reset_data', function( event, variation ){
            default_labels.forEach(function(element) {
                var type = element.find('.brapl_variation_replace').data('type');
                $('.brapl_variation_replace').closest('.berocket_better_labels.berocket_better_labels_'+type).html(element.html()).removeClass('berocket_hide_variations_load');
            });
        });
        $( 'form.variations_form' ).on( 'found_variation', function( event, variation ){
            let var_id = variation.variation_id;

            if ( !var_id ) {
                var_id = $(this).closest('.product').attr('id').replace('product-', '');
            }

            $.ajax({
                type: 'POST',
                url: brlabelsHelper.ajax_url,
                data: {
                    'action': 'variation_label',
                    'variation_id': var_id
                },
                success: function(result){
                    var new_element = $('<div>'+result+'</div>');
                    $('.brapl_variation_replace').each(function() {
                        var type = $(this).data('type');
                        var replace = $(this).closest('.berocket_better_labels');
                        var replaceWith = new_element.find('.berocket_better_labels.berocket_better_labels_'+type);
                        if( replaceWith.length ) {
                            replaceWith.append($(this));
                            replaceWith.addClass('berocket_hide_variations_load');
                            replace.replaceWith(replaceWith);
                            replaceWith.trigger('br-update-product');
                            replaceWith.removeClass('berocket_hide_variations_load');
                        } else {
                            replace.find('*').not($(this)).remove();
                        }
                    });

                    berocket_regenerate_tooltip();
                }
            });
        });
    }); 
})(jQuery);