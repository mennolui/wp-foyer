<?php
/**
 * Default slide background template for the production slide format.
 *
 * @since	1.4.0
 * @since	1.5.1	Switched to using the new 'foyer' image size.
 *					Introduced image responsiveness by using wp_get_attachment_image.
 */

$slide = new Foyer_Slide( get_the_id() );

$production_id = get_post_meta( $slide->ID, 'slide_production_production_id', true );
$production = new WPT_Production( $production_id );

if ( ! empty( $production) && $production_attachment_id = $production->thumbnail() ) {

	?><div<?php $slide->background_classes(); ?><?php $slide->background_data_attr();?>>
		<figure>
			<?php echo wp_get_attachment_image( $production_attachment_id, 'foyer' ); ?>
		</figure>
	</div><?php

}
