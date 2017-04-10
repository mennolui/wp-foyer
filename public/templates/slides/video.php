<?php
/**
 * Video slide format template.
 *
 * @since	1.0.0
 */

$slide = new Foyer_Slide( get_the_id() );

$video_url = get_post_meta( get_the_id(), 'slide_video_video_url', true );
$video_start = get_post_meta( get_the_id(), 'slide_video_video_start', true );
$video_end = get_post_meta( get_the_id(), 'slide_video_video_end', true );
$video_wait_for_end = get_post_meta( get_the_id(), 'slide_video_video_wait_for_end', true );

$video_id = substr( $video_url, strrpos( $video_url, '/' ) );

?><div<?php $slide->classes(); ?><?php $slide->data_attr();?>>
	<div class="inner">
		<div class="youtube-video-container" id="<?php echo uniqid(); ?>"
			data-foyer-video-id="<?php echo $video_id; ?>"
			data-foyer-video-start="<?php echo $video_start; ?>"
			data-foyer-video-end="<?php echo $video_end; ?>"
			data-foyer-video-wait-for-end="<?php echo $video_wait_for_end; ?>"
		></div>
	</div>
</div>