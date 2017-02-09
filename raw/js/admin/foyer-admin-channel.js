(function( $ ) {
	'use strict';

	 $(function() {
		/* DOM is ready */

		setup_slides_editor_actions();

	});


	/* Slides editor */

	function setup_slides_editor_actions() {

		var $slides_editor = $('.foyer_slides_editor');
		var $slides_editor_slides = $('.foyer_slides_editor_slides');

		/* Set up add action in the slides editor */

		$('.foyer_slides_editor_add_select').unbind('change').change(function(e) {

			var slide_id = $(e.currentTarget).val();

			if ( slide_id > 0 ) {

				var data = {
					'action': 'foyer_slides_editor_add_slide',
					'channel_id': $slides_editor.data('channel-id'),
					'slide_id': slide_id,
					'nonce': foyer_slides_editor_security.nonce,
				};

				$.post(ajaxurl, data, function(response) {
					if (response != '') {
						$slides_editor_slides.replaceWith(response);
						$('.foyer_slides_editor_add_select').val('');
						setup_slides_editor_actions();
					}
				});

			}

			return false;

		});

		/* Set up remove action on slides in the slides editor */

		$('.foyer_slides_editor_slides_slide_remove').unbind('click').click(function(e) {

			if (confirm(foyer_slides_editor_defaults.confirm_remove_message)) {

				var data = {
					'action': 'foyer_slides_editor_remove_slide',
					'channel_id': $slides_editor.data('channel-id'),
					'slide_key': $(e.currentTarget).parents('.foyer_slides_editor_slides_slide').data('slide-key'),
					'nonce': foyer_slides_editor_security.nonce,
				};

				$.post(ajaxurl, data, function(response) {
					if (response != '') {
						$slides_editor_slides.replaceWith(response);
						setup_slides_editor_actions();
					}
				});

			}

			return false;

		});

		/* Set up jQuery UI sortable on slides in the slides editor */

		$slides_editor_slides.sortable({
			revert: 100,
			update: function( event, ui ) {
				// This event is triggered when the user stopped sorting and the DOM position has changed.

				// Find the reordered items, and store their post_id's in an array
				var slides = [];
				$(this).children().each(function() {
					slides[slides.length] = $(this).data('slide-id');
				});

				var data = {
					'action': 'foyer_slides_editor_reorder_slides', // Tell WordPress how to handle this ajax request
					'channel_id': $slides_editor.data('channel-id'), // The channel id
					'slide_ids': slides, // The post_id's of the reordered items, as string
					'nonce': foyer_slides_editor_security.nonce,
				};

				$.post(ajaxurl, data, function(response) {
					if (response != '') {
						$slides_editor_slides.replaceWith(response);
						setup_slides_editor_actions();
					}
				});

			}
		});

		$slides_editor_slides.disableSelection();

	}

})( jQuery );
