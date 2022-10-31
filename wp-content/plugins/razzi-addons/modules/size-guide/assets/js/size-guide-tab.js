jQuery( document ).ready( function( $ ) {
	$( '.razzi-size-guide-tabs' ).on( 'click', '.razzi-size-guide-tabs__nav li', function() {
        var $tab = $( this ),
            index = $tab.data( 'target' ),
            $panels = $tab.closest( '.razzi-size-guide-tabs' ).find( '.razzi-size-guide-tabs__panels' ),
            $panel = $panels.find( '.razzi-size-guide-tabs__panel[data-panel="' + index + '"]' );

        if ( $tab.hasClass( 'active' ) ) {
            return;
        }

        $tab.addClass( 'active' ).siblings( 'li.active' ).removeClass( 'active' );

        if ( $panel.length ) {
            $panel.addClass( 'active' ).siblings( '.razzi-size-guide-tabs__panel.active' ).removeClass( 'active' );
        }
    } );
} );