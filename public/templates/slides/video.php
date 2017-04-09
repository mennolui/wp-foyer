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

			// Load YouTube IFrame Player API code asynchronously
			(function() { // Closure, to not leak to the scope
				var tag = document.createElement('script');
				tag.src = "https://www.youtube.com/iframe_api";
				var firstScriptTag = document.getElementsByTagName('script')[0];
				firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
			})();

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
		</script>
	</div>
</div>