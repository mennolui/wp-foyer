var foyer_display_selector = '.foyer-display';
var foyer_channel_selector = '.foyer-channel';
var foyer_slides_selector = '.foyer-slides';
var foyer_slide_selector = '.foyer-slide';

jQuery(window).load(function() {

	if (jQuery(foyer_display_selector).length) {
		// We're viewing a display
		foyer_setup_display();
		foyer_setup_slide_group_classes();
	}

	foyer_fader_setup_slideshow();

});

function foyer_setup_slide_group_classes() {

	// Add a group class to all slides
	jQuery(foyer_slides_selector).children().addClass('foyer-slide-group-1');
}

function foyer_setup_display() {

	// Hide cursor
	jQuery(this).css('cursor','none');

	// Smart to refresh the entire display at least a couple of times a day
	major_refresh_timeout = setTimeout(foyer_display_reload_window, 8 * 60 * 60 * 1000); // (8 hours in milliseconds)

	// Load fresh display content every 5 minutes
	foyer_loader_intervalObject = window.setInterval(foyer_load_display_data, 30 * 1000) // (@todo: 5 minutes in milliseconds)
}

function foyer_load_display_data() {
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

	if (next_slide_group_class.length) {
		// Found an empty group, load html

		jQuery.get(window.location, function(html) {
			$new_html = jQuery(jQuery.parseHTML(html));

			if ($new_html.find(foyer_channel_selector).attr('class') !== jQuery(foyer_channel_selector).attr('class')) {
				// Channel ID has changed or its other properties have changed
				// Replace channel HTML and restart slideshow after current slideshow has shutdown
				foyer_fader_shutdown_slideshow(foyer_replace_channel, $new_html.find(foyer_channel_selector));
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
					jQuery(foyer_slides_selector).html($new_slides);
					foyer_fader_set_slide_active_next_classes();
				}
				else {
					// More than one slide currently, or one slide currently but more new slides
					// Add new slides from loaded HTML to next slide group
					jQuery(foyer_slides_selector).children().last().after($new_slides);

					jQuery(foyer_slides_selector).find('.'+next_slide_group_class).first().attrChange(function(attr_name) {
						// Fader has advanced into the next group, first slide has changed to active
						jQuery(foyer_slides_selector).find('.'+next_slide_group_class).first().attrChange(function(attr_name) {
							// First slide has changed from active to not active
							// Empty the current (now previous) group to allow loading of fresh content
							jQuery(foyer_slides_selector).find('.'+current_slide_group_class).remove();
						});
					});
				}

			}
		});
	}
}

function foyer_replace_channel($new_channel_html) {
	jQuery(foyer_channel_selector).replaceWith($new_channel_html);
	foyer_setup_slide_group_classes();

	// Use timeout to allow browser to detect class changing from next to active
	setTimeout(foyer_fader_setup_slideshow, 0.1 * 1000); // (0.1 seconds in milliseconds)
}

function foyer_display_reload_window() {
	window.location.reload();
}

jQuery(function() {
	(function(jQuery) {
	    var MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;

	    jQuery.fn.attrChange = function(callback) {
	        if (MutationObserver) {
	            var options = {
	                subtree: false,
	                attributes: true
	            };

	            var observer = new MutationObserver(function(mutations) {
	                mutations.forEach(function(e) {
						observer.disconnect(); // detect only first change
	                    callback.call(e.target, e.attributeName);
	                });
	            });

	            return this.each(function() {
	                observer.observe(this, options);
	            });
	        }
	    }
	})(jQuery);
});