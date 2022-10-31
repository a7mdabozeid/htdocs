(function ($){
    $(document).ready( function () {
        
        $(document).on('click', '.quantity_add_group', function(event) {
			var name_opt = $(this).parent().data('name');
			var last_g_num = $(this).parent().data('value');
            var html = '<tr class="quantity_groups_group">';
            html += '<td><a href="#remove" class="br_mm_remove_group"><i class="fa fa-times"></i></a>';
            html += '<input class="quantity_groups_name" style="width:70%;" type="text" data-name="name" name="' + name_opt + '[groups]['+last_g_num+'][name]"></td>';
            html += '</tr>';
            $(this).parent().find('.quantity_groups tbody').append($(html));
            last_g_num++;
            $(this).parent().data( 'value', last_g_num );            
        });
        
        $(document).on('click', '.br_mm_remove_group', function(event) {
            event.preventDefault();
            var $parent = $(this).parents('.quantity_groups_group');
            $parent.remove();
        });
        function check_br_minmax_fix_duplicate() {
            if( $('.br_minmax_fix_duplicate').prop('checked') ) {
                $('.br_minmax_fix_duplicate_show').show();
            } else {
                $('.br_minmax_fix_duplicate_show').hide();
            }
        }
        check_br_minmax_fix_duplicate();
        $(document).on('change', '.br_minmax_fix_duplicate', check_br_minmax_fix_duplicate);
        $(document).on('change', '.berocket_addons', function() {
            $(this).parents('.br_framework_submit_form').addClass('br_reload_form');
        });
        function brapl_use_local_text() {
            if( $('.brapl_use_local_text').length ) {
                if( $('.brapl_use_local_text').prop('checked') ) {
                    $('.berocket_text_input_message').show();
                } else {
                    $('.berocket_text_input_message').hide();
                }
            }
        }
        brapl_use_local_text();
        $(document).on('change', '.brapl_use_local_text', brapl_use_local_text);
    });
})(jQuery);
