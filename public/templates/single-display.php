<?php
/**
 * Display template.
 *
 * @since	1.0.0
 */

global $post;

$display = new Foyer_Display( get_the_id() );
$channel = new Foyer_Channel( $display->get_active_channel() );

?><html>
	<head><?php
		wp_head();
	?>
	</head>
	<body <?php body_class(); ?>><?php
		?><div class="foyer-display">
			<div class="foyer-channel foyer-channel-<?php echo $channel->ID; ?> foyer-transition-slide">
				<div class="foyer-slides"><?php

					$first_slide_class = 'next';
					foreach( $channel->get_slides() as $slide ) {

						$post = get_post( $slide->ID );
						setup_postdata( $post );

						?><div class="foyer-slide foyer-slide-<?php echo $slide->format(); ?> <?php echo $first_slide_class; ?>"
							data-foyer-slide-duration="2.5"><?php

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
