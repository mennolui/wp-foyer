<?php

global $post;

$channel = new Foyer_Channel( get_the_id() );
?><div class="foyer-channel foyer-channel-<?php echo $channel->ID; ?> foyer-transition-<?php echo $channel->get_slides_transition(); ?>">
	<div class="foyer-slides"><?php

		foreach( $channel->get_slides() as $slide ) {

			$post = get_post( $slide->ID );
			setup_postdata( $post );

			Foyer_Templates::get_template('partials/slide.php');

			wp_reset_postdata();
		}

	?></div>
</div><?php