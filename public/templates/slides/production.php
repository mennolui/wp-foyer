<?php
/**
 * Production slide template.
 *
 * @since	1.0.0
 */

$production = new WPT_Production( get_post_meta( get_the_id(), 'slide_production_production_id', true ));

$slide = new Foyer_Slide( get_the_id() );
$slide_image_url = $slide->image();

// Fallback to production image if slide image is not set or not found.
if ( empty( $slide_image_url ) ) {
	$production_attachment_id = $production->thumbnail();
	if ( ! empty( $production_attachment_id ) ) {
		$production_attachment_src = wp_get_attachment_image_src( $production_attachment_id, 'foyer_fhd_square' );
		if ( ! empty ( $production_attachment_src[0] ) ) {
			$slide_image_url = $production_attachment_src[0];
		}
	}
}

?><div<?php $slide->class(); ?><?php $slide->data();?>>
	<div class="inner">
		<figure><?php
			if ( ! empty( $slide_image_url ) ) {
				?><img src="<?php echo $slide_image_url; ?>" /><?php
			}
		?></figure>
		<div class="foyer-slide-fields">
			<h1><?php echo $production->title(); ?></h1>
		</div>
	</div>
</div>