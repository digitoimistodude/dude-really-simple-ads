jQuery(document).ready(function($) {
	var d = new Date();
	var time_current = d.getTime();
	var time_saved = sessionStorage.getItem('drsacountcookie')

	if( (time_current - time_saved ) > drsa.counter_cookie_timeout ) {
		sessionStorage.setItem('drsacountcookie', time_current);
		drsa_make_show_call();
	}

	$(drsa.click_counter_element).click(function(e){
		drsa_make_click_call();
	});
});

function drsa_make_show_call() {
	var data = {
		'action': 'drsa_count',
		'nonce': drsa.nonce,
		'ad': drsa.ad
	};

	jQuery.post(drsa.ajax_url, data, function(response) {});
}

function drsa_make_click_call() {
	var data = {
		'action': 'drsa_count',
		'nonce': drsa.nonce,
		'ad': drsa.ad,
		'type': 'click'
	};

	jQuery.post(drsa.ajax_url, data, function(response) {});
}
