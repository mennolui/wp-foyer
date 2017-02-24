var foyer_slides_selector = '.foyer-slides';
var foyer_slide_selector = '.foyer-slide';

jQuery(window).load(function() {

	// Set up container structure
	jQuery(foyer_slide_selector).wrapAll('<div class="foyer-slide-group foyer-slide-group-1"></div>');
	jQuery('.foyer-slide-group-1').after('<div class="foyer-slide-group foyer-slide-group-2"></div>');

	foyer_fader_setup_slideshow();
	foyer_setup_display();

});

function foyer_setup_display() {

	// Hide cursor
	jQuery(this).css('cursor','none');

	// Smart to refresh the entire display at least a couple of times a day
	major_refresh_timeout = setTimeout(foyer_display_reload_window, 8 * 60 * 60 * 1000); // (8 hours in milliseconds)

	// Load fresh display content every 5 minutes
	foyer_loader_intervalObject = window.setInterval(foyer_load_display_data, 5 * 60 * 1000) // (5 minutes in milliseconds)
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
		// Found an empty group, load data
		$next_slide_group.load(window.location + ' ' + foyer_slides_selector + ' > *', function(){
			$next_slide_group.find('.foyer-slide').first().attrChange(function(attr_name) {
				// Fader has advanced into the next group]
				// Empty the current (now previous) group to allow loading of fresh content
				$current_slide_group.empty();
			});
		});
	}
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