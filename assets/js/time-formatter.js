(function( $ ) {
	$( document ).ready( function() {
		$( '.wpua-time-holder' ).each( function() {
			var timeStamp = $( this ).attr( 'data-timestamp' );

			if ( timeStamp ) {
				$( this ).text( moment( timeStamp * 1000 ).format( timeFormatterSettings.timeFormat ) );
			}
		})
	} );
})( jQuery );
