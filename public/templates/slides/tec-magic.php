<?php
/**
 * The Event Calendar Magic slide format template.
 */

$slide = new Foyer_Slide( get_the_id() );

$limit = intval( get_post_meta( $slide->ID, 'slide_tecm_limit', true ) );
$categories = get_post_meta( $slide->ID, 'slide_tecm_categories', true );
$venue =  intval( get_post_meta( $slide->ID, 'slide_tecm_venue', true ) );
$organizer =  intval( get_post_meta( $slide->ID, 'slide_tecm_organizer', true ) );

if ( empty( $categories ) ) {
	$categories = array();
}
else {
	$categories = array_map( 'intval', $categories );
}

$tec_args = array(
	'start_date' => 'now',
	'cat' => implode( ',', $categories ),
	'venue' => ($venue != 0) ? $venue : '',
	'organizer' =>( $organizer != 0) ? $organizer : '',
	'limit' => $limit,
	'context' => 'foyer_slide_tec_magic',
);
foreach ( tribe_get_events( $tec_args ) as $tec_magic ) {
	$include_year = true;
	if( tribe_event_is_multiday( $tec_magic->ID ) && ( tribe_get_start_date( $tec_magic->ID, true, 'Y' ) == tribe_get_display_end_date( $tec_magic->ID, true, 'Y' ) ) ) {
		$include_year = false;
	}

	if( tribe_event_is_all_day( $tec_magic->ID ) ) {
		$event_date = tribe_get_start_date( $tec_magic->ID, true, tribe_get_date_format( $include_year ) ) . ' - ' .
	              ( tribe_event_is_multiday( $tec_magic->ID ) ?
					  tribe_get_display_end_date( $tec_magic->ID, true, tribe_get_date_format( true ) ) : ''
				  );
	} else {
		$event_date = tribe_get_start_date( $tec_magic->ID, true, tribe_get_datetime_format( true ) ) . ' Uhr - ' .
					( tribe_event_is_multiday( $tec_magic->ID ) ?
					  tribe_get_display_end_date( $tec_magic->ID, true, tribe_get_datetime_format( true ) ) :
					  tribe_get_end_time( $tec_magic->ID, tribe_get_time_format( true ) )
					) . ' Uhr';
	}
    ?><div<?php $slide->classes(); ?><?php $slide->data_attr();?>>
		<div class="inner">
			<div class="foyer-slide-fields">
				<div class="foyer-slide-field foyer-slide-field-title"><?php echo $tec_magic->post_title; ?></div>
				<div class="foyer-slide-field foyer-slide-field-date"><?php echo $event_date; ?></div>
				<?php if (tribe_has_venue($tec_magic->ID)) { ?>
				<div class="foyer-slide-field foyer-slide-field-venue"><?php echo tribe_get_venue($tec_magic->ID); ?></div>
				<?php }
				if (tribe_has_organizer($tec_magic->ID)) { ?>
				<div class="foyer-slide-field foyer-slide-field-organizer"><?php echo tribe_get_organizer($tec_magic->ID); ?></div>
				<?php } ?>
			</div>
		</div><?php

		$background_args = array( 'tec_magic' => $tec_magic );
		$slide->background( $background_args );

	?></div><?php
}
