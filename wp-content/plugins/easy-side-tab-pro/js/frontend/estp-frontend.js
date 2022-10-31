 jQuery(document).ready(function($) {
	
	//On Tab Title Click Show The Content Div
	$('.estp-content-slider-title ').on('click', function(){
			var $this = $(this);
			if($this.closest('.estp-slide-content-style-main-wrap').parent().hasClass('estp-slide-content-style-2')){
				if($this.hasClass('estp-handle-clicked')){
					$this.removeClass('estp-handle-clicked');
					$this.closest('.estp-content-slider').removeClass('estp-content-show-style-2');
					$this.closest('.estp-slide-content-style-main-wrap').parent().removeClass('estp-content-style-2-active');
				}
				else{
					$this.addClass('estp-handle-clicked');
					$this.closest('.estp-content-slider').addClass('estp-content-show-style-2');
					$this.closest('.estp-slide-content-style-main-wrap').parent().addClass('estp-content-style-2-active');
				}
			}else if($this.closest('.estp-slide-content-style-main-wrap').parent().hasClass('estp-slide-content-style-1')){
			
				if($this.closest('.estp-content-slider').find(".estp-slider-content").hasClass("estp-content-hidden")){
				  $this.closest('.estp-content-slider').find(".estp-slider-content").removeClass('estp-content-hidden');
				  $this.closest('.estp-content-slider').find(".estp-slider-content").addClass('estp-content-show');
				}
				else{  // if click garda estp-content-hidden chaina bhane
				  $this.closest('.estp-content-slider').find(".estp-slider-content").addClass('estp-content-hidden');
				  $this.closest('.estp-content-slider').find(".estp-slider-content").removeClass('estp-content-show');
				}
			}

	});

	//On Close Button Click Dismiss The Content Div
	$('span.estp-close-slider-content').on( 'click', function(){
		if($('.estp-slider-content').hasClass('estp-content-hidden'))
		{
			$(".estp-slider-content").removeClass('estp-content-show');
			$(".estp-slider-content").addClass('estp-content-hidden');	
		}
		else
		{
			$(".estp-slider-content").addClass('estp-content-hidden');
		  	$(".estp-slider-content").removeClass('estp-content-show');	
		}
	});


	//Hide Or Show Tab on Mobile Device
	if( $("#estp-front-display-wrap").hasClass( "estp-mobile-display-on" ) ) 
    {
		var mobile = (/iphone|ipod|android|blackberry|mini|windows\sce|palm/i.test(navigator.userAgent.toLowerCase()));  
	    if (mobile) 
	    { 
	        $("#estp-front-display-wrap").show();// OR you can use $('.navWrap').css('display', 'block');
	    } 
	    else{
	    	$("#estp-front-display-wrap").show();// OR you can use $('.navWrap').css('display', 'none');
	    }
	}
	else
	{
		var mobile = (/iphone|ipod|android|blackberry|mini|windows\sce|palm/i.test(navigator.userAgent.toLowerCase()));  
	    if (mobile) 
	    { 
	        $("#estp-front-display-wrap").hide();// OR you can use $('.navWrap').css('display', 'block');
	    } 
	    else{
	    	$("#estp-front-display-wrap").show();// OR you can use $('.navWrap').css('display', 'none');
	    }
	}

	//hide style-2 content slider when clicked outside the slider
	$(document).on('mouseup', function(e) 
	{
	    var container = $(".estp-frontend-display-wrap .estp-content-handle"); //tab bar
	    var container2 = $(".estp-frontend-display-wrap .estp-content-disp"); //content div

	    // if the target of the click isn't the container nor a descendant of the container
	    if (!container.is(e.target) && container.has(e.target).length === 0 && !container2.is(e.target) && container2.has(e.target).length === 0) 
	    {
	    	$('.estp-frontend-display-wrap').removeClass('estp-content-style-2-active');
	    
	    }
	});

	//hide estp-popup-display when clicked outside the slider
	$(document).on('mouseup', function(e) 
	{
		var container = $(".estp-tab-element"); //tab bar
	    var container2 = $(".estp-popup-display"); //content div

	    // if the target of the click isn't the container nor a descendant of the container
	    if (!container.is(e.target) && container.has(e.target).length === 0 && !container2.is(e.target) && container2.has(e.target).length === 0)
	    {
	    	if(!$('.estp-tab-wrapper').hasClass('estp-template-3') && !$('.estp-tab-wrapper').hasClass('estp-template-4'))//if except template-3
			{
	    		$('.estp-popup-display').removeClass('estp-popup-display-show');
	    		if($('.estp-tab-wrapper').hasClass('estp-template-5'))
				{
					$('.estp-inner-tab-wrapper').removeClass('estp-temp5-popup-active');
					$('.estp-template-5').removeClass('estp-animated estp-shake');
					$('.estp-template-5').find('.estp-tab-element').removeClass('estp-popup-active');
					if($('.estp-tab-wrapper').hasClass('estp-btm-lft-side-tab') || $('.estp-tab-wrapper').hasClass('estp-btm-rht-side-tab') )
					{
						$('.estp-tab-wrapper').find('.estp-inner-tab-wrapper').css({'bottom' : '0px'});
					}
				}
				if($('.estp-tab-wrapper').hasClass('estp-template-6'))	
				{
					$('.estp-tab-wrapper.estp-template-6 .estp-inner-tab-wrapper .estp-tab-element').removeClass('estp-temp-6-popup');
					$('.estp-tab-wrapper.estp-template-6 .estp-inner-tab-wrapper .estp-tab-element').removeClass('estp-temp6-hide');
					$('.estp-template-6 .estp-inner-tab-wrapper .estp-popup-display').removeClass('estp-popup-display-show');
					if($('.estp-tab-wrapper').hasClass('estp-btm-lft-side-tab') || $('.estp-tab-wrapper').hasClass('estp-btm-rht-side-tab') )
					{
						$('.estp-inner-tab-wrapper .estp-tab-element').css({'bottom':'0px'});
					}
				}
				if($('.estp-tab-wrapper').hasClass('estp-template-7'))
				{
					$('.estp-tab-wrapper.estp-template-7 .estp-inner-tab-wrapper .estp-tab-element').removeClass('estp-temp7-active-tab');
				}
				if($('.estp-tab-wrapper').hasClass('estp-template-8'))
				{
					$('.estp-popup-display').removeClass('estp-animated estp-zoomInLeft estp-zoomInRight estp-zoomInUp');
					$('.estp-tab-wrapper.estp-template-8 .estp-inner-tab-wrapper .estp-tab-element').removeClass('estp-temp8-active-tab');
					$('.estp-tab-wrapper.estp-template-8').removeClass('estp-active');
					$('.estp-tab-overlay').removeClass('estp-tab-overlay-active');
				}
				if($('.estp-tab-wrapper').hasClass('estp-template-9'))
				{
					$('.estp-tab-wrapper.estp-template-9 .estp-inner-tab-wrapper .estp-tab-element .estp-tab-element-inner').show();
					$('.estp-tab-wrapper.estp-template-9 .estp-inner-tab-wrapper .estp-tab-element .estp-close-active-tab').hide();
					$('.estp-tab-element').removeClass('estp-popup-active');
				}
				if($('.estp-tab-wrapper').hasClass('estp-template-1'))
				{
					$('.estp-tab-wrapper.estp-template-1 .estp-main-tab-wrap .estp-inner-tab-wrapper').removeClass('estp-temp1-popup-active');
					$('.estp-tab-wrapper.estp-template-1 .estp-main-tab-wrap .estp-inner-tab-wrapper .estp-tab-element').removeClass('estp-popup-active');

					if($('.estp-tab-wrapper').hasClass('estp-btm-lft-side-tab') || $('.estp-tab-wrapper').hasClass('estp-btm-rht-side-tab') )
					{
						$('.estp-main-tab-wrap .estp-inner-tab-wrapper').css({'bottom':'0px'});
					}
				}
				if($('.estp-tab-wrapper').hasClass('estp-template-10'))
				{
					$('.estp-tab-wrapper.estp-template-10 .estp-main-tab-wrap .estp-inner-tab-wrapper').removeClass('estp-temp10-popup-active');
					$('.estp-tab-wrapper.estp-template-10 .estp-main-tab-wrap .estp-inner-tab-wrapper .estp-tab-element').removeClass('estp-popup-active');
					$('.estp-template-10').removeClass('estp-animated estp-bounceInLeft estp-bounceInRight estp-bounceInUp');
					if($('.estp-tab-wrapper').hasClass('estp-btm-lft-side-tab') || $('.estp-tab-wrapper').hasClass('estp-btm-rht-side-tab') )
					{
						$('.estp-main-tab-wrap .estp-inner-tab-wrapper').css({'bottom':'0px'});
					}
				}
				//New
				if( $('.estp-tab-wrapper').hasClass('estp-template-11') )
				{
					$('.estp-tab-wrapper.estp-template-11 .estp-inner-tab-wrapper').removeClass('estp-popup-active');
				}
				if( $('.estp-tab-wrapper').hasClass('estp-template-12') )
				{
					$('.estp-tab-wrapper.estp-template-12 .estp-inner-tab-wrapper').removeClass('estp-popup-active');
				}
				if( $('.estp-tab-wrapper').hasClass('estp-template-13') )
				{
					$('.estp-tab-wrapper.estp-template-13 .estp-inner-tab-wrapper').removeClass('estp-popup-active');
				}
				if( $('.estp-tab-wrapper').hasClass('estp-template-14') )
				{
					$('.estp-tab-wrapper.estp-template-14 .estp-inner-tab-wrapper').removeClass('estp-popup-active');
				}
				if( $('.estp-tab-wrapper').hasClass('estp-template-15') )
				{
					$('.estp-tab-wrapper.estp-template-15 .estp-inner-tab-wrapper').removeClass('estp-popup-active');
				}
				if($('.estp-tab-wrapper').hasClass('estp-template-2'))
				{
					$('.estp-popup-display').removeClass('estp-animated estp-bounceOutLeft estp-bounceOutRight estp-bounceOutUp');
					$('.estp-tab-element').removeClass('estp-popup-active');					
				}

	    	}
	    	else if($('.estp-tab-wrapper').hasClass('estp-template-4'))//if except template-3
			{
				$('.estp-popup-display').closest('.estp-inner-tab-wrapper').removeClass('estp-temp-4-move');
				$('.estp-inner-tab-wrapper').removeClass('estp-animated estp-shake estp-shake-bottom');
			}
	    }
		
	});

	//hide and show tab content on tab click 
	$(document).on('click', '.estp-tab-wrapper .estp-inner-tab-wrapper .estp-tab-element', function(){
	
		$('.estp-popup-display-content').mCustomScrollbar('scrollTo',['top',null]);
			
		if( !$(this).closest('.estp-tab-wrapper').hasClass('estp-template-1') && 
			!$(this).closest('.estp-tab-wrapper').hasClass('estp-template-5') && 
			!$(this).closest('.estp-tab-wrapper').hasClass('estp-template-9') &&
			!$(this).closest('.estp-tab-wrapper').hasClass('estp-template-10') &&
			!$(this).closest('.estp-inner-tab-wrapper').hasClass('estp-page-scroll-nav') )
		{
			if($(this).closest('.estp-tab-wrapper').hasClass('estp-fixed'))
			{
				if($(this).closest('.estp-tab-wrapper').hasClass('estp-lft-side-tab') || $(this).closest('.estp-tab-wrapper').hasClass('estp-rht-side-tab'))
				{
					var window_height = $(window).outerHeight();

					var popup_display_height = $(this).siblings('.estp-popup-display').outerHeight(true);
					var popup_pos_top = $(this).siblings('.estp-popup-display').offset().top;
					
					var button_display_top = $(this).offset().top;
					var button_display_height = $(this).outerHeight(true);
					if($(this).closest('.estp-tab-wrapper').hasClass('estp-template-6') || $(this).closest('.estp-tab-wrapper').hasClass('estp-template-7') || $(this).closest('.estp-tab-wrapper').hasClass('estp-template-8'))
					{
						var button_display_height = $(this).outerWidth(true);
					}


					var window_top = $(window).scrollTop();
					var window_height = $(window).outerHeight(true);
					var button_top_from_window = button_display_top - window_top;
					var button_bottom_pos_from_window = button_top_from_window + button_display_height;
					var popup_pos_from_window = button_bottom_pos_from_window - popup_display_height;

				  	var popup_pos_btm_frm_top =  (popup_pos_top - window_top) + (popup_display_height);
				  	var move_up_popup = parseInt(popup_display_height - button_display_height);
					console.log('Top move: ' + move_up_popup);
				  	
				  	//test
				  	var new_popup_shift_pos = popup_pos_top - move_up_popup;
				  	console.log('New Shift Popup Pos : ' + new_popup_shift_pos);

					if(popup_pos_btm_frm_top > window_height)
					{
						if(new_popup_shift_pos < window_top )
						{
							$(this).siblings('.estp-popup-display').css({ 
																'position' : 'fixed',
																'top' : ($(window).height() / 2) - ($(this).siblings('.estp-popup-display').outerHeight() / 2) 
																 });
						}
						else{

						$(this).siblings('.estp-popup-display').css({ 
																'top' : -(move_up_popup)+'px'
																 });
						}

						
					}
					
				}
			}
		}

		if($(this).closest('.estp-tab-wrapper').hasClass('estp-template-3'))
		{
			
			if(!$(this).children('a').hasClass('estp-tab-link') && (!$(this).hasClass('estp-temp3-close-btn-show') ) )
			{
				var $this = $(this);
				var animation_style = $this.siblings('.estp-popup-display').data('animation');
					

				$(this).siblings('.estp-popup-display').addClass('estp-popup-display-show');
				$(this).siblings('.estp-popup-display').addTemporaryClass(animation_style, 1400);

				$('.estp-tab-element').not($this).removeClass('estp-temp3-close-btn-show');

				if($('.estp-tab-element').not($this))
				{
					var not_current = $this.siblings('.estp-popup-display');
					$('.estp-popup-display').not(not_current).removeClass('estp-popup-display-show');
					$('.estp-popup-display').not(not_current).removeClass(animation_style);
				}

				$(this).addClass('estp-temp3-close-btn-show');
			}
			else // When The Template 3 Close button is Clicked
			{
				$(this).removeClass('estp-temp3-close-btn-show');
				
				$(this).siblings('.estp-popup-display').removeClass('estp-popup-display-show');
				$(this).siblings('.estp-popup-display').addTemporaryClass('estp-fadeOut',1400);

				$(this).siblings('.estp-popup-display').siblings('.estp-tab-element').show();
			}
		}

		if( $(this).closest('.estp-tab-wrapper').hasClass('estp-template-1') )
		{
			if(!$(this).children('a').hasClass('estp-tab-link'))
			{
				var $this = $(this);
				var tabType = $this.data('tabtype');

				$this.closest('.estp-tab-wrapper').find('.estp-inner-tab-wrapper').addClass('estp-temp1-popup-active');

				if($this.hasClass('estp-popup-active')) //which means the btn was prev clicked
				{
					$('.estp-template-1').find('.estp-popup-display').removeClass('estp-popup-display-show');
					$('.estp-template-1 .estp-inner-tab-wrapper .estp-tab-element').removeClass('estp-popup-active');
					$('.estp-template-1 .estp-inner-tab-wrapper').removeClass('estp-temp1-popup-active');
				}
				
				else //if not prev clicked , add class
				{
					$this.closest('.estp-template-1').find('.estp-popup-display').addClass('estp-popup-display-show');
					$('.estp-template-1 .estp-inner-tab-wrapper .estp-tab-element').removeClass('estp-popup-active');
					$this.addClass('estp-popup-active'); //after click add active class
				}

				//hide all the popup-display-content before click and show popup-content only after click
				$this.closest('.estp-template-1').find('.estp-popup-display').find('.estp-main-popup-content').hide();

				$this.closest('.estp-template-1').find('.estp-popup-display').find('.'+tabType+'-popup').show();
				
				if($this.closest('.estp-tab-wrapper').hasClass('estp-btm-lft-side-tab') || $this.closest('.estp-tab-wrapper').hasClass('estp-btm-rht-side-tab') )
				{
					if($this.closest('.estp-tab-wrapper').find('.estp-inner-tab-wrapper').hasClass('estp-temp1-popup-active') && $this.hasClass('estp-popup-active'))
					{
						var popup_disp_height = $this.closest('.estp-tab-wrapper').find('.estp-popup-display').height() + 19;
						
						$this.closest('.estp-tab-wrapper').find('.estp-inner-tab-wrapper').css({"bottom": popup_disp_height+'px'});
					}
					else
					{
						$this.closest('.estp-tab-wrapper').find('.estp-inner-tab-wrapper').css({"bottom": '0px'});	
					}
				}

				$this.closest('.estp-tab-wrapper').children('.estp-inner-tab-wrapper').animate({
				    scrollTop: $this.offset().top // Means Less header height
				},4000);
			}
		}

		if( $(this).closest('.estp-tab-wrapper').hasClass('estp-template-10') )
		{
			if(!$(this).parent().hasClass('estp-tab-link'))
			{
				var $this = $(this);
				var tabType = $this.data('tabtype');
				var animate_style = $this.closest('.estp-tab-wrapper').data('animation');

				$this.closest('.estp-tab-wrapper').find('.estp-inner-tab-wrapper').addClass('estp-temp10-popup-active');
				
				if($this.hasClass('estp-popup-active')) //which means the btn was prev clicked
				{
					$('.estp-template-10').find('.estp-popup-display').removeClass('estp-popup-display-show');
					$('.estp-template-10').closest('.estp-tab-wrapper').removeClass(animate_style);
					$('.estp-template-10 .estp-main-tab-wrap .estp-inner-tab-wrapper .estp-tab-element').removeClass('estp-popup-active');
					$('.estp-template-10 .estp-main-tab-wrap .estp-inner-tab-wrapper').removeClass('estp-temp10-popup-active');
				}
				
				else //if not prev clicked , add class
				{
					$this.closest('.estp-template-10').find('.estp-popup-display').addClass('estp-popup-display-show');
					$this.closest('.estp-template-10').closest('.estp-tab-wrapper').addClass(animate_style);
					$('.estp-template-10 .estp-main-tab-wrap .estp-inner-tab-wrapper .estp-tab-element').removeClass('estp-popup-active');
					$this.toggleClass('estp-popup-active'); //after click add active class
				}

				//hide all the popup-display-content before click and show popup-content only after click
				$this.closest('.estp-template-10').find('.estp-popup-display').find('.estp-main-popup-content').hide();

				$this.closest('.estp-template-10').find('.estp-popup-display').find('.'+tabType+'-popup').show();

				if($this.closest('.estp-tab-wrapper').hasClass('estp-btm-lft-side-tab') || $this.closest('.estp-tab-wrapper').hasClass('estp-btm-rht-side-tab') )
				{
					if($this.closest('.estp-tab-wrapper').find('.estp-inner-tab-wrapper').hasClass('estp-temp10-popup-active') && $this.hasClass('estp-popup-active'))
					{
						var popup_disp_height = $this.closest('.estp-tab-wrapper').find('.estp-popup-display').height() + 19;
						
						$this.closest('.estp-tab-wrapper').find('.estp-inner-tab-wrapper').css({"bottom": popup_disp_height+'px'});
					}
					else
					{
						$this.closest('.estp-tab-wrapper').find('.estp-inner-tab-wrapper').css({"bottom": '0px'});	
					}
				}
			}
		}

		if( $('.estp-tab-wrapper').hasClass('estp-template-4') )
		{
			if(!$(this).children('a').hasClass('estp-tab-link'))
			{
				var $this = $(this).closest('.estp-inner-tab-wrapper');
				var parentOf = $this;
				var animate_style = $this.data('animation');
				
				if($this.hasClass('estp-page-scroll-nav')) {
					return;
				}

				$('.estp-inner-tab-wrapper').not($this).removeClass('estp-temp-4-move');
				$('.estp-inner-tab-wrapper').not($this).removeClass(animate_style);
				$this.toggleClass('estp-temp-4-move');
			}
			
		}



		if($(this).closest('.estp-tab-wrapper').hasClass('estp-template-6'))
		{	
			if($(this).closest('.estp-inner-tab-wrapper').hasClass('estp-page-scroll-nav')) {
				return;
			}
			if(!$(this).children('a').hasClass('estp-tab-link'))
			{
				$('.estp-tab-element').not($(this)).removeClass('estp-temp-6-popup');
				$(this).toggleClass('estp-temp-6-popup');

				$(this).removeClass('estp-temp6-hide');
				if(!$(this).hasClass('estp-temp-6-popup'))
				{
					$('.estp-tab-element').not($(this).closest('.estp-inner-tab-wrapper').find('.estp-tab-element')).removeClass('estp-temp6-hide');
				}
				else{

					$('.estp-tab-element').not($(this).closest('.estp-inner-tab-wrapper').find('.estp-tab-element')).addClass('estp-temp6-hide');
				}

				
				$('.estp-popup-display').not($(this).closest('.estp-inner-tab-wrapper').find('.estp-popup-display')).removeClass('estp-popup-display-show');
				$(this).closest('.estp-inner-tab-wrapper').find('.estp-popup-display').toggleClass('estp-popup-display-show');
				if($(this).siblings('.estp-popup-display').hasClass('estp-popup-display-show'))
				{
					var popup_disp_height = $(this).siblings('.estp-popup-display').height() + 19;
					$(this).css({'bottom' : popup_disp_height+'px'});
				}
				else{
					$(this).css({'bottom' : '0px'});	
				}
			}			
		}

		if($(this).closest('.estp-tab-wrapper').hasClass('estp-template-7'))
		{
			if(!$(this).children('a').hasClass('estp-tab-link'))
			{
				var selected_tab = $(this).closest('.estp-inner-tab-wrapper').find('.estp-tab-element');

				$(this).closest('.estp-tab-wrapper').find('.estp-popup-display').not($(this).closest('.estp-inner-tab-wrapper').find('.estp-popup-display')).removeClass('estp-popup-display-show');	
				$(this).closest('.estp-inner-tab-wrapper').find('.estp-popup-display').toggleClass('estp-popup-display-show');
				$('.estp-tab-element').not(selected_tab).removeClass('estp-temp7-active-tab');
				$(this).toggleClass('estp-temp7-active-tab');
			}	
		}

		if($(this).closest('.estp-tab-wrapper').hasClass('estp-template-8'))
		{
			if(!$(this).children('a').hasClass('estp-tab-link'))
			{
				var animate_style = $(this).siblings('.estp-popup-display').data('animation');

				$(this).closest('.estp-tab-wrapper').find('.estp-tab-element').not($(this).closest('.estp-inner-tab-wrapper').find('.estp-tab-element')).removeClass('estp-temp8-active-tab');
				$(this).toggleClass('estp-temp8-active-tab');

				$(this).closest('.estp-tab-wrapper').find('.estp-popup-display').not($(this).closest('.estp-inner-tab-wrapper').find('.estp-popup-display')).removeClass('estp-popup-display-show');
				$(this).closest('.estp-tab-wrapper').find('.estp-popup-display').not($(this).closest('.estp-inner-tab-wrapper').find('.estp-popup-display')).removeClass(animate_style);
				$(this).closest('.estp-inner-tab-wrapper').find('.estp-popup-display').toggleClass('estp-popup-display-show');
				$(this).closest('.estp-inner-tab-wrapper').find('.estp-popup-display').toggleClass(animate_style);				
				
				if($(this).closest('.estp-inner-tab-wrapper').find('.estp-popup-display').hasClass('estp-popup-display-show'))
				{
					$(this).closest('.estp-tab-wrapper').addClass('estp-active');
					$('.estp-tab-overlay').addClass('estp-tab-overlay-active');
				}
				else
				{
					$(this).closest('.estp-tab-wrapper').removeClass('estp-active');
					$('.estp-tab-overlay').removeClass('estp-tab-overlay-active');
				}
			}
		}

		//New
		if($(this).closest('.estp-tab-wrapper').hasClass('estp-template-11') || $(this).closest('.estp-tab-wrapper').hasClass('estp-template-12') || $(this).closest('.estp-tab-wrapper').hasClass('estp-template-13') || $(this).closest('.estp-tab-wrapper').hasClass('estp-template-14') || $(this).closest('.estp-tab-wrapper').hasClass('estp-template-15') ) 
		{
			if( !$(this).children('a').hasClass('estp-tab-link') ) 
			{
				var animate_style = $(this).siblings('.estp-popup-display').data('animation');

				$(this).closest('.estp-tab-wrapper').find('.estp-popup-display').not( $(this).closest('.estp-inner-tab-wrapper').find('.estp-popup-display') ).removeClass('estp-popup-display-show');
				$(this).closest('.estp-inner-tab-wrapper').find('.estp-popup-display').toggleClass('estp-popup-display-show');
				
				$(this).closest('.estp-inner-tab-wrapper').find('.estp-popup-display').addTemporaryClass(animate_style, 2000);
				$(this).closest('.estp-tab-wrapper').find('.estp-inner-tab-wrapper').removeClass('estp-popup-active');
				$(this).closest('.estp-inner-tab-wrapper').addClass('estp-popup-active');
			}
		}



		if($(this).closest('.estp-tab-wrapper').hasClass('estp-template-9'))
		{
			if(!$(this).children('a').hasClass('estp-tab-link'))
			{
				var animate_style = $(this).siblings('.estp-popup-display').data('animation');
				var current_element = $(this);

				$(this).closest('.estp-tab-wrapper').find('.estp-popup-display').not($(this).closest('.estp-inner-tab-wrapper').find('.estp-popup-display')).removeClass('estp-popup-display-show');
				$(this).closest('.estp-tab-wrapper').find('.estp-popup-display').not($(this).closest('.estp-inner-tab-wrapper').find('.estp-popup-display')).removeClass(animate_style);
				$(this).closest('.estp-inner-tab-wrapper').find('.estp-popup-display').toggleClass('estp-popup-display-show');
				$(this).closest('.estp-inner-tab-wrapper').find('.estp-popup-display').addTemporaryClass(animate_style,2000);
				
				$('.estp-tab-element-inner').not($(this).find('.estp-tab-element-inner')).show();
				$('.estp-close-active-tab').not($(this).find('.estp-close-active-tab')).hide();
				$('.estp-tab-element').not(current_element).removeClass('estp-popup-active');
				
				$(this).find('.estp-tab-element-inner').toggle();
				$(this).find('.estp-close-active-tab').toggle();
				$(this).toggleClass('estp-popup-active');
			}
		}

		if( $(this).closest('.estp-tab-wrapper').hasClass('estp-template-2') )
		{ 
			var $this = $(this);
			var current_popup_disp = $this.closest('.estp-inner-tab-wrapper').find('.estp-popup-display');
			var animation_style = $(this).closest('.estp-tab-wrapper').find('.estp-popup-display').data('animation');

			//to remove estp-popup-display-show from all other div.estp-popup-display
			$('.estp-popup-display').not(current_popup_disp).removeClass('estp-popup-display-show');
			$('.estp-tab-element').not($this).removeClass('estp-popup-active');
			$('.estp-popup-display').not(current_popup_disp).removeClass(animation_style);
			
			//to hide and show the estp-popup-display div
			$(this).closest('.estp-inner-tab-wrapper').find('.estp-popup-display').toggleClass('estp-popup-display-show');
			$(this).toggleClass('estp-popup-active');
			$(this).closest('.estp-inner-tab-wrapper').find('.estp-popup-display').toggleClass(animation_style);	
		}		
	});


	$(document).on('submit', '.estp-subscription-form', function (e) {
        e.preventDefault();
        var subscription_type = $(this).data('subscription-type');
        var tab_id = $(this).data('tab-id');
        var tab_unique_key = $(this).data('tab-unique-key');
        var mail_notification = $(this).data('mail-notification');
        if($(this).hasClass('estp-subscription-form-layout1') || $(this).hasClass('estp-subscription-form-layout-4'))
        {
        	var name = $(this).find('.estp-subscription-name').val();
        	var email_address = $(this).find('.estp-subscription-email').val();
        	var success_message = $(this).data('subscription-success-message');
        	var already_subscribed = $(this).data('already-subscribed-msg');        	
        }
        else if($(this).hasClass('estp-subscription-form-layout2') || $(this).hasClass('estp-subscription-form-layout3') || $(this).hasClass('estp-subscription-form-layout5'))
        {
        	var name = '-';
        	var email_address = $(this).find('.estp-subscription-email').val();
        	var success_message = $(this).data('subscription-success-message');
        	var already_subscribed = $(this).data('already-subscribed-msg');
        }
        
        if (email_address == '') 
        {
            var error_message = $(this).data('subscription-error-message');
            
            $('.estp-subscription-message').html('<span class="estp-subscription-fail-msg">'+error_message+'</span>').fadeOut(5000);
        }
        else 
        {
            $.ajax({
                type: 'post',
                url: estp_frontend_ajax.front_ajax_url,
                data: {
                	name: name,
                    email: email_address,
                    success_message: success_message,
                    error_message: error_message,
                    already_subscribed: already_subscribed,
                    tab_id: tab_id,
                    tab_unique_key: tab_unique_key,
                    subscription_type: subscription_type,
                    mail_notification: mail_notification,
                    _wpnonce: estp_frontend_ajax.front_ajax_nonce,
                    action: 'estp_subscribe_action',
                    _action: 'subscription-form'
                },
                beforeSend: function (xhr) {
                    $('.estp-subscribe-loader').show();
                },
                success: function (res) {
                    $('.estp-subscribe-loader').hide();
                    res = $.parseJSON(res); 

                    if (res.success == 1)  //empty the email input if subscribe success 
                    {
                    	$('.estp-subscription-message').html('<span class="estp-subscription-success-msg">'+res.message+'</span>').fadeOut(5000);
                        $('.estp-subscription-form input[name="email"]').val('');
                    }

                    if(res.success == '2') //Something went wrong message
                    	$('.estp-subscription-message').html('<span class="estp-subscription-fail-msg">'+res.message+'</span>').fadeOut(5000);	
                    
					if(res.success == '3') //Already Subscribed
                    	$('.estp-subscription-message').html('<span class="estp-subscription-fail-msg">'+res.message+'</span>').fadeOut(5000);	                    
                    
                }
            });
        }
    });
    $('.estp-subscription-form input[name="email"]').keyup(function () {
        $('.estp-subscription-message').html('');
    });    
	
	//for template-5 close btn
	$(document).on('click','.estp-temp-5-close-btn', function(){
		$(this).closest('.estp-popup-display').removeClass('estp-popup-display-show');
		$('.estp-template-5 .estp-inner-tab-wrapper .estp-tab-element').removeClass('estp-popup-active');
		$(this).closest('.estp-template-5').removeClass('animated shake');
		var foo = $(this).parent().closest('.estp-tab-wrapper').find('.estp-inner-tab-wrapper');
		
		$(this).parent().closest('.estp-tab-wrapper').find('.estp-inner-tab-wrapper').removeClass('estp-temp5-popup-active');

		if($('.estp-tab-wrapper').hasClass('estp-btm-lft-side-tab') || $('.estp-tab-wrapper').hasClass('estp-btm-rht-side-tab') )
		{
			$('.estp-tab-wrapper').find('.estp-inner-tab-wrapper').css({'bottom' : '0px'});
		}
	});

	//For Template-7 Close Btn Click action 
	$(document).on('click','.estp-temp-7-close-btn', function(){
		$(this).parent().siblings('.estp-tab-element').removeClass('estp-temp7-active-tab');
		$(this).parent().removeClass('estp-popup-display-show');
	});

	//For Template-8 Close Btn Click action 
	$(document).on('click','.estp-temp-8-close-btn', function(){
		$(this).parent().siblings('.estp-tab-element').removeClass('estp-temp8-active-tab');
		$(this).parent().removeClass('estp-popup-display-show');
		$(this).closest('.estp-popup-display').removeClass('estp-animated estp-zoomInLeft estp-zoomInRight estp-zoomInUp');

		$('.estp-tab-wrapper.estp-template-8').removeClass('estp-active');
		$('.estp-tab-overlay').removeClass('estp-tab-overlay-active');
	});

	$(document).on('click','.estp-template-5 .estp-inner-tab-wrapper .estp-tab-element', function(){
		var $this = $(this);
		var tabType = $this.data('tabtype');
		var animate_style = $this.closest('.estp-tab-wrapper').data('animation');
		if($this.closest('.estp-inner-tab-wrapper').hasClass('estp-page-scroll-nav'))
		{
			return;
		}
		if( tabType != 'estp-inner-link' || tabType != 'estp-external-link' )
		{
			$this.closest('.estp-tab-wrapper').find('.estp-inner-tab-wrapper').addClass('estp-temp5-popup-active');

			if($this.hasClass('estp-popup-active')) //which means the btn was prev clicked
			{
				$('.estp-template-5').find('.estp-popup-display').removeClass('estp-popup-display-show');
				$('.estp-template-5').removeClass(animate_style);
				$('.estp-template-5 .estp-inner-tab-wrapper .estp-tab-element').removeClass('estp-popup-active');
				$('.estp-template-5 .estp-inner-tab-wrapper').removeClass('estp-temp5-popup-active');
			}
			
			else //if not prev clicked , add class
			{
				$this.closest('.estp-template-5').find('.estp-popup-display').addClass('estp-popup-display-show');
				$this.closest('.estp-template-5').addClass(animate_style);
				$('.estp-template-5 .estp-inner-tab-wrapper .estp-tab-element').removeClass('estp-popup-active');
				$this.addClass('estp-popup-active'); //after click add active class
			}

			//hide all the popup-display-content before click and show popup-content only after click
			$this.closest('.estp-template-5').find('.estp-popup-display').find('.estp-main-popup-content').hide();

			$this.closest('.estp-template-5').find('.estp-popup-display').find('.'+tabType+'-popup').show();
			
			if($this.closest('.estp-tab-wrapper').hasClass('estp-btm-lft-side-tab') || $this.closest('.estp-tab-wrapper').hasClass('estp-btm-rht-side-tab') )
			{
				if($this.closest('.estp-tab-wrapper').find('.estp-inner-tab-wrapper').hasClass('estp-temp5-popup-active') && $this.hasClass('estp-popup-active'))
				{
					var popup_disp_height = $this.closest('.estp-tab-wrapper').find('.estp-popup-display').height() + 19;
					
					$this.closest('.estp-tab-wrapper').find('.estp-inner-tab-wrapper').css({"bottom": popup_disp_height+'px'});
				}
				else
				{
					$this.closest('.estp-tab-wrapper').find('.estp-inner-tab-wrapper').css({"bottom": '0px'});	
				}
			}
		}
	});

	// Event for the Scroll Type Tab click
	$(document).on('click', '.estp-page-scroll-nav', function(e) {
		e.preventDefault(e);
		let $this = $(this);
		let scroll_speed = $this.data('scroll-speed');
		let scrollType = $this.data('scroll-type');
		$this.removeClass('estp-element-active');

		let scrollTopCall = (scrollType) => {
							return new Promise( (resolve, reject) => {
								if(scrollType != '' && scrollType == 'custom_element' ) {
									resolve(true);
								} else if( scrollType != '' && scrollType == 'scroll_to_top' ) {
									reject(false);
								}
							});
						}
		scrollTopCall(scrollType)
		.then( custom_element => { 
			let scrollElementId = $this.data('scroll-element-id');
			let scrollTop = $('#'+scrollElementId).offset().top;
			scrollFunction(scrollTop, scroll_speed);
		})
		.catch( scrollToTop => { 
			let scrollTop = 0; 
			scrollFunction(scrollTop, scroll_speed);
		});		
	});

	scrollFunction = (scrollTop, scroll_speed) => {
		$('html').stop().animate({
			'scrollTop' : scrollTop
		}, scroll_speed, 'swing', function() {

		}).promise().then(onScrollEvent);
	}

	// Function to run after scroll
	onScrollEvent = () => {
		$('.estp-page-scroll-nav').each(function() {
			let $this = $(this);
			let scrollElementId = $this.data('scroll-element-id');
			if($this.data('scroll-type') == 'custom_element' )
				var elementTop = $('#' + scrollElementId).offset().top;
			
			let scrollTopPos = $(document).scrollTop();
			let scrollBtmPos = $(document).scrollTop() + $(document).height();
			let scrollType = $this.data('scroll-type');
			var elementBottom = elementTop + $('#'+scrollElementId).outerHeight();
			var viewportTop = $(window).scrollTop();
			var viewportBottom = viewportTop + $(window).height();

			if( scrollType != '' && scrollType == 'custom_element' ) {
				// console.log('Element Top = '+elementTop);
				// console.log('Scroll Top Pos = '+scrollTopPos);
				// console.log('Scroll Btm Pos = '+scrollBtmPos);
				// If sccroll pos > element top pos and < element bottom position
				// if ( ((elementTop -2) <= scrollTopPos) && ((elementTop -2) + $('#' + scrollElementId).height() > scrollTopPos) ) {
				// if ( !((elementTop -2) > scrollTopPos && (elementTop -2) < scrollBtmPos) || !((elementTop -2) + $('#' + scrollElementId).height() < scrollBtmPos) ) {
				// if ( ($('#'+scrollElementId).position().top -80  <= scrollTopPos) && ($('#'+scrollElementId).position().top + $('#'+scrollElementId).height() > scrollTopPos) ) {	
				if((elementBottom > viewportTop && (elementTop < viewportBottom)) )	{
					$this.removeClass('estp-element-active');
		        	$this.addClass('estp-element-active');
					$this.hide();
		        } else {
		            $this.removeClass('estp-element-active');
					$this.show();
		        }
			} else if( scrollType != '' && scrollType == 'scroll_to_top' ) {
				if(scrollTopPos > 2) {
					$this.removeClass('estp-element-active');
					$this.show();	
				} else {
					$this.removeClass('estp-element-active');
					$this.addClass('estp-element-active');
					$this.hide();
				}
			}
			$this.removeClass('estp-popup-active');
		});
	}

	// On Scroll Event
	$(document).on('scroll', () => {
		onScrollEvent();
	});
});

(function($){

    $(window).on("load",function(){
        $(".estp-popup-display-content").mCustomScrollbar({
		    theme:"dark",
		    scrollInertia : 500,
		});
    });

    $.fn.extend({ 

        addTemporaryClass: function(className, duration) 
        {
            var elements = this;
            setTimeout(function() {
                elements.removeClass(className);
            }, duration);

            return this.each(function() {
                $(this).addClass(className);
            });
        }
    });

})(jQuery);

