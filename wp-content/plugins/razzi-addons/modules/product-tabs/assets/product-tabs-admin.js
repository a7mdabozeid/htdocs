jQuery(document).ready(function ($) {
	//	Remove fields in product tab type default
	var $tab_default = $('.wp-list-table .razzi_product_tab_type-default');
	$tab_default.find('.check-column input[type="checkbox"]').remove();

	$( '#the-list' ).find('.razzi_product_tab_type-default').each(function() {
		var $row_actions = $(this).find('.column-title .row-actions'), 
			$edit = $row_actions.find('button.editinline');

			$row_actions.children().remove();
			$row_actions.append($edit);
			$edit.show();
	});

	$tab_default.find('.column-title').on('click', '.row-title, .row-actions .edit > a, .row-actions .trash > a', function(e){
		e.preventDefault();
		$(this).closest('.column-title').find('.row-actions .editinline').trigger('click');
	});

	// Show disale checkbox
	$( '#the-list' ).on(
		'click',
		'.editinline',
		function() {
			var post_id = $( this ).closest( 'tr' ).attr( 'id' );
			post_id = post_id.replace( 'post-', '' );
			var $disable = $( '#product_tab_disable_' + post_id );
			if ( 'yes' === $disable.val() ) {
				$( 'input[name="_product_tab_disable"]', '.inline-edit-row' ).prop( 'checked', true );
			} else {
				$( 'input[name="_product_tab_disable"]', '.inline-edit-row' ).prop( 'checked', false );
			}
		}
	);

	// Change field product tab type
	var $tab_settings = $('#razzi-product-tabs-settings'),
		$tab_type = $tab_settings.find('.razzi-product-tab--input'),
		$tab_product = $tab_settings.find('.razzi-product-tabs--product'),
		$tab_categories = $tab_settings.find('.razzi-product-tabs--categories');
	$tab_type.on('change', function(){
		var type = $(this).filter(':checked').val();
		$tab_product.hide();
		$tab_categories.hide();
		if( type == 'product' ) {
			$tab_product.show();
		} else if( type == 'global' ) {
			$tab_categories.show();
		}

	});
	$tab_type.filter( ':checked' ).trigger( 'change' );

	// Sort product tabs
	$( 'table.widefat tbody th, table.widefat tbody td' ).css( 'cursor', 'move' );

	$( 'table.widefat tbody' ).sortable({
		items: 'tr:not(.inline-edit-row)',
		cursor: 'move',
		axis: 'y',
		containment: 'table.widefat',
		scrollSensitivity: 40,
		helper: function( event, ui ) {
			ui.each( function() {
				$( this ).width( $( this ).width() );
			});
			return ui;
		},
		start: function( event, ui ) {
			ui.item.css( 'background-color', '#ffffff' );
			ui.item.children( 'td, th' ).css( 'border-bottom-width', '0' );
			ui.item.css( 'outline', '1px solid #dfdfdf' );
		},
		stop: function( event, ui ) {
			ui.item.removeAttr( 'style' );
			ui.item.children( 'td,th' ).css( 'border-bottom-width', '1px' );
		},
		update: function( event, ui ) {
			$( 'table.widefat tbody th, table.widefat tbody td' ).css( 'cursor', 'default' );
			$( 'table.widefat tbody' ).sortable( 'disable' );

			var postid     = ui.item.attr( 'id' ).replace( 'post-', '' );
			var prevpostid = ui.item.prev().attr( 'id' ) ? ui.item.prev().attr( 'id' ).replace( 'post-', '' ) : 0;
			var nextpostid = ui.item.next().attr( 'id' ) ? ui.item.next().attr( 'id' ).replace( 'post-', '' ) : 0;

			// Show Spinner
			ui.item.find( '.check-column' ).append( '<img alt="processing" src="images/wpspin_light.gif" class="waiting" style="margin-left: 6px;" />' );

			// Go do the sorting stuff via ajax
			$.post(
				ajaxurl,
				{ action: 'razzi_product_tab_ordering', id: postid, previd: prevpostid, nextid: nextpostid },
				function( response ) {
					ui.item.find( '.check-column img' ).remove();
					$( 'table.widefat tbody th, table.widefat tbody td' ).css( 'cursor', 'move' );
					$( 'table.widefat tbody' ).sortable( 'enable' );
				}
			);

			// fix cell colors
			$( 'table.widefat tbody tr' ).each( function() {
				var i = $( 'table.widefat tbody tr' ).index( this );
				if ( i%2 === 0 ) {
					$( this ).addClass( 'alternate' );
				} else {
					$( this ).removeClass( 'alternate' );
				}
			});
		}
	});

});