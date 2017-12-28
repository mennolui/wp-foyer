<?php

/**
 * Adds admin functionality for the Image slide background.
 *
 * Functionality was copied from Foyer_Admin_Slide_Format_Video (removed in 1.4.0).
 *
 * @since		1.4.0
 *
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Slide_Background_Image {

	/**
	 * Saves the additional data of the Image slide background.
	 *
	 * @since	1.4.0
	 *
	 * @param 	int		$post_id	The Post ID of the slide being saved.
	 * @return 	void
	 */
	static function save_slide_background( $post_id ) {
		$slide_bg_image_image = intval( $_POST['slide_bg_image_image'] );
		if ( empty( $slide_bg_image_image ) ) {
			$slide_bg_image_image = '';
		}

		update_post_meta( $post_id, 'slide_bg_image_image', $slide_bg_image_image );
	}

	/**
	 * Outputs the meta box for the Image slide background.
	 *
	 * @since	1.4.0
	 *
	 * @param 	WP_Post	$post	The post of the slide that is being edited.
	 * @return 	void
	 */
	static function slide_background_meta_box( $post ) {

		wp_enqueue_media();

		$slide_bg_image_image = get_post_meta( $post->ID, 'slide_bg_image_image', true );

		?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="slide_bg_image_image"><?php esc_html_e( 'Background image', 'foyer' ); ?></label>
					</th>
					<td>
						<div class="slide_image_field<?php if ( empty( $slide_bg_image_image ) ) { ?> empty<?php } ?>">
							<div class="image-preview-wrapper">
								<img class="slide_image_preview" src="<?php echo esc_url( wp_get_attachment_url( $slide_bg_image_image ) ); ?>" height="100">
							</div>

							<input type="button" class="button slide_image_upload_button" value="<?php esc_html_e( 'Upload image', 'foyer' ); ?>" />
							<input type="button" class="button slide_image_delete_button" value="<?php esc_html_e( 'Remove image', 'foyer' ); ?>" />
							<input type="hidden" name="slide_bg_image_image" class="slide_image_value" value='<?php echo intval( $slide_bg_image_image ); ?>'>
						</div>
					</td>
				</tr>
			</tbody>
		</table><?php
	}

}
