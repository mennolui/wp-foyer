<?php

/**
 * Adds admin functionality for the Events Calendar slide format.
 *
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 * @author		Thomas Göttgens <tgoettgens@gmail.com>
 */
class Foyer_Admin_Slide_Format_TEC {

	/**
	 * Saves additional data for the Events Calendar slide format.
	 *
	 * @param	int		$post_id	The ID of the post being saved.
	 * @return	void
	 */
	static function  save_slide_tec( $post_id ) {
		$slide_tec_event_id = intval( $_POST['slide_tec_event_id'] );
		if ( empty( $slide_tec_event_id ) ) {
			$slide_tec_event_id = '';
		}

		update_post_meta( $post_id, 'slide_tec_event_id', $slide_tec_event_id );
	}

	/**
	 * Outputs the meta box for the Events Calendar slide format.
	 *
	 * @param	WP_Post	$post	The post of the current slide.
	 * @return	void
	 */
	static function slide_tec_meta_box( $post ) {

	?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="slide_tec_event_id"><?php _ex( 'Event', 'slide-format', 'foyer' ); ?></label>
					</th>
					<td>
						<select name="slide_tec_event_id">
							<option value=""></option><?php

							foreach ( tribe_get_events( array( 'posts_per_page' => 999, 'start_date' => 'now') ) as $tec ) {
								?><option value="<?php echo intval( $tec->ID ); ?>" <?php selected( get_post_meta( $post->ID, 'slide_tec_event_id', true ), $tec->ID, true ); ?>><?php echo esc_html( $tec->post_title ); ?></option><?php
							}
						?></select>
					</td>
				</tr>
			</tbody>
		</table><?php
	}
}
