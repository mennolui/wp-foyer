<?php
/**
 * YouTube Video slide background template.
 *
 * @since	1.4.0
 * @since	1.5.1	Removed the container's unique ID attribute. Our JS no longer relies on this unique ID
 *					as this failed when page caching was enabled. Fixes issue #15.
 */

$slide = new Foyer_Slide( get_the_id() );

$video_url = get_post_meta( $slide->ID, 'slide_bg_video_video_url', true );
$video_start = get_post_meta( $slide->ID, 'slide_bg_video_video_start', true );
$video_end = get_post_meta( $slide->ID, 'slide_bg_video_video_end', true );
$hold_slide = get_post_meta( $slide->ID, 'slide_bg_video_hold_slide', true );
$enable_sound = get_post_meta( $slide->ID, 'slide_bg_video_enable_sound', true );

// URL is saved in format https://youtu.be/r9tbusKyvMY
// We need the ID, the last bit
$video_id = substr( $video_url, strrpos( $video_url, '/' ) + 1 );

if ( ! empty( $video_id ) ) {

	?><div<?php $slide->background_classes(); ?><?php $slide->background_data_attr();?>>
		<div class="youtube-video-container"
			data-foyer-video-id="<?php echo $video_id; ?>"
			data-foyer-video-start="<?php echo $video_start; ?>"
			data-foyer-video-end="<?php echo $video_end; ?>"
			data-foyer-hold-slide="<?php echo $hold_slide; ?>"
			data-foyer-output-sound="<?php echo $enable_sound; ?>"
		></div>
	</div><?php

}
