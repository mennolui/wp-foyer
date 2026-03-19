<?php

/**
 * Adds admin functionality for the Events Calendar Magic slide format.
 *
 * @since		1.7.0
 *
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 * @author		Thomas Göttgens <tgoettgens@gmail.com>
 */
class Foyer_Admin_Slide_Format_TEC_Magic {

	/**
	 * Saves additional data for the Events Calendar Magic slide format.
	 *
	 * @param	int		$post_id	The ID of the post being saved.
	 * @return	void
	 */
	static function save_slide( $post_id ) {
		$slide_tecm_limit = intval( $_POST['slide_tecm_limit'] );
		if ( empty( $slide_tecm_limit ) ) {
			$slide_tecm_limit = '';
		}

		$slide_tecm_categories = '';
		if (
			! empty( $_POST['slide_tecm_categories'] ) &&
			! empty( $_POST['slide_tecm_categories'][0] )
		) {
			$slide_tecm_categories = array_map( 'intval', $_POST['slide_tecm_categories'] );
		}
		
		$slide_tecm_venue = intval ( $_POST['slide_tecm_venue'] );
		if ( empty( $slide_tecm_venue ) ) {
			$slide_tecm_venue = '';
		}
		
		$slide_tecm_organizer = intval ( $_POST['slide_tecm_organizer'] );
		if ( empty( $slide_tecm_organizer ) ) {
			$slide_tecm_organizer = '';
		}

		update_post_meta( $post_id, 'slide_tecm_limit', $slide_tecm_limit );
		update_post_meta( $post_id, 'slide_tecm_categories', $slide_tecm_categories );
		update_post_meta( $post_id, 'slide_tecm_venue', $slide_tecm_venue );
		update_post_meta( $post_id, 'slide_tecm_organizer', $slide_tecm_organizer );
	}

	/**
	 * Outputs the meta box for the Events Calendar Magic slide format.
	 *
	 * @param	WP_Post	$post	The post of the current slide.
	 * @return	void
	 */
	static function slide_meta_box( $post ) {

		$slide_tecm_limit = intval( get_post_meta( $post->ID, 'slide_tecm_limit', true ) );
		$slide_tecm_venue = intval(  get_post_meta( $post->ID, 'slide_tecm_venue', true ) );
		$slide_tecm_organizer = intval(  get_post_meta( $post->ID, 'slide_tecm_organizer', true ) );

		$slide_tecm_categories = get_post_meta( $post->ID, 'slide_tecm_categories', true );
		if ( empty( $slide_tecm_categories ) ) {
			$slide_tecm_categories = array();
		}
		
		?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="slide_tecm_limit"><?php _e( 'Display a maximum of', 'foyer' ); ?></label>
					</th>
					<td>
						<input type="number" min="0" step="1" id="slide_tecm_limit" name="slide_tecm_limit" class="small-text" value="<?php echo $slide_tecm_limit; ?>" /> <?php _e( 'events', 'foyer' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_tecm_categories[]"><?php _e( 'Only display events from categories', 'foyer' ); ?></label>
					</th>
					<td>
						<select name="slide_tecm_categories[]" multiple><?php
							foreach ( get_terms( array('taxonomy' => TribeEvents::TAXONOMY, 'hide_empty' => false ) ) as $cat ) {
								?><option value="<?php echo intval( $cat->term_id ); ?>" <?php if ( in_array( $cat->term_id, $slide_tecm_categories ) ) { ?>selected<?php } ?>><?php echo esc_html( $cat->name ); ?></option><?php
							}
						?></select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_tecm_venue"><?php _e( 'Only display events from venues', 'foyer' ); ?></label>
					</th>
					<td>
						<select name="slide_tecm_venue"><option value="" <?php if ( $slide_tecm_venue == "" ) { ?>selected<?php } ?> /><?php
							foreach ( tribe_get_venues() as $cat ) {
								?><option value="<?php echo intval( $cat->ID ); ?>" <?php if ( $cat->ID == $slide_tecm_venue  ) { ?>selected<?php } ?>><?php echo esc_html( $cat->post_title ); ?></option><?php
							}
						?></select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_tecm_organizer"><?php _e( 'Only display events from organizers', 'foyer' ); ?></label>
					</th>
					<td>
						<select name="slide_tecm_organizer"><option value="" <?php if ( $slide_tecm_organizer == "" ) { ?>selected<?php } ?> /><?php
							foreach ( tribe_get_organizers() as $cat ) {
								?><option value="<?php echo intval( $cat->ID ); ?>" <?php if ( $cat->ID == $slide_tecm_organizer ) { ?>selected<?php } ?>><?php echo esc_html( $cat->post_title ); ?></option><?php
							}
						?></select>
					</td>
				</tr>
			</tbody>
		</table><?php
	}
}
