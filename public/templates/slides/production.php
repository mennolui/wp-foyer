<?php
/**
 * Production slide template.
 *
 * @since	1.0
 */

$production = new WPT_Production( get_post_meta( get_the_id(), 'slide_production_production_id', true ));

?><div class="inner">
	<figure>
		<img src="<?php echo wp_get_attachment_url( get_post_meta( get_the_id(), 'slide_production_image', true ) ); ?>" />
	</figure>
	<div class="foyer-slide-fields">
		<h1><?php echo $production->title(); ?></h1>
	</div>
</div>