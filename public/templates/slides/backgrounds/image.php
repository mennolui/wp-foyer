<?php
/**
 * Image slide background template.
 *
 * @since	1.4.0
 * @since	1.5.1	Switched to using the new 'foyer' image size.
 *					Introduced image responsiveness by using wp_get_attachment_image.
 */

$slide = new Foyer_Slide( get_the_id() );

$attachment_id = get_post_meta( $slide->ID, 'slide_bg_image_image', true );

if ( ! empty( $attachment_id ) ) {

	?><div<?php $slide->background_classes(); ?><?php $slide->background_data_attr();?>>
		<figure>
			<?php echo wp_get_attachment_image( $attachment_id, 'foyer' ); ?>
		</figure>
	</div><?php

}