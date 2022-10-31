class RazziProductShortcodeWidgetHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                gallery: '.woocommerce-product-gallery'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');

        return {
            $gallery: this.$element.find(selectors.gallery)
        };

    }

    getSwipperOptions() {
        const swiperOptions = {
            watchOverflow: true,
            slidesPerView: this.elements.$gallery.data('columns'),
            spaceBetween: 15,
            navigation: {
                nextEl: this.elements.$gallery.find('.rz-gallery-button-next'),
                prevEl: this.elements.$gallery.find('.rz-gallery-button-prev'),
            },
            breakpoints: {
                300: {
                    spaceBetween: 0,
                    allowTouchMove: false,
                },
                991: {
                    spaceBetween: 15,
                },
            }
        };

        return swiperOptions;
    }

    getFlexSliderInit() {
        var self = this;

        if (!self.$element.closest('body').hasClass('elementor-editor-active')) {
            return;
        }
        const options = {
            selector: '.woocommerce-product-gallery__wrapper > .woocommerce-product-gallery__image',
            allowOneSlide: false,
            animation: "slide",
            animationLoop: false,
            controlNav: "thumbnails",
            animationSpeed: 500,
            directionNav: false,
            rtl: false,
            slideshow: false,
            smoothHeight: true,
            start: function () {
                self.elements.$gallery.css('opacity', 1);
            },
        };

        this.elements.$gallery.flexslider(options);
    }

    getImageZoomInit($target) {

        if (!this.$element.closest('body').hasClass('elementor-editor-active')) {
            return;
        }

        if (!jQuery.fn.zoom) {
            return;
        }


        const settings = this.getElementSettings();

        if (settings.show_image_zoom === 'show') {

            var zoom_options = jQuery.extend({
                touch: false
            });

            $target.trigger('zoom.destroy');
            $target.zoom(zoom_options);
        }
    }

    getVariationSwatcher() {

        var $variations = this.$element.find('.variations_form');
        if (typeof wc_add_to_cart_variation_params !== 'undefined') {
            $variations.find('td.value select').each(function () {
                jQuery(this).on('change', function () {
                    var value = jQuery(this).find('option:selected').text();
                    jQuery(this).closest('tr').find('td.label .razzi-attr-value').html(value);
                }).trigger('change');
            });
        }
    }

    getSwiperInit() {
        var $thumbnail = this.$element.find('.flex-control-thumbs');
        $thumbnail.wrap('<div class="woocommerce-product-gallery__thumbs-carousel swiper-container linked-gallery-carousel"></div>');
        $thumbnail.addClass('swiper-wrapper');
        $thumbnail.find('li').addClass('swiper-slide');
        $thumbnail.after('<span class="razzi-svg-icon rz-gallery-button-prev rz-swiper-button"><svg class="svg-icon" aria-hidden="true" role="img" focusable="false" width="13" height="24" viewBox="0 0 13 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.467723 13.1881L10.2737 23.5074C10.8975 24.1642 11.9089 24.1642 12.5324 23.5074C13.1559 22.8512 13.1559 21.787 12.5324 21.1308L3.85554 11.9998L12.5321 2.86914C13.1556 2.21269 13.1556 1.14853 12.5321 0.492339C11.9086 -0.164113 10.8973 -0.164113 10.2735 0.492339L0.46747 10.8118C0.155705 11.14 0 11.5698 0 11.9998C0 12.43 0.156009 12.86 0.467723 13.1881Z"></path></svg></span>');
        $thumbnail.after('<span class="razzi-svg-icon rz-gallery-button-next rz-swiper-button"><svg class="svg-icon" aria-hidden="true" role="img" focusable="false" width="13" height="24" viewBox="0 0 13 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.5323 13.1881L2.72626 23.5074C2.10248 24.1642 1.09112 24.1642 0.467647 23.5074C-0.155882 22.8512 -0.155882 21.787 0.467647 21.1308L9.14446 11.9998L0.467899 2.86914C-0.15563 2.21269 -0.15563 1.14853 0.467899 0.492339C1.09143 -0.164113 2.10273 -0.164113 2.72651 0.492339L12.5325 10.8118C12.8443 11.14 13 11.5698 13 11.9998C13 12.43 12.844 12.86 12.5323 13.1881Z"></path></svg></span>');

        jQuery('li', $thumbnail).append('<span/>');

        new Swiper(this.elements.$gallery.find('.linked-gallery-carousel'), this.getSwipperOptions());
    }

    getLightBoxGalleryInit() {
        var self = this;
        const settings = this.getElementSettings();
        if (this.$element.closest('body').hasClass('elementor-editor-active')) {
            if (settings.show_lightbox !== 'show' && settings.show_image_zoom !== 'show') {
                this.elements.$gallery.on('click', '.woocommerce-product-gallery__image > a', function (e) {
                    return false;
                });
            }

            if (settings.show_lightbox === 'show' && settings.show_image_zoom === 'show') {
                this.elements.$gallery.on('click', '.woocommerce-product-gallery__image > .zoomImg', function (e) {
                    jQuery(this).closest('.woocommerce-product-gallery__image').find('a').trigger('click');
                });

            }

            if (jQuery.fn.quantityDropdown) {
                self.$element.find('.quantity .qty').quantityDropdown();
            }
        } else {

            if (settings.show_lightbox !== 'show') {
                this.elements.$gallery.on('click', '.woocommerce-product-gallery__image > a', function (e) {
                    return false;
                });
            }
        }
    }

    getCountDownInit() {

        if (!this.$element.closest('body').hasClass('elementor-editor-active')) {
            return;
        }

        if (!jQuery.fn.rz_countdown) {
            return;
        }

        this.$element.find('.razzi-countdown').rz_countdown();
    }


    onInit() {
        var self = this;
        super.onInit();

        this.getCountDownInit();

        this.getLightBoxGalleryInit();

        this.getFlexSliderInit();

        this.getVariationSwatcher();

        this.getImageZoomInit(this.elements.$gallery.find('.woocommerce-product-gallery__image'));


        this.elements.$gallery.imagesLoaded(function () {
            setTimeout(function () {
                self.getSwiperInit();
            }, 200);

        });
    }
}

class RazziProductsMasonryWidgetHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                container: '.razzi-products-masonry'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');

        return {
            $container: this.$element.find(selectors.container)
        };

    }

    productsFound() {
        var $found = this.elements.$container.find('.razzi-posts__found-inner'),
            $foundEls = $found.find('.count-bar'),
            $current = $found.find('.current-post').html(),
            $total = $found.find('.found-post').html(),
            pecent = ($current / $total) * 100;

        $foundEls.css('width', pecent + '%');
    }

    loadProductsGrid() {
        // Load Products
        var self = this;

        this.elements.$container.on('click', '.woocommerce-pagination a.next', function (e) {
            e.preventDefault();

            var $el = jQuery(this),
                $nav = $el.closest('.woocommerce-pagination'),
                url = $el.attr('href'),
                $products = $el.closest('.razzi-products-masonry').find('ul.products'),
                $currentPosts = $products.find('li.product:not(.has-banner)').length,
                $found = $el.closest('.razzi-products-masonry').find('.razzi-posts__found');

            $nav.addClass('loading');

            jQuery.get(url, function (response) {
                var $content = jQuery(response).find('.razzi-products-masonry ul.products li.product'),
                    $navNew = jQuery(response).find('.razzi-products-masonry .woocommerce-pagination'),
                    $foundItem = jQuery(response).find('.razzi-products-masonry ul.products li.product:not(.has-banner)'),
                    $numberPosts = $foundItem.length + $currentPosts;

                // Add animation class
                for (var index = 0; index < $content.length; index++) {
                    jQuery($content[index]).css('animation-delay', index * 100 + 'ms');
                }
                $content.addClass('razziFadeInUp');

                $products.append($content);
                $nav.replaceWith($navNew);

                jQuery(document.body).trigger('razzi_products_masonry_loaded', [$content, true]);

                $found.find('.current-post').html(' ' + $numberPosts);
                self.productsFound();

                $navNew.find('.next').parent().addClass('btn-load-more');
                $navNew.removeClass('loading');
            });
        });
    };

    onInit() {
        var self = this;
        super.onInit();

        this.elements.$container.find('.woocommerce-pagination .next').parent().addClass('btn-load-more');

        self.loadProductsGrid();

    }
}

class RazziProductsDealWidgetHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                container: '.razzi-products-deal'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');

        return {
            $container: this.$element.find(selectors.container)
        };

    }

    getProductSwiperInit() {
        const settings = this.getElementSettings();

        var $list = this.elements.$container.find('ul.product-loop-layout-deal');
        $list.find('li.product').addClass('swiper-slide');
        $list.after('<div class="swiper-pagination"></div>');
        $list.after('<div class="swiper-scrollbar"></div>');

        var options = {
            watchOverflow: true,
            pagination: {
                el: this.elements.$container.find('.swiper-pagination'),
                clickable: true
            },
            scrollbar: {
                el: '.swiper-scrollbar',
                hide: false,
                draggable: true
            },
            loop: settings.infinite == 'yes' ? true : false,
            autoplay: settings.autoplay == 'yes' ? true : false,
            speed: settings.speed,
            spaceBetween: 30,
            on: {
                init: function () {
                    this.$el.css('opacity', 1);
                }
            },
            breakpoints: {
                300: {
                    slidesPerView: settings.slidesToShow_mobile ? settings.slidesToShow_mobile : razziData.mobile_portrait,
                    slidesPerGroup: settings.slidesToScroll_mobile ? settings.slidesToScroll_mobile : razziData.mobile_portrait,
                    spaceBetween: 15,
                },
                480: {
                    slidesPerView: settings.slidesToShow_mobile ? settings.slidesToShow_mobile : razziData.mobile_portrait,
                    slidesPerGroup: settings.slidesToScroll_mobile ? settings.slidesToScroll_mobile : razziData.mobile_portrait,
                    spaceBetween: 15,
                },
                768: {
                    slidesPerView: settings.slidesToShow_tablet ? settings.slidesToShow_tablet : 3,
                    slidesPerGroup: settings.slidesToScroll_tablet ? settings.slidesToScroll_tablet : 3,
                },
                1024: {
                    slidesPerView: settings.slidesToShow > 5 ? 5 : settings.slidesToShow,
                    slidesPerGroup: settings.slidesToScroll > 5 ? 5 : settings.slidesToScroll,
                    spaceBetween: 30
                },
                1366: {
                    slidesPerView: settings.slidesToShow > 6 ? 6 : settings.slidesToShow,
                    slidesPerGroup: settings.slidesToScroll > 6 ? 6 : settings.slidesToScroll
                },
                1500: {
                    slidesPerView: settings.slidesToShow,
                    slidesPerGroup: settings.slidesToScroll
                }
            }
        };

        new Swiper(this.elements.$container.find('.linked-products-deal-carousel'), options);
    }

    onInit() {
        var self = this;
        super.onInit();

        this.elements.$container.find('.razzi-countdown').rz_countdown();

        this.elements.$container.imagesLoaded(function () {
            setTimeout(function () {
                self.getProductSwiperInit();
            }, 200);

        });
    }
}

class RazziProductsShowcaseWidgetHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                container: '.razzi-products-showcase'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');

        return {
            $container: this.$element.find(selectors.container)
        };

    }

    getProductSwiperInit() {
        var $products = this.elements.$container.find('ul.products');

        $products.addClass('swiper-wrapper');
        $products.find('li.product').addClass('swiper-slide');
        $products.after('<div class="swiper-pagination"></div>');

        var galleryBox = new Swiper(this.elements.$container.find('.showcase-image'), {
            watchOverflow: true,
            watchSlidesVisibility: true,
            watchSlidesProgress: true,
            allowTouchMove: false,
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
        });
        var galleryImage = new Swiper(this.elements.$container.find('.showcase-box .product-content'), {
            watchOverflow: true,
            thumbs: {
                swiper: galleryBox,
                autoScrollOffset: 1
            },
            navigation: {
                nextEl: this.elements.$container.find('.rz-swiper-showcase-button-next'),
                prevEl: this.elements.$container.find('.rz-swiper-showcase-button-prev'),
            },
            pagination: {
                el: this.elements.$container.find('.swiper-pagination'),
                clickable: true
            },
        });
    }

    onInit() {
        var self = this;
        super.onInit();

        self.getProductSwiperInit();
    }
}

class RazziProductsCarouselWidgetHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                container: '.razzi-products-carousel'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');

        return {
            $container: this.$element.find(selectors.container)
        };

    }

    getProductSwiperInit() {
        const settings = this.getElementSettings();

        var $products = this.elements.$container.find('ul.products');
        $products.wrap('<div class="swiper-container linked-elementor-product-carousel" style="opacity: 0;"></div>');
        $products.addClass('swiper-wrapper');
        $products.find('li.product').addClass('swiper-slide');
        $products.after('<div class="swiper-pagination"></div>');
        $products.after('<div class="swiper-scrollbar"></div>');

        var options = {
            watchOverflow: true,
            loop: settings.infinite == 'yes' ? true : false,
            autoplay: settings.autoplay == 'yes' ? true : false,
            speed: settings.speed,
            spaceBetween: 30,
            navigation: {
                nextEl: this.elements.$container.find('.rz-swiper-button-next'),
                prevEl: this.elements.$container.find('.rz-swiper-button-prev'),
            },
            pagination: {
                el: this.elements.$container.find('.swiper-pagination'),
                clickable: true
            },
            scrollbar: {
                el: '.swiper-scrollbar',
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
                    slidesPerView: settings.slidesToShow_mobile ? settings.slidesToShow_mobile : razziData.mobile_portrait,
                    slidesPerGroup: settings.slidesToScroll_mobile ? settings.slidesToScroll_mobile : razziData.mobile_portrait,
                    spaceBetween: 15,
                },
                480: {
                    slidesPerView: settings.slidesToShow_mobile ? settings.slidesToShow_mobile : razziData.mobile_portrait,
                    slidesPerGroup: settings.slidesToScroll_mobile ? settings.slidesToScroll_mobile : razziData.mobile_portrait,
                    spaceBetween: 15,
                },
                768: {
                    slidesPerView: settings.slidesToShow_tablet ? settings.slidesToShow_tablet : 3,
                    slidesPerGroup: settings.slidesToScroll_tablet ? settings.slidesToScroll_tablet : 3,
                },
                1024: {
                    slidesPerView: settings.slidesToShow > 5 ? 5 : settings.slidesToShow,
                    slidesPerGroup: settings.slidesToScroll > 5 ? 5 : settings.slidesToScroll,
                    spaceBetween: 30
                },
                1366: {
                    slidesPerView: settings.slidesToShow > 6 ? 6 : settings.slidesToShow,
                    slidesPerGroup: settings.slidesToScroll > 6 ? 6 : settings.slidesToScroll
                },
                1500: {
                    slidesPerView: settings.slidesToShow,
                    slidesPerGroup: settings.slidesToScroll
                }
            }
        };

        new Swiper(this.elements.$container.find('.linked-elementor-product-carousel'), options);
    }

    addClassProductLoopLayout() {
        var $products = this.elements.$container.find('ul.products');

        if ( ! $products.hasClass( 'product-loop-layout-5' ) && ! $products.hasClass( 'product-loop-layout-6' ) ) {
            return;
        }

        $products.closest( '.woocommerce' ).addClass( 'product-loop-outsite' );
    }

    onInit() {
        var self = this;
        super.onInit();

        self.getProductSwiperInit();
        self.addClassProductLoopLayout();
    }
}

class RazziProductOfCategoryWidgetHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                container: '.razzi-product-of-category'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');

        return {
            $container: this.$element.find(selectors.container)
        };

    }

    getProductSwiperInit() {
        const settings = this.getElementSettings();

        var $products = this.elements.$container.find('ul.products');
        $products.wrap('<div class="swiper-container linked-elementor-product-carousel" style="opacity: 0;"></div>');
        $products.addClass('swiper-wrapper');
        $products.find('li.product').addClass('swiper-slide');
        $products.after('<div class="swiper-pagination"></div>');
        $products.after('<div class="swiper-scrollbar"></div>');

        var options = {
            watchOverflow: true,
            loop: settings.infinite == 'yes' ? true : false,
            autoplay: settings.autoplay == 'yes' ? true : false,
            speed: settings.speed,
            spaceBetween: 30,
            navigation: {
                nextEl: this.elements.$container.find('.rz-swiper-button-next'),
                prevEl: this.elements.$container.find('.rz-swiper-button-prev'),
            },
            pagination: {
                el: this.elements.$container.find('.swiper-pagination'),
                clickable: true
            },
            scrollbar: {
                el: '.swiper-scrollbar',
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
                    slidesPerView: settings.slidesToShow_mobile ? settings.slidesToShow_mobile : razziData.mobile_portrait,
                    slidesPerGroup: settings.slidesToScroll_mobile ? settings.slidesToScroll_mobile : razziData.mobile_portrait,
                    spaceBetween: 20,
                },
                480: {
                    slidesPerView: settings.slidesToShow_mobile ? settings.slidesToShow_mobile : razziData.mobile_portrait,
                    slidesPerGroup: settings.slidesToScroll_mobile ? settings.slidesToScroll_mobile : razziData.mobile_portrait,
                },
                768: {
                    slidesPerView: settings.slidesToShow_tablet ? settings.slidesToShow_tablet : 3,
                    slidesPerGroup: settings.slidesToScroll_tablet ? settings.slidesToScroll_tablet : 3,
                },
                900: {
                    slidesPerView: 4,
                    slidesPerGroup: 4,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 5,
                    slidesPerGroup: 5,
                    spaceBetween: 30,
                },
                1200: {
                    slidesPerView: settings.slidesToShow > 5 ? 5 : settings.slidesToShow,
                    slidesPerGroup: settings.slidesToScroll > 5 ? 5 : settings.slidesToScroll
                },
                1366: {
                    slidesPerView: settings.slidesToShow > 6 ? 6 : settings.slidesToShow,
                    slidesPerGroup: settings.slidesToScroll > 6 ? 6 : settings.slidesToScroll
                },
                1500: {
                    slidesPerView: settings.slidesToShow,
                    slidesPerGroup: settings.slidesToScroll
                }
            }
        };

        new Swiper(this.elements.$container.find('.linked-elementor-product-carousel'), options);
    }

    addClassProductLoopLayout() {
        var $products = this.elements.$container.find('ul.products');

        if ( ! $products.hasClass( 'product-loop-layout-5' ) && ! $products.hasClass( 'product-loop-layout-6' ) ) {
            return;
        }

        $products.closest( '.woocommerce' ).addClass( 'product-loop-outsite' );
    }

    onInit() {
        var self = this;
        super.onInit();

        self.getProductSwiperInit();
        self.addClassProductLoopLayout();
    }
}

class RazziProductsCategorytabsWidgetHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                container: '.razzi-product-category-tabs'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');

        return {
            $container: this.$element.find(selectors.container)
        };

    }

    getProductCategory($selector, settings) {
        var $list = $selector.find('ul.category-list');

        $list.find('li.cat-item').addClass('swiper-slide');
        $list.after('<div class="swiper-pagination"></div>');
        $list.after('<span class="razzi-svg-icon rz-category-arrow-prev rz-swiper-button"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg></span>');
        $list.after('<span class="razzi-svg-icon rz-category-arrow-next rz-swiper-button"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg></span>');

        var options = {
            watchOverflow: true,
            loop: settings.infinite == 'yes' ? true : false,
            autoplay: settings.autoplay == 'yes' ? true : false,
            speed: 500,
            pagination: {
                el: $selector.find('.swiper-pagination'),
                clickable: true
            },
            navigation: {
                nextEl: $selector.find('.rz-category-arrow-next'),
                prevEl: $selector.find('.rz-category-arrow-prev'),
            },
            spaceBetween: 30,
            on: {
                init: function () {
                    this.$el.css('opacity', 1);
                }
            },
            breakpoints: {
                300: {
                    slidesPerView: settings.slidesToShow_mobile ? settings.slidesToShow_mobile : razziData.mobile_portrait,
                    slidesPerGroup: settings.slidesToScroll_mobile ? settings.slidesToScroll_mobile : razziData.mobile_portrait,
                    spaceBetween: 15,
                },
                480: {
                    slidesPerView: settings.slidesToShow_mobile ? settings.slidesToShow_mobile : razziData.mobile_portrait,
                    slidesPerGroup: settings.slidesToScroll_mobile ? settings.slidesToScroll_mobile : razziData.mobile_portrait,
                    spaceBetween: 15,
                },
                768: {
                    slidesPerView: settings.slidesToShow_tablet ? settings.slidesToShow_tablet : 3,
                    slidesPerGroup: settings.slidesToScroll_tablet ? settings.slidesToScroll_tablet : 3,
                },
                1024: {
                    slidesPerView: settings.slidesToShow > 5 ? 5 : settings.slidesToShow,
                    slidesPerGroup: settings.slidesToScroll > 5 ? 5 : settings.slidesToScroll,
                    spaceBetween: 30
                },
                1366: {
                    slidesPerView: settings.slidesToShow > 6 ? 6 : settings.slidesToShow,
                    slidesPerGroup: settings.slidesToScroll > 6 ? 6 : settings.slidesToScroll
                },
                1500: {
                    slidesPerView: settings.slidesToShow,
                    slidesPerGroup: settings.slidesToScroll
                }
            }
        };

        new Swiper($selector.find('.linked-products-category'), options);
    };

    productTabs($tabs, $el, $currentTab) {
        $tabs.find('.tabs-nav a').removeClass('active');
        $el.addClass('active');
        $tabs.find('.tabs-panel').removeClass('active');
        $currentTab.addClass('active');
    }

    getCategoryAJAXHandler($el, $tabs) {
        var self = this,
            tab = $el.data('href'),
            $currentTab = $tabs.find('.tabs-' + tab),
            $tabContent = $tabs.find('.tabs-content');

        if ($currentTab.hasClass('tab-loaded')) {
            self.productTabs($tabs, $el, $currentTab);
            return;
        }

        $tabContent.addClass('loading');

        var data = {},
            elementSettings = $currentTab.data('settings'),
            ajax_url = razziData.ajax_url.toString().replace('%%endpoint%%', 'ra_elementor_load_category');

        jQuery.each(elementSettings, function (key, value) {
            data[key] = value;
        });

        jQuery.post(
            ajax_url,
            data,
            function (response) {
                if (!response) {
                    return;
                }

                $currentTab.find('.tab-content').html(response.data);
                const settings = self.getElementSettings();

                self.getProductCategory($currentTab, settings);

                self.productTabs($tabs, $el, $currentTab);
                $tabContent.removeClass('loading');

                $currentTab.addClass('tab-loaded');
            }
        );
    };

    onInit() {
        var self = this;
        const settings = this.getElementSettings();

        super.onInit();

        var $selector = this.elements.$container,
            $panels = $selector.find('.tab-loaded');


        self.getProductCategory($panels, settings);
        $selector.find('.tabs-nav').on('click', 'a', function (e) {
            e.preventDefault();
            self.getCategoryAJAXHandler(jQuery(this), $selector);
        });

    }
}

class RazziProductstabWidgetHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                container: '.razzi-products-tabs'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');

        return {
            $container: this.$element.find(selectors.container)
        };

    }

    getProductCarousel($selector, settings) {
        var $products = $selector.find('ul.products');

        $products.wrap('<div class="swiper-container linked-products-carousel" style="opacity: 0;"></div>');
        $products.addClass('swiper-wrapper');
        $products.find('li.product').addClass('swiper-slide');
        $products.after('<div class="swiper-pagination"></div>');
        $products.after('<div class="swiper-scrollbar"></div>');

        var options = {
            loop: settings.infinite == 'yes' ? true : false,
            autoplay: settings.autoplay == 'yes' ? true : false,
            speed: settings.speed ? settings.speed : 500,
            pagination: {
                el: this.elements.$container.find('.swiper-pagination'),
                clickable: true
            },
            scrollbar: {
                el: '.swiper-scrollbar',
                hide: false,
                draggable: true
            },
            spaceBetween: 30,
            on: {
                init: function () {
                    this.$el.css('opacity', 1);
                }
            },
            breakpoints: {
                300: {
                    slidesPerView: settings.slidesToShow_mobile ? settings.slidesToShow_mobile : razziData.mobile_portrait,
                    slidesPerGroup: settings.slidesToScroll_mobile ? settings.slidesToScroll_mobile : razziData.mobile_portrait,
                    spaceBetween: 15,
                },
                480: {
                    slidesPerView: settings.slidesToShow_mobile ? settings.slidesToShow_mobile : razziData.mobile_portrait,
                    slidesPerGroup: settings.slidesToScroll_mobile ? settings.slidesToScroll_mobile : razziData.mobile_portrait,
                    spaceBetween: 15,
                },
                768: {
                    slidesPerView: settings.slidesToShow_tablet ? settings.slidesToShow_tablet : 3,
                    slidesPerGroup: settings.slidesToScroll_tablet ? settings.slidesToScroll_tablet : 3,
                },
                1024: {
                    slidesPerView: settings.slidesToShow > 5 ? 5 : settings.slidesToShow,
                    slidesPerGroup: settings.slidesToScroll > 5 ? 5 : settings.slidesToScroll,
                    spaceBetween: 30
                },
                1366: {
                    slidesPerView: settings.slidesToShow > 6 ? 6 : settings.slidesToShow,
                    slidesPerGroup: settings.slidesToScroll > 6 ? 6 : settings.slidesToScroll
                },
                1500: {
                    slidesPerView: settings.slidesToShow,
                    slidesPerGroup: settings.slidesToScroll
                }
            }
        };

        new Swiper(this.elements.$container.find('.linked-products-carousel'), options);
    };

    /**
     * Get Product AJAX
     */
    getProductsAJAXHandler($el, $tabs) {
        var self = this,
            tab = $el.data('href'),
            $currentTab = $tabs.find('.tabs-' + tab),
            $tabContent = $tabs.find('.tabs-content');


        if ($currentTab.hasClass('tab-loaded')) {
            self.productTabs($tabs, $el, $currentTab);
            return;
        }

        $tabContent.addClass('loading');

        var data = {},
            elementSettings = $currentTab.data('settings'),
            ajax_url = razziData.ajax_url.toString().replace('%%endpoint%%', 'ra_elementor_load_products_grid');

        const settings = this.getElementSettings();

        jQuery.post(
            ajax_url,
            {
                settings: elementSettings
            },
            function (response) {
                if (!response) {
                    return;
                }

                var content = response.data;

                $currentTab.prepend(content);

                self.getProductCarousel($currentTab, settings);

                if ($currentTab.find('.products').is(':empty')) {
                    var $text = $currentTab.find('.page-number').data('text');
                    $currentTab.addClass('products-empty');
                    if ($text) {
                        $currentTab.find('.products').html('<li>' + $text + '</li>')
                    }
                }

                $currentTab.addClass('tab-loaded');

                self.productTabs($tabs, $el, $currentTab);

                $tabContent.removeClass('loading');

                jQuery(document.body).trigger('razzi_products_loaded', [jQuery(content), true]);
            }
        );
    };

    productTabs($tabs, $el, $currentTab) {
        $tabs.find('.tabs-nav a').removeClass('active');
        $el.addClass('active');
        $tabs.find('.tabs-panel').removeClass('active');
        $currentTab.addClass('active');
    }

    onInit() {
        var self = this;
        const settings = this.getElementSettings();

        super.onInit();

        var $selector = this.elements.$container,
            $panels = $selector.find('.tab-loaded');

        self.getProductCarousel($panels, settings);
        $selector.find('.tabs-nav').on('click', 'a', function (e) {
            e.preventDefault();
            self.getProductsAJAXHandler(jQuery(this), $selector);
        });
    }
}

class RazziProductstabGridWidgetHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                container: '.razzi-products-tabs-grid'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');

        return {
            $container: this.$element.find(selectors.container)
        };

    }

    /**
     * Get Product AJAX
     */
    getProductsAJAXHandler($el, $tabs) {
        var self = this,
            tab = $el.data('href'),
            $currentTab = $tabs.find('.tabs-' + tab),
            $tabContent = $tabs.find('.tabs-content');


        if ($currentTab.hasClass('tab-loaded')) {
            self.productTabs($tabs, $el, $currentTab);
            return;
        }

        $tabContent.addClass('loading');

        var data = {},
            elementSettings = $currentTab.data('settings'),
            ajax_url = razziData.ajax_url.toString().replace('%%endpoint%%', 'ra_elementor_load_products_grid');

        const settings = this.getElementSettings();

        jQuery.post(
            ajax_url,
            {
                settings: elementSettings
            },
            function (response) {
                if (!response) {
                    return;
                }

                var content = response.data;

                $currentTab.prepend(content);

                if ($currentTab.find('.products').is(':empty')) {
                    var $text = $currentTab.find('.page-number').data('text');
                    $currentTab.addClass('products-empty');
                    if ($text) {
                        $currentTab.find('.products').html('<li>' + $text + '</li>')
                    }
                }

                $currentTab.addClass('tab-loaded');

                self.productTabs($tabs, $el, $currentTab);

                $tabContent.removeClass('loading');

                jQuery(document.body).trigger('razzi_products_loaded', [jQuery(content), true]);
            }
        );
    };

    productTabs($tabs, $el, $currentTab) {
        $tabs.find('.tabs-nav a').removeClass('active');
        $el.addClass('active');
        $tabs.find('.tabs-panel').removeClass('active');
        $currentTab.addClass('active');
    }

    loadMoreProducts() {
        var ajax_url = razziData.ajax_url.toString().replace('%%endpoint%%', 'ra_elementor_load_products_grid');

        // Load Products
        this.elements.$container.on('click', 'a.ajax-load-products', function (e) {
            e.preventDefault();

            var $el = jQuery(this),
                $settings = $el.closest('.tabs-panel').data('settings');

            if ($el.hasClass('loading')) {
                return;
            }

            $el.addClass('loading');

            jQuery.post(
                ajax_url,
                {
                    page: $el.attr('data-page'),
                    settings: $settings
                },
                function (response) {
                    if (!response) {
                        return;
                    }

                    $el.removeClass('loading');

                    var $data = jQuery(response.data),
                        $products = $data.find('li.product'),
                        $container = $el.closest('.tabs-panel'),
                        $grid = $container.find('ul.products'),
                        $page_number = $data.find('.page-number').data('page');

                    // If has products
                    if ($products.length) {
                        $products.addClass('razziFadeInUp');

                        $grid.append($products);

                        if ($page_number == '0') {
                            $el.remove();
                        } else {
                            $el.attr('data-page', $page_number);
                        }
                    }

                    jQuery(document.body).trigger('razzi_products_loaded', [$products, true]);
                }
            );
        });
    };

    onInit() {
        var self = this;

        super.onInit();

        var $selector = this.elements.$container;

        $selector.find('.tabs-nav').on('click', 'a', function (e) {
            e.preventDefault();
            self.getProductsAJAXHandler(jQuery(this), $selector);
        });
        self.loadMoreProducts();
    }
}

class RazziDealsCarouselWidgetHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                container: '.razzi-deals-carousel'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');

        return {
            $container: this.$element.find(selectors.container)
        };

    }

    getProductSwiperInit() {
        const settings = this.getElementSettings();

        var $wrapper = this.elements.$container.find('.razzi-deals-carousel__inner');

        $wrapper.after('<div class="swiper-pagination"></div>');
        $wrapper.after('<span class="razzi-svg-icon rz-swiper-button-prev rz-swiper-button"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg></span>');
        $wrapper.after('<span class="razzi-svg-icon rz-swiper-button-next rz-swiper-button"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg></span>');

        var options = {
            loop: settings.infinite == 'yes' ? true : false,
            autoplay: settings.autoplay == 'yes' ? true : false,
            speed: settings.autoplay_speed ? settings.autoplay_speed : 500,
            delay: settings.delay ? settings.delay : 500,
            watchOverflow: true,
            navigation: {
                nextEl: this.elements.$container.find('.rz-swiper-button-next'),
                prevEl: this.elements.$container.find('.rz-swiper-button-prev'),
            },
            pagination: {
                el: this.elements.$container.find('.swiper-pagination'),
                clickable: true
            },
            effect: settings.effect,
            fadeEffect: {
                crossFade: true
            },
            parallax: true,
            on: {
                init: function () {
                    this.$el.css('opacity', 1);
                }
            },
        };

        new Swiper(this.elements.$container.find('.linked-deals-carousel'), options);
    }

    onInit() {
        var self = this;
        super.onInit();

        this.elements.$container.find('.razzi-countdown').rz_countdown();
        self.getProductSwiperInit();
    }
}

class RazziProductsGridWidgetHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                container: '.razzi-products-grid'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');

        return {
            $container: this.$element.find(selectors.container)
        };

    }

    loadProductsGrid() {
        var ajax_url = razziData.ajax_url.toString().replace('%%endpoint%%', 'ra_elementor_load_products_grid');
        const settings = this.getElementSettings();

        // Load Products
        this.elements.$container.on('click', 'a.ajax-load-products', function (e) {
            e.preventDefault();

            var $el = jQuery(this);

            if ($el.hasClass('loading')) {
                return;
            }

            $el.addClass('loading');

            jQuery.post(
                ajax_url,
                {
                    page: $el.attr('data-page'),
                    settings: settings
                },
                function (response) {
                    if (!response) {
                        return;
                    }

                    $el.removeClass('loading');

                    var $data = jQuery(response.data),
                        $products = $data.find('li.product'),
                        $container = $el.closest('.razzi-products-grid'),
                        $grid = $container.find('ul.products'),
                        $page_number = $data.find('.page-number').data('page');

                    // If has products
                    if ($products.length) {
                        $products.addClass('razziFadeInUp');

                        $grid.append($products);

                        if ($page_number == '0') {
                            $el.remove();
                        } else {
                            $el.attr('data-page', $page_number);
                        }
                    }

                    jQuery(document.body).trigger('razzi_products_loaded', [$products, true]);
                }
            );
        });
    };

    loadProductsPagination() {
        // Load Products
        this.elements.$container.on('click', '.woocommerce-pagination a', function (e) {
            e.preventDefault();

            var $el = jQuery(this),
                $nav = $el.closest('.woocommerce-pagination'),
                url = $el.attr('href'),
                $products = $el.closest('.razzi-products-grid').find('ul.products');

            $nav.addClass('loading');

            jQuery.get(url, function (response) {
                var $content = jQuery(response).find('.razzi-products-grid ul.products li.product'),
                    $navNew = jQuery(response).find('.razzi-products-grid .woocommerce-pagination');

                $products.html($content);
                $nav.replaceWith($navNew);

                jQuery(document.body).trigger('razzi_products_loaded', [$content, true]);

                $navNew.removeClass('loading');
            });
        });
    }

    loadProductsInfinite() {
        if (!this.elements.$container.find('.ajax-load-products').hasClass('ajax-infinite')) {
            return;
        }
        var $container = this.elements.$container;
        jQuery(window).on('scroll', function () {
            if ($container.find('.ajax-load-products').is(':in-viewport')) {
                $container.find('.ajax-load-products').trigger('click');
            }
        }).trigger('scroll');
    };

    onInit() {
        var self = this;
        super.onInit();

        self.loadProductsPagination();
        self.loadProductsGrid();
        self.loadProductsInfinite();
    }
}

class RazziProductLoopHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                container: 'ul.products'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');

        return {
            $container: this.$element.find(selectors.container)
        };

    }

    getProductLoopHoverSlider() {
        var $selector = this.elements.$container.find('.product-thumbnails--slider'),
            options = {
                loop: false,
                autoplay: false,
                speed: 800,
                watchOverflow: true,
                lazy: true,
                breakpoints: {}
            };

        $selector.find('.woocommerce-loop-product__link').addClass('swiper-slide');

        $selector.imagesLoaded(function () {
            setTimeout(function () {
                $selector.each(function () {
                    options.navigation = {
                        nextEl: jQuery(this).find('.rz-product-loop-swiper-next'),
                        prevEl: jQuery(this).find('.rz-product-loop-swiper-prev'),
                    }
                    new Swiper(jQuery(this), options);
                });
            }, 200);
        });
    }

    getProductLoopHover () {

        if (! this.elements.$container.hasClass('product-loop-layout-8')) {
            return;
        }

        this.elements.$container.on('mouseover', '.product-inner', function () {

            if (jQuery(this).hasClass('has-transform')) {
                return;
            }

            if (jQuery(this).closest('ul.products').hasClass('shortcode-element')) {
                return;
            }


            var $product_bottom = jQuery(this).find('.product-loop__buttons'),
                product_bottom_height = $product_bottom.outerHeight(),
                $product_summary = jQuery(this).find('.product-summary');

            jQuery(this).addClass('has-transform');
            $product_summary.css({
                '-webkit-transform': "translateY(-" + product_bottom_height + "px)",
                'transform': "translateY(-" + product_bottom_height + "px)"
            });

        });


    };


    onInit() {
        super.onInit();

        if (!this.$element.closest('body').hasClass('elementor-editor-active')) {
            return;
        }

        this.getProductLoopHoverSlider();
        this.getProductLoopHover();
    }
}

class RazziProductsRecentlyViewedWidgetHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                container: '.razzi-products-recently-viewed-carousel'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings( 'selectors' );

        return {
            $container: this.$element.find( selectors.container )
        };
    }

    hoverInforProduct () {
        var self = this,
            $selector = self.elements.$container,
            $product = $selector.find('li.product');

        $product.on('mousemove', function(e){
            var el = jQuery(this),
                left = e.pageX - el.offset().left + 10,
                top = e.pageY - el.offset().top + 10;

            el.find('.product-infor')
                .show()
                .css({ left: left, top: top });

        }).on('mouseout', function(){
            jQuery(this).find('.product-infor').hide();
        });
    }

    getSwiperOption(){
        const settings = this.getElementSettings();
        const selector = this.elements.$container;
        const breakpoints = elementorFrontend.config.breakpoints;
        var $products = selector.find( 'ul.products' );

        if( selector.find( '.no-products' ).length ) {
            return;
        }

        $products.addClass( 'swiper-wrapper' );
        $products.find( 'li.product' ).addClass( 'swiper-slide' );

        const   options = {
                    loop            : 'yes' === settings.infinite,
                    autoplay        : 'yes' === settings.autoplay,
                    speed           : settings.autoplay_speed,
                    delay           : settings.delay,
                    watchOverflow   : true,
                    navigation : {
                        nextEl: selector.find('.rz-swiper-button-next'),
                        prevEl: selector.find('.rz-swiper-button-prev'),
                    },
                    scrollbar       : {
                        el: selector.find('.swiper-scrollbar'),
                        hide: false,
                        draggable: true
                    },
                    pagination: {
                       el: selector.find('.swiper-pagination'),
                       type: 'bullets',
                       clickable: true
                    },
                    spaceBetween  : 30,
                    on              : {
                        init: function() {
                            selector.css( 'opacity', 1 );
                        }
                    },
                    breakpoints     : {}
                };

        options.breakpoints[breakpoints.xs] = { slidesPerView: settings.slidesToShow_mobile, slidesPerGroup: settings.slidesToScroll_mobile  };
        options.breakpoints[breakpoints.md] = { slidesPerView: settings.slidesToShow_tablet, slidesPerGroup: settings.slidesToScroll_tablet  };
        options.breakpoints[breakpoints.lg] = { slidesPerView: settings.slidesToShow, slidesPerGroup: settings.slidesToScroll };

        return options;
    }

    getSwiperInit() {
        new Swiper( this.elements.$container.find('.products-content'), this.getSwiperOption() );
    }

    /**
     * Get Product AJAX
     */
    getProductsHandler () {
        var self = this,
            $selector = self.elements.$container;

        if ($selector.hasClass('loaded')) {
            return;
        }

        var elementSettings = $selector.data('settings');

        if( elementSettings.load_ajax != 'yes' ){
            self.hoverInforProduct();
            self.getSwiperInit();

            return;
        }

        jQuery(window).on('scroll', function () {
            if (jQuery(document.body).find('.razzi-products-recently-viewed-carousel').is(':in-viewport')) {
                self.getProductsAJAXHandler();
            }
        }).trigger('scroll');
    };

    getProductsAJAXHandler () {
        var self = this,
            $selector = self.elements.$container;

        if ($selector.hasClass('loaded')) {
            return;
        }

        if ($selector.data('requestRunning')) {
            return;
        }

        $selector.data('requestRunning', true);

        var elementSettings = $selector.data('settings'),
            ajax_url = razziData.ajax_url.toString().replace('%%endpoint%%', 'ra_elementor_load_recently_viewed_products');

        jQuery.post(
            ajax_url,
            {
                settings: elementSettings
            },
            function (response) {
                if (!response) {
                    return;
                }

                var $content = jQuery(response.data).children('li.product');

                $selector.find('.products-content').html(response.data);
                if ($selector.find('.product-list').hasClass('no-products')) {
                    $selector.addClass('no-products');
                }
                self.hoverInforProduct();
                self.getSwiperInit();
                $selector.addClass('loaded');
                $selector.data('requestRunning', false);

                jQuery(document.body).trigger('razzi_products_loaded', [$content, true]);
            }
        );
    };

    onInit() {
        super.onInit();
        this.getProductsHandler();

    }
}

