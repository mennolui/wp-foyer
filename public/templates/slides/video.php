<?php
/**
 * Video slide format template.
 *
 * @since	1.0.0
 */

$slide = new Foyer_Slide( get_the_id() );

?><div<?php $slide->classes(); ?><?php $slide->data_attr();?>>
	<div class="inner">
		<?php echo apply_filters( 'the_content', 'https://www.youtube.com/watch?v=r9tbusKyvMY' ); ?>
	</div>
</div>