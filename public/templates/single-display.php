<?php
/**
 * Display template.
 *
 * @since	1.0.0
 */

global $post;

?><html>
	<head><?php
		wp_head( );
	?></head>
	<body <?php body_class();?>>
		<div class="display">
			<div class="channel">
				<div class="slides"><?php

					$display = new Foyer_Display( get_the_id() );
					$channel = new Foyer_Channel( $display->get_active_channel() );

					foreach( $channel->get_slides() as $slide ) {

						$post = get_post( $slide->ID );
						setup_postdata( $post );

						Foyer_Templates::get_template('slides/'.$slide->format().'.php');
					}

					wp_reset_postdata();
				?></div>
			</div>
		</div><?php
		wp_footer();
	?></body>
</html>


