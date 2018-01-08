<?php
/**
 * PDF slide format template.
 *
 * @since	1.1.0
 */

$slide = new Foyer_Slide( get_the_id() );

$slide_pdf_file = get_post_meta( get_the_id(), 'slide_pdf_file', true );
if ( ! empty( $slide_pdf_file ) ) {
	// PDF file exists

	$slide_images = get_post_meta( $slide_pdf_file, '_foyer_pdf_images', true );
	if ( ! empty( $slide_images ) ) {
		// PDF page images were generated

		$uploads = wp_upload_dir( null, false );

		foreach ( $slide_images as $slide_image ) {

			$slide_image_url = trailingslashit( $uploads['baseurl'] ) . $slide_image;

			?><div<?php $slide->classes(); ?><?php $slide->data_attr();?>>
				<div class="inner">
					<figure><?php
						if ( ! empty( $slide_image ) ) {
							?><img src="<?php echo esc_url( $slide_image_url ); ?>" /><?php
						}
					?></figure>
				</div>
				<?php $slide->background(); ?>
			</div><?php

		}
	}
}
