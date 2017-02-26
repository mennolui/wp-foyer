<?php
/**
 * Slide template.
 *
 * @since	1.0
 */

$slide = new Foyer_Slide( get_the_id() );

?><html>
	<head><?php
		wp_head( );
	?></head>
	<body <?php body_class();?>>
		<div class="foyer-preview-9-16">

			<div class="foyer-slide foyer-slide-<?php echo $slide->format(); ?>"><?php

				$first_slide_class = '';
				Foyer_Templates::get_template('slides/'.$slide->format().'.php');

			?></div>

		</div><?php
		wp_footer();
	?></body>
</html>


