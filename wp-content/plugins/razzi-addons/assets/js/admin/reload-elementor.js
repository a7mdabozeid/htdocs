jQuery(document).ready(function ($) {

    if (typeof elementor === undefined) {
        return;
    }

    elementor.settings.page.addChangeCallback('rz_hide_header_section', function (newValue) {
        $e.run('document/save/update', {}).then(function () {
            elementor.reloadPreview();
        });
    });

    elementor.settings.page.addChangeCallback('rz_header_layout', function (newValue) {
        $e.run('document/save/update', {}).then(function () {
            elementor.reloadPreview();
        });
    });

    elementor.settings.page.addChangeCallback('rz_header_background', function (newValue) {
        if (newValue === 'transparent') {
            $('#elementor-preview-iframe').contents().find('body').addClass('header-transparent');
        } else {
            $('#elementor-preview-iframe').contents().find('body').removeClass('header-transparent');
        }
    });

    elementor.settings.page.addChangeCallback('rz_header_text_color', function (newValue) {
        if (newValue === 'dark') {
            $('#elementor-preview-iframe').contents().find('body').addClass('header-transparent-text-dark');
            $('#elementor-preview-iframe').contents().find('body').removeClass('header-transparent-text-light');
        } else if (newValue === 'light') {
            $('#elementor-preview-iframe').contents().find('body').addClass('header-transparent-text-light');
            $('#elementor-preview-iframe').contents().find('body').removeClass('header-transparent-text-dark');
        } else {
            $('#elementor-preview-iframe').contents().find('body').removeClass('header-transparent-text-light');
            $('#elementor-preview-iframe').contents().find('body').removeClass('header-transparent-text-dark');
        }
    });

    elementor.settings.page.addChangeCallback('rz_hide_header_border', function (newValue) {
        if( newValue === '1' ) {
            $('#elementor-preview-iframe').contents().find('#site-header').removeClass('site-header__border');
        } else {
            $('#elementor-preview-iframe').contents().find('#site-header').addClass('site-header__border');
        }

    });

    elementor.settings.page.addChangeCallback('rz_header_primary_menu', function (newValue) {
        $e.run('document/save/update', {}).then(function () {
            elementor.reloadPreview();
        });
    });

    elementor.settings.page.addChangeCallback('rz_content_width', function (newValue) {
        $e.run('document/save/update', {}).then(function () {
            elementor.reloadPreview();
        });
    });

    elementor.settings.page.addChangeCallback('rz_content_top_spacing', function (newValue) {
        $('#elementor-preview-iframe').contents().find('#content').removeClass('no-top-spacing custom-top-spacing');
        if (newValue !== 'default') {
            $('#elementor-preview-iframe').contents().find('#content').addClass(newValue + '-top-spacing');
        }
    });

    elementor.settings.page.addChangeCallback('rz_content_bottom_spacing', function (newValue) {
        $('#elementor-preview-iframe').contents().find('#content').removeClass('no-bottom-spacing custom-bottom-spacing');
        if (newValue !== 'default') {
            $('#elementor-preview-iframe').contents().find('#content').addClass(newValue + '-bottom-spacing');
        }
    });

    elementor.settings.page.addChangeCallback('rz_hide_page_header', function (newValue) {
        var $page_header = $('#elementor-preview-iframe').contents().find('#page-header');
        if( $page_header.length ) {
            if( newValue === '1' ) {
                $page_header.addClass('hidden');
            } else {
                $page_header.removeClass('hidden');
            }
        } else {
            $e.run('document/save/update', {}).then(function () {
                elementor.reloadPreview();
            });
        }

    });

    elementor.settings.page.addChangeCallback('rz_hide_title', function (newValue) {
        var $page_title = $('#elementor-preview-iframe').contents().find('#page-header .page-header__title');
        if( $page_title.length ) {
            if( newValue === '1' ) {
                $page_title.addClass('hidden');
            } else {
                $page_title.removeClass('hidden');
            }
        } else {
            $e.run('document/save/update', {}).then(function () {
                elementor.reloadPreview();
            });
        }

    });

    elementor.settings.page.addChangeCallback('rz_hide_breadcrumb', function (newValue) {
        var $breadcrumb = $('#elementor-preview-iframe').contents().find('#page-header .site-breadcrumb');
        if( $breadcrumb.length ) {
            if( newValue === '1' ) {
                $breadcrumb.addClass('hidden');
            } else {
                $breadcrumb.removeClass('hidden');
            }
        } else {
            $e.run('document/save/update', {}).then(function () {
                elementor.reloadPreview();
            });
        }

    });

    elementor.settings.page.addChangeCallback('rz_page_header_spacing', function (newValue) {
        $('#elementor-preview-iframe').contents().find('.page-header__title').removeClass('custom-spacing');
        if (newValue !== 'default') {
            $('#elementor-preview-iframe').contents().find('.page-header__title').addClass(newValue + '-spacing');
        }
    });


    // Boxed layout
    $has_boxed = 0;
    elementor.settings.page.addChangeCallback('rz_disable_page_boxed', function (newValue) {
        var $body = $('#elementor-preview-iframe').contents().find('body');

        $has_boxed = $has_boxed === 0 ? $body.hasClass('razzi-boxed-layout') : $has_boxed;
        if($has_boxed){
            if (newValue === '1') {
                $body.removeClass('razzi-boxed-layout');
            } else {
                $body.addClass('razzi-boxed-layout');
            }
        };

    });

    // Footer
    $has_border = 0;
    elementor.settings.page.addChangeCallback('rz_footer_section_border_top', function (newValue) {
        var $footer = $('#elementor-preview-iframe').contents().find('#site-footer');

        $has_border = $has_border === 0 ? $footer.hasClass('has-divider') : $has_border;

        if( newValue == '1' ) {
            $footer.addClass('has-divider');
        } else if( newValue == 'default' ) {
            if( $has_border ) {
                $footer.addClass('has-divider');
            }
        }
         else {
            $footer.removeClass('has-divider');
        }

    });

    elementor.settings.page.addChangeCallback('rz_footer_section_custom_border_color', function (newValue) {
        var $footer = $('#elementor-preview-iframe').contents().find('#site-footer');

        if( newValue ) {
            $footer.css('border-color', newValue);
        } else {
            $footer.removeAttr('style');
        }

    });

    elementor.settings.page.addChangeCallback('rz_hide_footer_section', function (newValue) {
        var $footer = $('#elementor-preview-iframe').contents().find('#site-footer');
        if( $footer.find('.footer-extra').length || $footer.find('.footer-main').length || $footer.find('.footer-widgets').length || $footer.find('.footer-newsletter').length ) {
            if( newValue === '1' ) {
                $footer.addClass('hidden');
            } else {
                $footer.removeClass('hidden');
            }
        } else {
            $e.run('document/save/update', {}).then(function () {
                elementor.reloadPreview();
            });
        }

    });

    elementor.settings.page.addChangeCallback('rz_department_menu_display', function (newValue) {
        $('#elementor-preview-iframe').contents().find('.header-department').removeClass('show_menu_department');
        if (newValue == 'onpageload') {
            $('#elementor-preview-iframe').contents().find('.header-department').addClass('show_menu_department');
        }
    });

});