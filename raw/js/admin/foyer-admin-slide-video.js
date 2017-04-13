var foyer_yt_player;

jQuery( function() {

	if (jQuery('#slide_video_video_url').val().length) {
		// YouTube video URL is set on load, validate it and load preview
		foyer_admin_slide_video_validate_youtube_video_url();
	}

	jQuery('#slide_video_video_url').on('change', function() {
		// Validate changed YouTube video URL and load preview
		foyer_admin_slide_video_validate_youtube_video_url();
	});

	jQuery('#slide_video_video_start').on('change', function() {
		// Update player with changed start time
		foyer_admin_slide_video_update_youtube_video_preview();
	});

	jQuery('#slide_video_video_end').on('change', function() {
		// Update player with changed end time
		foyer_admin_slide_video_update_youtube_video_preview();
	});

});

function foyer_admin_load_youtube_api() {
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

	var video_id = jQuery('#slide_video_video_id').val();
	var start = jQuery('#slide_video_video_start').val();
	var end = jQuery('#slide_video_video_end').val();

	// Set up player and store its reference
	window.foyer_yt_player = new YT.Player('foyer-admin-video-preview', {
		width: '480',
		height: '270',
		videoId: video_id,
		playerVars: {
			'controls': 0,
			'modestbranding': 1,
			'rel': 0,
			'showinfo': 0,
			'start': start,
			'end': end,
		},
		events: {
			'onReady': foyer_admin_slide_video_youtube_api_ready,
		}
	});
}

function foyer_admin_slide_video_update_youtube_video_preview() {
	if (jQuery('#slide_video_video_id').val().length) {
		// Video ID is set, update preview
		if (window.foyer_yt_player) {
			var player = window.foyer_yt_player;

			var video_id = jQuery('#slide_video_video_id').val();
			var start = jQuery('#slide_video_video_start').val();
			var end = jQuery('#slide_video_video_end').val();

			if (video_id) {
				player.mute();
				player.loadVideoById( {videoId: video_id, startSeconds: start, endSeconds: end} );
			}
		}
		else {
			foyer_admin_load_youtube_api();
		}
	}
}

function foyer_admin_slide_video_validate_youtube_video_url() {
	var video_metadata = foyer_get_video_id(jQuery('#slide_video_video_url').val());
	console.log(video_metadata);

	if (video_metadata && video_metadata.id && 'youtube' == video_metadata.service) {
		// Valid YouTube video URL, rewrite URL field and update the video preview
		jQuery('#slide_video_video_url').val('https://youtu.be/' + video_metadata.id);
		jQuery('#slide_video_video_id').val(video_metadata.id);

		jQuery('#slide_video_video_url_notification').addClass('hidden');

		foyer_admin_slide_video_update_youtube_video_preview();
	}
	else {
		// Not a valid URL, pause video, empty video ID and show message
		if (window.foyer_yt_player) {
			var player = window.foyer_yt_player;
			player.pauseVideo();
		}
		jQuery('#slide_video_video_id').val('');
		jQuery('#slide_video_video_url_notification').removeClass('hidden');
	}
}

function foyer_admin_slide_video_youtube_api_ready() {
	if (window.foyer_yt_player) {
		var player = window.foyer_yt_player;
		player.mute();
		player.playVideo();
	}
}
