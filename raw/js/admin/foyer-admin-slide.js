function update_slide_format_meta_boxes() {	
	var meta_boxes;
	var slide_format;
	
	meta_boxes = jQuery('.postbox[id*=foyer_slide_format_]');
	slide_format = jQuery('#foyer_slide_format input[name=slide_format]:checked').val();
	
	meta_boxes.hide().filter('#foyer_slide_format_'+slide_format).show();		
}

jQuery( function() {
	update_slide_format_meta_boxes();
	jQuery('#foyer_slide_format input[name=slide_format]').on('change', function() {
		update_slide_format_meta_boxes();
	});
});

jQuery( function() {
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
			if (file_frame) {
				file_frame.uploader.uploader.param('post_id', set_to_post_id);
				file_frame.open();
				return;
			} else {
				wp.media.model.settings.post.id = set_to_post_id;
			}
			file_frame = wp.media.frames.file_frame = wp.media({
				title: foyer_slide_format_default.text_select_photo,
				button: {
					text: foyer_slide_format_default.text_use_photo
				},
				multiple: false
			});
			file_frame.on('select', function() {
				var attachment;
				attachment = file_frame.state().get('selection').first().toJSON();
				slide_image_field.find('.slide_image_preview').attr('src', attachment.url).css('width', 'auto');
				slide_image_field.find('.slide_image_value').val(attachment.id);
				wp.media.model.settings.post.id = wp_media_post_id;
				slide_image_field.removeClass('empty');
			});
			file_frame.open();
		});
		jQuery('.slide_image_delete_button').on('click', function(event) {
			var slide_image_field;
			var file_frame;
			event.preventDefault();
			slide_image_field = jQuery(this).parent();
			slide_image_field.find('.slide_image_preview').attr('src', '');
			slide_image_field.find('.slide_image_value').val('');
			slide_image_field.addClass('empty');
		});
		jQuery('a.add_media').on('click', function() {
			wp.media.model.settings.post.id = wp_media_post_id;
		});
    }
});