class RazziProductsRecentlyViewedWidgetGridHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                container: '.razzi-products-recently-viewed-grid'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings( 'selectors' );

        return {
            $container: this.$element.find( selectors.container )
        };
    }

    hoverInforProduct () {
        var self = this,
            $selector = self.elements.$container,
            $product = $selector.find('li.product');

        $product.on('mousemove', function(e){
            var el = jQuery(this),
                left = e.pageX - el.offset().left + 10,
                top = e.pageY - el.offset().top + 10;

            el.find('.product-infor')
                .show()
                .css({ left: left, top: top });

        }).on('mouseout', function(){
            jQuery(this).find('.product-infor').hide();
        });
    }

    resetCookie () {
        var $selector = this.elements.$container;

        $selector.on('click', '.reset-button' , function(e){
            e.preventDefault();
            document.cookie = "woocommerce_recently_viewed=null;expires=365;path=/";

            jQuery(this).closest('.razzi-products-recently-viewed-grid').removeClass('has-products').find('.products-content, .woocommerce-pagination').remove();

        });
    }

    onInit() {
        super.onInit();
        this.hoverInforProduct();
        this.resetCookie();
    }
}

jQuery(window).on('elementor/frontend/init', () => {


    elementorFrontend.hooks.addAction('frontend/element_ready/widget', ($element) => {
        elementorFrontend.elementsHandler.addHandler(RazziProductLoopHandler, {$element});
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/razzi-product-shortcode.default', ($element) => {
        elementorFrontend.elementsHandler.addHandler(RazziProductShortcodeWidgetHandler, {$element});
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/razzi-products-masonry.default', ($element) => {
        elementorFrontend.elementsHandler.addHandler(RazziProductsMasonryWidgetHandler, {$element});
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/razzi-products-deal.default', ($element) => {
        elementorFrontend.elementsHandler.addHandler(RazziProductsDealWidgetHandler, {$element});
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/razzi-products-showcase.default', ($element) => {
        elementorFrontend.elementsHandler.addHandler(RazziProductsShowcaseWidgetHandler, {$element});
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/razzi-product-carousel.default', ($element) => {
        elementorFrontend.elementsHandler.addHandler(RazziProductsCarouselWidgetHandler, {$element});
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/razzi-product-of-category.default', ($element) => {
        elementorFrontend.elementsHandler.addHandler(RazziProductOfCategoryWidgetHandler, {$element});
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/razzi-product-category-tabs.default', ($element) => {
        elementorFrontend.elementsHandler.addHandler(RazziProductsCategorytabsWidgetHandler, {$element});
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/razzi-product-tab.default', ($element) => {
        elementorFrontend.elementsHandler.addHandler(RazziProductstabWidgetHandler, {$element});
    });
    elementorFrontend.hooks.addAction('frontend/element_ready/razzi-product-tab-grid.default', ($element) => {
        elementorFrontend.elementsHandler.addHandler(RazziProductstabGridWidgetHandler, {$element});
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/razzi-deals-carousel.default', ($element) => {
        elementorFrontend.elementsHandler.addHandler(RazziDealsCarouselWidgetHandler, {$element});
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/razzi-products-grid.default', ($element) => {
        elementorFrontend.elementsHandler.addHandler(RazziProductsGridWidgetHandler, {$element});
    });

    elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-products-recently-viewed-carousel.default', ( $element ) => {
        elementorFrontend.elementsHandler.addHandler( RazziProductsRecentlyViewedWidgetHandler, { $element } );
    } );

    elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-products-recently-viewed-grid.default', ( $element ) => {
        elementorFrontend.elementsHandler.addHandler( RazziProductsRecentlyViewedWidgetGridHandler, { $element } );
    } );

});
