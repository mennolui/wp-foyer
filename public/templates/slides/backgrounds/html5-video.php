<?php
/**
 * HTML5 Video slide background template.
 *
 * @since	1.6.0
 */

$slide = new Foyer_Slide( get_the_id() );

$video_url = get_post_meta( $slide->ID, 'slide_bg_html5_video_video_url', true );
$video_start = get_post_meta( $slide->ID, 'slide_bg_html5_video_video_start', true );
$video_end = get_post_meta( $slide->ID, 'slide_bg_html5_video_video_end', true );
$hold_slide = get_post_meta( $slide->ID, 'slide_bg_html5_video_hold_slide', true );
$enable_sound = get_post_meta( $slide->ID, 'slide_bg_html5_video_enable_sound', true );

if ( ! empty( $video_url ) ) {

	?><div<?php $slide->background_classes(); ?><?php $slide->background_data_attr();?>>
		<div class="html5-video-container"
			data-foyer-video-start="<?php echo $video_start; ?>"
			data-foyer-video-end="<?php echo $video_end; ?>"
			data-foyer-hold-slide="<?php echo $hold_slide; ?>"
			data-foyer-output-sound="<?php echo $enable_sound; ?>"
		>
			<video preload="auto" playsinline muted data-object-fit>
				<source src="<?php echo $video_url; ?>">
			</video>
		</div>
	</div><?php

}
