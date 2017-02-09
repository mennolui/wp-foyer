<?php
	
$display = new Foyer_Display( get_the_id() );
$channel = new Foyer_Channel( $display->get_active_channel() );

?><div class="channel"><?php	

	foreach( $channel->get_slides() as $slide ) {
		
		global $post;
		$post = get_post( $slide->ID );
		setup_postdata( $post );
		
		$slide_format = Foyer_Slides::get_slide_format_for_slide( $slide->ID );
	
		Foyer_Templates::get_template('slides/'.$slide->format().'.php');
		
	}
	
	wp_reset_postdata(  );
?></div>