( function ( $ ) {
	"use strict";

	const FR_Ajax = {

		issueSpinner: $( '.fr-refund-order-meta-box-actions .spinner' ),
		refund_customer_field: $( '#fr_refund_request_note' ),
		refund_status_field: $( '#fr_refund_request_status' ),
		refund_form_qty_fields: $( '.qty-input' ),

		sendRequest: function () {
			var _this = this;

			$( '.fr-refund-button' ).click( function () {

				let is_valid = true;
				let note = _this.refund_customer_field.val();
				let status = _this.refund_status_field.val();
				let refund_form_qty_fields = _this.refund_form_qty_fields.serialize();
				let order_ID = $( '#post_ID' ).val();

				if ( status.length < 5 ) {
					alert( 'Select status!' );
					is_valid = false;
				}
				if ( is_valid ) {
					_this.issueSpinner.css( 'visibility', 'visible' );
					let refund_request_data = {
						note: note,
						status: status,
						order_ID: order_ID,
						form: refund_form_qty_fields
					};
					$.ajax( {
						type: 'POST',
						url: ajaxurl + '?action=fr_refund_request',
						data: refund_request_data,
						success: function ( response ) {
							if ( response.success === true ) {
								_this.issueSpinner.css( 'visibility', 'hidden' );
								_this.refund_customer_field.val( '' );
								_this.refund_status_field.val( '' );
								location.replace( fr_meta_box.redirect_url );
							} else {
								alert( response.data.error_details );
								_this.issueSpinner.css( 'visibility', 'hidden' );
							}
						},
						async: true
					} );
				}

				return false;
			} );
		},

		formatMoney: function ( number, decPlaces, decSep, thouSep ) {
			decPlaces = isNaN( decPlaces = Math.abs( decPlaces ) ) ? 2 : decPlaces,
				decSep = typeof decSep === "undefined" ? "." : decSep;
			thouSep = typeof thouSep === "undefined" ? "," : thouSep;
			var sign = number < 0 ? "-" : "";
			var i = String( parseInt( number = Math.abs( Number( number ) || 0 ).toFixed( decPlaces ) ) );
			var j = ( j = i.length ) > 3 ? j % 3 : 0;

			return sign +
				( j ? i.substr( 0, j ) + thouSep : "" ) +
				i.substr( j ).replace( /(\decSep{3})(?=\decSep)/g, "$1" + thouSep ) +
				( decPlaces ? decSep + Math.abs( number - i ).toFixed( decPlaces ).slice( 2 ) : "" );
		},

		number_format: function ( value ) {
			return this.formatMoney( value.toString(), 2, fr_meta_box.decimal_point, fr_meta_box.thousand_point );
		},
		calculateRefundTotals: function () {
			let _this = this;
			$( '.fr-refund-table' ).on( 'change', '.item-qty input', function () {
				let qty = $( this ).val();
				let value = parseFloat( $( this ).attr( 'data-item-price' ) ) * qty;
				if ( $( this ).attr( 'type' ) === 'checkbox' ) {
					if ( $( this ).prop( 'checked' ) ) {
						value = parseFloat( $( this ).attr( 'data-item-price' ) );
					} else {
						value = 0;
					}
				}
				$( this ).closest( 'tr' ).find( '.item-total-refund-qty' ).html( _this.number_format( value ) );
				$( this ).closest( 'tr' ).find( '.item-refund-total' ).html( _this.number_format( value ) );

				let total_amount = 0;
				let total_qty = 0;
				let total_amount_input = $( '.refund-total-calc' );
				let total_qty_wrapper = $( '.refund-total-qty' );

				let total_qty_input = $( '#refund-total-qty-input' );
				$( '.fr-refund-table .item-qty input' ).each( function () {
					let qty = $( this ).val();
					let total_value = parseFloat( $( this ).attr( 'data-item-price' ) ) * qty;

					if ( $( this ).attr( 'type' ) === 'checkbox' ) {
						if ( $( this ).prop( 'checked' ) ) {
							total_value = parseFloat( $( this ).attr( 'data-item-price' ) );
							qty = 1;
						} else {
							total_value = 0;
							qty = 0;
						}
					}

					total_amount += parseFloat( total_value );
					total_qty += parseInt( qty );

				} )

				let rounded_total_amount = Math.round( ( total_amount + Number.EPSILON ) * 100 ) / 100;

				total_amount_input.html( _this.number_format( rounded_total_amount ) );
				total_qty_wrapper.html( total_qty );
				total_qty_input.val( total_qty );
			} );

			$( '.fr-refund-table .item-qty input' ).trigger( 'change' );
		},
	}

	FR_Ajax.sendRequest();
	FR_Ajax.calculateRefundTotals();

} )( jQuery );
