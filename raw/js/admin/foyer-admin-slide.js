/**
 * Initializes the slide background select on the slide admin page, with the saved background.
 *
 * @since	1.4.0
 *
 * @return 	void
 */
function init_slide_background_select() {
	var $slide_background_select;
	var slide_background;

	$slide_background_select = jQuery('#foyer_slide_content select[name=slide_background]');
	slide_background = jQuery('#foyer_slide_content select[name=slide_background]').val();

	update_slide_background_select();

	$slide_background_select.find('option[value="'+slide_background+'"]').attr('selected','selected');
}

/**
 * Hide/unhides slide background meta boxes on the slide admin page.
 *
 * @since	1.4.0
 *
 * @return 	void
 */
function update_slide_background_meta_boxes() {
	var $meta_boxes;
	var slide_format;

	$meta_boxes = jQuery('.foyer_slide_backgrounds > *');
	slide_background = jQuery('#foyer_slide_content select[name=slide_background]').val();

	$meta_boxes.hide().filter('#foyer_slide_background_'+slide_background).show();
}

/**
 * Rebuilds the slide background select on the slide admin page, for the selected slide format.
 *
 * @since	1.4.0
 *
 * @return 	void
 */
function update_slide_background_select() {
	var $slide_background_select;
	var slide_format;
	var slide_format_backgrounds;

	$slide_background_select = jQuery('#foyer_slide_content select[name=slide_background]');
	slide_format = jQuery('#foyer_slide_content select[name=slide_format]').val();
	slide_format_backgrounds = foyer_slide_formats_backgrounds[slide_format];

	$slide_background_select.empty();

	if (slide_format_backgrounds) {
		jQuery.each(slide_format_backgrounds, function(key, data) {
			$slide_background_select.append(
				jQuery('<option></option>').attr('value', key).text(data.title)
			);
		});
	}
}

/**
 * Hide/unhides slide format meta boxes on the slide admin page.
 *
 * @since	1.0.0
 * @since	1.4.0	Rewritten to work with the new content meta box that includes format and background selects and their content.
 *
 * @return 	void
 */
function update_slide_format_meta_boxes() {
	var $meta_boxes;
	var slide_format;

	$meta_boxes = jQuery('.foyer_slide_formats > *');
	slide_format = jQuery('#foyer_slide_content select[name=slide_format]').val();

	$meta_boxes.hide().filter('#foyer_slide_format_'+slide_format).show();
}

jQuery( function() {

	if (jQuery('#foyer_slide_content select[name=slide_format], #foyer_slide_content select[name=slide_background]').length) {
		// Hide/unhide meta boxes on page load.
		init_slide_background_select();
		update_slide_format_meta_boxes();
		update_slide_background_meta_boxes();
	}

	// Hide/unhide meta boxes if user selects another slide format or background.
	jQuery('#foyer_slide_content select[name=slide_format]').on('change', function() {
		update_slide_background_select();
		update_slide_format_meta_boxes();
		update_slide_background_meta_boxes();
	});
	jQuery('#foyer_slide_content select[name=slide_background]').on('change', function() {
		update_slide_background_meta_boxes();
	});

});

/**
 * Handle file uploads for slide file fields.
 *
 * @since	1.0.0
 * @since	1.1.3	Fixed an issue where adding an image to a slide was only possible when
 *					the image was already in the media library.
 * @since	1.5.2	Removed setting the width to auto on the preview image, sizing is now done with CSS.
 * @since	1.6.0	Limited the media selector to certain file types, based on newly added file_type_* classes.
 * @since	1.6.0	Renamed everything slide_image_* to slide_file_*, image_preview_url to file_preview_url.
 *					Displayed different texts in the media frame for each file type.
 *					Set the value of a new possible input to contain the attachment url.
 *					Triggered the change event after setting input values.
 *
 * Based on: http://jeroensormani.com/how-to-include-the-wordpress-media-selector-in-your-plugin/
 */
jQuery( function() {

	// Uploading files
	var wp_media_post_id;

	if (typeof window.wp !== 'undefined' && window.wp.media) {
		wp_media_post_id = wp.media.model.settings.post.id;

		jQuery('.slide_file_upload_button').on('click', function(event) {
			var slide_file_field;
			var file_frame;
			var file_type = 'image';

			if ( jQuery(this).parent('.slide_file_field').hasClass('file_type_video') ) {
				file_type = 'video';
			}
			if ( jQuery(this).parent('.slide_file_field').hasClass('file_type_pdf') ) {
				file_type = 'application/pdf';
			}

			event.preventDefault();

			slide_file_field = jQuery(this).parent();

			// If the media frame already exists, reopen it.
			if (file_frame) {

				// Open frame
				file_frame.open();
				return;
			}

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				title: foyer_slide_file_defaults[file_type].text_select,
				button: {
					text: foyer_slide_file_defaults[file_type].text_use
				},
				library: {
					type: file_type
				},
				multiple: false // Set to true to allow multiple files to be selected
			});

			// When an image is selected, run a callback.
			file_frame.on('select', function() {

				// We set multiple to false so only get one image from the uploader
				var attachment;
				attachment = file_frame.state().get('selection').first().toJSON();

				// Do something with attachment.id and/or attachment.url here
				var file_preview_url;

				if (typeof(attachment.sizes) !== 'undefined' && typeof(attachment.sizes.full.url) !== 'undefined') {
					file_preview_url = attachment.sizes.full.url;
				}
				else {
					file_preview_url = attachment.url;
				}

				slide_file_field.find('.slide_file_preview').attr('src', file_preview_url);

				slide_file_field.find('.slide_file_value_url').val(attachment.url);
				slide_file_field.find('.slide_file_value_url').trigger('change');

				slide_file_field.find('.slide_file_value').val(attachment.id);
				slide_file_field.find('.slide_file_value').trigger('change');

				// Restore the main post ID
				wp.media.model.settings.post.id = wp_media_post_id;

				slide_file_field.removeClass('empty');
			});

			// Finally, open the modal
			file_frame.open();
		});

		// Delete the selected image.
		jQuery('.slide_file_delete_button').on('click', function(event) {
			var slide_file_field;
			var file_frame;
			event.preventDefault();
			slide_file_field = jQuery(this).parent();

			slide_file_field.find('.slide_file_preview').attr('src', '');

			slide_file_field.find('.slide_file_value_url').val('');
			slide_file_field.find('.slide_file_value_url').trigger('change');

			slide_file_field.find('.slide_file_value').val('');
			slide_file_field.find('.slide_file_value').trigger('change');

			slide_file_field.addClass('empty');
		});

		// Restore the main ID when the add media button is pressed
		jQuery('a.add_media').on('click', function() {
			wp.media.model.settings.post.id = wp_media_post_id;
		});
    }
});
