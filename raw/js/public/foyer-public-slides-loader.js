jQuery(window).load(function() {

	var $slides = jQuery('.foyer-slides');
	$slides.after('<div class="foyer-slides-container foyer-slides-container-2"></div>');
	$slides.wrap('<div class="foyer-slides-container foyer-slides-container-1"></div>');

	$container = jQuery('.foyer-slides-container-1');

	if ($container.length > 0) {
		$foyer_fader_slideshows = $container.find('.foyer-slides');
		foyer_fader_setup_slideshows();
//		foyer_display_load_data();
	}

});

function foyer_display_load_data() {

	// Hide cursor
	jQuery(this).css('cursor','url("../img/nocursor.gif"), none;');

	// Smart to refresh the entire display at least a couple of times a day
	majorrefresh = setTimeout(foyer_display_reload_window, 28800000); // (28800000 is 8 hours in milliseconds format)

	var data = {
		'action': 'foyer_display_load_data',
		'channel_id': 1,
	};

	jQuery.post(ajaxurl, data, function(response) {
		if (response != '') {
			$holder.html(response);

			foyer_fader_slideshows = jQuery('.fader-slideshow');

			if (foyer_fader_slideshows.length > 0) {
				foyer_fader_setup_slideshows();
			}

		}
	});

}

function foyer_display_reload_window() {
	window.location.reload();
}
