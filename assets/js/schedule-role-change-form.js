(function( $ ) {
	var tailStart = tail.DateTime( "#wpua_tail_start_datetime", {
		dateStart: wpuaSettings.currentTime * 1000
	} );

	tailStart.on( 'change', function() {
		$( '#wpua_start_datetime' ).val( this.select.getTime() / 1000 );
	} );

	tailStart.on( 'open', function() {
		if ( this.dt.querySelector( "#calendar-apply-start" ) ) {
			return;
		}

		var div = document.createElement( "DIV" );
			div.className = "calendar-apply";
			div.id = "calendar-apply-start";
			div.innerHTML = '<button class="button">' + wpuaSettings.applyLabel + '</button>';

		this.dt.appendChild( div );

		$( '#calendar-apply-start' ).on( 'click', 'button', function() {
			tailStart.close();
		} );
	} );

	$( '#wpua_tail_start_datetime_dismiss' ).on( 'click', function() {
		tailStart.e.setAttribute("data-value", '');
		$( '#wpua_tail_start_datetime' ).val( '' );
		$( '#wpua_start_datetime' ).val( '' );
	} );

	var tailEnd = tail.DateTime( "#wpua_tail_end_datetime", {
		dateStart: wpuaSettings.currentTime * 1000
	} );

	tailEnd.on( 'change', function() {
		$( '#wpua_end_datetime' ).val( this.select.getTime() / 1000 );
	} );

	tailEnd.on( 'open', function() {
		if ( this.dt.querySelector( "#calendar-apply-end" ) ) {
			return;
		}

		var div = document.createElement( "DIV" );
			div.className = "calendar-apply";
			div.id = "calendar-apply-end";
			div.innerHTML = '<button class="button">' + wpuaSettings.applyLabel + '</button>';

		this.dt.appendChild( div );

		$( '#calendar-apply-end' ).on( 'click', 'button', function() {
			tailEnd.close();
		} );
	} );

	$( '#wpua_tail_end_datetime_dismiss' ).on( 'click', function() {
		tailEnd.e.setAttribute("data-value", '');
		$( '#wpua_tail_end_datetime' ).val( '' );
		$( '#wpua_end_datetime' ).val( '' );
	} );
})( jQuery );
