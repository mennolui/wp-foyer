<?php
/**
 * Video slide format template.
 *
 * @since	1.0.0
 */

$slide = new Foyer_Slide( get_the_id() );

?><div<?php $slide->classes(); ?><?php $slide->data_attr();?>>
	<div class="inner">
		<div class="youtube-video-container" id="<?php echo uniqid(); ?>"
			data-foyer-video-id="r9tbusKyvMY"
			data-foyer-video-start="35"
			data-foyer-video-end="60"
			data-foyer-video-wait-for-end="1"
		></div>


		<script>
			var foyer_slide_video_selector = '.foyer-slide-video';

			jQuery('body').on('slides:before-binding-events', foyer_slides_selector, function ( event ) {
				console.log('binding');

				jQuery('body').on('slides:next-slide', foyer_slides_selector, function( event ) {
					// if current active slide is 'wait'
					console.log('trying next');

					// Set container
					var container = jQuery(foyer_slide_video_selector).filter('.active').find('.youtube-video-container');

					if (1 == container.data('foyer-video-wait-for-end')) {
						// We should wait for the end of the video before proceeding to the next slide

						// Set player reference
						var player = window.yt_players[container.attr('id')]

						var end = container.data('foyer-video-end');
						var duration = player.getDuration();
						var current_time = player.getCurrentTime();

						if ( duration < end || !end ) {
							end = duration;
						}

						console.log(current_time);

						if ( current_time >= end - foyer_ticker_css_transition_duration ) {
							// video almost ended, do not prevent next slide
						}
						else {
							// wait a bit more, prevent next slide
							console.log('prevented next');
							event.stopImmediatePropagation();
							event.preventDefault();

							// try again in 0.5 seconds
							setTimeout(function() {
								jQuery(foyer_slides_selector).trigger('slides:next-slide');
							}, 0.5 * 1000);
						}
					}
				});
			});

			jQuery('body').on('slide:became-active', foyer_slide_video_selector, function( event ) {
				console.log('became');

				// Set container
				var container = jQuery(this).find('.youtube-video-container');

				// Set player reference
				var player = window.yt_players[container.attr('id')]

				if (player) {
					// player exists
					console.log('play');

					// seek to start
					player.playVideo();
				}

				// Make sure the event is only triggered once
				event.stopImmediatePropagation();
			});

			jQuery('body').on('slide:left-active', foyer_slide_video_selector, function( event ) {
				console.log('left');

				// Set container
				var container = jQuery(this).find('.youtube-video-container');

				// Set player reference
				var player = window.yt_players[container.attr('id')]

				if (player) {
					console.log('pause');
					// Stop video whenever CSS transitions are over
					setTimeout(function() {
						player.seekTo(container.data('foyer-video-start'));
						player.pauseVideo();
					}, foyer_ticker_css_transition_duration * 1000);
				}

				// Make sure the event is only triggered once
				event.stopImmediatePropagation();
			});

			// 2. This code loads the IFrame Player API code asynchronously.
			var tag = document.createElement('script');

			tag.src = "https://www.youtube.com/iframe_api";
			var firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

			// 3. This function creates an <iframe> (and YouTube player)
			//    after the API code downloads.

			// Define a player storage object, to expose methods, without having to create a new class instance again.
			var yt_players = {};

			function onYouTubeIframeAPIReady() {
				jQuery(foyer_slide_video_selector).each(function() {
					var player_id = jQuery(this).find('.youtube-video-container').attr('id');
					var video_id = jQuery(this).find('.youtube-video-container').data('foyer-video-id');

					if (player_id && video_id) {
						window.yt_players[player_id] = new YT.Player(player_id, {
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
								'onReady': foyer_youtube_player_init(player_id),
							}
						});
					}
				});
				console.log(window.yt_players);
			}

			// 4. The API will call this function when the video player is ready.
			function foyer_youtube_player_init(player_id) {

				return function (event) {

					// Set container
					var container = jQuery('#' + player_id);

					// Set player reference
					var player = window.yt_players[player_id];

					player.mute();

					// trigger buffering so video is ready to play when needed
					player.seekTo(container.data('foyer-video-start'));

					if (! jQuery('#' + player_id).parents(foyer_slide_video_selector).hasClass('active')) {
						// when this video slide is not active, pause
						// so it can start playing whenever it becomes active
						player.pauseVideo();
					}
				}
			}
		</script>
	</div>
</div>