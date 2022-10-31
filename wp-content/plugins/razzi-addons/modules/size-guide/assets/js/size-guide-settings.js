jQuery( document ).ready( function( $ ) {
	$( 'select#razzi_size_guide_button_position' ).on( 'change', function() {
		var $el = $( this );

		if ( 'bellow_attribute' === $el.val() ) {
			$el.closest( 'tr' ).next( 'tr' ).show();
		} else {
			$el.closest( 'tr' ).next( 'tr' ).hide();
		}
	} ).trigger( 'change' );

	$( 'select#razzi_size_guide_display' ).on( 'change', function() {
		var $el = $( this );

		if ( 'tab' === $el.val() ) {
			$el.closest( 'tr' ).nextAll( ':lt(2)' ).hide();
		} else {
			$el.closest( 'tr' ).nextAll( ':lt(2)' ).show();

			$( 'select#razzi_size_guide_button_position' ).trigger( 'change' );
		}
	} ).trigger( 'change' );
} );