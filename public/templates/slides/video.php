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
$hold_slide = get_post_meta( get_the_id(), 'slide_video_hold_slide', true );

$video_id = substr( $video_url, strrpos( $video_url, '/' ) );

?><div<?php $slide->classes(); ?><?php $slide->data_attr();?>>
	<div class="inner">
		<div class="youtube-video-container" id="<?php echo uniqid(); ?>"
			data-foyer-video-id="<?php echo $video_id; ?>"
			data-foyer-video-start="<?php echo $video_start; ?>"
			data-foyer-video-end="<?php echo $video_end; ?>"
			data-foyer-hold-slide="<?php echo $hold_slide; ?>"
		></div>
	</div>
</div>