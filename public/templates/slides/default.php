<?php
/**
 * Default slide format template.
 *
 * @since	1.0.0
 */

$slide = new Foyer_Slide( get_the_id() );

?><div<?php $slide->classes(); ?><?php $slide->data_attr();?>>
	<div class="inner">
		<figure>
			<img src="<?php $slide->image_url(); ?>" />
		</figure>
	</div>
</div>