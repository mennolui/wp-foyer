var foyer_fader_shutdown = false;
var foyer_fader_shutdown_callback;
var foyer_fader_shutdown_callback_options;

function foyer_fader_setup_slideshow() {
	jQuery(foyer_slide_selector).first().addClass('active');
	foyer_fader_set_timeout();
}

function foyer_fader_set_timeout(sec) {
	// Get duration for active slide
	var duration = parseFloat(jQuery(foyer_slide_selector + '.active').data('foyer-slide-duration'));

	if (!duration>0) {
		duration = 5;
	}

	setTimeout(foyer_fader_next_slide, duration * 1000); // (seconds in milliseconds)
}

function foyer_fader_next_slide() {
	var $active_slide = jQuery(foyer_slide_selector + '.active');
	var next_index = jQuery(foyer_slide_selector).index($active_slide) + 1;

	if (next_index >= jQuery(foyer_slide_selector).length) {
		next_index = 0;
	}

	$active_slide.removeClass('active');

	if (foyer_fader_shutdown) {
		foyer_fader_shutdown = false;
		foyer_fader_shutdown_callback(foyer_fader_shutdown_callback_options);
	}
	else {
		jQuery(foyer_slide_selector).eq(next_index).addClass('active');
		foyer_fader_set_timeout();
	}
}

function foyer_fader_shutdown_slideshow(callback, options) {
	foyer_fader_shutdown = true;
	foyer_fader_shutdown_callback = callback;
	foyer_fader_shutdown_callback_options = options;
}