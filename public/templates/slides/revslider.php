<?php
/**
 * Revslider slide format template.
 *
 * @since	1.6.0
 */

$slide = new Foyer_Slide( get_the_id() );

$slider_id = get_post_meta( $slide->ID, 'slide_revslider_slider_id', true );

?><div<?php $slide->classes(); ?><?php $slide->data_attr();?>>
	<div class="inner">
		<?php if ( ! empty( $slider_id ) ) { ?>
			<?php Foyer_Revslider::output_slider( $slider_id ); ?>
		<?php } ?>
	</div>
	<?php $slide->background(); ?>
</div>