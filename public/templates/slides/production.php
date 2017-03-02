<?php
/**
 * Production slide template.
 *
 * @since	1.0
 */

$production = new WPT_Production( get_post_meta( get_the_id(), 'slide_production_production_id', true ));

$slide_image = get_post_meta( get_the_id(), 'slide_production_image', true );

if ( !empty( $slide_image ) ) {
	$slide_image_url = wp_get_attachment_image( $slide_image, 'foyer_default_slide_image' );
}

// Fallback to production image if slide image is not set or not found.
if ( empty( $slide_image_url ) ) {
	$slide_image = $production->thumbnail();
	if ( !empty( $slide_image ) ) {
		$slide_image_url = wp_get_attachment_image( $slide_image, 'foyer_default_slide_image' );
	}
}

?><div class="inner">
	<figure><?php
		if (!empty( $slide_image_url )) {
			?><img src="<?php echo $slide_image_url; ?>" /><?php
		}		
	?></figure>
	<div class="foyer-slide-fields">
		<h1><?php echo $production->title(); ?></h1>
	</div>
</div>