jQuery(document).ready(function($) {
	var d = new Date();
	var time_current = d.getTime();

	$.each( drsa.ads, function( key, ad ) {
		// make the cookie key for this ad
		// cookie contains the place and id in case there is campign and ad changes
		var ad_count_cookie = 'drsa_count_' + ad.place + ad.adid;
		var time_saved = sessionStorage.getItem( ad_count_cookie );

		// if cookie timeout throttle is past, send view
		if( ( time_current - time_saved ) > drsa.counter_cookie_timeout ) {
			sessionStorage.setItem( ad_count_cookie, time_current );
			drsa_make_show_call( ad );
		}

		$( ad.click_counter_element ).click(function(e) {
			e.preventDefault();
			drsa_make_click_call( ad );

			if ( ad.open_blank ) {
				window.open( $(this).attr('href') );
			} else {
				window.location.href = $(this).attr('href');
			}
		});
	} );
});

function drsa_make_show_call( ad ) {
	console.log('show');
	console.log( ad );
	var data = {
		'action': 'drsa_count',
		'nonce': ad.nonce,
		'ad': ad.adid
	};

	jQuery.post( drsa.ajax_url, data, function(response) {} );
}

function drsa_make_click_call( ad ) {
	var data = {
		'action': 'drsa_count',
		'nonce': ad.nonce,
		'ad': ad.adid,
		'type': 'click'
	};

	jQuery.post( drsa.ajax_url, data, function(response) {} );
}
