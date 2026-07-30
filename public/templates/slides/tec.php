<?php
/**
 * The Event Calendar slide format template.
 */

$slide = new Foyer_Slide( get_the_id() );

$slide_tec_event_id = intval( get_post_meta( $slide->ID, 'slide_tec_event_id', true ) );

$tec= tribe_get_event( $slide_tec_event_id  );
$event_date = tribe_get_start_date( $tec->ID, !tribe_event_is_all_day( $tec->ID ), tribe_get_date_format( true ) ) . ' - ' .
			  ( tribe_event_is_multiday( $tec->ID ) ?
				tribe_get_display_end_date( $tec->ID, !tribe_event_is_all_day( $tec->ID ), tribe_get_date_format( true ) ) :
				tribe_get_end_time( $tec->ID, tribe_get_date_format( true ) )
			  );
?><div<?php $slide->classes(); ?><?php $slide->data_attr();?>>
	<div class="inner">
		<div class="foyer-slide-fields">
			<div class="foyer-slide-field foyer-slide-field-title"><?php echo $tec->post_title; ?></div>
			<div class="foyer-slide-field foyer-slide-field-date"><?php echo $event_date; ?></div>
			<?php if (tribe_has_venue($tec->ID)) { ?>
			<div class="foyer-slide-field foyer-slide-field-venue"><?php echo tribe_get_venue($tec->ID); ?></div>
			<?php }
			if (tribe_has_organizer($tec->ID)) { ?>
			<div class="foyer-slide-field foyer-slide-field-organizer"><?php echo tribe_get_organizer($tec->ID); ?></div>
			<?php } ?>
		</div>
	</div><?php

	$background_args = array( 'tec' => $tec );
	$slide->background( $background_args );

?></div>
	