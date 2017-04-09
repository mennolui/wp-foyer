var foyer_slide_video_selector = '.foyer-slide-video';
var foyer_yt_players = {};

jQuery(document).ready(function() {

	if (jQuery(foyer_slides_selector).length) {
		// Our view includes video slides, load YouTube API and bind events
		foyer_slide_video_load_youtube_api();
		foyer_slide_video_bind_events();
	}

});

function foyer_slide_video_load_youtube_api() {
	// Load YouTube IFrame Player API code asynchronously
	(function() { // Closure, to not leak to the scope
		var tag = document.createElement('script');
		tag.src = "https://www.youtube.com/iframe_api";
		var firstScriptTag = document.getElementsByTagName('script')[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
	})();
}

// Loop over all video slides whenever the YouTube IFrame Player API is ready
function onYouTubeIframeAPIReady() {
	jQuery(foyer_slide_video_selector).each(function() {

		// Set container
		var container = jQuery(this).find('.youtube-video-container');

		var player_id = container.attr('id');
		var video_id = container.data('foyer-video-id');

		if (player_id && video_id) {
			// Set up player and store its reference
			window.foyer_yt_players[player_id] = new YT.Player(player_id, {
				width: '1920',
				height: '1080',
				videoId: video_id,
				playerVars: {
					'controls': 0,
					'loop': 1,
					'modestbranding': 1,
					'rel': 0,
					'showinfo': 0,
				},
				events: {
					'onReady': foyer_slide_video_youtube_player_ready(player_id),
				}
			});
		}
	});
	console.log(window.foyer_yt_players);
}

function foyer_slide_video_bind_events() {

	jQuery('body').on('slides:before-binding-events', foyer_slides_selector, function ( event ) {
		// The slides ticker is about to set up binding events
		// Bind the slides:next-slide event early so we can prevent its default action if we need to
		console.log('binding');

		jQuery('body').on('slides:next-slide', foyer_slides_selector, function( event ) {
			// The next slide event is triggered
			// Determine if we should prevent its default action or not
			console.log('trying next');

			// Set container
			var container = jQuery(foyer_slide_video_selector).filter('.active').find('.youtube-video-container');

			// Set player reference
			var player = window.foyer_yt_players[container.attr('id')]

			if (1 == container.data('foyer-video-wait-for-end') && player) {
				// We should wait for the end of the video before proceeding to the next slide

				var end = container.data('foyer-video-end');
				var duration = player.getDuration();
				var current_time = player.getCurrentTime();

				if ( duration < end || !end ) {
					end = duration;
				}

				console.log(current_time);

				if ( current_time >= end - foyer_ticker_css_transition_duration ) {
					// Video almost ended, do not prevent next slide
				}
				else {
					// Not ended yet, prevent next slide
					console.log('prevented next');
					event.stopImmediatePropagation();

					// Try again in 0.5 seconds
					setTimeout(function() {
						jQuery(foyer_slides_selector).trigger('slides:next-slide');
					}, 0.5 * 1000);
				}
			}
		});
	});

	jQuery('body').on('slide:became-active', foyer_slide_video_selector, function( event ) {
		// A video slide became active
		console.log('became');

		// Set container
		var container = jQuery(this).find('.youtube-video-container');

		// Set player reference
		var player = window.foyer_yt_players[container.attr('id')]

		if (player) {
			// Player exists
			console.log('play');

			// Seek to start
			player.playVideo();
		}
	});

	jQuery('body').on('slide:left-active', foyer_slide_video_selector, function( event ) {
		// A video slide left the active state
		console.log('left');

		// Set container
		var container = jQuery(this).find('.youtube-video-container');

		// Set player reference
		var player = window.foyer_yt_players[container.attr('id')]

		if (player) {
			// Player exists
			console.log('pause');

			// Stop video whenever CSS transitions are over
			setTimeout(function() {
				player.seekTo(container.data('foyer-video-start'));
				player.pauseVideo();
			}, foyer_ticker_css_transition_duration * 1000);
		}
	});
}

// This function is called by the YouTube IFrame Player API whenever a player is ready
function foyer_slide_video_youtube_player_ready(player_id) {

	return function(event) {

		// Set container
		var container = jQuery('#' + player_id);

		// Set player reference
		var player = window.foyer_yt_players[player_id];

		// No sound
		player.mute();

		// Trigger buffering so video is ready to play when needed
		player.seekTo(container.data('foyer-video-start'));

		if (! jQuery('#' + player_id).parents(foyer_slide_video_selector).hasClass('active')) {
			// When this video slide is not active at this very moment, pause,
			// so it can start playing whenever it becomes active
			player.pauseVideo();
		}
	}
}
