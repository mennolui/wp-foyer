(function( $ ) {
	'use strict';

	 $(function() {
		/* DOM is ready */

		setup_preview_actions();

	});


	/**
	 * Activates the preview action buttons.
	 *
	 * @since	1.0.0
	 * @return void
	 */
	function setup_preview_actions() {

		var $preview_actions = $('.foyer-preview-actions .foyer-orientation-button');
		var $preview = $('.foyer-preview');

		$preview_actions.on( 'click', function() {

			var orientation_choice = jQuery(this).attr('data-orientation');

			$preview_actions.removeClass('active');

			for( var orientation_key in foyer_preview.orientations ) {
				$preview.removeClass( 'foyer-preview-' + orientation_key );
			}

			$preview.addClass( 'foyer-preview-'+orientation_choice );

			jQuery(this).addClass('active');

			save_orientation_choice( orientation_choice );
		});
	}

	/**
	 * Submits the user's orientation choice for the current Display, Channel or Slide.
	 *
	 * @since	1.0.0
	 * @return 	void
	 */
	function save_orientation_choice( orientation ) {
		var data = {
			'action': 'foyer_preview_save_orientation_choice',
			'orientation': orientation,
			'object_id' : foyer_preview.object_id,
		};
		jQuery.post(foyer_preview.ajax_url, data );
	}
})( jQuery );