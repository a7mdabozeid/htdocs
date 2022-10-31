(function ($) {

    /**Premium Nav Menu */
    var PremiumNavMenuHandler = function ($scope, $) {

        // we don't need to wait for content dom load since the script is loaded in the footer.
        $scope.find('.premium-nav-widget-container').removeClass('elementor-invisible');

        var settings = $scope.find('.premium-nav-widget-container').data('settings');

        if (!settings) {
            return;
        }

        var $menuContainer = $scope.find('.premium-mobile-menu'),
            $menuToggler = $scope.find('.premium-hamburger-toggle'),
            $hamMenuCloser = $scope.find('.premium-mobile-menu-close'),
            $centeredItems = $scope.find('.premium-mega-content-centered'),
            stickyProps = {},
            refreshPos = false,
            stickyIndex = 'stickyPos' +  $scope.data('id'),
            stickyWidthIndex = 'stickyWidth' +  $scope.data('id');

        /**
         * Save current device to use it later to determine if the device changed on resize.
         */
        window.PaCurrStickyDevice = elementorFrontend.getCurrentDeviceMode();

        $centeredItems.each(function (index, item) {
            $(item).closest(".premium-nav-menu-item").addClass("premium-mega-item-static");
        });

        if ('slide' === settings.mobileLayout || 'slide' === settings.mainLayout) {
            $scope.addClass('premium-ver-hamburger-menu');
        }

        var isMobileMenu = isDesktopMenu = null;
        checkBreakPoint(settings);
        checkStickyEffect();

        // init widget events.

        $hamMenuCloser.on('click', function () {
            $scope.find('.premium-mobile-menu-outer-container, .premium-nav-slide-overlay').removeClass('premium-vertical-toggle-open');
        });

        $menuToggler.on('click', function () {
            if ('slide' === settings.mobileLayout || 'slide' === settings.mainLayout) {
                $scope.find('.premium-mobile-menu-outer-container, .premium-nav-slide-overlay').addClass('premium-vertical-toggle-open');
            } else {
                // $menuContainer.toggleClass('premium-active-menu');
                if ($($menuContainer).hasClass('premium-active-menu')) {
                    $scope.find('.premium-mobile-menu-container').slideUp('slow', function() {
                        $menuContainer.removeClass('premium-active-menu');
                        $scope.find('.premium-mobile-menu-container').show();
                    });
                } else {

                    $menuContainer.addClass('premium-active-menu');
                }
            }

            $menuToggler.toggleClass('premium-toggle-opened'); // show/hide close icon/text.
        });

        $menuContainer.find('.premium-nav-menu-item.menu-item-has-children a, .premium-mega-nav-item a').on('click', function (e) {

            if ($(this).find(".premium-dropdown-icon").length < 1)
                return;

            var $parent = $(this).parent(".premium-nav-menu-item");

            e.stopPropagation();
            e.preventDefault();

            //If it was opened, then close it.
            if ($parent.hasClass('premium-active-menu')) {
                $parent.toggleClass('premium-active-menu');

            } else {
                //Close any other opened items.
                $menuContainer.find('.premium-active-menu').toggleClass('premium-active-menu');
                //Then, open this item.
                $parent.toggleClass('premium-active-menu');
                // make sure the parent node is always open whenever the child node is opened.
                $($parent).parents('.premium-nav-menu-item.menu-item-has-children').toggleClass('premium-active-menu');
            }
        });

        $(document).on('click', '.premium-nav-slide-overlay', function () {
            $scope.find('.premium-mobile-menu-outer-container, .premium-nav-slide-overlay').removeClass('premium-vertical-toggle-open');
        });

        $(document).on('click.PaCloseMegaMenu', function(event) {
            var isTabsItem = $(event.target).closest('.premium-tabs-nav-list-item').length,
                isWidgetContainer = $(event.target).closest('.premium-nav-widget-container').length;

            if ( !isWidgetContainer && !isTabsItem) {
                if ($($menuContainer).hasClass('premium-active-menu')) {
                    $menuToggler.click();
                }
            }
        });

        $(window).on('resize', function () {

            if ( window.PaCurrStickyDevice !== elementorFrontend.getCurrentDeviceMode()) {
                refreshPos = true;
                window.PaCurrStickyDevice = elementorFrontend.getCurrentDeviceMode();
            }

            checkBreakPoint(settings);
            checkStickyEffect();
        });

        // vertical toggler.
        if ($scope.hasClass('premium-ver-toggle-yes') && $scope.hasClass('premium-ver-click')) {
            $scope.find('.premium-ver-toggler').on('click', function() {
                $scope.find('.premium-nav-widget-container').toggleClass('premium-ver-collapsed', 500);
            });
        }

        function checkBreakPoint(settings) {

            //Trigger small screen menu.
            if (settings.breakpoint >= $(window).width() && !isMobileMenu) {
                // remove the vertical toggler.
                $scope.find('.premium-ver-toggler').css('display','none');
                $scope.addClass('premium-hamburger-menu');
                $scope.find('.premium-active-menu').removeClass('premium-active-menu');
                stretchDropdown($scope.find('.premium-stretch-dropdown .premium-mobile-menu-container'));

                isMobileMenu = true;
                isDesktopMenu = false;

                //Trigger large screen menu.
            } else if (settings.breakpoint < $(window).width() && !isDesktopMenu) {

                // show the vertical toggler if enabled.
                if ($scope.hasClass('premium-ver-toggle-yes')) {
                    $scope.find('.premium-ver-toggler').css('display','flex');
                }

                $menuToggler.removeClass('premium-toggle-opened');
                $scope.find(".premium-mobile-menu-container .premium-active-menu").removeClass("premium-active-menu");
                $scope.removeClass('premium-hamburger-menu premium-ham-dropdown');
                $scope.find('.premium-vertical-toggle-open').removeClass('premium-vertical-toggle-open');
                $scope.find('.premium-nav-default').removeClass('premium-nav-default');

                isDesktopMenu = true;
                isMobileMenu = false;
            }

        }

        /**
         * Full Width Option.
         * Shows the mobile menu beneath the widget's parent(section).
         */
        function stretchDropdown($menu) {

            var $parentSec = $($scope).closest('.elementor-top-section'),
                width = $($parentSec).outerWidth(),
                widgetTop = $scope.offset().top,
                parentBottom = $($parentSec).offset().top + $($parentSec).outerHeight(),
                stretchTop = parentBottom - widgetTop,
                stretchLeft = $scope.offset().left - $($parentSec).offset().left;

            $($menu).css({
                width: width + 'px',
                left: '-' + stretchLeft + 'px',
                top: stretchTop + 'px',
            });
        }

        /**
         * Sticky Effect.
         */

        function checkStickyEffect() {

            var isSticky = $scope.hasClass('premium-nav-sticky-yes') &&
                // settings.stickyOptions &&
                $( '#' + settings.stickyOptions.targetId ).length &&
                ! settings.stickyOptions.disableOn.includes( elementorFrontend.getCurrentDeviceMode() );

            if ( isSticky ) {
                stickyProps = settings.stickyOptions;

                stickyProps.spacerClass = 'premium-sticky-spacer-' + $( '#' + stickyProps.targetId ).data('id');

                $( '#' + stickyProps.targetId ).addClass('premium-sticky-active');

                setStickyWidth( stickyProps );

                // Add spacer to save the sticky target space in the dom.
                if ( 0 === $('.' + stickyProps.spacerClass).length ) {
                    $('<div class="'+ stickyProps.spacerClass + '"></div>').insertBefore( '#' + stickyProps.targetId );
                }

                $(window).on('load', applyStickyEffect);
                $(window).on('scroll.PaStickyNav', applyStickyEffect);

            } else {
                $(window).off('scroll.PaStickyNav');

                $('<div class="'+ stickyProps.spacerClass + '"></div>').remove(); // remove spacer
                $( '#' + stickyProps.targetId ).removeClass('premium-sticky-parent premium-sticky-active premium-sticky-parent-'+ $scope.data('id')).css({ // unset style
                    top: 'unset',
                    width: 'inherit',
                    position: 'relative'
                });
            }
        }

        /**
         * we need to get the original width before setting
         * the position to fixed.
         */
        function setStickyWidth( stickyProps ) {
            // TODO: check if we can use the spacer's width directly instead.
            var currStickyWidth = stickyWidthIndex + elementorFrontend.getCurrentDeviceMode(),
                isSticky = $( '#' + stickyProps.targetId ).hasClass('premium-sticky-parent'); // ==> fixed position

            if (isSticky) {
                $( '#' + stickyProps.targetId ).css({
                    position: 'relative',
                    width: 'inherit'
                });
            }

            window[currStickyWidth] = $( '#' + stickyProps.targetId ).outerWidth() + 'px';

            if ( isSticky ) {

                $( '#' + stickyProps.targetId ).css({
                    position: 'fixed',
                    width: window[currStickyWidth]
                });
            }
        }

        function applyStickyEffect() {

            var $adminBarHeight = elementorFrontend.elements.$wpAdminBar.height() ? elementorFrontend.elements.$wpAdminBar.height() : 0,
                scrollTop = $(window).scrollTop() + $adminBarHeight,
                currStickyWidth = stickyWidthIndex + elementorFrontend.getCurrentDeviceMode();

            if ( ! window[ stickyIndex ] || refreshPos ) { // save the offset
                window[ stickyIndex ] = $( '.' + stickyProps.spacerClass ).offset().top;
                refreshPos = false;
            }

            if ( scrollTop >= window[ stickyIndex ] ) {

                $('.' + stickyProps.spacerClass).css('height', $( '#' + stickyProps.targetId ).outerHeight()  + 'px');
                $( '#' + stickyProps.targetId ).addClass('premium-sticky-parent premium-sticky-parent-'+ $scope.data('id')).css({
                    width: window[currStickyWidth],
                    top: $adminBarHeight,
                    position: 'fixed'
                });

            } else {
                $('.' + stickyProps.spacerClass).css('height', '0px');
                $( '#' + stickyProps.targetId ).removeClass('premium-sticky-parent premium-sticky-parent-'+ $scope.data('id')).css({
                    top: 'unset',
                    width: 'inherit',
                    position: 'relative'
                });
            }

            // sticky on scroll option.
            if (stickyProps.onScroll) {
                var $element = document.querySelector('#' + stickyProps.targetId + '.premium-sticky-parent');

                if ( $element ) {
                    $('#' + stickyProps.targetId + '.premium-sticky-parent').addClass('premium-sticky-scroll-yes');
                    var headroom  = new Headroom($element,
                        {
                            tolerance: 5,
                            classes: {
                                initial: "animated",
                                pinned: "slideDown",
                                unpinned: "slideUp",
                                offset: {
                                    up: $( '#' + stickyProps.targetId ).outerHeight() + 150, // first time only.
                                },
                            }
                        });

                    headroom.init();
                }
            } else {
                $('#' + stickyProps.targetId + '.premium-sticky-parent').removeClass('premium-sticky-scroll-yes');
            }
        }
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/premium-nav-menu.default', PremiumNavMenuHandler);
    });

})(jQuery);