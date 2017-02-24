var foyer_channel_selector = '.foyer-channel';
var foyer_slides_selector = '.foyer-slides';
var foyer_slide_selector = '.foyer-slide';

jQuery(window).load(function() {

	foyer_setup_display();
	foyer_setup_containers();
	foyer_fader_setup_slideshow();

});

function foyer_setup_containers() {

	// Set up container structure
	jQuery(foyer_slide_selector).wrapAll('<div class="foyer-slide-group foyer-slide-group-1"></div>');
	jQuery('.foyer-slide-group-1').after('<div class="foyer-slide-group foyer-slide-group-2"></div>');
}

function foyer_setup_display() {

	// Hide cursor
	jQuery(this).css('cursor','none');

	// Smart to refresh the entire display at least a couple of times a day
	major_refresh_timeout = setTimeout(foyer_display_reload_window, 8 * 60 * 60 * 1000); // (8 hours in milliseconds)

	// Load fresh display content every 5 minutes
	foyer_loader_intervalObject = window.setInterval(foyer_load_display_data, 20 * 1000) // (@todo: 5 minutes in milliseconds)
}

function foyer_load_display_data() {
	var $current_slide_group;
	var $next_slide_group;

	if (!jQuery('.foyer-slide-group-1').children().length) {
		// Group 1 is empty, up next
		$next_slide_group = jQuery('.foyer-slide-group-1');
		$current_slide_group = jQuery('.foyer-slide-group-2');
	}
	else if (!jQuery('.foyer-slide-group-2').children().length) {
		// Group 2 is empty, up next
		$next_slide_group = jQuery('.foyer-slide-group-2');
		$current_slide_group = jQuery('.foyer-slide-group-1');
	}

	if ($next_slide_group.length) {
		// Found an empty group, load html

		jQuery.get(window.location, function(html) {
			$new_html = jQuery(jQuery.parseHTML(html));

			if ($new_html.find(foyer_channel_selector).attr('class') !== jQuery(foyer_channel_selector).attr('class')) {
				// Channel ID has changed or its other properties have changed
				// Replace channel HTML and restart slideshow when current slideshow has shutdown
				foyer_fader_shutdown_slideshow(foyer_replace_channel, $new_html.find(foyer_channel_selector));
			}
			else {
				// Channel unchanged
				if (
					1 === $current_slide_group.children().length &&
					1 === $new_html.find(foyer_slides_selector).children().length
				) {
					// Only one slide currently & one slide new slide
					// Replace current slide group slides with new slide from loaded HTML
					$current_slide_group.html($new_html.find(foyer_slides_selector).children());
					foyer_fader_activate_first_slide();
				}
				else {
					// More than one slide currently, or one slide currently but more new slides
					// Add new slides from loaded HTML to next slide group
					$next_slide_group.html($new_html.find(foyer_slides_selector).children());

					$next_slide_group.find(foyer_slide_selector).first().attrChange(function(attr_name) {
						// Fader has advanced into the next group, first slide has changed to active
						$next_slide_group.find(foyer_slide_selector).first().attrChange(function(attr_name) {
							// First slide has changed from active to not active
							// Empty the current (now previous) group to allow loading of fresh content
							$current_slide_group.empty();
						});
					});
				}

			}
		});

	}
}

function foyer_replace_channel($new_channel_html) {
	jQuery(foyer_channel_selector).replaceWith($new_channel_html);

	foyer_setup_containers();
	foyer_fader_setup_slideshow();
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