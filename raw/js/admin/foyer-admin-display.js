jQuery(document).ready(function() {

	foyer_display_setup_channel_scheduler();

});

function foyer_display_setup_channel_scheduler() {

	var foyer_channel_scheduler_datetime_format = 'Y-m-d H:i';
	var foyer_channel_scheduler_duration = 1 * 60 * 60; // one hour
	var foyer_channel_scheduler_locale = 'nl';

	jQuery.foyer_datetimepicker.setLocale(foyer_channel_scheduler_locale);

	$start_datetime = jQuery('#foyer_channel_editor_scheduled_channel_start');
	$end_datetime = jQuery('#foyer_channel_editor_scheduled_channel_end');

	$start_datetime.foyer_datetimepicker({
		format: foyer_channel_scheduler_datetime_format,
		dayOfWeekStart : 1,
		lang: 'nl',
		step: 15,
		onChangeDateTime: function(start) {
			if (start) {
				if (!$end_datetime.val() || new Date($end_datetime.val()) < start) {
					var new_end = new Date(start.getTime() + foyer_channel_scheduler_duration * 1000)
					$end_datetime.val(new_end.dateFormat(foyer_channel_scheduler_datetime_format));
				}
			}
		}
	});

	$end_datetime.foyer_datetimepicker({
		format: foyer_channel_scheduler_datetime_format,
		dayOfWeekStart : 1,
		step: 15
	});

}
