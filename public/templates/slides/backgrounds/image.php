<?php
/**
 * Image slide background template.
 *
 * @since	1.4.0
 * @since	1.5.1	Refactored the template using the new background_image() method for responsive images.
 */

$slide = new Foyer_Slide( get_the_id() );

$attachment_id = get_post_meta( $slide->ID, 'slide_bg_image_image', true );
$image = new Foyer_Image( $attachment_id );

if ( ! empty( $image ) ) {

	?><div<?php $slide->background_classes(); ?><?php $slide->background_data_attr(); ?>>
		<figure>
			<?php $image->html(); ?>
		</figure>
	</div><?php

}