var taMegaMenu;

(function ($, _) {
    'use strict';

    var api,
        wp = window.wp;

    api = taMegaMenu = {
        init: function () {
            api.$body = $('body');
            api.$modal = $('#tamm-settings');
            api.itemData = {};
            api.templates = {
                menus: wp.template('tamm-menus'),
                title: wp.template('tamm-title'),
                mega: wp.template('tamm-mega'),
                background: wp.template('tamm-background'),
                icon: wp.template('tamm-icon'),
                content: wp.template('tamm-content'),
                general: wp.template('tamm-general'),
                general_2: wp.template('tamm-general_2')
            };

            api.frame = wp.media({
                library: {
                    type: 'image'
                }
            });

            this.initActions();
        },

        initActions: function () {
            api.$body
                .on('click', '.opensettings', this.openModal)
                .on('click', '.tamm-modal-backdrop, .tamm-modal-close, .tamm-button-cancel', this.closeModal);

            api.$modal
                .on('click', '.tamm-menu a', this.switchPanel)
                .on('click', '.tamm-column-handle', this.resizeMegaColumn)
                .on('click', '.tamm-button-save', this.saveChanges)
                .on('change', '.rz-tamm-panel-mega-width-field select', this.toggleField)
                .on('change', '.rz-tamm-panel-icon_type select', this.toggleField);
        },

        openModal: function () {
            api.getItemData(this);

            api.$modal.show();
            api.$body.addClass('modal-open');
            api.render();

            return false;
        },

        closeModal: function () {
            api.$modal.hide().find('.tamm-content').html('');
            api.$body.removeClass('modal-open');
            return false;
        },

        switchPanel: function (e) {
            e.preventDefault();

            var $el = $(this),
                panel = $el.data('panel');

            $el.addClass('active').siblings('.active').removeClass('active');
            api.openSettings(panel);
        },

        render: function () {
            // Render menu
            api.$modal.find('.tamm-frame-menu .tamm-menu').html(api.templates.menus(api.itemData));

            var $activeMenu = api.$modal.find('.tamm-menu a.active');

            // Render content
            this.openSettings($activeMenu.data('panel'));
        },

        openSettings: function (panel) {
            var $content = api.$modal.find('.tamm-frame-content .tamm-content'),
                $panel = $content.children('#tamm-panel-' + panel);

            if ($panel.length) {
                $panel.addClass('active').siblings().removeClass('active');
            } else {
                $content.append(api.templates[panel](api.itemData));
                $content.children('#tamm-panel-' + panel).addClass('active').siblings().removeClass('active');

                if ('mega' === panel) {
                    api.initMegaColumns();
                }
                if ('background' === panel) {
                    api.initBackgroundFields();
                }
                if ('icon' === panel) {
                    api.initIconFields();
                    api.initBackgroundFields();
                }
            }

            // Render title
            var title = api.$modal.find('.tamm-frame-menu .tamm-menu a[data-panel=' + panel + ']').data('title');
            api.$modal.find('.tamm-frame-title').html(api.templates.title({title: title}));
        },

        toggleField: function () {
            var field = $(this).closest('.setting-field');

            field.siblings().hide();
            field.siblings('.setting-field-' + $(this).val() + '').show();

        },


        resizeMegaColumn: function (e) {
            e.preventDefault();

            var steps = ['8.33%', '16.66%', '25.00%', '33.33%', '41.66%', '50.00%', '58.33%', '66.66%', '75.00%', '83.33%', '91.66%', '100.00%'],
                $el = $(this),
                $column = $el.closest('.tamm-submenu-column'),
                width = $column.data('width'),
                current = _.indexOf(steps, width),
                next;

            if (-1 === current) {
                return;
            }

            if ($el.hasClass('tamm-resizable-w')) {
                next = current == steps.length ? current : current + 1;
            } else {
                next = current == 0 ? current : current - 1;
            }

            $column[0].style.width = steps[next];
            $column.data('width', steps[next]);
            $column.find('.menu-item-depth-0 .menu-item-width').val(steps[next]);
        },

        initMegaColumns: function () {
            var $columns = api.$modal.find('#tamm-panel-mega .tamm-submenu-column'),
                defaultWidth = '25.00%';

            if (!$columns.length) {
                return;
            }

            // Support maximum 4 columns
            if ($columns.length < 4) {
                defaultWidth = String((100 / $columns.length).toFixed(2)) + '%';
            }

            _.each($columns, function (column) {
                var width = column.dataset.width;

                width = width || defaultWidth;

                column.style.width = width;
                column.dataset.width = width;
                $(column).find('.menu-item-depth-0 .menu-item-width').val(width);
            });
        },

        initBackgroundFields: function () {
            api.$modal.find('.background-color-picker').wpColorPicker();

            // Background image
            api.$modal.on('click', '.background-image .upload-button', function (e) {
                e.preventDefault();

                var $el = $(this);

                // Remove all attached 'select' event
                api.frame.off('select');

                // Update inputs when select image
                api.frame.on('select', function () {
                    // Update input value for single image selection
                    var url = api.frame.state().get('selection').first().toJSON().url;

                    $el.siblings('.background-image-preview').html('<img src="' + url + '">');
                    $el.siblings('input').val(url);
                    $el.siblings('.remove-button').removeClass('hidden');
                });

                api.frame.open();
            }).on('click', '.background-image .remove-button', function (e) {
                e.preventDefault();

                var $el = $(this);

                $el.siblings('.background-image-preview').html('');
                $el.siblings('input').val('');
                $el.addClass('hidden');
            });

            // Background position
            api.$modal.on('change', '.background-position select', function () {
                var $el = $(this);

                if ('custom' == $el.val()) {
                    $el.next('input').removeClass('hidden');
                } else {
                    $el.next('input').addClass('hidden');
                }
            });
        },

        initIconFields: function () {
            var $input = api.$modal.find('#tamm-icon-input'),
                $preview = api.$modal.find('#tamm-selected-icon');

            api.$modal.find('.tamm-icon-color-picker').wpColorPicker();

            $preview.on('click', '.tamm-selected-icon__inner', function () {
                $(this).children().remove();
                $input.val('');
            });


        },

        getItemData: function (menuItem) {
            var $menuItem = $(menuItem).closest('li.menu-item'),
                $menuData = $menuItem.find('.tamm-data'),
                $menuDataIconSVG = $menuItem.find('.tamm-data-icons'),
                children = $menuItem.childMenuItems();

            api.itemData = {
                depth: $menuItem.menuItemDepth(),
                megaData: {
                    mega: $menuData.data('mega'),
                    mega_width: $menuData.data('mega_width'),
                    mega_align: $menuData.data('mega_align'),
                    width: $menuData.data('width'),
                    background: $menuData.data('background'),
                    icon_type: $menuData.data('icon_type'),
                    icon_image: $menuData.data('icon_image'),
                    icon_svg: $menuDataIconSVG.html(),
                    icon_color: $menuData.data('icon_color'),
                    hideText: $menuData.data('hide-text'),
                    isLabel: $menuData.data('is-label'),
                    content: $menuData.html()
                },
                data: $menuItem.getItemData(),
                children: [],
                originalElement: $menuItem.get(0)
            };

            if (!_.isEmpty(children)) {
                _.each(children, function (item) {
                    var $item = $(item),
                        $itemData = $item.find('.tamm-data'),
                        $itemDataIconSVG = $menuItem.find('.tamm-data-icons'),
                        depth = $item.menuItemDepth();

                    api.itemData.children.push({
                        depth: depth,
                        subDepth: depth - api.itemData.depth - 1,
                        data: $item.getItemData(),
                        megaData: {
                            mega: $itemData.data('mega'),
                            mega_width: $itemData.data('mega_width'),
                            mega_align: $itemData.data('mega_align'),
                            width: $itemData.data('width'),
                            background: $itemData.data('background'),
                            icon: $itemData.data('icon'),
                            icon_type: $itemData.data('icon_type'),
                            icon_image: $itemData.data('icon_image'),
                            icon_svg: $itemDataIconSVG.html(),
                            icon_color: $itemData.data('icon_color'),
                            hideText: $itemData.data('hide-text'),
                            isLabel: $itemData.data('is-label'),
                            content: $itemData.html()
                        },
                        originalElement: item
                    });
                });
            }

        },

        setItemData: function (item, data, depth) {
            if (!_.has(data, 'mega')) {
                data.mega = false;
            }

            if (depth == 0) {
                if (!_.has(data, 'hideText')) {
                    data.hideText = false;
                }

                if (!_.has(data, 'isLabel')) {
                    data.isLabel = false;
                }
            }

            var $dataHolder = $(item).find('.tamm-data');

            if (_.has(data, 'content')) {
                $dataHolder.html(data.content);
                delete data.content;
            }

            $dataHolder.data(data);

            if (_.has(data, 'icon_svg')) {
                $dataHolder = $(item).find('.tamm-data-icons');
                $dataHolder.html(data.icon_svg);
                delete data.icon_svg;
            }

        },

        getFieldName: function (name, id) {
            name = name.split('.');
            name = '[' + name.join('][') + ']';

            return 'menu-item[' + id + ']' + name;
        },

        saveChanges: function () {

            if (api.$modal.find('.tamm-content .rz-tamm-panel-icon_type select').val() !== 'image') {
                api.$modal.find('.tamm-content .rz-tamm-panel-icon_image').val('');
            }

            if (api.$modal.find('.tamm-content .rz-tamm-panel-icon_type select').val() !== 'svg') {
                api.$modal.find('.tamm-content .rz-tamm-panel-icon_svg').val('');
            }

            var data = api.$modal.find('.tamm-content :input, .tamm-content .mega-data-icon_svg').serialize(),
                $spinner = api.$modal.find('.tamm-toolbar .spinner');


            $spinner.addClass('is-active');

            $.post(ajaxurl, {
                action: 'tamm_save_menu_item_data',
                data: data
            }, function (res) {
                if (!res.success) {
                    return;
                }


                var data = res.data['menu-item'];

                // Update parent menu item
                if (_.has(data, api.itemData.data['menu-item-db-id'])) {
                    api.setItemData(api.itemData.originalElement, data[api.itemData.data['menu-item-db-id']], 0);
                }

                _.each(api.itemData.children, function (menuItem) {
                    if (!_.has(data, menuItem.data['menu-item-db-id'])) {
                        return;
                    }

                    api.setItemData(menuItem.originalElement, data[menuItem.data['menu-item-db-id']], 1);
                });

                $spinner.removeClass('is-active');
                api.closeModal();
            });
        }
    };

    $(function () {
        taMegaMenu.init();
    });
})(jQuery, _);