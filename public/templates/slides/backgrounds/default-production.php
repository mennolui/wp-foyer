<?php
/**
 * Default slide background template for the production slide format.
 *
 * @since	1.4.0
 */

$slide = new Foyer_Slide( get_the_id() );

$production_id = get_post_meta( $slide->ID, 'slide_production_production_id', true );
$production = new WPT_Production( $production_id );

if ( ! empty( $production ) ) {

	$production_attachment_id = $production->thumbnail();

	if ( ! empty( $production_attachment_id ) ) {

		$production_attachment_src = wp_get_attachment_image_src( $production_attachment_id, 'foyer_fhd_square' );

		if ( ! empty( $production_attachment_src[0] ) ) {

			?><div<?php $slide->background_classes(); ?><?php $slide->background_data_attr();?>>
				<figure>
					<img src="<?php echo $production_attachment_src[0]; ?>" />
				</figure>
			</div><?php

		}
	}
}
