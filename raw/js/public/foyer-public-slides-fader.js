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
	jQuery(foyer_slide_selector).eq(next_index).addClass('active');

	foyer_fader_set_timeout();
}