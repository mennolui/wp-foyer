var foyer_slide_revslider_selector = '.foyer-slide-revslider';
var foyer_revsliders = {};

/**
 * Sets up the RevSlider slide public functionality.
 *
 * @since	1.6.0
 */
jQuery(document).ready(function() {

	if (jQuery(foyer_slide_revslider_selector).length) {
		// Our view includes RevSlider slides, init RevSliders and bind events
		foyer_slide_revslider_init_revsliders();
		foyer_slide_revslider_bind_display_loading_events();
		foyer_slide_revslider_bind_ticker_events();
	}
});

function getCurrentSliderAPI() {

    var slider = jQuery('.rev_slider');
    if(!slider.length) return false;

console.log('revapi' + slider.attr('id').split('rev_slider_')[1].split('_')[0]);
    return eval('revapi' + slider.attr('id').split('rev_slider_')[1].split('_')[0]);

}

/**
 * Binds events to be able to set up RevSliders in newly loaded slide groups and replaced channels.
 *
 * @since	1.6.0
 */
function foyer_slide_revslider_bind_display_loading_events() {

	jQuery('body').on('channel:replaced-channel', foyer_channel_selector, function ( event ) {
		foyer_slide_revslider_init_revsliders();
		foyer_slide_revslider_cleanup_revsliders();
	});

	jQuery('body').on('slides:loaded-new-slide-group', foyer_slides_selector, function ( event ) {
		foyer_slide_revslider_init_revsliders();
	});

	jQuery('body').on('slides:removed-old-slide-group', foyer_slides_selector, function ( event ) {
		foyer_slide_revslider_cleanup_revsliders();
	});
}

/**
 * Binds events to be able to start and stop RevSlider at the right time, and prevent advancing to the next slide.
 *
 * @since	1.6.0
 */
function foyer_slide_revslider_bind_ticker_events() {

	jQuery('body').on('slides:before-binding-events', foyer_slides_selector, function ( event ) {
		// The slides ticker is about to set up binding events
		// Bind the slides:next-slide event early so we can prevent its default action if we need to

		jQuery('body').on('slides:next-slide', foyer_slides_selector, function( event ) {
			// The next slide event is triggered
			// Determine if we should prevent its default action or not

			// Set container
			var container = jQuery(foyer_slide_revslider_selector).filter('.active').find('.revslider-container');

			// Set RevSlider reference
			var revslider = window.foyer_revsliders[container.attr('id')]

			if (1 == container.data('foyer-hold-slide')) {
				// We should wait for a signal from the RevSlider before proceeding to the next slide, but only when it is ready

				if (revslider) {
					// RevSlider exists, prevent next slide
//					event.stopImmediatePropagation();
				}
			}
		});
	});

	jQuery('body').on('slide:became-active', foyer_slide_revslider_selector, function( event ) {
		// A RevSlider slide became active

		// Set container
		var container = jQuery(this).find('.revslider-container');

		// Set RevSlider reference
		var revslider = window.foyer_revsliders[container.attr('id')]

		if (revslider && container.find('.revslider-initialised').length) { // @todo: api method?
			// RevSlider exists and is ready

			revslider.on('revolution.slide.onloaded', function() {
				// (Re)start slider
				revslider.revstart();
				console.log('revstart');
			});
		}
	});

	jQuery('body').on('slide:left-active', foyer_slide_revslider_selector, function( event ) {
		// A RevSlider slide left the active state

		// Set container
		var container = jQuery(this).find('.revslider-container');

		// Set RevSlider reference
		var revslider = window.foyer_revsliders[container.attr('id')]

		if (revslider) {
			// RevSlider exists and is ready

			// Stop video whenever CSS transitions are over
		}
	});
}

/**
 * Cleans up unused RevSlider references.
 *
 * Used after newly loaded slide groups and replaced channels.
 *
 * @since	1.6.0
 */
function foyer_slide_revslider_cleanup_revsliders() {
	for (var revslider_id in window.foyer_revsliders) {
		if (!jQuery('#' + revslider_id).length) {
			// RevSlider is no longer present in the document, remove its player reference
			delete window.foyer_revsliders[revslider_id];
		}
	}
}

/**
 * Inits all new RevSliders, storing slider references for later use.
 *
 * @since	1.6.0
 */
function foyer_slide_revslider_init_revsliders() {
	// Loop over any RevSlider containers that are not yet initialized
	jQuery('.revslider-container').each(function() {

		var revapii = getCurrentSliderAPI();
		if(revapii) {
			// start new slider
			// assuming the "revapi1.revstart()" option has
			// been turned on in the slider's General Settings
			console.log('revstarti');
			revapii.revstart();
			console.log('revstarti');
		}
		return;
		// Set container
		var container = jQuery(this);

		if (!container.attr('id')) {
			console.log('new revslider');
			// Not initialized, set unique ID attribute
			container.attr('id', 'revslider-container-' + Math.random().toString(36).substr(2, 16));

			var revslider_id = container.attr('id');

			if (revslider_id) {
				// Set up RevSlider and store its reference
				revslider = container.find('.rev_slider').show().revolution({
					waitForInit: true,
					stopAtSlide: 2,
					stopAfterLoops: 0,
				});

				console.log('new revslider_id');

//				revslider.revredraw();

				revslider.revstart();
				console.log('revstart1');

				revslider.on('revolution.slide.onloaded', function() {
					// (Re)start slider
					revslider.revstart();
					console.log('revstart');
				});

				window.foyer_revsliders[revslider_id] = revslider;
			}
		}
	});
	console.log(window.foyer_revsliders);
}

/**
 * Prepares the video so it is ready for playback.
 *
 * Invoked whenever the player is ready.
 *
 * @since	1.4.0
 * @since	1.5.1	Video slides no longer play when previewed while editing a Channel.
 *					Muting of video is now optional, based on the foyer-output-sound data attribute.
 * @since	1.5.5	Invoked a method that resizes the YouTube player to cover the entire slide background
 *					with video. Also on window resize.
 *
 * @param	string	player_id	The ID of the player
 */
function foyer_slide_bg_video_prepare_player_for_playback(player_id) {

	return function(event) {

		// Set container
		var container = jQuery('#' + player_id);

		// Set player reference
		var player = window.foyer_yt_players[player_id];

		// Make sure YouTube player covers the entire slide background with video, also on window resize
		foyer_slide_bg_video_resize_youtube_to_cover(player_id);
		jQuery(window).on('resize', function() {
			foyer_slide_bg_video_resize_youtube_to_cover(player_id);
		});

		if ((window.self != window.top) && (top.location.href.search('/post.php?') != -1)) {
			// Viewed on a slide displayed within a Channel edit page: don't play video
			return;
		}

		if (!container.data('foyer-output-sound')) {
			// No sound (unless enable sound option is checked)
			player.mute();
		}

		// Trigger buffering so video is ready to play when needed
		player.seekTo(container.data('foyer-video-start'));

		if (
			jQuery(foyer_slides_selector).length &&
			! jQuery('#' + player_id).parents(foyer_slide_bg_video_selector).hasClass('active')
		) {
			// Viewed on a channel or display: When this video slide is not active at this very moment,
			// pause, so it can start playing whenever it becomes active
			player.pauseVideo();
		}
	}
}
