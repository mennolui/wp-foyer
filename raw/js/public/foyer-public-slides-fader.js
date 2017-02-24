var foyer_fader_shutdown = false;
var foyer_fader_shutdown_callback;
var foyer_fader_shutdown_callback_options;

function foyer_fader_setup_slideshow() {
	foyer_fader_set_slide_active_next_classes();
	foyer_fader_set_timeout();
}

function foyer_fader_set_slide_active_next_classes() {
	jQuery(foyer_slide_selector).first().removeClass('next').addClass('active');
	jQuery(foyer_slide_selector).first().next().addClass('next');
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
	var slide_count = jQuery(foyer_slide_selector).length;

	var new_active_index = jQuery(foyer_slide_selector).index($active_slide) + 1;
	if (new_active_index >= slide_count) {
		new_active_index = 0;
	}

	var new_next_index = new_active_index + 1;
	if (new_next_index >= slide_count) {
		new_next_index = 0;
	}

	$active_slide.removeClass('active');

	if (foyer_fader_shutdown) {
		foyer_fader_shutdown = false;

		// Trigger callback, but only after some time has passed to finish all CSS transitions
		setTimeout(function() {
			foyer_fader_shutdown_callback(foyer_fader_shutdown_callback_options);
		}, 2 * 1000); // (2 seconds in milliseconds)
	}
	else {
		jQuery(foyer_slide_selector).eq(new_active_index).removeClass('next').addClass('active');
		jQuery(foyer_slide_selector).eq(new_next_index).addClass('next');
		foyer_fader_set_timeout();
	}
}

function foyer_fader_shutdown_slideshow(callback, options) {
	foyer_fader_shutdown = true;
	foyer_fader_shutdown_callback = callback;
	foyer_fader_shutdown_callback_options = options;
}