<?php
/**
 * Production slide format template.
 *
 * @since	1.0.0
 * @since	1.0.1	Sanitized the output.
 * @since	1.4.0	Rewrote the template to work with slide background.
 */

$slide = new Foyer_Slide( get_the_id() );
$production_id = get_post_meta( $slide->ID, 'slide_production_production_id', true );
$production = new WPT_Production( $production_id );

?><div<?php $slide->classes(); ?><?php $slide->data_attr();?>>
	<div class="inner">
		<?php if ( ! empty( $production_id ) ) { ?>
			<div class="foyer-slide-fields">
				<h1><?php echo esc_html( $production->title() ); ?></h1>
				<div class="date"><?php echo $production->dates_html(); ?></div>
			</div>
		<?php } ?>
	</div>
	<?php $slide->background(); ?>
</div>