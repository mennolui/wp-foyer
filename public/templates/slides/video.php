<?php
/**
 * Video slide format template.
 *
 * @since	1.0.0
 */

$slide = new Foyer_Slide( get_the_id() );

?><div<?php $slide->classes(); ?><?php $slide->data_attr();?>>
	<div class="inner">
		<div class="youtube-video-container" id="<?php echo uniqid(); ?>"
			data-foyer-video-id="r9tbusKyvMY"
			data-foyer-video-start="35"
			data-foyer-video-end="60"
			data-foyer-video-wait-for-end="1"
		></div>
	</div>
</div>