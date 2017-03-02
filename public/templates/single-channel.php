<?php
/**
 * Channel template.
 *
 * @since	1.0.0
 */

global $post;

$channel = new Foyer_Channel( get_the_id() );

?><html>
	<head><?php
		wp_head( );
	?></head>
	<body <?php body_class();?>>
		<div class="foyer-preview-9-16">

			<div class="foyer-channel foyer-channel-<?php echo $channel->ID; ?> foyer-transition-<?php echo $channel->get_slides_transition(); ?>">
				<div class="foyer-slides"><?php

					$first_slide_class = 'next';
					foreach( $channel->get_slides() as $slide ) {

						$post = get_post( $slide->ID );
						setup_postdata( $post );

						?><div class="foyer-slide foyer-slide-<?php echo $slide->format(); ?> <?php echo $first_slide_class; ?>"
							data-foyer-slide-duration="<?php echo $channel->get_slides_duration(); ?>"><?php

							$first_slide_class = '';
							Foyer_Templates::get_template('slides/'.$slide->format().'.php');

						?></div><?php
					}

					wp_reset_postdata();
				?></div>
			</div>

		</div><?php
		wp_footer();
	?></body>
</html>


