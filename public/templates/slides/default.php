<?php
/**
 * Default slide template.
 *
 * @since	1.0
 */
 
$slide_image = get_post_meta( get_the_id(), 'slide_default_image', true );

if (!empty($slide_image)) {
	$slide_image_url = wp_get_attachment_image( $slide_image, 'foyer_default_slide_image' );
}

?><div class="inner">
	<figure><?php 
		if ( !empty($slide_image_url) ) {
			?><img src="<?php echo $slide_image_url; ?>" /><?php
		}
	?></figure>
</div>