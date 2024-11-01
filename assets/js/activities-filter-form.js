(function( $ ) {
	let tailLater = tail.DateTime( "#wpua_activity_later_than" );

	tailLater.on( 'change', function() {
		$( '#wpua_activity_later_than_timestamp' ).val( this.select.getTime() / 1000 );
	} );

	tailLater.on( 'open', function() {
		if ( this.dt.querySelector( "#calendar-apply-later" ) ) {
			return;
		}

		var div = document.createElement( "DIV" );
			div.className = "calendar-apply";
			div.id = "calendar-apply-later";
			div.innerHTML = '<button class="button">' + wpuaFormFilterSettings.applyLabel + '</button>';

		this.dt.appendChild( div );

		$( '#calendar-apply-later' ).on( 'click', 'button', function() {
			tailLater.close();
		} );
	} );

	var tailSooner = tail.DateTime( "#wpua_activity_sooner_than" );

	tailSooner.on( 'change', function() {
		$( '#wpua_activity_sooner_than_timestamp' ).val( this.select.getTime() / 1000 );
	} );

	tailSooner.on( 'open', function() {
		if ( this.dt.querySelector( "#calendar-apply-sooner" ) ) {
			return;
		}

		var div = document.createElement( "DIV" );
			div.className = "calendar-apply";
			div.id = "calendar-apply-sooner";
			div.innerHTML = '<button class="button">' + wpuaFormFilterSettings.applyLabel + '</button>';

		this.dt.appendChild( div );

		$( '#calendar-apply-sooner' ).on( 'click', 'button', function() {
			tailSooner.close();
		} );
	} );
})( jQuery );
