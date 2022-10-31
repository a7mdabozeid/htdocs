jQuery(document).ready(function($) {

    // Saving the State of checked posts of post types
    jQuery('.estp-add-new-item').on('click','div.estp_individual_term .estp_post_type_term',function(){
        var target = jQuery(this);
        var selected_id = jQuery(this).val();
        var hidden_field_value = jQuery(this).closest('.estp-specific-page-nav-disp').find('.estp_checked_cma').val();
        var hidden_field_array = hidden_field_value.split(',');
        if (jQuery(this).prop('checked')) {
            if (hidden_field_value != '') {
                var match_status = false;
                for(index in hidden_field_array){
                    if (hidden_field_array[index] == selected_id) {
                        match_status = true;
                    }
                }
                if (!match_status) {
                    jQuery(this).closest('.estp-specific-page-nav-disp').find('.estp_checked_cma').val( hidden_field_value + ',' + selected_id);
                }
            }
            else{
                jQuery(this).closest('.estp-specific-page-nav-disp').find('.estp_checked_cma').val(selected_id);
            }
            
        }
        else{
            var new_hidden_field = new Array();
            iterator = 0;
            for(index in hidden_field_array){
                if (hidden_field_array[index] != selected_id) {
                    new_hidden_field[iterator] = hidden_field_array[index];
                    iterator++;
                }
            }
            var hidden_field_string = new_hidden_field.join();
            jQuery(this).closest('.estp-specific-page-nav-disp').find('.estp_checked_cma').val(hidden_field_string);
        }
    });

    // Throwing data for pagination ajax 
    jQuery('.estp-add-new-item').on('click','.estp_pagination_links a',function(e){
        e.preventDefault();
        var target = jQuery(this);
        var checked_state = jQuery(this).closest('.estp-specific-page-nav-disp').find('.estp_checked_cma').val();
        var pagination_nonce = jQuery(this).closest('.estp-specific-page-nav-disp').find('.pagination_nonce').val();
        var key_val_pair = new Array();
        var link = jQuery(this).attr('href');
        var query_string = link.split('?')[1];
        var parameters = query_string.split('&');
        var iterator = 0;
        for( index in parameters ){
            if (parameters[index].search('paged') > -1) {
                key_val_pair[iterator] = parameters[index];
                iterator++;
                jQuery(this).closest('.estp-specific-page-nav-disp').find('.estp_pagination_links').each(function(){
                    var data_val = jQuery(this).data('paged');
                    var data_key_val = data_val.split('=')[0];
                    var para_key_val = parameters[index].split('=')[0];
                    if (data_key_val != para_key_val) {
                        console.log('matched');
                        key_val_pair[iterator] = data_val;
                        iterator++;
                    }
                });
            }
        }
        jQuery.post(
            ajaxurl,
            {
                'action':    'estp_pagination_links',
                'key_val_pair': key_val_pair,
                'checked_state': checked_state,
                'nonce_pagination': pagination_nonce
            },
            function(response){
                var clear_response = response.slice(0,-1);
                console.log(clear_response);
                target.closest('.estp-specific-page-nav-disp').find('.estp_post_types_div').html(clear_response);
            }
        );
    });
	
	$('div.estp-nav-tab-wrapper a').click(function(){
		var tab_id = $(this).attr('data-tab');
		$('.estp-content').hide();
		$('div.estp-nav-tab-wrapper a').removeClass('estp-nav-tab-active');
		$(this).addClass('estp-nav-tab-active');
		$("#"+tab_id).show();
	});

    // To Hide Custom Scroll ID Field
    $('.estp-add-new-item').on('change', '.estp-scroll-type',function() {
        var $this = $(this);
        if( $this.val() == 'custom_element' ) {
            $(this).closest('.estp-page-scroll-nav').find('.estp-custom-element-id').show();
        } else {
            $(this).closest('.estp-page-scroll-nav').find('.estp-custom-element-id').hide();
        }
    });
	
	//show fields according to Link Type (Internal External and contentSlider)
	$('.estp-add-new-item').on('click','.estp-link-type', function(){
	  var $this = $(this);

	  if($this.val() == 'internal') {
	    $this.closest('.estp-item-body').find('.estp-internal-tab').show();
	    $this.closest('.estp-item-body').find('.estp-external-tab').hide();
	   	$this.closest('.estp-item-body').find('.estp-contentSlider-type').hide();
        $this.closest('.estp-item-body').find('.estp-page-scroll-nav').hide();
	  }
	  else if($this.val() == 'external'){
	  	$this.closest('.estp-item-body').find('.estp-external-tab').show();	
	  	$this.closest('.estp-item-body').find('.estp-internal-tab').hide();
	  	$this.closest('.estp-item-body').find('.estp-contentSlider-type').hide();
        $this.closest('.estp-item-body').find('.estp-page-scroll-nav').hide();
	  }
	  else if($this.val() == 'content_slider'){
	    $this.closest('.estp-item-body').find('.estp-contentSlider-type').show();   
	    $this.closest('.estp-item-body').find('.estp-external-tab').hide();
	    $this.closest('.estp-item-body').find('.estp-internal-tab').hide();
        $this.closest('.estp-item-body').find('.estp-page-scroll-nav').hide();
	  }
      else if($this.val() == 'scroll_navigation') {
        $this.closest('.estp-item-body').find('.estp-page-scroll-nav').show();
        $this.closest('.estp-item-body').find('.estp-internal-tab').hide();
        $this.closest('.estp-item-body').find('.estp-external-tab').hide();
        $this.closest('.estp-item-body').find('.estp-contentSlider-type').hide();
      }
	  else{
	  	return false;
	  }
	});

	//Customize Layout Hide Show on Customize box checked/unchecked
	$('#estp-customize_layout_select').on('click',function(){
		if($(this).is(':checked'))
		{
			$('#estp-customize-fields-show').show();
		}
		else
		{
			$('#estp-customize-fields-show').hide();
		}
	});


	//display settings for template image
    $div_select = $('#estp-display-settings-wrap');
	$div_select.on('change', '.estp-image-selector', function () {
        var selected_tmp_img = $(this).find('option:selected').data('img');
        $(this).closest('#estp-display-settings-wrap').find('.estp-layout-template-image img').attr('src', selected_tmp_img);
    });

	$('.color-field').wpColorPicker();

	//display content slider bgcolor and text color option
	$('.estp-link-type').change(function(){ 
            
            var option = $(this).val();

            if(option == 'content_slider'){
            	$('.estp-content-slider-dynamic-options').show();
                $('.estp-add-item-wrap').find('.estp-social-icons').show();
				$('#estp-select-content-slide-style').show();
            }else{
            	$('.estp-content-slider-dynamic-options').hide();
				$('#estp-select-content-slide-style').hide();
            }
    });


	//hide color customize for close button when style 2 is selected else show
	$('#estp-slide-style-select').change(function(){ 
            
            var selected_style = $( "#estp-slide-style-select option:selected" ).val();

            if(selected_style == 'style-1'){
				$('#estp-content-slide-close-option').show();
            }
            if(selected_style == 'style-2'){
				$('#estp-content-slide-close-option').hide();
            }
    });

 

    //validation
    var error1 = false;
    var error2 = false;
    // var error3 = false;

    jQuery("#estp-tab-title input[type='text']").on("change keyup",function(){
    	
		if (jQuery(this).val().length !=0) 
		{
			
			jQuery(this).parent().children("span").remove();
			error1 = false;
		}
	});

	jQuery("#estp-tab-text input[type='text']").on("change keyup",function(){
    	
		if (jQuery(this).val().length !=0) 
		{
			
			jQuery(this).parent().children("span").remove();
			error2 = false;
		}
	});


	jQuery("#main-form").on("submit",function(event){
		
		var tab_title = jQuery("#estp-tab-title input[type='text']").val().trim(); 
		//var tab_text = jQuery("#estp-tab-text input[type='text']").val().trim(); 
		//var tab_content = jQuery("#estp-tab-content textarea#estp-content").val().trim(); 

		if (tab_title.length == 0) 
		{
			jQuery("#estp-tab-title input[type='text']").parent().children("span").remove();
			jQuery("#estp-tab-title input[type='text']").parent().append("<span style='color:red;'>This field should not be left empty</span>");
			error1 = true;
		}
		// if (tab_text.length == 0) 
		// {
		// 	jQuery("#estp-tab-text input[type='text']").parent().children("span").remove();
		// 	jQuery("#estp-tab-text input[type='text']").parent().append("<span style='color:red;'>This field should not be left empty</span>");
		// 	error2 = true;
		// }

        if (error1) 
		{
			event.preventDefault();
		}
		else
		{
			return;
		}
	});

	// toggle caret-up/down icons on tab settings page 
	$('.estp-add-item-wrap').show();
	$(".estp-tab-items h3.estp-tab-items-drop").on('click',function(){

        $('.estp-add-item-wrap').toggleClass('estp-wrap-active');

		$('.estp-add-item-wrap').slideToggle(800);
		if($('.estp-add-item-wrap').hasClass('estp-wrap-active'))
		{
			$('.estp-tab-items h2 span').html('<i class="fa fa-caret-down"></i>');
		}
		else if(!$('.estp-add-item-wrap').hasClass('estp-wrap-active'))
		{
			$('.estp-tab-items h2 span').html('<i class="fa fa-caret-up"></i>');
		}
	});

	//Tab Settings Page delete Item
	$('.estp-add-item-wrap').on('click', '.item_delete', function (e) {
        if (confirm($(this).data('confirm'))) {
            $(this).closest('.estp-item-wrap').remove();
            if($('.estp-item-wrap').length <=6 ){
              $('.estp-add-button').prop('disabled', false);
            }
            e.preventDefault();
        } else {
            e.preventDefault();
        }
    });

	//sortable
    /** Sortable initialization */
    $('.estp-add-new-item').sortable({
        items: '.estp-item-wrap',
        containment: 'parent',
        handle: '.item_sort',
        tolerance: 'pointer',
        cursor: "move",
        update: function () {
        }
    });


    //Add New Item using AJAX
	$all_section = $('.estp-add-item-wrap');
    $all_section.on('click', '.estp-add-button', function (e) {
    	var $this = $(this);

        $parent = $(this).parent().find('.estp-add-new-item');

        $action = $(this).data('action');
        add_new_div = $parent;

        if($parent.find('.estp-item-wrap').length <= 12) //less than 6 items$_POS
        {  
        	if($action == 'add_item')
        	{
        		$.ajax({

        			url: estp_backend_ajax.ajax_url,
        			type: 'post',
        			data: {
                        action: 'estp_backend_ajax',
                        _action: 'add_new_item_action',
                        estp_nonce: estp_backend_ajax.ajax_nonce
                    },
                    beforeSend: function() {
                      $this.prop('disabled', true);
                    },
                    success: function (response) {

                        add_new_div.append(response);
                        $('.estp-icon-picker').iconPicker();

                        var response = $(response);
                        var key = response.find('.estp_key_unique').val();
                        var key1 = "estp-html-text";
                        var key21 = "estp_content_"+key;

                        tinymce.execCommand( 'mceRemoveEditor', false, key1 );
                        tinymce.execCommand( 'mceAddEditor', false, key21 );
                        quicktags({id : key21});

                        //init tinymce
                        tinymce.init({
                           selector: key21,          
		                   relative_urls:false,
		                   remove_script_host:false,
		                   convert_urls:false,
		                   browser_spellcheck:true,
		                   fix_list_elements:true,
		                   entities:"38,amp,60,lt,62,gt",
		                   entity_encoding:"raw",
		                   keep_styles:false,
		                   paste_webkit_styles:"font-weight font-style color",
		                   preview_styles:"font-family font-size font-weight font-style text-decoration text-transform",
		                   wpeditimage_disable_captions:false,
		                   wpeditimage_html5_captions:true,
		                   plugins:"charmap,hr,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpeditimage,wpgallery,wplink,wpdialogs,wpview",
		                   // selector:"#" + fullId,
		                   resize:"vertical",
		                   menubar:false,
		                   wpautop:true,
		                   indent:false,
		                   toolbar1:"bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv",
                           toolbar2:"formatselect,underline,alignjustify,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",
		                   toolbar3:"",
		                   toolbar4:"",
		                   tabfocus_elements:":prev,:next",
		                   body_class:"id post-type-post post-status-publish post-format-standard",

                        }); //init tinymce ends


                        $this.prop('disabled', false);
                    }
        		});
        	}
        	e.preventDefault();  //if not add_item

        }
        else{  //if there more than 6 items
        	alert('Maximum FAQ addition reached.');
        }

    });

     $('.estp-tab-items').on('click', '.estp-item-header', function (e) {
        $(this).closest('.estp-item-wrap').find('.estp-item-body').slideToggle("slow");
        $(this).closest('.estp-item-wrap').find('.estp-item-hide-show').toggleClass('active-item-show-hide');
    });

    $( ".estp-tab-items" ).on('keyup', '.estp-tab-text', function (e) {
    	
    	if( $(this).val() == '' )
    	{
    		var count = $(this).parent().parent().parent().find('.estp-item-header').find('.estp-item-header-title').attr('data-count');
    		$(this).closest('.estp-item-wrap').find('.estp_title_text_disp').text('Tab '+count);
    	}
    	else
    	{
    		$(this).closest('.estp-item-wrap').find('.estp_title_text_disp').text($(this).val());
    	}
    });

    //Display Content Type In Tab Settings
    $('.estp-add-new-item').on('change','.estp-content-type-select',function(){   
        var $this = $(this); //estp-content-type-select
        var selected_style = $this.find('option:selected').val();
        $this.closest('.estp-contentSlider-type').find('.estp-content-type').hide();
        $this.closest('.estp-contentSlider-type').find('.estp-'+selected_style).show();         
    });
    

    //Sortable Social Icons
     $(function() {
        $( ".estp-sort-social-icons" ).sortable({
            cancel: ".estp-socialicons-header-title, .estp-socialicons-layout-wrap",
            placeholder: "estp-social-icon-placeholder",
            containment: "parent",
            axis: "y",
            handle:".estp-icon-handle"
        });
     });

     //shortcode focus and copy
    $('.estp-shortcode-value').click(function () {
        $(this).select();
        $(this).focus();
        document.execCommand('copy');
        $(this).next('.estp-copied-info').show().delay(1000).fadeOut();
    });


    // var post_id_attr;
	$('.estp-add-new-item').on('click','.estp-select-post-type',function(e){

	var $this = $(this);
	var post_type = [];
	var taxonomy = [];
	var term = [];
	var blog_nonce = $this.closest('.estp-recent-blog').find('.estp-blog-nonce').val();

	var i = 0;
	$('.estp-post-type').each(function(){
        var $this = $(this);
        if($this.prop('checked'))
		{
			post_type[i] = $this.attr('id');
			i++;
		}
	});


	i = 0;
    $('.estp-taxonomy-type').each(function(){
        var $this = $(this);
        if ($this.prop('checked')) 
        {
            taxonomy[i] = $this.data('post-taxonomy');
            i++;
        }
    });

    i = 0;
    $(".estp-term-type").each(function(){
            var $this = $(this);
        if ($this.prop('checked')) 
        {
            term[i] = $this.data('post-taxonomy-termslug');
            i++;
        }
    });

    jQuery.post(ajaxurl,
        {
            'action': 'estp_blog_ajax',
            'blog_nonce':blog_nonce,
            'post_type': post_type,
            'taxonomy' : taxonomy,
            'term' : term
        },
        function(response){
        	
        	$this.closest('.estp-recent-blog').find('.estp-show-blog-post').html(response);
        }
    );
   call_fxn($this);
});

var call_fxn = function($this)
{
	if($this.hasClass('estp-select-post-type') && $this.hasClass('estp-post-type') && $this.prop('checked'))
	{	

        $this.closest('.estp-field-wrap').find('.estp-show-tax').show();
    	
	}
    else if($this.hasClass('estp-select-post-type') && $this.hasClass('estp-post-type') && $this.prop('checked',false))
    {
        $this.closest('.estp-field-wrap').find('.estp-taxonomy-type').prop('checked',false);
        $this.closest('.estp-field-wrap').find('.estp-term-type').prop('checked',false);
        $this.closest('.estp-field-wrap').find('.estp-show-tax').hide();
    }

    if( $this.hasClass('estp-taxonomy-type') && $this.prop('checked') )
    {
        $this.closest('.estp-field-wrap').find('.estp-show-term').show();
    }
    else if($this.hasClass('estp-taxonomy-type') && $this.prop('checked',false))
    {
        $this.closest('.estp-field-wrap').find('.estp-term-type').prop('checked',false);
        $this.closest('.estp-field-wrap').find('.estp-show-term').hide();
    }   

}
$('.estp-icon-picker').iconPicker();
$('.estp-add-new-item').on('change','.estp-tab_icon-type', function(){
    
    $this = $(this);
    var icon_type = $this.val();

    if(icon_type == 'available_icon')
    {
        $this.closest('.estp-item-body').find('.estp_upload_own_icon').fadeOut();
        $this.closest('.estp-item-body').find('.estp_available_icon').fadeIn();
    }
    else if(icon_type == 'upload_own')
    {
        $this.closest('.estp-item-body').find('.estp_available_icon').fadeOut();
        $this.closest('.estp-item-body').find('.estp_upload_own_icon').fadeIn();
    }
    else if(icon_type == 'none')
    {
        $this.closest('.estp-item-body').find('.estp_upload_own_icon').fadeOut();
        $this.closest('.estp-item-body').find('.estp_available_icon').fadeOut();
    }
});

$('.estp-add-new-item').on('change','.estp-subscribe-image-selector', function(){
    
    var $this = $(this);
    var selected_layout = $this.val();

    var val = 'subscribe-form-layout-';

    if(selected_layout == val+'5')
       $this.closest('.estp-subscription-form').find('.estp_subscribe_btn_icon').show();
    
    else
        $this.closest('.estp-subscription-form').find('.estp_subscribe_btn_icon').hide();

    if(selected_layout == val+'1' || selected_layout == val+'2' || selected_layout == val+'4' || selected_layout == val+'5')
       $this.closest('.estp-subscription-form').find('.estp-subscription-description').show(); 
       
     
    else
      $this.closest('.estp-subscription-form').find('.estp-subscription-description').hide();   
    
    
    if(selected_layout == val+'3' || selected_layout == val+'4')
        $this.closest('.estp-subscription-form').find('.estp-subscription-title').show();

    else
        $this.closest('.estp-subscription-form').find('.estp-subscription-title').hide();
});
$('.estp-subscribe-image-selector').trigger('change');

$('.estp-add-new-item').on('change','.estp-woocommerce-image-selector',function(){
    var $this = $(this);
    var selected_layout = $this.val();

    var val = 'woocommerce-layout-';

    if(selected_layout == val+'2' || selected_layout == val+'4')
        $this.closest('.estp-woocommerce-product').find('.estp_woocommerce_btn_icon').show();

    else
        $this.closest('.estp-woocommerce-product').find('.estp_woocommerce_btn_icon').hide();
});
$('.estp-woocommerce-image-selector').trigger('change');


$('.estp-showdate-options').on('change, click',function(){
    if($(this).is(':checked'))
    {
        $(this).closest('.estp-woocommerce-product').find('.estp-show-date-wrapper').show();
    }
    else{
        $(this).closest('.estp-woocommerce-product').find('.estp-show-date-wrapper').hide();   
    }

});


//display settings for template image

$('.estp-add-new-item').on('change', '.estp-image-selector', function () {

    var selected_twt_img = $(this).find('option:selected').data('img');
    var value1 = $(this).siblings('.estp-image-preview-wrap').find('.estp-twitter-layout-template-image img').attr('src', selected_twt_img);
});

//display settings for blogs image
$('.estp-add-new-item').on('change', '.estp-blogs-image-selector', function () {
  
    var selected_blog_img = $(this).find('option:selected').data('img');
    var value1 = $(this).siblings('.estp-image-preview-wrap').find('.estp-blogs-layout-template-image img').attr('src', selected_blog_img);
});



//display settings for subscription form image
$('.estp-add-new-item').on('change', '.estp-subscribe-image-selector', function () {
  
    var selected_subscribe_img = $(this).find('option:selected').data('img');
    var value1 = $(this).siblings('.estp-image-preview-wrap').find('.estp-subscribe-layout-template-image img').attr('src', selected_subscribe_img);
});

//display settings for social icons image
$('.estp-add-new-item').on('change', '.estp-socialicons-image-selector', function () {
  
    var selected_socialicons_img = $(this).find('option:selected').data('img');
    var value1 = $(this).siblings('.estp-image-preview-wrap').find('.estp-socialicons-layout-template-image img').attr('src', selected_socialicons_img);
});

//display settings for blogs image
$('.estp-add-new-item').on('change', '.estp-woocommerce-image-selector', function () {
  
    var selected_woocommerce_img = $(this).find('option:selected').data('img');
    var value1 = $(this).siblings('.estp-image-preview-wrap').find('.estp-woocommerce-layout-template-image img').attr('src', selected_woocommerce_img);
});

$('#estp-checkall').click(function () {
    if ($(this).is(':checked')) {
        $('.estp-select-subs').attr('checked', 'checked');
    } else {
        $('.estp-select-subs').removeAttr('checked');

    }
});

$('#estp-checkall-tab').click(function () {
    if ($(this).is(':checked')) {
        $('.estp-select-tab').attr('checked', 'checked');
    } else {
        $('.estp-select-tab').removeAttr('checked');

    }
});

/**
 * Fetches Taxonomies as per Post Types
 *
 * @since 1.0.0
 */
 var notices = estp_backend_js_object.strings;
$('.estp-add-new-item').on('change','.estp-post-type-trigger',function() {
    
    var $selector = $(this);
    var post_type = $selector.val();
   
    $.ajax({
        type: 'post',
        url: estp_backend_js_object.ajax_url,
        data: {
            post_type: post_type,
            action: 'estp_post_type_taxonomy_action',
            _post_type_taxonomy_wpnonce: estp_backend_js_object.ajax_nonce,
        },
        beforeSend: function (xhr) {
            $selector.parent().find('.estp-ajax-loader').show();
        },
        success: function (res) {
            //alert(res);
            $selector.closest('.estp-recent-blog').find('.estp-ajax-loader').hide();
            $selector.closest('.estp-recent-blog').find('.estp-post-taxonomy-trigger').html(res);
            $selector.closest('.estp-recent-blog').find('.estp-post-terms-trigger').html('<option value="">' + notices.post_terms_dropdown_label + '</option>');
        }
    });
});

/**
* Fetches Terms as per Taxonomies
*
* @since 1.0.0
*/
$('.estp-add-new-item').on('change','.estp-post-taxonomy-trigger', function() {

    var $selector = $(this);
    var taxonomy = $selector.val();
    $.ajax({
        type: 'post',
        url: estp_backend_js_object.ajax_url,
        data: {
            taxonomy: taxonomy,
            action: 'estp_taxonomy_terms_action',
            _post_type_taxonomy_wpnonce: estp_backend_js_object.ajax_nonce,
        },
        beforeSend: function (xhr) {
            $selector.parent().find('.estp-ajax-loader').show();
        },
        success: function (res) {
            //alert(res);
            $selector.parent().find('.estp-ajax-loader').hide();
            $selector.closest('.estp-recent-blog').find('.estp-post-terms-trigger').html(res);
        }
    });
});
	
$('#remove-tabs').click(function(){
    var chkcntr = $('.estp-select-tab:checkbox:checked').length; 
    
    if( chkcntr == 0 || chkcntr<=0)
    {
        alert('0 Items Checked. Please Check Any item to delete');
        return false;
    }
    else if( chkcntr > 0 )
    {
        confirm_status = confirm('Are you sure you want to delete all selected Tab?');
        if(!confirm_status)
        {
            return false;
        }
        return true;
    }
});

if (!document.getElementById("estp-import-btn")) {
//It does not exist
}
else
{
    document.getElementById("estp-import-btn").onchange = function () 
    {
        document.getElementById("estp_import_filename").value = this.value;
    };
}

// Mailchimp Settings
$('body').find('select#estp-form-subscription-type').on('change', function(){
    var subscription_type = $(this).val();
    if(subscription_type == 'mailchimp_subscription') {
        $(this).closest('.estp-item-wrap').find('#estp-mailchimp-lists-show').show();
    } else {
        $(this).closest('.estp-item-wrap').find('#estp-mailchimp-lists-show').hide();
    }
});

// Function to duplicate the tab_setting
$('body').find('.estp-tab-copy').on('click', function(e) {
    e.preventDefault(e);
    let $this = $(this);
    let tab_id = $this.data('tab-id');
    $.ajax({
        url: estp_backend_js_object.ajax_url,
        type: 'post',
        data: {
            tab_id: tab_id,
            action: 'estp_tab_copy',
            _wpnonce: estp_backend_js_object.ajax_nonce,
        },
        beforeSend: function(xhr) {
            estp_generate_info('<img src='+estp_backend_js_object.ajax_loader+'> Loading Please Wait ...', 'ajax');
        },
        success: function (res) {
            var res = JSON.parse(res);
            if(res.error == 1) {
                estp_generate_info(res.error_message, 'error');
            } else {
                estp_generate_info(res.success_message, 'info');
                window.location = res.redirect_url;
            }
        }
    });
});

});

