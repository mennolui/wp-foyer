<?php

/**
 * Adds admin functionality for the Default slide format.
 *
 * @since		1.3.1	Moved two methods from Foyer_Admin_Slide.
 *
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Slide_Format_Default {

	/**
	 * Saves the additional data of the default slide format.
	 *
	 * @since	1.0.0
	 * @since	1.0.1				Improved validating & sanitizing of the user input.
	 * @since	1.3.1				Moved to its own class, Foyer_Admin_Slide_Format_Default.
	 * @since	1.3.1				Removed saving of the slide_default_subtitle field, that was never implemented.
	 *
	 * @param 	int		$post_id	The Post ID of the slide being saved.
	 * @return 	void
	 */
	static function save_slide_default( $post_id ) {
		$slide_default_image = intval( $_POST['slide_default_image'] );
		if ( empty( $slide_default_image ) ) {
			$slide_default_image = '';
		}

		update_post_meta( $post_id, 'slide_default_image', $slide_default_image );
	}

	/**
	 * Outputs the meta box for the default slide format.
	 *
	 * @since	1.0.0
	 * @since	1.0.1			Escaped and sanitized the output.
	 * @since	1.3.1			Moved to its own class, Foyer_Admin_Slide_Format_Default.
	 * @since	1.3.1			Fixed a label that pointed to a non-existent field slide_default_subtitle, via for.
	 *
	 * @param 	WP_Post	$post	The post of the slide that is being edited.
	 * @return 	void
	 */
	static function slide_default_meta_box( $post ) {

		wp_enqueue_media();

		$slide_default_image = get_post_meta( $post->ID, 'slide_default_image', true );

		?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="slide_default_image"><?php esc_html_e('Background image', 'foyer'); ?></label>
					</th>
					<td>
						<div class="slide_image_field<?php if ( empty( $slide_default_image ) ) { ?> empty<?php } ?>">
							<div class="image-preview-wrapper">
								<img class="slide_image_preview" src="<?php echo esc_url( wp_get_attachment_url( $slide_default_image ) ); ?>" height="100">
							</div>

							<input type="button" class="button slide_image_upload_button" value="<?php esc_html_e( 'Upload image', 'foyer' ); ?>" />
							<input type="button" class="button slide_image_delete_button" value="<?php esc_html_e( 'Remove image', 'foyer' ); ?>" />
							<input type="hidden" name="slide_default_image" class="slide_image_value" value='<?php echo intval( $slide_default_image ); ?>'>
						</div>
					</td>
				</tr>
			</tbody>
		</table><?php
	}

}
