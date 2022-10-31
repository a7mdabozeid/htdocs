(function ($) {
    'use strict';

    var ControlFMautocomplete = elementor.modules.controls.BaseData.extend({
        onReady: function () {

            this.rzAutocomplete(this);

            this.rzRemoveData(this);

            this.rzSortable(this);

            this.rzOnRender(this);
        },
        rzAutocomplete: function (self) {
            var $input_value = self.$el.find('.ra_autocomplete_value'),
                self_value = $input_value.val(),
                multiple = $input_value.data('multiple'),
                step = '',
                item_value = '';

            self.$el.find('.ra_autocomplete_param').autocomplete({
                minLength: 1,
                source: function (request, response) {
                    $.ajax({
                        url: ajaxurl,
                        dataType: 'json',
                        method: 'post',
                        data: {
                            action: 'ra_get_autocomplete_suggest',
                            term: request.term,
                            source: $input_value.data('source')
                        },
                        success: function (data) {
                            response(data.data);
                        }
                    })
                },
                response: function (event, ui) {
                    self.$el.find('.ra_autocomplete').removeClass('loading');
                },
                search: function (event, ui) {
                    self.$el.find('.ra_autocomplete').addClass('loading');
                },
                select: function (event, ui) {

                    item_value = ui.item.value;

                    if (item_value === 'nothing-found') {
                        return false;
                    }
                    self_value = $input_value.val();
                    if (self_value !== '') {
                        step = ',';
                    }

                    var template = '<li class="ra_autocomplete-label" data-value="' + item_value + '">' +
                        '<span class="ra_autocomplete-data">' + ui.item.label + '</span>' +
                        '<a href="#" class="ra_autocomplete-remove">×</a>' +
                        '</li>';

                    if (multiple) {
                        self.$el.find('.ra_autocomplete').append(template);
                        self_value = self_value + step + item_value;
                    } else {
                        if( self.$el.find('.ra_autocomplete .ra_autocomplete-label').length > 0 ) {
                            self.$el.find('.ra_autocomplete .ra_autocomplete-label').replaceWith(template);
                        } else {
                            self.$el.find('.ra_autocomplete').append(template);
                        }
                        self.$el.find('.ra_autocomplete .ra_autocomplete-label').replaceWith(template);
                        self_value = item_value;
                    }

                    self.$el.find('.ra_autocomplete_param').val('');
                    $input_value.val(self_value);
                    self.setValue(self_value);

                    return false;
                },
                open: function (event) {
                    $(event.target).data('uiAutocomplete').menu.activeMenu.addClass('elementor-autocomplete-menu fm-autocomplete-menu');
                }
            }).autocomplete('instance')._renderItem = function (ul, item) {
                return $('<li>')
                    .attr('data-value', item.value)
                    .append(item.label)
                    .appendTo(ul);
            };
            return self_value;
        },
        rzRemoveData: function (self) {
            var $input_value = self.$el.find( '.ra_autocomplete_value' );
			self.$el.find( '.ra_autocomplete' ).on( 'click', '.ra_autocomplete-remove', function ( e ) {
				e.preventDefault();
				var $this = $( this ),
					self_value = '';

				$this.closest( '.ra_autocomplete-label' ).remove();

				self.$el.find( '.ra_autocomplete' ).find( '.ra_autocomplete-label' ).each( function () {
					self_value = self_value + ',' + $( this ).data( 'value' );
				} );
				$input_value.val(self_value);
                self.setValue( self_value );

            } );
            
            
        },
        rzSortable: function (self) {
            var sortable = self.$el.find('.ra_autocomplete_value').data('sortable'),
                self_value = '';
            if (sortable) {
                self.$el.find('.ra_autocomplete').sortable({
                    items: 'li.ra_autocomplete-label',
                    update: function (event, ui) {

                        self_value = '';

                        self.$el.find('.ra_autocomplete').find('li.ra_autocomplete-label').each(function () {
                            self_value = self_value + ',' + $(this).data('value');
                        });

                        self.setValue(self_value);
                    }
                });
            }
        },
        rzOnRender: function (self) {
            var $input_value = self.$el.find('.ra_autocomplete_value'),
                self_value = $input_value.val();

            $.ajax({
                url: ajaxurl,
                dataType: 'json',
                method: 'post',
                data: {
                    action: 'ra_get_autocomplete_render',
                    term: self_value,
                    source: $input_value.data('source')
                },
                success: function (data) {
                    if (data) {
                        self.$el.find('.ra_autocomplete').append(data.data);
                        self.$el.find('.ra_autocomplete').find('li.ra_autocomplete-loading').remove();
                    }
                }
            });
        },
        onBeforeDestroy: function () {
            if (this.ui.input.data('autocomplete')) {
                this.ui.input.autocomplete('destroy');
            }

            this.$el.remove();
        }
    });
    elementor.addControlView('rzautocomplete', ControlFMautocomplete);

})
(jQuery);