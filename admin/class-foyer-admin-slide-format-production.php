<?php

/**
 * Adds admin functionality for the Production slide format.
 *
 * @since		1.1.0
 *
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Slide_Format_Production {

	/**
	 * Saves additional data for the Production slide format.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Improved validating & sanitizing of the user input.
	 * @since	1.1.0	Moved here from Foyer_Theater, and changed to static.
	 * @since	1.4.0	Removed saving of slide_production_image since background images are now handled by slide backgrounds.
	 *
	 * @param	int		$post_id	The ID of the post being saved.
	 * @return	void
	 */
	static function save_slide_production( $post_id ) {
		$slide_production_production_id = intval( $_POST['slide_production_production_id'] );
		if ( empty( $slide_production_production_id ) ) {
			$slide_production_production_id = '';
		}

		update_post_meta( $post_id, 'slide_production_production_id', $slide_production_production_id );
	}

	/**
	 * Outputs the meta box for the Production slide format.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped & sanitized the output.
	 * @since	1.1.0	Moved here from Foyer_Theater, and changed to static.
	 * @since	1.2.6	Changed the displayed name from Production to Event, same terminology as in Theater for WordPress.
	 * @since	1.3.1	Fixed two labels that pointed to a non-existent field slide_default_subtitle, via for.
	 * @since	1.4.0	Removed the slide_production_image admin field since background images are now handled by slide backgrounds.
	 *
	 * @param	WP_Post	$post	The post of the current slide.
	 * @return	void
	 */
	static function slide_production_meta_box( $post ) {

		global $wp_theatre;

		?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="slide_production_production_id"><?php _ex( 'Event', 'slide-format', 'foyer' ); ?></label>
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
			</tbody>
		</table><?php
	}
}
