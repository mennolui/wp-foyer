<?php
/**
 * Video slide format template.
 *
 * @since	1.0.0
 */

$slide = new Foyer_Slide( get_the_id() );

?><div<?php $slide->classes(); ?><?php $slide->data_attr();?>>
	<div class="inner">
		<div class="video" id="youtube-player-1"></div>


	    <script>
	      // 2. This code loads the IFrame Player API code asynchronously.
	      var tag = document.createElement('script');

	      tag.src = "https://www.youtube.com/iframe_api";
	      var firstScriptTag = document.getElementsByTagName('script')[0];
	      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

	      // 3. This function creates an <iframe> (and YouTube player)
	      //    after the API code downloads.
	      var youtube_player_1;
	      function onYouTubeIframeAPIReady() {
	        youtube_player_1 = new YT.Player('youtube-player-1', {
	          width: '1920',
	          height: '1080',
	          videoId: 'r9tbusKyvMY',
	          events: {
	            'onReady': onPlayerReady,
	          }
	        });
	      }

	      // 4. The API will call this function when the video player is ready.
	      function onPlayerReady(event) {
	        event.target.playVideo();
	      }
	    </script>
	</div>
</div>