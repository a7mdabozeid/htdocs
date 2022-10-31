wp.customize.controlConstructor['wcboost-variation-swatches-size'] = wp.customize.Control.extend( {
	ready: function() {
		var control = this;

		control.container.on( 'change', 'input', function() {
			var size = {};

			control.container.find( 'input' ).each( function() {
				size[ this.dataset.name ] = parseInt( this.value );
			} );

			control.setting.set( size );
		} );
	}
} );