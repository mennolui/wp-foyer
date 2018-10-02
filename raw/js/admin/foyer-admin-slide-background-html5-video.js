/**
 * Sets up the HTML5 Video slide format admin functionality.
 *
 * @since	1.X.X
 */
jQuery( function() {

	if (jQuery('#slide_bg_html5_video_video_url').val() && jQuery('#slide_bg_html5_video_video_url').val().length) {
		// HTML5 Video URL is set on load, load preview
		foyer_admin_slide_bg_html5_video_update_url_status();
		foyer_admin_slide_bg_html5_video_update_youtube_video_preview();
	}

	jQuery('#slide_bg_html5_video_video_url').on('change', function() {
		// Update player with changed URL
		foyer_admin_slide_bg_html5_video_update_url_status();
		foyer_admin_slide_bg_html5_video_update_youtube_video_preview();
	});

	jQuery('#slide_bg_html5_video_video_start').on('change', function() {
		// Update player with changed start time
		foyer_admin_slide_bg_html5_video_update_youtube_video_preview();
	});

	jQuery('#slide_bg_html5_video_video_end').on('change', function() {
		// Update player with changed end time
		foyer_admin_slide_bg_html5_video_update_youtube_video_preview();
	});

	jQuery('#slide_bg_html5_video_enable_sound').on('change', function() {
		// Update player's mute status
		foyer_admin_slide_bg_html5_video_update_player_mute();
	});

});

/**
 * Updates the slide admin video preview player's mute status.
 *
 * Invoked whenever a video starts playing, or the mute property is toggled by the user.
 *
 * @since	1.X.X
 */
function foyer_admin_slide_bg_html5_video_update_player_mute() {

	// Set video reference
	var vid = jQuery('#slide_bg_html5_video_video_preview').get(0);

	if (vid) {

		if (jQuery('#slide_bg_html5_video_enable_sound').prop('checked')) {
			vid.muted = false;
		}
		else {
			vid.muted = true;
		}
	}
}

function foyer_admin_slide_bg_html5_video_update_url_status() {
	if (jQuery('#slide_bg_html5_video_video_url').val().length) {
		// HTML5 Video URL is set, remove empty class
		jQuery('#slide_bg_html5_video_file_field').removeClass('empty');
	}
	else {
		// No HTML5 Video URL set, add empty class
		jQuery('#slide_bg_html5_video_file_field').addClass('empty');
	}
}

/**
 * Updates the slide admin video preview with new parameters as entered by the user, and restarts playback.
 *
 * @since	1.X.X
 */
function foyer_admin_slide_bg_html5_video_update_youtube_video_preview() {
	if (jQuery('#slide_bg_html5_video_video_url').val() && jQuery('#slide_bg_html5_video_video_url').val().length) {
		// Video URL is set, update preview

		// Set video reference
		var vid = jQuery('#slide_bg_html5_video_video_preview').get(0);

		if (vid) {

			var url = jQuery('#slide_bg_html5_video_video_url').val();
			var start = jQuery('#slide_bg_html5_video_video_start').val();
			var end = jQuery('#slide_bg_html5_video_video_end').val();

			foyer_admin_slide_bg_html5_video_update_player_mute();

			// Hide error message
			jQuery('#slide_bg_html5_video_video_url_notification').addClass('hidden');

			vid.src = url;
			vid.currentTime = start;

			vid.addEventListener('error', function(event) {

				if ( 4 === this.error.code ) {
					// Not a usable source, show error message
					jQuery('#slide_bg_html5_video_video_url_notification').removeClass('hidden');
				}
			}, true);

			vid.play();

			jQuery('#slide_bg_html5_video_video_url_notification').addClass('hidden');

			vid.ontimeupdate = function() {

				if ( this.duration < end || !end ) {
					end = this.duration;
				}

				if ( this.currentTime >= end ) {
					// Video reached the end, pause
					vid.pause();
				}
			};

		}
	}
}
