(function ($) {
	'use strict';

	var razzi = razzi || {};
	razzi.init = function () {
		razzi.$body = $(document.body),
			razzi.$window = $(window),
			razzi.$header = $('#site-header');

		this.preloader();

		// Common
		this.toggleModals();
		this.toggleOffPopup();
		// Header
		this.stickyHeader();
		this.instanceSearch();
		this.focusSearchField();
		this.menuSideBar();
		this.scrollSection();

		this.miniCartQuantity();
		this.closeTopbar();

		// Blog
		this.blogFilterAjax();
		this.blogLoadingAjax();
		this.postFound();
		this.postsRelated();

		// Product Loop
		this.addWishlist();
		this.productLoopHover();
		this.productLoopATCForm();
		this.productLoopHoverSlider();
		this.productLoopHoverZoom();
		this.productQuickView();
		this.productQuantityDropdown();
		this.productQuantityNumber();
		this.updateQuantityAuto();
		this.productLoopFormAJAX();

		this.productLoaded();

		// Single Product
		this.productLightBox();
		this.reviewProduct();
		this.stickyAddToCart();

		// Product Notification
		this.openMiniCartPanel();
		this.addedToCartNotice();
		this.productPopupATC();
		this.addedToWishlistNotice();

		this.addToCartSingleAjax();

		// Cart
		this.cartPageQuantity();
		this.crossSellProductsCarousel();

		// Account
		this.accountOrder();
		this.loginPanel();
		this.loginPanelAuthenticate();

		this.newsletterPopup();
		this.backToTop();
		this.recentlyViewedProducts();

		// Mobile
		this.footerDropdown();
		this.historyBack();

		// Style
		this.inlineStyle();
	};

	/**
	 * Preloader.
	 */
	 razzi.preloader = function() {
		var $preloader = $( '#preloader' );

		if ( ! $preloader.length ) {
			return;
		}

		var ignorePreloader = false;

		$( document.body ).on( 'click', 'a[href^=mailto], a[href^=tel]', function() {
			ignorePreloader = true;
		} );

		$( window ).on( 'beforeunload', function( event ) {
			if ( ! ignorePreloader ) {
				$preloader.fadeIn();
			}

			ignorePreloader = false;
		} );

		setTimeout( function() {
			$preloader.fadeOut();
		}, 200 );

		window.onpageshow = function( event ) {
			if ( event.persisted ) {
				setTimeout( function() {
					$preloader.fadeOut();
				}, 200 );
			}
		};
	};

	/**
	 * Toggle modals.
	 */
	razzi.toggleModals = function () {
		$(document.body).on('click', '[data-toggle="modal"]', function (event) {
			var target = '#' + $(this).data('target');

			if ($(target).hasClass('open')) {
				razzi.closeModal(target);
			} else if (razzi.openModal(target)) {
				event.preventDefault();
			}

		}).on('click', '.rz-modal .button-close, .rz-modal .off-modal-layer', function (event) {
			event.preventDefault();
			razzi.closeModal(this);
		}).on('keyup', function (e) {
			if (e.keyCode === 27) {
				razzi.closeModal();
			}
		});
	};

	/**
	 * Open a modal.
	 *
	 * @param string target
	 */
	razzi.openModal = function (target) {
		var $target = $(target);

		if (!$target.length) {
			return false;
		}

		$target.fadeIn();
		$target.addClass('open');

		$(document.body).addClass('modal-opened ' + $target.attr('id') + '-opened').trigger('razzi_modal_opened', [$target]);

		if( $target.attr('id') == 'search-modal' ) {
			$('.ra-search-modal .search-field').focus();
		} else if( $target.attr('id') == 'account-modal' ) {
			$('.woocommerce-account .input-text[name="username"]').focus();
		}

		var widthScrollBar = window.innerWidth - document.documentElement.clientWidth;
		$(document.body).css({'padding-right': widthScrollBar, 'overflow': 'hidden'});

		if ($(document.body).hasClass('header-transparent')) {
			razzi.$header.css({'right': widthScrollBar});
		}

		if ($(document.body).hasClass('header-sticky')) {
			$(document.body).find('#site-header.minimized').css({'right': widthScrollBar, 'transition' : 'none'});
		}

		return true;
	}

	/**
	 * Close a modal.
	 *
	 * @param string target
	 */
	razzi.closeModal = function (target) {
		if (!target) {
			$('.rz-modal').removeClass('open').fadeOut();

			$('.rz-modal').each(function () {
				var $modal = $(this);

				if (!$modal.hasClass('open')) {
					return;
				}

				$modal.removeClass('open').fadeOut();
				$(document.body).removeClass($modal.attr('id') + '-opened');
			});
		} else {
			target = $(target).closest('.rz-modal');
			target.removeClass('open loaded').fadeOut();

			$(document.body).removeClass(target.attr('id') + '-opened');
		}

		$(document.body).removeAttr('style');
		razzi.$header.removeAttr('style');

		$(document.body).removeClass('modal-opened').trigger('razzi_modal_closed', [target]);
	}

	/**
	 * Toggle off-screen panels
	 */
	razzi.toggleOffPopup = function () {
		$(document.body).on('click', '[data-toggle="off-popup"]', function (event) {
			var target = '#' + $(this).data('target');

			if ($(target).hasClass('open')) {
				razzi.closeOffPopup(target);
			} else if (razzi.openOffPopup(target)) {
				event.preventDefault();
			}
		}).on('click', '.offscreen-popup .button-close, .offscreen-popup .backdrop', function (event) {
			event.preventDefault();

			razzi.closeOffPopup(this);
		}).on('keyup', function (e) {
			if (e.keyCode === 27) {
				razzi.closeOffPopup();
			}
		});
	};

	/**
	 * Open off popup panel.
	 */
	razzi.openOffPopup = function (target) {
		var $target = $(target);

		if (!$target.length) {
			return false;
		}

		$target.fadeIn();
		$target.addClass('open');

		$(document.body).addClass('offpopup-opened ' + $target.attr('id') + '-opened').trigger('razzi_off_popup_opened', [$target]);

		return true;
	}

	/**
	 * Close off popup panel.
	 */
	razzi.closeOffPopup = function (target) {
		if (!target) {
			$('.offscreen-popup').each(function () {
				var $panel = $(this);

				if (!$panel.hasClass('open')) {
					return;
				}

				$panel.removeClass('open').fadeOut();
				$(document.body).removeClass($panel.attr('id') + '-opened');
			});
		} else {
			target = $(target).closest('.offscreen-popup');
			target.removeClass('open').fadeOut();

			$(document.body).removeClass(target.attr('id') + '-opened');
		}

		$(document.body).removeClass('offpopup-opened').trigger('razzi_off_popup_closed', [target]);
	}

	/**
	 * Product instance search
	 */
	razzi.instanceSearch = function () {
		if (razziData.header_ajax_search != '1') {
			return;
		}

		var $modal = $('#search-modal, .header-search');

		$modal.on('change', '.product-cat-dd', function () {
			var value = $(this).find('option:selected').text().trim();
			$modal.find('.product-cat-label .label').html(value);
		});

		$modal.find('.products-search').on('submit', function () {
			if ($(this).find('.product-cat-dd').val() == '0') {
				$(this).find('.product-cat-dd').removeAttr('name');
			}
		});

		var xhr = null,
			searchCache = {},
			$form = $modal.find('form');

		$modal.on('keyup', '.search-field', function (e) {
			var valid = false;

			if (typeof e.which == 'undefined') {
				valid = true;
			} else if (typeof e.which == 'number' && e.which > 0) {
				valid = !e.ctrlKey && !e.metaKey && !e.altKey;
			}

			if (!valid) {
				return;
			}

			if (xhr) {
				xhr.abort();
			}

			$modal.find('.result-list-found, .result-list-not-found').html('');
			$modal.find('.result-title').addClass('not-found');

			var $currentForm = $(this).closest('.form-search'),
				$search = $currentForm.find('input.search-field');

			if ($search.val().length < 2) {
				$currentForm.removeClass('searching searched actived found-products found-no-product invalid-length');
			}

			search($currentForm);
		}).on('change', '.product-cat-dd', function () {
			if (xhr) {
				xhr.abort();
			}

			$modal.find('.result-list-found').html('');
			$modal.find('.result-title').addClass('not-found');
			var $currentForm = $(this).closest('.form-search');

			search($currentForm);
		}).on('focus', '.product-cat-dd', function () {
			if (xhr) {
				xhr.abort();
			}

			$modal.find('.product-cat-label').addClass('border-color-dark');
		}).on('focusout', '.product-cat-dd', function () {
			if (xhr) {
				xhr.abort();
			}

			$modal.find('.product-cat-label').removeClass('border-color-dark');
		}).on('focusout', '.search-field', function () {
			var $currentForm = $(this).closest('.form-search'),
				$search = $currentForm.find('input.search-field');

			if ($search.val().length < 2) {
				$currentForm.removeClass('searching searched actived found-products found-no-product invalid-length');
			}
		});

		$modal.on('click', '.close-search-results', function (e) {
			e.preventDefault();
			$modal.find('.search-field').val('');
			$modal.find('.form-search').removeClass('searching searched actived found-products found-no-product invalid-length');
			$modal.find('.result-title').addClass('not-found');

			$modal.find('.result-list-found').html('');
		});

		$(document).on('click', function (e) {
			if ($('#search-modal').find('.form-search').hasClass('actived')) {
				return;
			}
			var target = e.target;

			if ($(target).closest('.products-search').length < 1) {
				$form.removeClass('searching actived found-products found-no-product invalid-length');
			}
		});

		/**
		 * Private function for search
		 */
		function search($currentForm) {
			var $search = $currentForm.find('input.search-field'),
				keyword = $search.val(),
				cat = 0,
				$results = $currentForm.find('.search-results');

			if ($modal.hasClass('ra-search-form')) {
				$results = $modal.find('.search-results');
			}

			if ($currentForm.find('.product-cat-dd').length > 0) {
				cat = $currentForm.find('.product-cat-dd').val();
			}


			if (keyword.trim().length < 2) {
				$currentForm.removeClass('searching found-products found-no-product').addClass('invalid-length');
				return;
			}

			$currentForm.removeClass('found-products found-no-product').addClass('searching');

			var keycat = keyword + cat,
				url = $form.attr('action') + '?' + $form.serialize();

			if (keycat in searchCache) {
				var result = searchCache[keycat];

				$currentForm.removeClass('searching');
				$currentForm.addClass('found-products');
				$results.html(result.products);


				$(document.body).trigger('razzi_ajax_search_request_success', [$results]);

				$currentForm.removeClass('invalid-length');
				$currentForm.addClass('searched actived');
			} else {
				var data = {
						'term': keyword,
						'cat': cat,
						'ajax_search_number': razziData.header_search_number,
						'search_type': razziData.search_content_type
					},
					ajax_url = razziData.ajax_url.toString().replace('%%endpoint%%', 'razzi_instance_search_form');

				xhr = $.post(
					ajax_url,
					data,
					function (response) {
						var $products = response.data;

						$currentForm.removeClass('searching');
						$currentForm.addClass('found-products');
						$results.html($products);
						$currentForm.removeClass('invalid-length');


						$(document.body).trigger('razzi_ajax_search_request_success', [$results]);

						// Cache
						searchCache[keycat] = {
							found: true,
							products: $products
						};

						$results.find('.view-more a').attr('href', url);

						$currentForm.addClass('searched actived');
					}
				);
			}
		}

	}

	razzi.getOptionsDropdown = function () {
		var options = {
			onChange: function (value, input) {
			   razzi.updateCartAJAX(value, input);
			}
		};

		return options;
	}

	razzi.updateCartAJAX = function (value, input) {
		var $row = $( input ).closest('.woocommerce-mini-cart-item'),
		key = $row.find('a.remove').data('cart_item_key'),
		nonce = $row.find('.woocommerce-cart-item__qty').data('nonce'),
		ajax_url = wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'update_cart_item');

		if ($.fn.block) {
			$row.block({
				message: null,
				overlayCSS: {
					opacity: 0.6,
					background: '#fff'
				}
			});
		}

		$.post(
			ajax_url, {
				cart_item_key: key,
				qty: value,
				security: nonce
			}, function (response) {
				if (!response || !response.fragments) {
					return;
				}

				if ($.fn.unblock) {
					$row.unblock();
				}

				$( document.body ).trigger( 'added_to_cart', [response.fragments, response.cart_hash] );
			}).fail(function () {
			if ($.fn.unblock) {
				$row.unblock();
			}

			return;
		});
	}

	/**
	 * Make the cart widget more flexible.
	 */
	razzi.miniCartQuantity = function () {
		if (typeof wc_add_to_cart_params === undefined) {
			$('.woocommerce-mini-cart-item .quantity .qty').prop('disabled', true);

			$(document.body).on('wc_fragments_refreshed removed_from_cart', function () {
				$('.woocommerce-mini-cart-item .quantity .qty').prop('disabled', true);
			});

			return;
		}

		if ($.fn.quantityDropdown && ! razzi.$body.hasClass('product-qty-number')) {
			var options = razzi.getOptionsDropdown();
			$('.woocommerce-mini-cart-item .quantity .qty').quantityDropdown(options);

			$(document.body).on('wc_fragments_refreshed removed_from_cart added_to_cart', function () {
				$('.woocommerce-mini-cart-item .quantity .qty').quantityDropdown(options);
			});
		}

	};

	/**
	 * Make the cart widget more flexible.
	 */
	razzi.cartPageQuantity = function () {
		if (typeof wc_add_to_cart_params === undefined) {
			$('.woocommerce-cart-form__cart-item .quantity .qty').prop('disabled', true);

			$(document.body).on('wc_fragments_refreshed removed_from_cart', function () {
				$('.woocommerce-cart-form__cart-item .quantity .qty').prop('disabled', true);
			});

			return;
		}


		if ($.fn.quantityDropdown && ! razzi.$body.hasClass('product-qty-number')) {
			var options = '';
			$('.woocommerce-cart-form__cart-item .quantity .qty').quantityDropdown(options);

			$(document.body).on('wc_fragments_refreshed removed_from_cart added_to_cart', function () {
				$('.woocommerce-cart-form__cart-item .quantity .qty').quantityDropdown(options);
			});
		}

	};

	/**
	 * Quantity dropdown
	 */

	razzi.productQuantityDropdown = function () {
		if ($.fn.quantityDropdown && ! razzi.$body.hasClass('product-qty-number')) {
			// Quantity dropdown
			$('div.product, .razzi-sticky-add-to-cart').find('.quantity .qty').quantityDropdown();

			$(document.body).on('wc_fragments_refreshed removed_from_cart added_to_cart', function () {
				$('div.product, .razzi-sticky-add-to-cart').find('.quantity .qty').quantityDropdown();
			});
		}
	};

	/**
	 * Change product quantity
	 */
	 razzi.productQuantityNumber = function () {
		if( ! razzi.$body.hasClass('product-qty-number') ) {
			return;
		}

		razzi.$body.on('click', '.razzi-qty-button', function (e) {
			e.preventDefault();

			var $this = $(this),
				$qty = $this.siblings('.qty'),
				current = 0,
				min = parseFloat($qty.attr('min')),
				max = parseFloat($qty.attr('max')),
				step = parseFloat($qty.attr('step'));

			if ($qty.val() !== '') {
				current = parseFloat($qty.val());
			} else if ($qty.attr('placeholder') !== '') {
				current = parseFloat($qty.attr('placeholder'))
			}

			min = min ? min : 0;
			max = max ? max : current + 1;

			if ($this.hasClass('decrease') && current > min) {
				$qty.val(current - step);
				$qty.trigger('change');
			}
			if ($this.hasClass('increase') && current < max) {
				$qty.val(current + step);
				$qty.trigger('change');
			}

		});

	};

	razzi.updateQuantityAuto = function() {
		$( document.body ).on( 'change', 'table.cart .qty', function() {
			if (typeof razziData.update_cart_page_auto !== undefined && razziData.update_cart_page_auto == '1') {
				razzi.$body.find('button[name="update_cart"]').attr( 'clicked', 'true' ).prop( 'disabled', false ).attr( 'aria-disabled', false );
				razzi.$body.find('button[name="update_cart"]').trigger('click');
			}
		} );

		$( document.body ).on( 'change', '.woocommerce-mini-cart .qty', function() {
			razzi.updateCartAJAX( this.value, this );
		} );
	}

	/**
	 * Cross sell products carousel.
	 */
	razzi.crossSellProductsCarousel = function () {
		var $crossSells = $('.woocommerce .cross-sells');

		if (!$crossSells.length) {
			return;
		}

		var $products = $crossSells.find('ul.products');

		$products.wrap('<div class="swiper-container linked-products-carousel" style="opacity: 0;"></div>');
		$products.after('<div class="swiper-scrollbar"></div>');
		$products.addClass('swiper-wrapper');
		$products.find('li.product').addClass('swiper-slide');

		new Swiper($crossSells.find('.linked-products-carousel'), {
			loop: false,
			spaceBetween: 30,
			scrollbar: {
				el: $crossSells.find('.swiper-scrollbar'),
				hide: false,
				draggable: true
			},
			on: {
				init: function () {
					this.$el.css('opacity', 1);
				}
			},
			breakpoints: {
				300: {
					slidesPerView: razziData.mobile_portrait,
					slidesPerGroup: razziData.mobile_portrait,
					spaceBetween: 20,
				},
				480: {
					slidesPerView: razziData.mobile_landscape,
					slidesPerGroup: razziData.mobile_landscape,
				},
				768: {
					spaceBetween: 20,
					slidesPerView: 3,
					slidesPerGroup: 3
				},
				992: {
					slidesPerView: 3,
					slidesPerGroup: 3
				},
				1200: {
					slidesPerView: 4,
					slidesPerGroup: 4
				}
			}
		});
	};

	// Filter Ajax
	razzi.blogFilterAjax = function () {

		razzi.$body.find('#razzi-posts__taxs-list').on('click', 'a', function (e) {
			e.preventDefault();
			var btn = $(this),
				url = btn.attr('href');

			razzi.$body.trigger('razzi_blog_filter_ajax', url, $(this));

			razzi.$body.on('razzi_ajax_filter_request_success', function (e, btn) {
				$(this).addClass('selected');
				razzi.postFound();
			});
		});

		razzi.$body.on('razzi_blog_filter_ajax', function (e, url) {

			var $container = $('.razzi-posts__wrapper'),
				$categories = $('#razzi-posts__taxs-list');

			$('.razzi-posts__loading').addClass('show');

			if ('?' == url.slice(-1)) {
				url = url.slice(0, -1);
			}

			url = url.replace(/%2C/g, ',');

			history.pushState(null, null, url);

			if (razzi.ajaxXHR) {
				razzi.ajaxXHR.abort();
			}

			razzi.ajaxXHR = $.get(url, function (res) {
				$container.replaceWith($(res).find('.razzi-posts__wrapper'));
				$categories.html($(res).find('#razzi-posts__taxs-list').html());

				$('.razzi-posts__loading').removeClass('show');
				$('.razzi-posts__wrapper .blog-wrapper').addClass('animated razziFadeInUp');

				razzi.$body.trigger('razzi_ajax_filter_request_success', [res, url]);

			}, 'html');


		});
	};

	razzi.blogLoadingAjax = function () {

		razzi.$body.on('click', '#razzi-blog-previous-ajax a', function (e) {
			e.preventDefault();

			var $found = $('.razzi-posts__found');

			if ($(this).data('requestRunning')) {
				return;
			}

			$(this).data('requestRunning', true);

			var $posts = $(this).closest('#primary'),
				$postList = $posts.find('.razzi-posts__list'),
				currentPosts = $postList.children('.blog-wrapper').length,
				$pagination = $(this).parents('.load-navigation');

			$pagination.addClass('loading');

			$.get(
				$(this).attr('href'),
				function (response) {
					var content = $(response).find('.razzi-posts__list').children('.blog-wrapper'),
						numberPosts = content.length + currentPosts,
						$pagination_html = $(response).find('.load-navigation').html();
					$pagination.addClass('loading');

					$pagination.html($pagination_html);
					$postList.append(content);
					$pagination.find('a').data('requestRunning', false);
					// Add animation class
					for (var index = 0; index < content.length; index++) {
						$(content[index]).css('animation-delay', index * 100 + 'ms');
					}
					content.addClass('razziFadeInUp');
					$found.find('.current-post').html(' ' + numberPosts);
					$pagination.removeClass('loading');

					razzi.postFound();
				}
			);
		});

	};

	razzi.postFound = function (el) {
		var $found = $('.razzi-posts__found-inner'),
			$foundEls = $found.find('.count-bar'),
			$current = $found.find('.current-post').html(),
			$total = $found.find('.found-post').html(),
			pecent = ($current / $total) * 100;

		$foundEls.css('width', pecent + '%');
	}

	razzi.postsRelated = function () {
		var $selector = $('.razzi-posts__related'),
			$el = $selector.find('.razzi-post__related-content'),
			$col = $el.data('columns'),
			options = {
				loop: false,
				autoplay: false,
				speed: 800,
				watchOverflow: true,
				spaceBetween: 30,
				pagination: {
					el: $selector.find('.swiper-pagination'),
					type: 'bullets',
					clickable: true
				},
				breakpoints: {
					0: {
						slidesPerView: 1,
						slidesPerGroup: 1
					},

					380: {
						slidesPerView: 2,
						slidesPerGroup: 2
					},

					992: {
						slidesPerView: 3,
						slidesPerGroup: 3
					},

					1025: {
						slidesPerView: parseInt($col),
						slidesPerGroup: parseInt($col)
					},
				}
			};

		$selector.find('.blog-wrapper').addClass('swiper-slide');

		new Swiper($el, options);
	};

	// Toggle Menu Sidebar
	razzi.menuSideBar = function () {
		var $menuSidebar = $('#primary-menu.has-arrow, #topbar, #hamburger-modal, #mobile-menu-modal, #mobile-category-menu-modal, .header-v6 .main-navigation'),
			$menuClick = $('#hamburger-modal, #mobile-menu-modal, #mobile-category-menu-modal, .header-v6 #primary-menu');
		$menuSidebar.find('.nav-menu .menu-item-has-children').removeClass('active');
		$menuSidebar.find('.nav-menu .menu-item-has-children > a').prepend('<span class="toggle-menu-children"><span class="razzi-svg-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg></span></span>');
		$menuClick.find('.click-item li.menu-item-has-children > a').on('click', function (e) {
			e.preventDefault();
			$(this).closest('li').siblings().find('ul.sub-menu, ul.dropdown-submenu').slideUp();
			$(this).closest('li').siblings().removeClass('active');

			$(this).closest('li').children('ul.sub-menu, ul.dropdown-submenu').slideToggle();
			$(this).closest('li').toggleClass('active');
			return false;
		});

		$menuClick.find('.click-icon li.menu-item-has-children > a > .toggle-menu-children').on('click', function (e) {
			e.preventDefault();
			$(this).closest('li').siblings().find('ul.sub-menu, ul.dropdown-submenu').slideUp();
			$(this).closest('li').siblings().removeClass('active');

			$(this).closest('li').children('ul.sub-menu, ul.dropdown-submenu').slideToggle();
			$(this).closest('li').toggleClass('active');

			return false;
		});

		// Active menu header layout 7
		$('.header-v7 .main-navigation').find('li.menu-item > a').on('click', function (e) {
			e.preventDefault();
			$(this).closest('li').siblings().removeClass('active');
			$(this).closest('li').toggleClass('active');
		});
	};

	/**
	 * Close the topbar
	 */
	razzi.closeTopbar = function () {
		$(document.body).on('click', '.razzi-topbar__close', function (event) {
			event.preventDefault();

			$('#topbar, #topbar-mobile').slideUp();
		});
	};

	// Sticky Header
	razzi.stickyHeader = function () {

		if (!razzi.$body.hasClass('header-sticky')) {
			return;
		}

		var isHeaderTransparent = razzi.$body.hasClass('header-transparent'),
			$headerMinimized = $('#site-header-minimized'),
			heightHeaderMain = razzi.$header.find('.header-contents').hasClass('header-main') ? razzi.$header.find('.header-main').outerHeight() : 0,
			heightHeaderBottom = razzi.$header.find('.header-contents').hasClass('header-bottom') ? razzi.$header.find('.header-bottom').outerHeight() : 0,
			heightHeaderMobile = razzi.$header.find('.header-contents').hasClass('header-mobile') ? razzi.$header.find('.header-mobile').outerHeight() : 0,
			heightHeaderMinimized = heightHeaderMain + heightHeaderBottom;

			if( razzi.$header.hasClass('header-bottom-no-sticky') ) {
				heightHeaderMinimized = heightHeaderMain;
			} else if(razzi.$header.hasClass('header-main-no-sticky') ) {
				heightHeaderMinimized = heightHeaderBottom;
			}

			if( razzi.$body.hasClass('header-v6') ) {
				heightHeaderMinimized = heightHeaderBottom;
			}

			if( isHeaderTransparent ) {
				razzi.$header.addClass('has-transparent');
			}

		razzi.$window.on('scroll', function () {
			var scroll = razzi.$window.scrollTop(),
				scrollTop = razzi.$header.outerHeight(true),
				hBody = razzi.$body.outerHeight(true);

			if (hBody <= scrollTop + razzi.$window.height()) {
				return;
			}

			if (scroll > scrollTop) {

				razzi.$header.addClass('minimized');
				$('#razzi-header-minimized').addClass('minimized');
				razzi.$body.addClass('sticky-minimized');

				if (isHeaderTransparent) {
					razzi.$body.removeClass('header-transparent');
				}

				if (razzi.$window.width() > 992) {
					$headerMinimized.css('height', heightHeaderMinimized);
				} else {
					$headerMinimized.css('height', heightHeaderMobile);
				}

			} else {
				razzi.$header.removeClass('minimized');
				$('#razzi-header-minimized').removeClass('minimized');
				razzi.$body.removeClass('sticky-minimized');

				if (isHeaderTransparent) {
					razzi.$body.addClass('header-transparent');
				}

				$headerMinimized.removeAttr('style');
			}
		});
	};

	// add wishlist
	razzi.addWishlist = function () {
		razzi.$body.on('click', 'a.add_to_wishlist', function () {
			$(this).addClass('loading');
		});

		razzi.$body.on('added_to_wishlist', function (e, $el_wrap) {
			e.preventDefault();
			$('ul.products li.product .yith-wcwl-add-button a').removeClass('loading');
		});
	};

	// Product loop hover
	razzi.productLoopHover = function () {
		if (typeof razziData.product_loop_layout === 'undefined') {
			return;
		}

		if (razziData.product_loop_layout !== '8') {
			return;
		}


		var on_mobile = false;
		razzi.$window.on('resize', function () {
			if (razzi.$window.width() < 992) {
				on_mobile = true;
			} else {
				on_mobile = false;
			}
		}).trigger('resize');

		razzi.$body.on('mouseover', '.product-inner', function () {

			if (on_mobile) {
				return;
			}

			if ($(this).hasClass('has-transform')) {
				return;
			}

			if ($(this).closest('ul.products').hasClass('shortcode-element')) {
				return;
			}


			var $product_bottom = $(this).find('.product-loop__buttons'),
				product_bottom_height = $product_bottom.outerHeight(),
				$product_summary = $(this).find('.product-summary');

			$(this).addClass('has-transform');
			$product_summary.css({
				'-webkit-transform': "translateY(-" + product_bottom_height + "px)",
				'transform': "translateY(-" + product_bottom_height + "px)"
			});

		});


		$(document.body).on('tawcvs_initialized', function (e, form) {
			if (form.hasClass('variations_form_loop') && $.fn.tooltip) {
				form.find('.swatch').tooltip({disabled: true});
			}
		});


	};

	// Product loop hover
	razzi.productLoopFormAJAX = function () {
		if (typeof razziData.product_loop_layout === 'undefined') {
			return;
		}

		if (typeof razziData.product_loop_variation_ajax === 'undefined') {
			return;
		}

		if (razziData.product_loop_layout !== '9') {
			return;
		}

		if (razziData.product_loop_variation_ajax !== '1') {
			return;
		}

		razzi.$body.on('click', '.product-quick-shop-button', function (e) {
			e.preventDefault();

			if ($(this).hasClass('has-content')) {
				return;
			}

			$(this).addClass('has-content');

			var $product_id = $(this).data('product_id'),
				$product_inner = $(this).closest('li.product').find('.product-inner'),
				$product_form = $(this).closest('li.product').find('.product-loop__form');

			$product_inner.addClass('loading');

			$.ajax({
				url: razziData.ajax_url.toString().replace('%%endpoint%%', 'razzi_product_loop_form'),
				type: 'POST',
				data: {
					nonce: razziData.nonce,
					product_id: $product_id
				},
				success: function (response) {
					if (!response || response.data === '') {
						return;
					}
					$product_form.prepend(response.data);

					var $variations = $product_form.find('.variations_form');

					if (typeof $.fn.wc_variation_form !== 'undefined') {
						$variations.each(function () {
							$(this).wc_variation_form();
						});
					}

					$( document.body ).trigger( 'init_variation_swatches');

					setTimeout(function () {
						$product_inner.removeClass('loading').addClass('show-variations_form');
					}, 400);

				}
			})

		});


	};

	razzi.productLoopATCForm = function () {
		if (typeof razziData.product_loop_variation === 'undefined') {
			return;
		}

		if (razziData.product_loop_variation !== '1') {
			return;
		}

		razzi.$body.on('click', '.product-close-variations-form', function (e) {
			e.preventDefault();
			$(this).closest('.product-inner').removeClass('show-variations_form');
		});

		razzi.$body.on('click', '.product-quick-shop-button', function (e) {
			e.preventDefault();

			if (typeof razziData.product_loop_variation_ajax === 'undefined') {
				$(this).closest('.product-inner').addClass('show-variations_form');
			} else if ($(this).hasClass('has-content')) {
				$(this).closest('.product-inner').addClass('show-variations_form');
			}

		});

		razzi.$body.on('click', 'a.product_type_variable', function (e) {
			e.preventDefault();
			$(this).closest('li.product').find('.variations_form .single_add_to_cart_button').trigger('click');
		});

		razzi.$body.on('click', 'li.product .variations_form .single_add_to_cart_button', function (e) {
			e.preventDefault();
			var $this = $(this),
				$cartForm = $this.closest('.variations_form'),
				$cartButtonLoading = $this.closest('li.product').find('a.product_type_variable');

			if ($(this).is('.disabled')) {
				return;
			}

			razzi.addToCartFormAJAX($this, $cartForm, $cartButtonLoading);

			return false;
		});
	};

	razzi.addToCartSingleAjax = function () {

		var $selector = $('div.product, .razzi-sticky-add-to-cart');

		if ($selector.length < 1) {
			return;
		}

		if (!$selector.hasClass('product-add-to-cart-ajax')) {
			return;
		}

		$selector.find('form.cart').on('click', '.single_add_to_cart_button', function (e) {
			var $el = $(this),
				$cartForm = $el.closest('form.cart');

			if ($el.closest('.product').hasClass('product-type-external')) {
				return;
			}

			if ($cartForm.hasClass('buy-now-clicked')) {
				return;
			}

			if ($el.is('.disabled')) {
				return;
			}

			if ($cartForm.length > 0) {
				e.preventDefault();
			} else {
				return;
			}


			razzi.addToCartFormAJAX($el, $cartForm, $el);
		});

	};

	razzi.addToCartFormAJAX = function ($cartButton, $cartForm, $cartButtonLoading) {

		if ($cartButton.data('requestRunning')) {
			return;
		}

		$cartButton.data('requestRunning', true);

		var found = false;

		$cartButtonLoading.addClass('loading');
		if (found) {
			return;
		}
		found = true;

		var formData = $cartForm.serializeArray(),
			formAction = $cartForm.attr('action');

		if ($cartButton.val() != '') {
			formData.push({name: $cartButton.attr('name'), value: $cartButton.val()});
		}

		$(document.body).trigger('adding_to_cart', [$cartButton, formData]);

		$.ajax({
			url: formAction,
			method: 'post',
			data: formData,
			error: function (response) {
				window.location = formAction;
			},
			success: function (response) {
				if (!response) {
					window.location = formAction;
				}

				if (typeof wc_add_to_cart_params !== 'undefined') {
					if (wc_add_to_cart_params.cart_redirect_after_add === 'yes') {
						window.location = wc_add_to_cart_params.cart_url;
						return;
					}
				}

				var $message = '',
					className = 'info';
				if ($(response).find('.woocommerce-message').length > 0) {
					$(document.body).trigger('wc_fragment_refresh');
				} else {
					if (!$.fn.notify) {
						return;
					}

					var $checkIcon = '<span class="razzi-svg-icon message-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg></span>',
						$closeIcon = '<span class="razzi-svg-icon svg-active"><svg class="svg-icon" aria-hidden="true" role="img" focusable="false" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 1L1 14M1 1L14 14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path></svg></span>';

					if ($(response).find('.woocommerce-error').length > 0) {
						$message = $(response).find('.woocommerce-error').html();
						className = 'error';
						$checkIcon = '<span class="razzi-svg-icon message-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></span>';
					} else if ($(response).find('.woocommerce-info').length > 0) {
						$message = $(response).find('.woocommerce-info').html();
					}

					$.notify.addStyle('razzi', {
						html: '<div>' + $checkIcon + '<ul class="message-box">' + $message + '</ul>' + $closeIcon + '</div>'
					});

					$.notify('&nbsp', {
						autoHideDelay: 5000,
						className: className,
						style: 'razzi',
						showAnimation: 'fadeIn',
						hideAnimation: 'fadeOut'
					});
				}
				$cartButton.data('requestRunning', false);
				$cartButton.removeClass('loading');
				$cartButtonLoading.removeClass('loading');
				found = false;

			}
		});
	};

	razzi.productLoopHoverSlider = function () {

		var $selector = razzi.$body.find('ul.products .product-thumbnails--slider'),
			options = {
				loop: false,
				autoplay: false,
				speed: 800,
				watchOverflow: true,
				lazy: true,
				breakpoints: {}
			};

		$selector.find('.woocommerce-loop-product__link').addClass('swiper-slide');

		razzi.$body.find('ul.products').imagesLoaded(function () {
			setTimeout(function () {
				$selector.each(function () {
					options.navigation = {
						nextEl: $(this).find('.rz-product-loop-swiper-next'),
						prevEl: $(this).find('.rz-product-loop-swiper-prev'),
					}
					new Swiper($(this), options);
				});
			}, 200);
		});

	};

	/**
	 * Product thumbnail zoom.
	 */
	razzi.productLoopHoverZoom = function () {
		if (typeof razziData.product_loop_hover === 'undefined' || !$.fn.zoom) {
			return;
		}

		if (razziData.product_loop_hover !== 'zoom') {
			return;
		}

		var $seletor = razzi.$body.find('ul.products .product-thumbnail-zoom');
		$seletor.each(function () {
			var $el = $(this);

			$el.zoom({
				url: $el.attr('data-zoom_image')
			});
		});
	};


	/**
	 * Quick view modal.
	 */
	razzi.productQuickView = function () {
		$(document.body).on('click', '.quick-view-button', function (event) {
			event.preventDefault();

			var $el = $(this),
				product_id = $el.data('id'),
				$target = $('#quick-view-modal'),
				$container = $target.find('.woocommerce'),
				ajax_url = razziData.ajax_url.toString().replace('%%endpoint%%', 'product_quick_view');

			$target.addClass('loading').removeClass('loaded');
			$container.find('.product').html('');

			$.post(
				ajax_url,
				{
					action: 'razzi_get_product_quickview',
					product_id: product_id,
				},
				function (response) {
					$container.find('.product').replaceWith(response.data);

					if (response.success) {
						update_quickview();
					}

					$target.removeClass('loading').addClass('loaded');
					razzi.productQuantityDropdown();
					razzi.addToCartSingleAjax();
					razzi.$body.trigger('razzi_product_quick_view_loaded');

					if ($container.find('.deal-expire-countdown').length > 0) {
						$(document.body).trigger('razzi_countdown', [$('.deal-expire-countdown')]);
					}
				}
			).fail(function () {
				window.location.href = $el.attr('href');
			});

			/**
			 * Update quick view common elements.
			 */
			function update_quickview() {
				var $product = $container.find('.product'),
					$gallery = $product.find('.woocommerce-product-gallery'),
					$variations = $product.find('.variations_form');

				// Prevent clicking on gallery image link.
				$gallery.on('click', '.woocommerce-product-gallery__image a', function (event) {
					event.preventDefault();
				});

				$gallery.removeAttr('style');

				if ($gallery.find('.woocommerce-product-gallery__wrapper').children().length > 1) {
					$gallery.addClass('swiper-container');
					$gallery.find('.woocommerce-product-gallery__wrapper').addClass('swiper-wrapper');
					$gallery.find('.woocommerce-product-gallery__image').addClass('swiper-slide');
					$gallery.after('<span class="razzi-svg-icon rz-quickview-button-prev rz-swiper-button"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg></span>');
					$gallery.after('<span class="razzi-svg-icon rz-quickview-button-next rz-swiper-button"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg></span>');

					var options = {
						loop: false,
						autoplay: false,
						speed: 800,
						watchOverflow: true,
						navigation: {
							nextEl: '.rz-quickview-button-next',
							prevEl: '.rz-quickview-button-prev',
						},
						on: {
							init: function () {
								$gallery.css('opacity', 1);
							}
						},
					};

					$gallery.imagesLoaded(function () {
						new Swiper($gallery, options);
					});
				}

				if (typeof wc_add_to_cart_variation_params !== 'undefined') {

					$variations.each(function () {
						$(this).wc_variation_form();
					});
				}

				$( document.body ).trigger( 'init_variation_swatches');

			}
		});
	};

	razzi.productLoaded = function () {
		razzi.$body.on('razzi_products_loaded', function (e, $content) {

			var $variations = $content.find('.variations_form');

			if (typeof $.fn.wc_variation_form !== 'undefined') {
				$variations.each(function () {
					$(this).wc_variation_form();
				});
			}

			$( document.body ).trigger( 'init_variation_swatches');

			razzi.productLoopHoverSlider();
			razzi.productLoopHoverZoom();
		});
	};

	razzi.accountOrder = function () {
		if (!razzi.$body.hasClass('woocommerce-account')) {
			return;
		}

		$('.woocommerce-MyAccount-navigation .woocommerce-MyAccount-navigation-link > a').append('<span class="razzi-svg-icon "><svg class="svg-icon" aria-hidden="true" role="img" focusable="false" width="10" height="5" viewBox="0 0 10 5" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.50495 4.82011L0.205241 1.04856C-0.0684137 0.808646 -0.0684137 0.419663 0.205241 0.179864C0.478652 -0.0599547 0.922098 -0.0599547 1.19549 0.179864L5.00007 3.5171L8.80452 0.179961C9.07805 -0.0598577 9.52145 -0.0598577 9.79486 0.179961C10.0684 0.41978 10.0684 0.808743 9.79486 1.04866L5.49508 4.8202C5.35831 4.94011 5.17925 5 5.00009 5C4.82085 5 4.64165 4.94 4.50495 4.82011Z"/></svg></span>');

		$('table.my_account_orders').on('click', '.item-plus', function () {
			$(this).closest('ul').find('li').show();

			$(this).closest('ul').find('.item-plus').hide();
		});
	};

	/**
	 * Toggle register/login form in the login panel.
	 */
	razzi.loginPanel = function () {
		$(document.body)
			.on('click', '#account-modal .create-account', function (event) {
				event.preventDefault();

				$(this).closest('form.login').fadeOut(function () {
					$(this).next('form.register').fadeIn();
				});
			}).on('click', '#account-modal a.login', function (event) {
			event.preventDefault();

			$(this).closest('form.register').fadeOut(function () {
				$(this).prev('form.login').fadeIn();
			});
		});
	};

	/**
	 * Ajax login before refresh page
	 */
	razzi.loginPanelAuthenticate = function () {
		$('#account-modal').on('submit', 'form.login', function authenticate(event) {
			var username = $('input[name=username]', this).val(),
				password = $('input[name=password]', this).val(),
				remember = $('input[name=rememberme]', this).is(':checked'),
				nonce = $('input[name=woocommerce-login-nonce]', this).val(),
				$button = $('[type=submit]', this),
				$form = $(this),
				$box = $form.next('.woocommerce-error');

			if (!username) {
				$('input[name=username]', this).focus();

				return false;
			}

			if (!password) {
				$('input[name=password]', this).focus();

				return false;
			}

			$form.find('.woocommerce-error').remove();
			$button.html('<span class="razzi-button razzi-loading"></span>');

			if ($box.length) {
				$box.fadeOut();
			}

			var ajax_url = razziData.ajax_url.toString().replace('%%endpoint%%', 'razzi_login_authenticate');

			$.post(
				ajax_url,
				{
					security: nonce,
					username: username,
					password: password,
					remember: remember
				},
				function (response) {
					if (!response.success) {
						if (!$box.length) {
							$box = $('<div class="woocommerce-error" role="alert"/>');

							$box.append('<ul class="error-message" />')
								.append('<span class="razzi-svg-icon svg-icon icon-close size-normal close-message"><svg class="svg-icon" aria-hidden="true" role="img" focusable="false" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 1L1 14M1 1L14 14" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path></svg></span>');

							$box.hide().prependTo($form);
						}

						$box.find('.error-message').html('<li>' + response.data + '</li>');
						$box.fadeIn();
						$button.html($button.attr('value'));
					} else {
						$button.html($button.data('signed'));
						window.location.reload();
					}
				}
			);

			event.preventDefault();
		}).on('click', '.woocommerce-error .close-message', function () {
			// Remove the error message to fix the layout issue.
			$(this).closest('.woocommerce-error').fadeOut(function () {
				$(this).remove();
			});

			return false;
		});
	};

	/**
	 * Open product image lightbox when click on the zoom image
	 */
	razzi.productLightBox = function () {
		if (typeof wc_single_product_params === 'undefined' || wc_single_product_params.photoswipe_enabled !== '1') {
			$('.woocommerce-product-gallery').on('click', '.woocommerce-product-gallery__image', function (e) {
				e.preventDefault();
			});
			return false;
		}

		$('.woocommerce-product-gallery').on('click', '.zoomImg', function () {
			if (wc_single_product_params.flexslider_enabled) {
				$(this).closest('.woocommerce-product-gallery').children('.woocommerce-product-gallery__trigger').trigger('click');
			} else {
				$(this).prev('a').trigger('click');
			}
		});
	};

	/**
	 * Handle product reviews
	 */
	razzi.reviewProduct = function () {
		setTimeout(function () {
			$('#respond p.stars a').prepend('<span class="razzi-svg-icon "><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></span>');
		}, 100);
	};

	/**
	 * Open Mini Cart
	 */
	razzi.openMiniCartPanel = function () {
		if (typeof razziData.added_to_cart_notice === 'undefined') {
			return;
		}

		if (razziData.added_to_cart_notice.added_to_cart_notice_layout !== 'panel') {
			return;
		}

		var product_title = '1';
		$(document.body).on('adding_to_cart', function (event, $thisbutton) {
			product_title = '';
		});

		$(document.body)
			.on('added_to_cart wc_fragments_refreshed', function () {
				if (product_title !== '1') {
					razzi.openModal('#cart-modal');
				}
			});

	};

	razzi.addedToCartNotice = function () {

		if (typeof razziData.added_to_cart_notice === 'undefined' || !$.fn.notify) {
			return;
		}

		if (razziData.added_to_cart_notice.added_to_cart_notice_layout != 'simple') {
			return;
		}

		var product_title = '1';
		$(document.body).on('adding_to_cart', function (event, $thisbutton) {
			product_title = '';
			if (typeof $thisbutton.data('title') !== 'undefined') {
				product_title = $thisbutton.data('title');
			}

			product_title = typeof(product_title) === 'undefined' ? '' : product_title;
			if (product_title === '') {
				if ($thisbutton.closest('form.cart').not('.grouped_form').length) {
					product_title = $thisbutton.closest('form.cart').find('.rz_product_id').data('title');
				}
			}

		});

		$(document.body).on('added_to_cart', function () {
			if (product_title !== '1') {
				getaddedToCartNotice(product_title);
			}
		});

		$(document.body).on('wc_fragment_refresh', function () {
			if (product_title !== '1') {
				getaddedToCartNotice(product_title);
			}

		});

		function getaddedToCartNotice($content) {

			if ($content) {
				$content += ' ' + razziData.added_to_cart_notice.added_to_cart_text;
			} else {
				$content = razziData.added_to_cart_notice.successfully_added_to_cart_text;
			}

			$content += '<a href="' + razziData.added_to_cart_notice.cart_view_link + '" class="btn-button">' + razziData.added_to_cart_notice.cart_view_text + '</a>';

			var $checkIcon = '<span class="razzi-svg-icon message-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>',
				$closeIcon = '<span class="razzi-svg-icon svg-active"><svg class="svg-icon" aria-hidden="true" role="img" focusable="false" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 1L1 14M1 1L14 14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path></svg></span>';

			$.notify.addStyle('razzi', {
				html: '<div>' + $checkIcon + $content + $closeIcon + '</div>'
			});

			$.notify('&nbsp', {
				autoHideDelay: razziData.added_to_cart_notice.cart_notice_auto_hide,
				className: 'success',
				style: 'razzi',
				showAnimation: 'fadeIn',
				hideAnimation: 'fadeOut'
			});
		}
	};


	/**
	 * Toggle product popup add to cart
	 */
	razzi.productPopupATC = function () {

		if (typeof razziData.added_to_cart_notice === 'undefined') {
			return;
		}

		if (razziData.added_to_cart_notice.added_to_cart_notice_layout != 'popup') {
			return;
		}

		var $modal = $('#rz-popup-add-to-cart'),
			$product = $modal.find('.product-modal-content'),
			$recomended = $product.find('.rz-product-popup-atc__recommendation');

		if ($modal.length < 1) {
			return
		}

		var $product_item_id = 0,
			$product_id = 0;
		$(document.body).on('adding_to_cart', function (event, $thisbutton) {
			$product_item_id = $product_id = 0;
			if (typeof $thisbutton.data('product_id') !== 'undefined') {
				$product_id = $thisbutton.data('product_id');
				$product_item_id = '0,' + $product_id;
			}

			$product_id = typeof($product_id) === 'undefined' ? 0 : $product_id;

			if ($product_id === 0 && $thisbutton.closest('form.cart').length) {
				var $cartForm = $thisbutton.closest('form.cart');
				$product_id = $cartForm.find('.rz_product_id').val();

				$product_item_id = $product_id;

				if ($cartForm.hasClass('variations_form') && $cartForm.find('.single_variation_wrap .variation_id').length > 0) {
					$product_item_id = $cartForm.find('.single_variation_wrap .variation_id').val();
				}

				if ($cartForm.hasClass('grouped_form')) {
					$product_item_id = 0;
					$cartForm.find('.woocommerce-grouped-product-list-item').each(function () {
						if ($(this).find('.quantity .input-text').val() > 0) {
							var $id = $(this).attr('id');
							$id = $id.replace('product-', '');
							$product_item_id += ',' + $id;
						}
					});
				}
			}

		});

		$(document.body).on('added_to_cart', function () {
			if ($product_item_id && $product_id) {
				getProductPopupContent($product_id, $product_item_id);
				$product_item_id = 0;
				$product_id = 0;
			}
		});

		$(document.body).on('wc_fragments_refreshed', function () {
			if ($product_item_id && $product_id) {
				getProductPopupContent($product_id, $product_item_id);
				$product_item_id = 0;
				$product_id = 0;
			}

		});

		function getProductPopupContent($product_id, $product_item_id) {
			var $item_ids = $product_item_id.split(',');
			for (var i = 0; i < $item_ids.length; ++i) {
				$product.find('.mini-cart-item-' + $item_ids[i]).addClass('active');
			}

			$product.find('.woocommerce-mini-cart-item').not('.active').remove();
			$product.find('.woocommerce-mini-cart-item').find('.woocommerce-cart-item__qty, .remove_from_cart_button').remove();

			razzi.openModal($modal);
			$modal.addClass('loaded');
			if (!$recomended.hasClass('loaded')) {
				$recomended.removeClass('active').removeClass('hidden').addClass('loading');
				$.ajax({
					url: razziData.ajax_url.toString().replace('%%endpoint%%', 'razzi_product_popup_recommended'),
					type: 'POST',
					data: {
						nonce: razziData.nonce,
						product_id: $product_id
					},
					success: function (response) {
						if (!response || response.data === '') {
							$recomended.addClass('hidden');
							return;
						}
						$recomended.html(response.data);
						productsCarousel($recomended);
						$recomended.addClass('active');

					}
				})
			} else {
				if (!$recomended.hasClass('has-carousel')) {
					productsCarousel($recomended);
					$recomended.addClass('has-carousel');
				}
			}

		}

		function productsCarousel($selector) {

			if ($selector.length < 1) {
				return;
			}

			var $products = $selector.find('ul.product-items');

			if ($products.length < 1) {
				return;
			}

			$products.find('li.product-item').addClass('swiper-slide');
			$products.after('<div class="swiper-pagination"></div>');
			new Swiper($selector.find('.recommendation-products-carousel'), {
				loop: false,
				spaceBetween: 20,
				watchOverflow: true,
				navigation: {
					nextEl: $selector.find('.rz-swiper-button-next'),
					prevEl: $selector.find('.rz-swiper-button-prev'),
				},
				pagination: {
					el: $selector.find('.swiper-pagination'),
					clickable: true
				},
				on: {
					init: function () {
						this.$el.css('opacity', 1);
					}
				},
				breakpoints: {
					300: {
						slidesPerView: razziData.mobile_portrait,
						slidesPerGroup: razziData.mobile_portrait,
						spaceBetween: 20,
					},
					480: {
						slidesPerView: razziData.mobile_landscape,
						slidesPerGroup: razziData.mobile_landscape,
					},
					768: {
						spaceBetween: 20,
						slidesPerView: 3,
						slidesPerGroup: 3
					},
					992: {
						slidesPerView: 3,
						slidesPerGroup: 3
					},
					1200: {
						slidesPerView: 4,
						slidesPerGroup: 4
					}
				}
			});

		};

	};

	razzi.addedToWishlistNotice = function () {
		if (typeof razziData.added_to_wishlist_notice === 'undefined' || !$.fn.notify) {
			return;
		}

		razzi.$body.on('added_to_wishlist', function (e, $el_wrap) {
			var content = $el_wrap.data('product-title');
			getaddedToWishlistNotice(content);
		});

		function getaddedToWishlistNotice($content) {

			$content += ' ' + razziData.added_to_wishlist_notice.added_to_wishlist_text;

			$content += '<a href="' + razziData.added_to_wishlist_notice.wishlist_view_link + '" class="btn-button">' + razziData.added_to_wishlist_notice.wishlist_view_text + '</a>';


			var $checkIcon = '<span class="razzi-svg-icon message-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>',
				$closeIcon = '<span class="razzi-svg-icon svg-active"><svg class="svg-icon" aria-hidden="true" role="img" focusable="false" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 1L1 14M1 1L14 14" stroke="#A0A0A0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path></svg></span>';

			$.notify.addStyle('razzi', {
				html: '<div>' + $checkIcon + $content + $closeIcon + '</div>'
			});
			$.notify('&nbsp', {
				autoHideDelay: razziData.added_to_wishlist_notice.wishlist_notice_auto_hide,
				className: 'success',
				style: 'razzi',
				showAnimation: 'fadeIn',
				hideAnimation: 'fadeOut'
			});
		}
	}

	/**
	 * Newsletter popup.
	 */
	razzi.newsletterPopup = function () {
		if (!razziData.popup) {
			return;
		}
		var $modal = $('#newsletter-popup-modal'),
			days = parseInt(razziData.popup_frequency),
			delay = parseInt(razziData.popup_visible_delay);

		if (!$modal.length) {
			return;
		}

		if (document.cookie.match(/^(.*;)?\s*razzi_newsletter_popup_prevent\s*=\s*[^;]+(.*)?$/)) {
			return;
		}

		if (days > 0 && document.cookie.match(/^(.*;)?\s*razzi_newsletter_popup\s*=\s*[^;]+(.*)?$/)) {
			return;
		}

		delay = Math.max(delay, 0);
		delay = 'delay' === razziData.popup_visible ? delay : 0;

		function closeNewsLetter(days, $check) {
			var date = new Date(),
				value = date.getTime();

			if ($check) {
				date.setTime(date.getTime() + (1 * 24 * 60 * 60 * 1000));
				document.cookie = 'razzi_newsletter_popup_prevent=' + value + ';expires=' + date.toGMTString() + ';path=/';

			} else {
				document.cookie = 'razzi_newsletter_popup_prevent=' + value + ';expires=' + date.toGMTString() + ';path=/';

				date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
				document.cookie = 'razzi_newsletter_popup=' + value + ';expires=' + date.toGMTString() + ';path=/';
			}
		}

		setTimeout(function () {
			razzi.openModal($modal);
		}, delay * 1000);

		// Prevent this Pop-up
		$modal.on('click', '.n-close', function (e) {
			e.preventDefault();
			$(this).addClass('active');

			setTimeout(function () {
				closeNewsLetter(days, true);
				razzi.closeModal($modal);
			}, 800);

		});

		$(document.body).on('razzi_modal_closed', function (event, modal) {
			if (!$(modal).closest('.rz-modal').hasClass('newsletter-popup-modal')) {
				return;
			}

			if ($(modal).find('.n-close').hasClass('active')) {
				return;
			}

			closeNewsLetter(days, false);
		});
	};

	/**
	 * Back to top icon
	 */
	razzi.backToTop = function () {
		var $scrollTop = $('#gotop');

		razzi.$window.on('scroll', function () {
			if (razzi.$window.scrollTop() > razzi.$window.height()) {
				$scrollTop.addClass('show-scroll');
			} else {
				$scrollTop.removeClass('show-scroll');
			}
		});

		razzi.$body.on('click', '#gotop', function (e) {
			e.preventDefault();

			$('html, body').animate({scrollTop: 0}, 800);
		});
	};

	// Recently Viewed Products
	razzi.recentlyViewedProducts = function () {

		var $recently = $('#razzi-history-products');

		if ($recently.length < 1) {
			return;
		}

		if ($recently.hasClass('loaded')) {
			return;
		}

		if ($recently.hasClass('no-ajax')) {
			recentlyViewedProductsCarousel();
			hoverInforProduct();

			return;
		}

		razzi.$window.on('scroll', function () {
			if (razzi.$body.find('#razzi-history-products').is(':in-viewport')) {
				loadProductsAJAX();
			}
		}).trigger('scroll');

		function loadProductsAJAX() {
			if ($recently.hasClass('loaded')) {
				return;
			}
			if ($recently.data('requestRunning')) {
				return;
			}

			$recently.data('requestRunning', true);

			var ajax_url = razziData.ajax_url.toString().replace('%%endpoint%%', 'razzi_get_recently_viewed');
			$.post(
				ajax_url,
				function (response) {

					$recently.find('.recently-products ').html(response.data);
					if ($recently.find('.product-list').hasClass('no-products')) {
						$recently.addClass('no-products');
					}
					recentlyViewedProductsCarousel();
					hoverInforProduct();
					$recently.addClass('loaded');
					$recently.data('requestRunning', false);
					razzi.$body.trigger('razzi_products_loaded', [$recently, false]);
				}
			);
		}

		function hoverInforProduct() {
			var $product = $recently.find('li.product');

			$product.on('mousemove', function (e) {
				var el = $(this),
					left = e.pageX - el.offset().left + 10,
					right = left - el.find('.product-infor').outerWidth(),
					top = e.pageY - el.offset().top + 10;

				if( el.is(':last-child') ) {
					el.find('.product-infor')
					.show()
					.css({left: right, top: top});
				} else {
					el.find('.product-infor')
					.show()
					.css({left: left, top: top});
				}

			}).on('mouseout', function () {
				$(this).find('.product-infor').hide();
			});
		}

		function recentlyViewedProductsCarousel() {
			var $related = $('#razzi-history-products'),
				$products = $related.find('ul.products'),
				$col = $related.data('col');

			if (!$related.length) {
				return;
			}

			$col = $col ? $col : 4;

			$products.wrap('<div class="swiper-container history-products-carousel"></div>');
			$products.after('<div class="swiper-scrollbar"></div>');
			$products.addClass('swiper-wrapper');
			$products.find('li.product').addClass('swiper-slide');

			new Swiper($related.find('.history-products-carousel'), {
				loop: false,
				scrollbar: {
					el: $related.find('.swiper-scrollbar'),
					hide: false,
					draggable: true
				},
				on: {
					init: function () {
						if ($related.hasClass('no-ajax')) {
							$related.find('.recently-products').css('opacity', 1);
						}
					}
				},
				speed: 1000,
				spaceBetween: 30,
				breakpoints: {
					300: {
						slidesPerView: razziData.mobile_portrait == '' ? 2 : razziData.mobile_portrait,
						slidesPerGroup: razziData.mobile_portrait == '' ? 2 : razziData.mobile_portrait,
						spaceBetween: 15,
					},
					480: {
						slidesPerView: razziData.mobile_landscape == '' ? 3 : razziData.mobile_landscape,
						slidesPerGroup: razziData.mobile_landscape == '' ? 3 : razziData.mobile_landscape,
						spaceBetween: 15,
					},
					768: {
						spaceBetween: 20,
						slidesPerView: 3,
						slidesPerGroup: 3
					},
					992: {
						slidesPerView: 4,
						slidesPerGroup: 4
					},
					1200: {
						slidesPerView: $col,
						slidesPerGroup: $col
					}
				}
			});
		}
	};

	// Footer Dropdown
	razzi.footerDropdown = function () {
		$('.footer-widgets .widget').find('.widget-title').append('<span class="razzi-svg-icon"><svg aria-hidden="true" role="img" focusable="false" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg></span>');
		var $dropdown = $('.footer-widgets .widget'),
			$title = $dropdown.find('.widget-title');

		razzi.$window.on('resize', function () {
			if (razzi.$window.width() < 768) {
				$title.next('div').addClass('clicked');
				$title.parent().addClass('dropdown');
			} else {
				$title.next('div').removeClass('clicked');
				$title.next('div').removeAttr('style');
				$title.parent().removeClass('dropdown');
			}

		}).trigger('resize');

		$dropdown.on('click', '.widget-title', function (e) {
			e.preventDefault();
			if (!$title.parent().hasClass('dropdown')) {
				return;
			}
			$(this).next('.clicked').stop().slideToggle();
			$(this).toggleClass('active');
			return false;
		});
	};

	// Scroll Section
	razzi.scrollSection = function () {
		var baseURI = $('#rz-base-url').data('url');
		$('#primary-menu').on('click', 'a', function (e) {
			e.preventDefault();
			var currentHref = $(this).attr('href'),
				target = this.hash;

			if (target == '') {
				window.location.href = currentHref;
				return false;
			} else if ( target !== '' && $(target).length < 1)  {
				window.location.href = baseURI + target;
				return false;
			}
		});

	};

	razzi.inlineStyle = function() {
		razzi.$window.on('resize', function () {
			if (razzi.$window.width() < 601) {
				$('#wpadminbar').css('z-index', '999');
			} else {
				$('#wpadminbar').removeAttr('style');
			}
		}).trigger('resize');
	}

	  /**
     * History Back
     */
	razzi.historyBack = function () {
        razzi.$header.find('.razzi-history-back').on('click', function (e) {
            if (document.referrer != '') {
                e.preventDefault();

                window.history.go(-1);
                $(window).on('popstate', function (e) {
                    window.location.reload(true);
                });
            }

        });
    };

	/**
	 * Open quick links when focus on search field
	 */
	 razzi.focusSearchField = function() {
		$( '.header-search .search-field' ).on( 'focus', function() {
			var $quicklinks = $( this ).closest( '.header-search' ).find( '.quick-links' );

			if ( !$quicklinks.length ) {
				return;
			}

			$quicklinks.addClass( 'open' );
			$( this ).addClass( 'focused' );
		} );

		$( document.body ).on( 'click', 'div', function( event ) {
			var $target = $( event.target );

			if ( $target.is( '.header-search' ) || $target.closest( '.header-search' ).length ) {
				return;
			}

			$( '.quick-links', '.header-search' ).removeClass( 'open' );
			$( '.search-field', '.header-search' ).removeClass( 'focused' );
		} );
	};

	/**
	 * Init sticky cart form.
	 *
	 * @todo Support bundled products.
	 */
	 razzi.stickyAddToCart = function() {
		var $sticky = $( '#razzi-sticky-add-to-cart' );

		$sticky = ! $sticky.length ? $( '#rz-navigation-bar' ) : $sticky;

		if ( ! $sticky.length ) {
			return;
		}

		var $forms = $( 'form.cart', $sticky.closest( '.single-product' ) ),
			$image = $sticky.find( '.razzi-sticky-atc__product-image img' ),
			$price = $sticky.find( '.razzi-sticky-add-to-cart__content-price' ),
			doingSync = false;

		// Handle add-to-cart variations of 2 forms.
		$forms
			.on( 'reset_data', function() {
				$image.attr( 'src', $image.data( 'o_src' ) );
				$price.show().siblings( '.variation-price, .stock' ).remove();
			} )
			.on( 'found_variation', function( event, variation ) {
				if ( variation.image && variation.image.gallery_thumbnail_src && variation.image.gallery_thumbnail_src.length > 1 ) {
					$image.attr( 'src', variation.image.gallery_thumbnail_src );
				}

				if ( variation.availability_html && variation.availability_html.length ) {
					$price.hide().siblings( '.stock, .variation-price' ).remove();
					$price.after( variation.availability_html );
				} else {
					$price.siblings( '.stock' ).remove();

					if ( variation.price_html && variation.price_html.length ) {
						$price.hide().siblings( '.variation-price' ).remove();
						$price.after( $( variation.price_html ).addClass( 'variation-price' ) );
					}
				}
			} )
			// Sync inputs' value.
			.on( 'change', ':input', function() {
				// Avoid infinite loop.

				if ( doingSync ) {
					return;
				}

				doingSync = true;

				var $currentForm = $( this ).closest( 'form.cart' ),
					$targetForm = $forms.not( $currentForm );


				$targetForm.find( ':input[name="' + this.name + '"]' ).val( this.value ).trigger( 'change' );

				if( ! razzi.$body.hasClass('product-qty-number') ) {
					$targetForm.find( ':input[name="' + this.name + '"]' ).siblings('.qty-dropdown').find('.current .value').html(this.value);
				}

				doingSync = false;
			} );

	}

	/**
	 * Document ready
	 */
	$(function () {
		razzi.init();
	});

})(jQuery);