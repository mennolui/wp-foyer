<?php

/**
 * Adds admin functionality for the Upcoming Productions slide format.
 *
 * @since		1.7.0
 *
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Slide_Format_Upcoming_Productions {

	/**
	 * Saves additional data for the Upcoming Productions slide format.
	 *
	 * @since	1.7.0
	 *
	 * @param	int		$post_id	The ID of the post being saved.
	 * @return	void
	 */
	static function save_slide( $post_id ) {
		$slide_upcoming_productions_limit = intval( $_POST['slide_upcoming_productions_limit'] );
		if ( empty( $slide_upcoming_productions_limit ) ) {
			$slide_upcoming_productions_limit = '';
		}

		$slide_upcoming_productions_categories = '';
		if (
			! empty( $_POST['slide_upcoming_productions_categories'] ) &&
			! empty( $_POST['slide_upcoming_productions_categories'][0] )
		) {
			$slide_upcoming_productions_categories = array_map( 'intval', $_POST['slide_upcoming_productions_categories'] );
		}

		update_post_meta( $post_id, 'slide_upcoming_productions_limit', $slide_upcoming_productions_limit );
		update_post_meta( $post_id, 'slide_upcoming_productions_categories', $slide_upcoming_productions_categories );
	}

	/**
	 * Outputs the meta box for the Upcoming Productions slide format.
	 *
	 * @since	1.7.0
	 *
	 * @param	WP_Post	$post	The post of the current slide.
	 * @return	void
	 */
	static function slide_meta_box( $post ) {

		$slide_upcoming_productions_limit = intval( get_post_meta( $post->ID, 'slide_upcoming_productions_limit', true ) );

		$slide_upcoming_productions_categories = get_post_meta( $post->ID, 'slide_upcoming_productions_categories', true );
		if ( empty( $slide_upcoming_productions_categories ) ) {
			$slide_upcoming_productions_categories = array();
		}

		?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="slide_upcoming_productions_limit"><?php _e( 'Display a maximum of', 'foyer' ); ?></label>
					</th>
					<td>
						<input type="number" min="0" step="1" id="slide_upcoming_productions_limit" name="slide_upcoming_productions_limit" class="small-text" value="<?php echo $slide_upcoming_productions_limit; ?>" /> <?php _e( 'events', 'foyer' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_upcoming_productions_categories[]"><?php _e( 'Only display events from categories', 'foyer' ); ?></label>
					</th>
					<td>
						<select name="slide_upcoming_productions_categories[]" multiple><?php
							foreach ( get_categories( array( 'hide_empty' => false ) ) as $cat ) {
								?><option value="<?php echo intval( $cat->term_id ); ?>" <?php if ( in_array( $cat->term_id, $slide_upcoming_productions_categories ) ) { ?>selected<?php } ?>><?php echo esc_html( $cat->name ); ?></option><?php
							}
						?></select>
					</td>
				</tr>
			</tbody>
		</table><?php
	}
}
