(function($){
	'use strict';
	$(document).ready(function(){
	 	var timer;
	 	var timePeriod = cart_exit_intent_data.hours; //Time period in hours

	 	function showExitIntentForm(event){
	 		var currentTime 			= new Date().getTime();
			var last_time_displayed 	= localStorage.getItem('cart_ei_last_time');
			var last_time_moved_back 	= localStorage.getItem('cart_last_time_moved_back');
			var productCount 			= cart_exit_intent_data.product_count; //Products in the shopping cart

			if( event == 'cart_scrolling_up' || event == 'cart_going_back' ){ //In case if user is scrolling up or using back button
				if(productCount == 0){
					return;
		        }
		        else if(last_time_displayed == null || timePeriod == 0) { //If time period has passed or Exit Intent test mode is enabled
		            $('#cart-exit-intent-form').addClass('cart-visible'); //Display form
		        	$('#cart-exit-intent-form-backdrop').css('opacity', '').addClass('cart-visible'); //Show backdrop
		        	if(timePeriod != 0){
		        		localStorage.setItem('cart_ei_last_time', currentTime);
		        	}
		        }else{
		        	if(currentTime - last_time_displayed > timePeriod * 60 * 60 * 1000){ // If the time has expired, clear the cookie
						localStorage.removeItem('cart_ei_last_time');
					}
		        }
			}

			else if ( event.clientY <= 0 && event.target.tagName.toLowerCase() != 'select' && event.target.tagName.toLowerCase() != 'option' && event.target.tagName.toLowerCase() != 'input') {
		        if(productCount == 0){
		        	return;
		        }
		        else if(last_time_displayed == null || timePeriod == 0) { 
		            $('#cart-exit-intent-form').addClass('cart-visible'); //Display form
		        	$('#cart-exit-intent-form-backdrop').css('opacity', '').addClass('cart-visible'); //Show backdrop
		        	if(timePeriod != 0){
		        		localStorage.setItem('cart_ei_last_time', currentTime);
		        	}
		        }else{
		        	if(currentTime - last_time_displayed > timePeriod * 60 * 60 * 1000){ // If the time has expired, clear the cookie
						localStorage.removeItem('cart_ei_last_time');
					}
		        }
		    }
	 	}

		function getExitIntentMobile() {
			var cart_mobile = $('#cart-exit-intent-mobile').val();
			var smsalert_abcart_nonce = $('#smsalert_abcart_nonce').val();
			
			clearTimeout(timer);
			
			if (cart_mobile.length > 0)
			{ //Checking if the mobile field is valid
				var data = {
					action			:	'save_data',
					ab_cart_phone	:	cart_mobile,
					smsalert_abcart_nonce	:	smsalert_abcart_nonce
				}

				timer = setTimeout(function(){
					if(cart_exit_intent_data.is_user_logged_in){ //If the user is not logged in
						$.post(cart_exit_intent_data.ajaxurl, data, //Ajaxurl coming from localized script and contains the link to wp-admin/admin-ajax.php file that handles AJAX requests on Wordpress
						function(response) {
							//console.log(response);
						});
					}else{ //If the user is logged in
						$.post(cart_exit_intent_data.ajaxurl, data, //Ajaxurl coming from localized script and contains the link to wp-admin/admin-ajax.php file that handles AJAX requests on Wordpress
						function(response) {
							//console.log(response);
						});
					}
				}, 800);
			}else{
				//console.log('Not a valid e-mail or phone address');
			}
		}

		function insertExitIntentForm(){//Adding Exit Intent form in case if Ajax Add To Cart button pressed
			var data = {
				action: 		'insert_exit_intent',
				cart_insert: 	true
			}
			if($('#cart-exit-intent-form').length <= 0){ //If Exit intent HTML does not exist on page
				$.post(cart_exit_intent_data.ajaxurl, data, //Ajaxurl coming from localized script and contains the link to wp-admin/admin-ajax.php file that handles AJAX requests on Wordpress
				function(response){ //Response consists of HTML
					var output = response;
					$('body').append(output); //Adding Exit Intent form to the footer
					//Binding these functions once again since HTML added by Ajax is new
					$('#cart-exit-intent-mobile').on('keyup keypress change', getExitIntentMobile ); //All action happens on or after changing Mobile field. Data saved to Database only after Mobile fields have been entered.
					$('#cart-exit-intent-close, #cart-exit-intent-form-backdrop').on('click', closeExitIntentForm ); //Close Exit intent window
				});
			}

			cart_exit_intent_data.product_count = parseInt(cart_exit_intent_data.product_count) + 1; //Updating product count in cart data variable once Add to Cart button is pressed
		}

		function removeExitIntentFormIfEmptyCart(){//Removing Exit Intent form in case if cart emptied using Ajax
			var data = {
				action: 		'remove_exit_intent',
				cart_remove: 	true
			}
			if($('#cart-exit-intent-form').length > 0){ //If Exit intent HTML exists on page
				$.post(cart_exit_intent_data.ajaxurl, data, //Ajaxurl coming from localized script and contains the link to wp-admin/admin-ajax.php file that handles AJAX requests on Wordpress
				function(response){
					if(response.data == 'true'){ //If the cart is empty - removing exit intent HTML
						$('#cart-exit-intent-form').remove();
						$('#cart-exit-intent-form-backdrop').remove();
					}
				}); 
			}
		}

		function closeExitIntentForm(){ //Close exit intent window
 		    $('#cart-exit-intent-form').removeClass('cart-visible'); //Hide form
        	$('#cart-exit-intent-form-backdrop').removeClass('cart-visible'); //Hide backdrop
	 	}

		function checkIfTouchEnabled(event){		
			if( event.type == 'touchstart'){
				localStorage.setItem('cart_touch_device', true);
				$(document).off('touchstart', checkIfTouchEnabled ); //Removing checking if we are on touch device in case we find it out
			}
		}

		function checkScrollDirection(){ 			
			var cart_touch_device = localStorage.getItem('cart_touch_device');
			if( cart_touch_device ){ //Going further only in case we are on a touch enabled device
				if(cart_scrollSpeed() < -120){ //In case if the user scrolls up with a speed of 150
					showExitIntentForm('cart_scrolling_up');
				}
			}
		}

		var cart_scrollSpeed = (function(){ //Function that checks the speed of scrolling			
			
			var last_position, new_position, timer, delta, delay = 50;// in 'ms' (higher means lower fidelity )
			function clear() {
				last_position = null;
				delta = 0;
			}

			clear();
			return function(){
				new_position = window.scrollY;
				if ( last_position != null ){
					delta = new_position -  last_position;
				}
				last_position = new_position;
				clearTimeout(timer);
				timer = setTimeout(clear, delay);
				return delta;
			};
		})();

		function buildExitIntentHistoryLinks(action){ //Function that builds alternative history
			var cart_history_links = JSON.parse(localStorage.getItem('cart_history_links') || '[]');
			if(action == 'remove'){ //If we have a true value, then we are removing the link from history. Used in case if the user clicks the back button
				cart_history_links.pop();
			}else{ //Modifying history. Adding history link to a stack of history
				var link = {
					url: location.href
				};
				cart_history_links.push(link);
			}
			localStorage.setItem('cart_history_links', JSON.stringify(cart_history_links)); //Saving history in session
		}

		function handleExitIntentBackButton() {
			var cart_history_links = JSON.parse(localStorage.getItem('cart_history_links') || '[]');
			var last_history_link_id = cart_history_links.length - 1;
			var last_history_link_url = cart_history_links[last_history_link_id].url;
			var cart_show_exit_intent_on_back_button = localStorage.getItem('cart_show_exit_intent_on_back_button');
			if(timePeriod == 0){ //If Exit Intent test mode is enabled
				cart_show_exit_intent_on_back_button = null;
			}
			var count = 0;

			addEventListener('popstate', function(event){
				//Calculating how many pages should we be going back in history.
				while( cart_history_links.length > 0 && location.href == last_history_link_url && count < 10 ){ //In case if the history is too long, we are using count to exit the loop. If the current page link is equal to previous, we are looking for a new link that would be different from the current one and also looping until we have at least one link in the history
					last_history_link_url = cart_history_links.pop().url; //Removing last link from history
					count++;
					buildExitIntentHistoryLinks('remove'); //Removing the last element from the history
				}

				if( cart_show_exit_intent_on_back_button == null || cart_show_exit_intent_on_back_button == false ){ //In case if the user goes back for the first time, we are displaying the Exit Intent window
					localStorage.setItem('cart_show_exit_intent_on_back_button', true);
					showExitIntentForm('cart_going_back');
				}else{ //Going back in history
					history.go(-count);
				}
			});
		}

		function startExitIntentBack(){ //Fires functions once the document has been loaded and is ready
			var cart_touch_device = localStorage.getItem('cart_touch_device');
			if( cart_touch_device ){ //Runing functions only on touch enabled devices
				history.pushState(null, null, location.href); //Adding current url to history
				buildExitIntentHistoryLinks('add'); //Adding current url to history
				handleExitIntentBackButton();
			}			
		}
		startExitIntentBack();

		$(document).on('mouseleave', showExitIntentForm); //Displaying Exit intent if the mouse leaves the window
		$('#cart-exit-intent-mobile').on('keyup keypress change', getExitIntentMobile ); //All action happens on or after changing Mobile field. Data saved to Database only after Mobile fields have been entered.
		$('#cart-exit-intent-close, #cart-exit-intent-form-backdrop').on('click', closeExitIntentForm ); //Close Exit intent window
		$(document).on('added_to_cart', insertExitIntentForm ); //Calling Exit Intent form function output if Add to Cart button is pressed
		$(document).on('removed_from_cart', removeExitIntentFormIfEmptyCart ); //Firing the function if item is removed from cart via Ajax 
		$(document).on('touchstart', checkIfTouchEnabled );
		$(document).on('scroll', checkScrollDirection ); //Binding function to scroll event
	});
})(jQuery);