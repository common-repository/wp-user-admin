(function( $ ) {
	function searchUsers( term ) {
		let users = [];

		$.ajax( {
			url: wpApiSettings.endpoint + 'wp/v2/users',
			method: 'GET',
			async: false,
			data: {
				search: term
			},
			beforeSend: function ( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
			},
			success: function ( response ) {
				response.forEach( function( element ) {
					users.push( { label: element.name, ziber: element.id } );
				} );
			}
		} );

		return users;
	}

	$( '#wpua_new_role_user' ).autocomplete( {
		source: function( request, response ) {
			response( searchUsers( request.term ) );
		},
		select: function( event, ui ) {
			$( '#wpua_new_role_user_id' ).val( ui.item.ziber );
		}
	  } );
})( jQuery );
