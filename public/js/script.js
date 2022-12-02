jQuery(document).ready(function($) {
	var d = new Date();
	var time_current = d.getTime();

	$.each( drsa.ads, function( key, ad ) {
    drsa_maybe_send_view(ad, time_current);

		$( ad.click_counter_element ).on('click', function(e) {
			e.preventDefault();
			drsa_make_click_call( ad );

			if ( ad.open_blank ) {
				window.open( $(this).attr('href') );
			} else {
				window.location.href = $(this).attr('href');
			}
		});
	} );

  window.addEventListener('scroll', throttle_scroll(250, time_current));
});

function throttle_scroll(wait, time_current) {
  var time = Date.now();

  return function() {
    if ((time + wait - Date.now()) < 0) {
      drsa.ads.forEach(ad => {
        drsa_maybe_send_view(ad, time_current);
      });

      time = Date.now();
    }
  }
}

function drsa_make_show_call( ad ) {
	var data = {
		'action': 'drsa_count',
		'nonce': ad.nonce,
		'ad': ad.adid,
    'place': ad.place,
    'page': window.location.pathname
	};

	jQuery.post( drsa.ajax_url, data, function(response) {} );
}

function drsa_make_click_call( ad ) {
	var data = {
		'action': 'drsa_count',
		'nonce': ad.nonce,
		'ad': ad.adid,
		'type': 'click',
    'place': ad.place,
    'page': window.location.pathname
	};

	jQuery.post( drsa.ajax_url, data, function(response) {} );
}

function isInViewport(ad) {
  var element = document.querySelector(ad.click_counter_element);
  var bounding = element.getBoundingClientRect();

  if (
    bounding.top <= (window.innerHeight || document.documentElement.clientHeight) &&
    // bounding.left >= 0 &&
    // bounding.right <= (window.innerWidth || document.documentElement.clientWidth) &&
    bounding.bottom >= 0) {
    return true;
  } else {
    return false;
  }
}

function drsa_maybe_send_view(ad, time_current) {
  // make the cookie key for this ad
	// cookie contains the place and id in case there is campign and ad changes
	// also key should contain current page, in case of ad used accross site
  var ad_count_cookie = 'drsa_count_' + ad.place + ad.adid + '_' + window.location.pathname;
  var time_saved = sessionStorage.getItem( ad_count_cookie );
  // if cookie timeout throttle is not past, don't send view
  if( ( time_current - time_saved ) < drsa.counter_cookie_timeout ) {
    return;
  }

  if (!isInViewport(ad)) {
    return false;
  }

  sessionStorage.setItem( ad_count_cookie, time_current );
  drsa_make_show_call( ad );
}
