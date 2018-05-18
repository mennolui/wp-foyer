<?php

/**
 * Adds admin functionality for the Revslider slide format.
 *
 * @since		1.6.0
 *
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Slide_Format_Revslider {

	/**
	 * Saves additional data for the Revslider slide format.
	 *
	 * @since	1.6.0
	 *
	 * @param	int		$post_id	The ID of the post being saved.
	 * @return	void
	 */
	static function save_slide( $post_id ) {
		$slide_revslider_slider_id = intval( $_POST['slide_revslider_slider_id'] );
		if ( empty( $slide_revslider_slider_id ) ) {
			$slide_revslider_slider_id = '';
		}

		update_post_meta( $post_id, 'slide_revslider_slider_id', $slide_revslider_slider_id );
	}

	/**
	 * Outputs the meta box for the Revslider slide format.
	 *
	 * @since	1.6.0
	 *
	 * @param	WP_Post	$post	The post of the current slide.
	 * @return	void
	 */
	static function slide_meta_box( $post ) {

		$sliders = Foyer_Revslider::get_sliders();

		?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="slide_revslider_slider_id"><?php _ex( 'Slider Revolution', 'slide-format', 'foyer' ); ?></label>
					</th>
					<td>
						<select name="slide_revslider_slider_id">
							<option value=""></option><?php

							foreach ( $sliders as $slider_id => $slider_title ) {
								?><option value="<?php echo intval( $slider_id ); ?>" <?php selected( get_post_meta( $post->ID, 'slide_revslider_slider_id', true ), $slider_id, true ); ?>><?php echo esc_html( $slider_title ); ?></option><?php
							}
						?></select>
					</td>
				</tr>
			</tbody>
		</table><?php
	}
}
