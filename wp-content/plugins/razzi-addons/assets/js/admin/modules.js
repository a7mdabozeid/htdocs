jQuery(document).ready(function ($) {
	"use strict";

	// Ajax category search boxes
	$( ':input.razzi-tag-search' ).filter( ':not(.enhanced)' ).each( function() {
		if(typeof(wc_enhanced_select_params) === 'undefined') {
			return;
		}
		if(typeof $( this ).selectWoo === 'undefined') {
			return;
		}
		var select2_args = $.extend( {
			allowClear        : $( this ).data( 'allow_clear' ) ? true : false,
			placeholder       : $( this ).data( 'placeholder' ),
			minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : 3,
			escapeMarkup      : function( m ) {
				return m;
			},
			ajax: {
				url:         wc_enhanced_select_params.ajax_url,
				dataType:    'json',
				delay:       250,
				data:        function( params ) {
					return {
						term:     params.term,
						action:   'razzi_json_search_tags',
						security: razzi_wc_modules.search_tags_nonce
					};
				},
				processResults: function( data ) {
					var terms = [];
					if ( data ) {
						$.each( data, function( id, term ) {
							terms.push({
								id:   id,
								text: term
							});
						});
					}
					return {
						results: terms
					};
				},
				cache: true
			}
		}, '' );

		$( this ).selectWoo( select2_args ).addClass( 'enhanced' );
	});

});