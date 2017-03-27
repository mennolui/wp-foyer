/**
 * Hide/unhides slide format meta boxes on the slide admin page.
 *
 * @since	1.0.0
 * @return 	void
 */
function update_slide_format_meta_boxes() {
	var meta_boxes;
	var slide_format;

	meta_boxes = jQuery('.postbox[id*=foyer_slide_format_]');
	slide_format = jQuery('#foyer_slide_format input[name=slide_format]:checked').val();

	meta_boxes.hide().filter('#foyer_slide_format_'+slide_format).show();
}

jQuery( function() {

	// Hide/unhide meta boxes on page load.
	update_slide_format_meta_boxes();

	// Hide/unhide meta boxes if user selects another slide format.
	jQuery('#foyer_slide_format input[name=slide_format]').on('change', function() {
		update_slide_format_meta_boxes();
	});

});

/**
 * Handle file uploads for slide image fields
 * @since	1.0.0
 *
 * Based on: http://jeroensormani.com/how-to-include-the-wordpress-media-selector-in-your-plugin/
 */
jQuery( function() {

	// Uploading files
	var set_to_post_id
	var wp_media_post_id;

	if (wp.media) {
		wp_media_post_id = wp.media.model.settings.post.id;
		set_to_post_id = foyer_slide_format_default.photo;
		jQuery('.slide_image_upload_button').on('click', function(event) {
			var slide_image_field;
			var file_frame;
			event.preventDefault();
			slide_image_field = jQuery(this).parent();

			// If the media frame already exists, reopen it.
			if (file_frame) {

				// Set the post ID to what we want
				file_frame.uploader.uploader.param('post_id', set_to_post_id);

				// Open frame
				file_frame.open();
				return;
			} else {
				// Set the wp.media post id so the uploader grabs the ID we want when initialised
				wp.media.model.settings.post.id = set_to_post_id;
			}

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				title: foyer_slide_format_default.text_select_photo,
				button: {
					text: foyer_slide_format_default.text_use_photo
				},
				multiple: false // Set to true to allow multiple files to be selected
			});

			// When an image is selected, run a callback.
			file_frame.on('select', function() {

				// We set multiple to false so only get one image from the uploader
				var attachment;
				attachment = file_frame.state().get('selection').first().toJSON();

				// Do something with attachment.id and/or attachment.url here
				slide_image_field.find('.slide_image_preview').attr('src', attachment.sizes.full.url).css('width', 'auto');
				slide_image_field.find('.slide_image_value').val(attachment.id);

				// Restore the main post ID
				wp.media.model.settings.post.id = wp_media_post_id;

				slide_image_field.removeClass('empty');
			});

			// Finally, open the modal
			file_frame.open();
		});

		// Delete the selected image.
		jQuery('.slide_image_delete_button').on('click', function(event) {
			var slide_image_field;
			var file_frame;
			event.preventDefault();
			slide_image_field = jQuery(this).parent();
			slide_image_field.find('.slide_image_preview').attr('src', '');
			slide_image_field.find('.slide_image_value').val('');
			slide_image_field.addClass('empty');
		});

		// Restore the main ID when the add media button is pressed
		jQuery('a.add_media').on('click', function() {
			wp.media.model.settings.post.id = wp_media_post_id;
		});
    }
});