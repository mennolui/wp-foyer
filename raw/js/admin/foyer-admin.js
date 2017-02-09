// @codekit-prepend "foyer-admin-slide.js";

(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	 $(function() {
		 /* DOM is ready */

		 $('.foyer_slides_editor_form_action_remove').unbind('click').click(function(e) {

				if (confirm(foyer_slides_editor_defaults.confirm_remove_message)) {

					var data = {
						'action': 'foyer_slides_editor_remove_slide',
						'channel_id': $(e.currentTarget).parents('table').data('channel-id'),
						'slide_key': $(e.currentTarget).parents('tr').data('slide-key'),
						'nonce': foyer_slides_editor_security.nonce,
					};

					$.post(ajaxurl, data, function(response) {
						if (response != '') {
							$('*[data-slide-key="'+response+'"]').remove();
						}
					});

				}

				return false;


		 });
		 
	});


})( jQuery );
