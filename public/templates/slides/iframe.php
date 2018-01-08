<?php
/**
 * Iframe slide format template.
 *
 * @since	1.3.0
 */

$slide = new Foyer_Slide( get_the_id() );
$url = esc_url( get_post_meta( $slide->ID, 'slide_iframe_website_url', true ) );

?><div<?php $slide->classes(); ?><?php $slide->data_attr();?>>
	<div class="inner">
		<iframe src="<?php echo $url; ?>"></iframe>
	</div>
	<?php $slide->background(); ?>
</div>