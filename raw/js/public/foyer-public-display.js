var foyer_display_selector = '.foyer-display';
var foyer_channel_selector = '.foyer-channel';
var foyer_slides_selector = '.foyer-slides';
var foyer_slide_selector = '.foyer-slide';

jQuery(document).ready(function() {

	if (jQuery(foyer_display_selector).length) {
		// Our view includes a display, setup loading
		foyer_display_setup();
		foyer_display_setup_slide_group_classes();
	}

});

function foyer_display_setup_slide_group_classes() {

	// Add a group class to all slides
	jQuery(foyer_slides_selector).children().addClass('foyer-slide-group-1');
}

function foyer_display_setup() {

	// Hide cursor
	jQuery(this).css('cursor','none');

	// Smart to refresh the entire display at least a couple of times a day
	major_refresh_timeout = setTimeout(foyer_display_reload_window, 8 * 60 * 60 * 1000); // (8 hours in milliseconds)

	// Load fresh display content every 5 minutes
	foyer_loader_intervalObject = window.setInterval(foyer_display_load_data, 5 * 60 * 1000) // (5 minutes in milliseconds)
}

/**
 * Loads and processes new HTML for the current display.
 *
 * @since	1.?.?
 * @since	1.7.4	Added a trigger 'slides:removing-old-slide-group' that is fired just before a slide group is removed.
 *					Added the slide group class as parameter to the 'slides:removing-old-slide-group'
 *					and 'slides:loaded-new-slide-group' triggers, so these slide groups can be selectively targeted.
 */
function foyer_display_load_data() {
	var current_slide_group_class;
	var next_slide_group_class;

	if (!jQuery('.foyer-slide-group-1').length) {
		// No group 1 slides, add them
		next_slide_group_class = 'foyer-slide-group-1';
		current_slide_group_class = 'foyer-slide-group-2';
	}
	else if (!jQuery('.foyer-slide-group-2').length) {
		// No group 2 slides, add them
		next_slide_group_class = 'foyer-slide-group-2';
		current_slide_group_class = 'foyer-slide-group-1';
	}

	if (next_slide_group_class) {
		// Found an empty group, load html

		jQuery.get(window.location, function(html) {
			$new_html = jQuery(jQuery.parseHTML(jQuery.trim(html)));

			if ($new_html.filter(foyer_display_selector).hasClass('foyer-reset-display')) {
				// Use filter instead of find to target top-level element
				// https://stackoverflow.com/questions/15403600/jquery-not-finding-elements-in-jquery-parsehtml-result

				// Reset was requested
				// Reload after current slideshow has shutdown
				foyer_ticker_shutdown(foyer_display_reload_window);
			}
			else if ($new_html.find(foyer_channel_selector).attr('class') !== jQuery(foyer_channel_selector).attr('class')) {
				// Channel ID has changed or its other properties have changed
				// Replace channel HTML and restart slideshow after current slideshow has shutdown
				foyer_ticker_shutdown(foyer_display_replace_channel, $new_html.find(foyer_channel_selector));
			}
			else {
				// Channel unchanged
				var $new_slides = $new_html.find(foyer_slides_selector).children().addClass(next_slide_group_class);

				if (
					1 === jQuery(foyer_slides_selector).children().length &&
					1 === $new_html.find(foyer_slides_selector).children().length
				) {
					// Only one slide currently & one slide new slide
					// Replace current slide with new slide from loaded HTML
					jQuery(foyer_slides_selector)
						.trigger('slides:removing-old-slide-group', current_slide_group_class)
						.html($new_slides)
						.trigger('slides:loaded-new-slide-group', next_slide_group_class)
						.trigger('slides:removed-old-slide-group');
					foyer_ticker_set_slide_active_next_classes();
				}
				else {
					// More than one slide currently, or one slide currently but more new slides
					// Add new slides from loaded HTML to next slide group
					jQuery(foyer_slides_selector).children().last().after($new_slides);
					jQuery(foyer_slides_selector).trigger('slides:loaded-new-slide-group', next_slide_group_class);

					jQuery('body').one('slide:left-active', '.'+next_slide_group_class, function( event ) {
						// Ticker has advanced into the next group, and one of its slides (the first) has left active
						// Empty the current (now previous) group to allow loading of fresh content
						jQuery(foyer_slides_selector).trigger('slides:removing-old-slide-group', current_slide_group_class);
						jQuery(foyer_slides_selector).find('.'+current_slide_group_class).remove();
						jQuery(foyer_slides_selector).trigger('slides:removed-old-slide-group');
						return true;
					});
				}

			}
		});
	}
}

function foyer_display_replace_channel($new_channel_html) {
	jQuery(foyer_channel_selector).replaceWith($new_channel_html);
	jQuery(foyer_channel_selector).trigger('channel:replaced-channel');
	foyer_display_setup_slide_group_classes();

	// Use timeout to allow browser to detect class changing from next to active
	setTimeout(foyer_ticker_init, 0.1 * 1000); // (0.1 seconds in milliseconds)
}

function foyer_display_reload_window() {
	window.location.reload();
}