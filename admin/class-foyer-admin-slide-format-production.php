<?php

/**
 * Adds admin functionality for the Production slide format.
 *
 * @since		1.1.0
 *
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Slide_Format_Production {

	/**
	 * Saves additional data for the Production slide format.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Improved validating & sanitizing of the user input.
	 * @since	1.1.0	Moved here from Foyer_Theater, and changed to static.
	 *
	 * @param	int		$post_id	The ID of the post being saved.
	 * @return	void
	 */
	static function save_slide_production( $post_id ) {
		$slide_production_production_id = intval( $_POST['slide_production_production_id'] );
		if ( empty( $slide_production_production_id ) ) {
			$slide_production_production_id = '';
		}

		$slide_production_image = intval( $_POST['slide_production_image'] );
		if ( empty( $slide_production_image ) ) {
			$slide_production_image = '';
		}

		update_post_meta( $post_id, 'slide_production_production_id', $slide_production_production_id );
		update_post_meta( $post_id, 'slide_production_image', $slide_production_image );
	}

	/**
	 * Outputs the meta box for the Production slide format.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped & sanitized the output.
	 * @since	1.1.0	Moved here from Foyer_Theater, and changed to static.
	 * @since	1.2.6	Changed the displayed name from Production to Event, same terminology as in Theater for WordPress.
	 *
	 * @param	WP_Post	$post	The post of the current slide.
	 * @return	void
	 */
	static function slide_production_meta_box( $post ) {

		global $wp_theatre;

		$slide_production_image = get_post_meta( $post->ID, 'slide_production_image', true );

		?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="slide_default_subtitle"><?php _ex( 'Event', 'slide-format', 'foyer' ); ?></label>
					</th>
					<td>
						<select name="slide_production_production_id">
							<option value=""></option><?php

							foreach ( $wp_theatre->productions->get() as $production ) {
								?><option value="<?php echo intval( $production->ID ); ?>" <?php selected( get_post_meta( $post->ID, 'slide_production_production_id', true ), $production->ID, true ); ?>><?php echo esc_html( $production->title() ); ?></option><?php
							}
						?></select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_default_subtitle"><?php _e( 'Background image', 'foyer' ); ?></label>
					</th>
					<td>
						<div class="slide_image_field<?php if ( empty( $slide_production_image ) ) { ?> empty<?php } ?>">
							<div class="image-preview-wrapper">
								<img class="slide_image_preview" src="<?php echo esc_url( wp_get_attachment_url( $slide_production_image ) ); ?>" height="100">
							</div>

							<input type="button" class="button slide_image_upload_button" value="<?php _e( 'Upload image', 'foyer' ); ?>" />
							<input type="button" class="button slide_image_delete_button" value="<?php _e( 'Remove image', 'foyer' ); ?>" />
							<input type="hidden" name="slide_production_image" class="slide_image_value" value='<?php echo intval( $slide_production_image ); ?>'>
						</div>
					</td>
				</tr>
			</tbody>
		</table><?php
	}
}
