<?php
/**
 * Upcoming Productions slide format template.
 *
 * @since	1.7.0
 * @since	1.7.3	Fixed an issue where developers could not use HTML in the production title.
 */

global $wp_theatre;

$slide = new Foyer_Slide( get_the_id() );

$limit = intval( get_post_meta( $slide->ID, 'slide_upcoming_productions_limit', true ) );
$categories = get_post_meta( $slide->ID, 'slide_upcoming_productions_categories', true );

// Prepare categories for Theater productions query
if ( empty( $categories ) ) {
	$categories = array();
}
else {
	$categories = array_map( 'intval', $categories );
}

$production_args = array(
	'end_after' => 'now',
	'cat' => implode( ',', $categories ),
	'limit' => $limit,
	'context' => 'foyer_slide_upcoming_productions',
);

foreach ( $wp_theatre->productions->get( $production_args ) as $production ) {

	?><div<?php $slide->classes(); ?><?php $slide->data_attr();?>>
		<div class="inner">
			<div class="foyer-slide-fields">
				<div class="foyer-slide-field foyer-slide-field-title"><?php echo $production->title(); ?></div>
				<div class="foyer-slide-field foyer-slide-field-date"><?php echo $production->dates_html(); ?></div>
			</div>
		</div><?php

		$background_args = array( 'production' => $production );
		$slide->background( $background_args );

	?></div><?php
}
