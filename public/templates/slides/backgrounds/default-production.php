<?php
/**
 * Default slide background template for the production slide format.
 *
 * @since	1.4.0
 * @since	1.5.1	Refactored the template using the new background_image() method for responsive images.
 */

$slide = new Foyer_Slide( get_the_id() );

$production_id = get_post_meta( $slide->ID, 'slide_production_production_id', true );
$production = new WPT_Production( $production_id );

if ( ! empty( $production ) ) {

	$production_attachment_id = $production->thumbnail();

	if ( ! empty( $slide->get_background_image( $production_attachment_id ) ) ) {

		?><div<?php $slide->background_classes(); ?><?php $slide->background_data_attr(); ?>>
			<figure>
				<?php $slide->background_image( $production_attachment_id ); ?>
			</figure>
		</div><?php

	}
}
