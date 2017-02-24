<?php
/**
 * Channel template.
 *
 * @since	1.0.0
 */

global $post;

?><html>
	<head><?php
		wp_head( );
	?></head>
	<body <?php body_class();?>>
		<div class="foyer-channel">
			<div class="foyer-slides"><?php

				$channel = new Foyer_Channel( get_the_id() );

				foreach( $channel->get_slides() as $slide ) {

					$post = get_post( $slide->ID );
					setup_postdata( $post );

					?><div class="foyer-slide foyer-slide-<?php echo $slide->format(); ?>"><?php

						Foyer_Templates::get_template('slides/'.$slide->format().'.php');

					?></div><?php
				}

				wp_reset_postdata();
			?></div>
		</div><?php
		wp_footer();
	?></body>
</html>


