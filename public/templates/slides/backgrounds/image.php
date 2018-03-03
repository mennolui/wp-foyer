<?php
/**
 * Image slide background template.
 *
 * @since	1.4.0
 */

$slide = new Foyer_Slide( get_the_id() );

$attachment_id = get_post_meta( $slide->ID, 'slide_bg_image_image', true );
$attachment_img = wp_get_attachment_image( $attachment_id, 'foyer_fhd_square' );

if ( ! empty( $attachment_img ) ) {

	?><div<?php $slide->background_classes(); ?><?php $slide->background_data_attr();?>>
		<figure>
			<?php echo $attachment_img; ?>
		</figure>
	</div><?php

}