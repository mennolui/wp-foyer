<?php
/**
 * Slide template.
 *
 * @since	1.0
 */
?>
<html>
	<head><?php
		wp_head(  );
	?></head>
	<body <?php body_class();?>>
		<div class="foyer-slide foyer-slide-<?php echo $slide->format(); ?>"><?php

			$slide = new Foyer_Slide( get_the_id() );
			Foyer_Templates::get_template('slides/'.$slide->format().'.php');

		?></div><?php
		wp_footer();
	?></body>
</html>
