var foyer_ticker_shutdown_status = false;
var foyer_ticker_shutdown_callback;
var foyer_ticker_shutdown_callback_options;

var foyer_ticker_css_transition_duration = 1.5; // 1.5 seconds
var foyer_ticker_css_transition_duration_safe = foyer_ticker_css_transition_duration + 0.5; // add 0.5 seconds

jQuery(document).ready(function() {

	if (jQuery(foyer_slides_selector).length) {
		// Our view includes slides, initialize ticker
		foyer_ticker_bind_events();
		foyer_ticker_init();
	}

});

function foyer_ticker_bind_events() {
	// Allow others to bind events before us, so they can prevent ours
	jQuery(foyer_slides_selector).trigger('slides:before-binding-events');

	jQuery('body').on('slides:next-slide', foyer_slides_selector, function( event ) {
		// Store a reference to the active slide before it is removed
		var $active_slide = jQuery(foyer_slide_selector + '.active');

		// This potentially removes a complete slide group, so determine new active and next slide afterwards
		$active_slide.trigger('slide:leaving-active');

		var $new_active_slide = $active_slide.next();
		if (!$new_active_slide.length) {
			// No next sibling, use first
			$new_active_slide = jQuery(foyer_slide_selector).first();
		}

		var $new_next_slide = $new_active_slide.next();
		if (!$new_next_slide.length) {
			// No next sibling, use first
			$new_next_slide = jQuery(foyer_slide_selector).first();
		}

		if (foyer_ticker_shutdown_status) {
			foyer_ticker_shutdown_status = false;

			// Trigger callback, but only after some time has passed to finish all CSS transitions
			setTimeout(function() {
				foyer_ticker_shutdown_callback(foyer_ticker_shutdown_callback_options);
			}, foyer_ticker_css_transition_duration_safe * 1000);
		}
		else {
			$new_active_slide.trigger('slide:becoming-active');
			$new_next_slide.trigger('slide:becoming-next');
			foyer_ticker_set_active_slide_timeout();
		}
	});

	jQuery('body').on('slide:becoming-next', foyer_slide_selector, function( event ) {
		jQuery(this).addClass('next').trigger('slide:became-next');
	});
	jQuery('body').on('slide:becoming-active', foyer_slide_selector, function( event ) {
		jQuery(this).removeClass('next').addClass('active').trigger('slide:became-active');
	});
	jQuery('body').on('slide:leaving-active', foyer_slide_selector, function( event ) {
		jQuery(this).removeClass('active').trigger('slide:left-active');
	});

	jQuery(foyer_slides_selector).trigger('slides:after-binding-events');
}


function foyer_ticker_init() {
	foyer_ticker_set_slide_active_next_classes();
	foyer_ticker_set_active_slide_timeout();
}

function foyer_ticker_set_slide_active_next_classes() {
	jQuery(foyer_slide_selector).first().trigger('slide:becoming-active');
	jQuery(foyer_slide_selector).first().next().trigger('slide:becoming-next');
}

function foyer_ticker_set_active_slide_timeout() {
	// Get duration for active slide
	var duration = parseFloat(jQuery(foyer_slide_selector + '.active').data('foyer-slide-duration'));

	if (!duration>0) {
		duration = 5;
	}

	setTimeout(foyer_ticker_next_slide, duration * 1000); // (seconds in milliseconds)
}

function foyer_ticker_next_slide() {
	jQuery(foyer_slides_selector).trigger('slides:next-slide');
}

function foyer_ticker_shutdown(callback, options) {
	foyer_ticker_shutdown_status = true;
	foyer_ticker_shutdown_callback = callback;
	foyer_ticker_shutdown_callback_options = options;
}