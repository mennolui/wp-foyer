jQuery(document).ready(function() {

	foyer_display_setup_channel_scheduler();

});

function foyer_display_setup_channel_scheduler() {

	$start_datetime = jQuery('#foyer_channel_editor_scheduled_channel_start');
	$end_datetime = jQuery('#foyer_channel_editor_scheduled_channel_end');

	$start_datetime.foyer_datetimepicker({
		format: 'Y-m-d H:i',
		dayOfWeekStart : 1,
		step: 15,
	});

	$end_datetime.foyer_datetimepicker({
		format: 'Y-m-d H:i',
		dayOfWeekStart : 1,
		step: 15
	});

}
