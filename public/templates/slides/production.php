<?php
	$production = new WPT_Production( get_post_meta( get_the_id(), 'slide_production_production_id', true ));
?><div class="still">
	<h1><?php echo $production->title(); ?></h1>
	<img src="<?php echo wp_get_attachment_url( get_post_meta( $post->ID, 'slide_production_image', true ) ); ?>" />
</div>