var notice_timeout;
function estp_generate_info(info_text, info_type) {
    clearTimeout(notice_timeout);
    switch (info_type) {
        case 'error':
            var info_html = '<p class="estp-error">' + info_text + '</p>';
            break;
        case 'info':
            var info_html = '<p class="estp-info">' + info_text + '</p>';
            break;
        case 'ajax':
            var info_html = '<p class="estp-ajax">' +info_text+ '</p>';
        default:
            break;

    }
    jQuery('.estp-notice-head').html(info_html).show();
    if (info_type != 'ajax') {
        notice_timeout = setTimeout(function () {
            $('.estp-notice-head').slideUp(1000);
        }, 5000);
    }
}


function subscription_upld(current){
   
    var subscribe_image = wp.media({
        title: 'Upload Subscribe Image',
        multiple: false,
        library : {
            type: 'image'
        }
    }).open()
            .on('select',function(e){
                var uploaded_subscribe_img = subscribe_image.state().get('selection').first();
                var subscribe_img_url = uploaded_subscribe_img.toJSON().url;
                jQuery(current).closest('.estp_upload_subscribe_img').find('.estp-subscribe-image-url').val(subscribe_img_url);
                jQuery(current).closest('.estp-field-wrap').find('.estp-subscribe-img-preview img').attr('src',subscribe_img_url);
            });
}

function own_icon_upld(current){

    var image = wp.media({
        title: 'Upload Icon',
        multiple: false
    }).open()
            .on('select', function (e) {
                var uploaded_icon = image.state().get('selection').first();
                var img_url = uploaded_icon.toJSON().url;
                $(current).closest('.estp_upload_own_icon').find('.estp-image-url').val(img_url);
                $(current).closest('.estp-field-wrap').find('.estp-iconpreview img').attr('src', img_url);
            });
}

function enable_offset(val)
{

    var $this = $(val);

    if($this.is(":checked"))
        $this.closest('.estp-field-wrap').next().fadeIn();
    else
        $this.closest('.estp-field-wrap').next().fadeOut();
}