/** global wp, woocommerce_admin_meta_boxes */
jQuery( function( $ ) {
	'use strict';

	var mediaFrame = null;

	var WCBoostVariationSwatchesMeta = {
		init: function() {
			$( '.wcboost-variation-swatches__field-color input' ).wpColorPicker();

			$( document.body )
				.on( 'click', '.wcboost-variation-swatches__field-image .button-add-image, .wcboost-variation-swatches__field-image img', this.uploadImage )
				.on( 'click', '.wcboost-variation-swatches__field-image .button-remove-image', this.removeImage );

			$( '#wcboost_variation_swatches_data' )
				.on( 'change', '.wcboost-variaton-swatches__type-field select', this.toggleSwatchesMetabox )
				.on( 'change', '.wcboost-variaton-swatches__size-field select', this.toggleCustomSizeFields );

			$( '#variable_product_options' ).on( 'reload', this.reloadSwatchesPanel );

			$( '#product_attributes' )
				.on( 'click', 'button.add_new_attribute_with_swatches', this.openNewAttributeDialog );

			$( '#wcboost-variation-swatches-new-term-dialog' )
				.on( 'input', 'input[name="attribute_name"]', this.validateDialogInputs )
				.on( 'click', 'button.media-modal-close, .media-modal-backdrop', this.closeDialog )
				.on( 'click', 'button.button-add', this.ajaxAddTerm );
		},

		uploadImage: function( event ) {
			event.preventDefault();

			var $button = $( this );

			// Open the media frame.
			if ( mediaFrame ) {
				mediaFrame.off( 'select' );
			} else {
				mediaFrame = wp.media( {
					title   : $button.attr( 'aria-label' ),
					button  : {
						text: $button.data( 'choose' )
					},
					multiple: false
				} );
			}

			// When an image is selected, run a callback.
			mediaFrame.on( 'select', function () {
				var attachment = mediaFrame.state().get( 'selection' ).first().toJSON();

				$button.closest( '.wcboost-variation-swatches__field-image' ).find( 'input[type="hidden"]' ).val( attachment.id );
				$button.siblings( '.button-remove-image' ).removeClass( 'hidden' );

				var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
				$button.closest( '.wcboost-variation-swatches__field-image' ).find( 'img' ).attr( 'src', attachment_image );
			} );

			// Finally, open the modal.
			mediaFrame.open();
		},

		removeImage: function( event ) {
			event.preventDefault();

			var $button =  $( this );

			$button.addClass( 'hidden' );
			$button.closest( '.wcboost-variation-swatches__field-image' ).find( 'input[type="hidden"]' ).val( '' );
			$button.closest( '.wcboost-variation-swatches__field-image' ).find( 'img' ).attr( 'src', function() {
				return this.dataset.placeholder;
			} );
		},

		toggleSwatchesMetabox: function() {
			if ( this.value && this.value !== 'select' && this.value !== 'button' ) {
				$( this ).closest( '.options_group' ).siblings( '.options_group--swatches' ).find( '.form-field__swatches-' + this.value ).show().siblings().hide();
			} else {
				$( this ).closest( '.options_group' ).siblings( '.options_group--swatches' ).children().hide();
			}
		},

		toggleCustomSizeFields: function() {
			if ( this.value === 'custom' ) {
				$( this ).closest( '.form-field' ).next( '.form-field--custom-size' ).show();
			} else {
				$( this ).closest( '.form-field' ).next( '.form-field--custom-size' ).hide();
			}
		},

		reloadSwatchesPanel: function() {
			var this_page = window.location.toString();
			this_page = this_page.replace( 'post-new.php?', 'post.php?post=' + woocommerce_admin_meta_boxes.post_id + '&action=edit&' );

			$( '#wcboost_variation_swatches_data' ).load( this_page + ' #wcboost_variation_swatches_data_inner', function() {
				setTimeout( function() {
					$( '.wcboost-variation-swatches__field-color input' ).wpColorPicker();
				} );
			} );
		},

		closeDialog: function( event ) {
			event.preventDefault();

			$( '#wcboost-variation-swatches-new-term-dialog' ).hide();
		},

		openNewAttributeDialog: function( event ) {
			event.preventDefault();

			var $button = $( this ),
				$dialog = $( '#wcboost-variation-swatches-new-term-dialog' ),
				data = {
					type: $button.data( 'type' ),
					taxonomy : $button.closest( '.woocommerce_attribute.wc-metabox' ).data( 'taxonomy' )
				};

			$( 'input[name="attribute_taxonomy"]', $dialog ).val( data.taxonomy );
			$( 'input[name="attribute_type"]', $dialog ).val( data.type );
			$( '.form-field__swatches', $dialog ).children().hide().filter( '.wcboost-variation-swatches__field-' + data.type ).show();

			$dialog.show();
		},

		validateDialogInputs: function() {
			if ( ! this.value ) {
				this.classList.add( 'error' );
			} else {
				this.classList.remove( 'error' );
			}
		},

		ajaxAddTerm: function( event ) {
			event.preventDefault();

			var $button = $( this ),
				$dialog = $( '#wcboost-variation-swatches-new-term-dialog' ),
				$spinner = $( '.spinner', $dialog ),
				$message = $( '.wcboost-variation-swatches-modal__message', $dialog ),
				data = $( ':input', $dialog ).serializeObject();

			console.log(data);

			if ( ! data.attribute_name ) {
				$( 'input[name="attribute_name"]', $dialog ).focus();
				return;
			}

			// Send ajax request.
			$spinner.addClass( 'is-active' );
			$message.hide();
			$button.prop( 'disabled', true );

			wp.ajax.send( 'wcboost_variation_swatches_add_term', {
				data   : data,
				error  : function ( res ) {
					$spinner.removeClass( 'is-active' );
					$message.addClass( 'error' ).text( res ).show();
					$button.prop( 'disabled', false );
				},
				success: function ( res ) {
					$spinner.removeClass( 'is-active' );
					$message.addClass( 'success' ).text( res.message ).show();
					$button.prop( 'disabled', false );

					// Reset inputs.
					$( 'input[name="attribute_name"]', $dialog ).val( '' ).removeClass( 'error' );
					$( 'input[name="label"]', $dialog ).val( '' );
					$( '.wp-picker-clear', $dialog ).trigger( 'click' );
					$( '.button-remove-image', $dialog ).trigger( 'click' );

					// Add new attributes to the select box.
					var $metabox = $( '.woocommerce_attribute.wc-metabox[data-taxonomy="' + data.attribute_taxonomy + '"]', '#product_attributes' );

					$( 'select.attribute_values', $metabox )
						.append( '<option value="' + res.term_id + '" selected="selected">' + data.attribute_name + '</option>' )
						.change();
				}
			} );
		}
	};

	WCBoostVariationSwatchesMeta.init();
} );