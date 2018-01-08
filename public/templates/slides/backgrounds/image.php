<?php
/**
 * Image slide background template.
 *
 * @since	1.4.0
 */

$slide = new Foyer_Slide( get_the_id() );

$attachment_id = get_post_meta( $slide->ID, 'slide_bg_image_image', true );
$attachment_src = wp_get_attachment_image_src( $attachment_id, 'foyer_fhd_square' );

if ( ! empty( $attachment_src[0] ) ) {

	?><div<?php $slide->background_classes(); ?><?php $slide->background_data_attr();?>>
		<figure>
			<img src="<?php echo $attachment_src[0]; ?>" />
		</figure>
	</div><?php

}