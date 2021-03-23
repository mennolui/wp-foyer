<?php
/**
 * Default slide background template for the Events Calendar Magic slide format.
 */

$slide = new Foyer_Slide( get_the_id() );

if ( ! empty( $tec_magic ) && $tec_attachment_id = get_post_thumbnail_id($tec_magic->ID) ) {
	?><div<?php $slide->background_classes(); ?><?php $slide->background_data_attr();?>>
		<figure>
			<?php echo wp_get_attachment_image( $tec_attachment_id, 'foyer' ); ?>
		</figure>
	</div><?php

}
