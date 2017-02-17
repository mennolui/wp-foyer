<?php
/**
 * Default slide template.
 *
 * @since	1.0
 */
?><div class="inner">
	<img src="<?php echo wp_get_attachment_url( get_post_meta( get_the_id(), 'slide_default_image', true ) ); ?>" />
</div>