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
		
		var $preview_actions = $('.foyer-preview-actions button');
		var $preview = $('.foyer-preview');
		
		$preview_actions.on( 'click', function() {
			$preview_actions.removeClass('active');
			if ($preview.hasClass('foyer-preview-9-16')) {
				$preview.removeClass('foyer-preview-9-16').addClass('foyer-preview-16-9');
			} else {
				$preview.removeClass('foyer-preview-16-9').addClass('foyer-preview-9-16');				
			}
			jQuery(this).addClass('active');
		});
	}

})( jQuery );