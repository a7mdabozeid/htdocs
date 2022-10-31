( function ( $ ) {
	"use strict";

	const FormBuilder = {

		init: function () {
			$( '#fb-field-type' ).val( '' );
			$( '#fb-field-name' ).val( '' );
			$( '#fb-field-label' ).val( '' );
		},

		selectField: function () {
			$( '.form-builder-field-items a' ).click( function () {
				$( '.form-builder-field-items' ).find( 'a' ).removeClass( 'selected' );
				$( this ).addClass( 'selected' );
				let is_pro = $( this ).attr( 'data-pro' )
				if( is_pro === 'yes' ) {
					$('.fb-field-pro').show();
					$('.fb-field-wrapper').hide();
				} else {
					$('.fb-field-pro').hide();
					$('.fb-field-wrapper').show();
				}
				$( '#fb-field-type' ).val( $( this ).attr( 'data-type' ) );
				return false;
			} );
		},

		encodeHTML: function ( s ) {
			return s.replace( /&/g, '&amp;' ).replace( /</g, '&lt;' ).replace( /"/g, '&quot;' );
		},

		sanitizeKey: function ( s ) {
			if ( s === '' ) {
				return '_';
			}

			return s.replace( ' ', '_' ).replace( /[^a-zA-Z0-9._-]/g, '' ).toLowerCase();
		},

		addField: function () {
			let _this = this;

			$( 'button[name="fr-fb-add-field"]' ).click( function () {

				let type = $( '#fb-field-type' );
				let label = $( '#fb-field-label' );
				let name = $( '#fb-field-name' );
				let validation = true;
				if ( type.val() === '' ) {
					$( '.error_type' ).show();
					type.addClass( 'fr-fb-field-error ' );
					validation = false;
				} else {
					$( '.error_type' ).hide();
					type.removeClass( 'fr-fb-field-error ' );
				}

				if ( label.val() === '' ) {
					$( '.error_label' ).show();
					label.addClass( 'fr-fb-field-error ' );
					validation = false;
				} else {
					$( '.error_label' ).hide();
					label.removeClass( 'fr-fb-field-error ' );
				}

				if ( name.val() === '' ) {
					$( '.error_name' ).show();
					name.addClass( 'fr-fb-field-error ' );
					validation = false;
				} else {
					$( '.error_name' ).hide();
					name.removeClass( 'fr-fb-field-error ' );
				}

				if ( validation === true ) {
					let form_builder_data = {
						type: type.val(),
						label: _this.encodeHTML( label.val() ),
						name: _this.sanitizeKey( name.val() )
					};
					$.ajax( {
						type: 'POST',
						url: ajaxurl + '?action=fr_fb_insert_field',
						data: form_builder_data,
						success: function ( response ) {
							if ( response.success === true && response.data.field ) {
								$( '.form-builder-selected-fields-wrapper' ).append( response.data.field );
							} else {
								alert( response.data.error_details );
							}
						},
						async: true
					} );

					type.val( '' );
					label.val( '' );
					name.val( '' );

					$( '.form-builder-field-items' ).find( 'a' ).removeClass( 'selected' );
				}

			} );
		},

		appendField: function ( type, label, name ) {
			return this.appendFieldWrapper( type, label, name );
		},

		appendFieldWrapper: function ( type, label, name ) {
			let output;
			output = '<div class="fr-fb-field fb-field-wrapper visible-body">' +
				'<div class="fr-fb-header"><div class="sortable icon"></div><div class="label">' + label + '</div> <div class="type">' + type + '</div> <div class="remove fr-fb-remove-field icon"></div> <div class="collapse icon"></div></div>' +
				'<div class="fr-fb-body">' +
				'<p>' +
				'<label>' + fr_fb_i18n.label + '</label>' +
				'<input type="text" class="fr-fb-field-label regular-text" value="' + label + '" name="' + fr_fb_i18n.input_prefix + '[' + name + '][label]" />' +
				'</p>' +
				'<p>' +
				'<label>' + fr_fb_i18n.name + '</label><input type="text" disabled="disabled" value="' + name + '" class="regular-text" name="' + fr_fb_i18n.input_prefix + '[' + name + '][label]" />' +
				'<input type="hidden" value="' + type + '" name="' + fr_fb_i18n.input_prefix + '[' + name + '][type]" />' +
				'</p>' +
				'<p>' +
				'<label><input checked="checked" type="checkbox" value="1" name="' + fr_fb_i18n.input_prefix + '[' + name + '][enable]" /> ' + fr_fb_i18n.enable + '</label>' +
				'</p>' +
				'<p>' +
				'<label><input type="checkbox" value="1" name="' + fr_fb_i18n.input_prefix + '[' + name + '][required]" /> ' + fr_fb_i18n.required + '</label>' +
				'</p>' +
				'';

			let option_add_remove = '' +
				'<a class="add_row" href="#" data-name="' + name + '"><span class="dashicons dashicons-insert"></span></a>' +
				'<a class="remove_row" href="#"><span class="dashicons dashicons-remove"></span></a>';

			if ( type === 'select' || type === 'radio' || type === 'checkbox' ) {
				output += '<div class="tab-wrapper tab_options">';
				output += '<div><label>' + fr_fb_i18n.options + '</label></div>';
				output += '<div class="option-wrapper">';
				output += '<p class="option-field">';
				output += '<label class="option-label">' + fr_fb_i18n.value + ' ';
				output += '<input class="option-value" type="text" name="' + fr_fb_i18n.input_prefix + '[' + name + '][options][]" value="" size="16"/> ' + option_add_remove + '</label>';
				output += '</p>';
				output += '</div>';
				output += '</div>';
			}

			output += '</div>';
			output += '</div>';

			return output;
		},

		formBuilderMenuTab: function () {
			jQuery( '#form_builder_selected_fields' ).on( 'click', '.fr-fb-body-menu span', function () {
				$( this ).closest( '.fr-fb-body-menu' ).find( 'span.active' ).removeClass( 'active' );
				$( this ).addClass( 'active' );
				let tab_key = $( this ).attr( 'data-tab' );
				$( this ).closest( '.fr-fb-body' ).find( '.fr-fb-body-tab' ).hide();
				$( this ).closest( '.fr-fb-body' ).find( '.' + tab_key + '-tab' ).show();
			} );
		},

		addFieldOption: function () {
			jQuery( '#form_builder_selected_fields' ).on( 'click', '.add_row', function () {
				var name = $( this ).attr( 'data-name' );
				let option_add_remove = '<a class="add_row" href="#"><span class="dashicons dashicons-insert"></span></span></a><a class="remove_row" href="#"><span class="dashicons dashicons-remove"></span></a>';
				jQuery( this ).closest( '.tab-wrapper' ).find( '.option-wrapper' ).append(
					'<p class="option-field"><label class="option-label">' + fr_fb_i18n.value + ' <input class="option-value" type="text" name="' + fr_fb_i18n.input_prefix + '[' + name + '][options][]" value="" size="16" /> ' + option_add_remove + '</label></p>'
				);
				return false;
			} );
		},

		removeFieldOption: function () {
			jQuery( '#form_builder_selected_fields' ).on( 'click', '.remove_row', function () {
				if ( confirm( fr_fb_i18n.remove_confirm ) ) {
					// Save it!
					jQuery( this ).parent().remove();
				} else {
					return false;
				}
				return false;
			} );
		},

		updateHeaderName: function () {
			jQuery( '.form-builder-selected-fields-wrapper' ).on( 'keydown keyup', '.fr-fb-field-label', function () {
				let label = $( this ).closest( '.fr-fb-field' ).find( 'div.label' );
				label.html( $( this ).val() );
			} );
		},

		createFieldNameIsEmpty: function () {
			let _this = this;
			jQuery( '.form-builder-field-selector-wrapper' ).on( 'keydown keyup', '#fb-field-label', function () {
				let field_label = $( '#fb-field-label' );
				let field_name = $( '#fb-field-name' );
				field_name.val( _this.sanitizeKey( field_label.val() ) );
			} );
		},

		sortable: function () {
			jQuery( '.form-builder-selected-fields-wrapper' ).sortable( {
				items: '.fr-fb-field',
				handle: '.sortable'
			} );

			jQuery( '.option-wrapper' ).sortable( {
				items: '.option-field',
				handle: '.option-label'
			} );
		},

		slideToggle: function () {

			jQuery( document ).on( 'click', 'div.collapse,div.label', function ( e ) {
				e.preventDefault();
				var body = jQuery( this ).closest( '.fr-fb-field' ).find( '.fr-fb-body' );
				body.slideToggle( function () {
					jQuery( this ).closest( '.fr-fb-field' ).toggleClass( 'visible-body', body.is( ':visible' ) );
				} );
			} );
		},

		removeField: function () {
			jQuery( '#form_builder_selected_fields' ).on( 'click', '.fr-fb-remove-field', function () {
				if ( confirm( fr_fb_i18n.remove_confirm ) ) {
					// Save it!
					let element = jQuery( this ).parent().closest( '.fr-fb-field' );
					element.fadeOut();
					element.remove();
				} else {
					return false;
				}
				return false;
			} );
		},

		removeSubmitButton: function () {
			let form = $( '.form-builder-table' );
			if ( form.length ) {
				let button_wrapper = $( 'p.submit' );
				$( '#field-row-button' ).html( '<p class="submit">' + button_wrapper.html() + '</p>' );
				button_wrapper.remove();
			}
		},
		autoHideRefundButton: function () {
			let auto_hide_field = jQuery( ".auto-hide-checkbox" );
			auto_hide_field.change( function () {
				if ( jQuery( this ).prop( 'checked' ) ) {
					jQuery( '#auto-hide-row' ).show();
				} else {
					jQuery( '#auto-hide-row' ).hide();
				}
			} );
			auto_hide_field.trigger( 'change' );
		},

		getIndex: function () {
			let index, index_arr = [];
			let tr = $( '.flexible-refund-conditions tbody tr' );
			if ( tr.length === 0 ) {
				return 0;
			}

			tr.each( function ( i, e ) {
				index = parseInt( $( this ).attr( 'data-index' ) );
				index_arr[ i ] = index;
			} )
			index_arr.sort();
			return parseInt( index_arr[ index_arr.length - 1 ] ) + 1;
		},

		conditionTable: function () {
			let _this = this;
			$( '.flexible-refund-conditions' ).on( 'change', '.condition-type', function () {
				let wrapper = $( this ).closest( 'tr' );
				if ( wrapper.length ) {
					let tr_index = parseInt( wrapper.attr( 'data-index' ) );
					wrapper.find( '.condition-type-select-wrapper' ).html( $( '#' + this.value + '_select' ).html().replace( /__index__/gi, tr_index ) );
					$( document.body ).trigger( 'wc-enhanced-select-init' );
				}
			} );


			let condition_table = jQuery( '.flexible-refund-conditions' );
			if ( condition_table.length ) {
				condition_table.on( 'click', '.add_row', function () {

					let row_index = _this.getIndex();
					if ( isNaN( row_index ) ) {
						console.log( row_index );
						console.log( 'is nan' );
						return false;
					}

					let html = $( '#condition_row' ).html().replace( /__index__/gi, row_index );
					condition_table.find( 'tbody' ).append( html );
					$( condition_table ).trigger( 'wc-enhanced-select-init' );
					return false;
				} );

				condition_table.on( 'click', '.remove_row', function () {
					if ( confirm( fr_fb_i18n.remove_condition_confirm ) ) {
						jQuery( this ).closest( 'tr' ).remove();
					} else {
						return false;
					}
					return false;
				} );
			}

			let refund_order_button_select = $( '#flexible_refunds_order_button' );
			refund_order_button_select.change( function () {
				let condition_wrapper = $( '.flexible-refund-conditions' ).closest( 'tr' );
				if ( this.value === 'conditions' ) {
					condition_wrapper.show()
				} else {
					condition_wrapper.hide();
				}
				$( document.body ).trigger( 'wc-enhanced-select-init' );
			} )
			refund_order_button_select.trigger( 'change' );
		},
	}

	FormBuilder.init();
	FormBuilder.selectField();
	FormBuilder.addField();
	FormBuilder.formBuilderMenuTab();
	FormBuilder.updateHeaderName();
	FormBuilder.createFieldNameIsEmpty();
	FormBuilder.sortable();
	FormBuilder.slideToggle();
	FormBuilder.addFieldOption();
	FormBuilder.removeFieldOption();
	FormBuilder.removeField();
	FormBuilder.removeSubmitButton();
	FormBuilder.autoHideRefundButton();
	FormBuilder.conditionTable();


} )( jQuery );
