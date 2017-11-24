<?php
/**
 * Production slide format template.
 *
 * @since	1.0.0
 * @since	1.0.1			Sanitized the output.
 */

$production = new WPT_Production( get_post_meta( get_the_id(), 'slide_production_production_id', true ));

$slide = new Foyer_Slide( get_the_id() );
$slide_image_url = $slide->get_image_url();

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

?><div<?php $slide->classes(); ?><?php $slide->data_attr();?>>
	<div class="inner">
		<figure><?php
			if ( ! empty( $slide_image_url ) ) {
				?><img src="<?php echo esc_url( $slide_image_url ); ?>" /><?php
			}
		?></figure>
		<div class="foyer-slide-fields">
			<h1><?php echo esc_html( $production->title() ); ?></h1>
			<div class="date"><?php echo $production->dates_html(); ?></div>
		</div>
	</div>
</div>