<?php

/**
 * Adds admin functionality for the Text slide format.
 *
 * @since		1.5.0
 *
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Slide_Format_Text {

	/**
	 * Saves additional data for the Text slide format.
	 *
	 * @since	1.5.0
	 *
	 * @param	int		$post_id	The ID of the post being saved.
	 * @return	void
	 */
	static function save_slide( $post_id ) {
		$slide_text_pretitle = sanitize_text_field( $_POST['slide_text_pretitle'] );
		$slide_text_title = sanitize_text_field( $_POST['slide_text_title'] );
		$slide_text_subtitle = sanitize_text_field( $_POST['slide_text_subtitle'] );
		$slide_text_content = wp_kses_post( $_POST['slide_text_content'] );

		update_post_meta( $post_id, 'slide_text_pretitle', $slide_text_pretitle );
		update_post_meta( $post_id, 'slide_text_title', $slide_text_title );
		update_post_meta( $post_id, 'slide_text_subtitle', $slide_text_subtitle );
		update_post_meta( $post_id, 'slide_text_content', $slide_text_content );
	}

	/**
	 * Outputs the meta box for the Text slide format.
	 *
	 * @since	1.5.0
	 *
	 * @param	WP_Post	$post	The post of the current slide.
	 * @return	void
	 */
	static function slide_meta_box( $post ) {
		$slide_text_pretitle = get_post_meta( $post->ID, 'slide_text_pretitle', true );
		$slide_text_title = get_post_meta( $post->ID, 'slide_text_title', true );
		$slide_text_subtitle = get_post_meta( $post->ID, 'slide_text_subtitle', true );
		$slide_text_content = get_post_meta( $post->ID, 'slide_text_content', true );

		?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="slide_text_pretitle"><?php _e( 'Pre-title', 'foyer' ); ?></label>
					</th>
					<td>
						<input type="text" name="slide_text_pretitle" id="slide_text_pretitle" class="large-text" value="<?php echo esc_html( $slide_text_pretitle ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_text_title"><?php _e( 'Title', 'foyer' ); ?></label>
					</th>
					<td>
						<input type="text" name="slide_text_title" id="slide_text_title" class="large-text" value="<?php echo esc_html( $slide_text_title ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_text_subtitle"><?php _e( 'Subtitle', 'foyer' ); ?></label>
					</th>
					<td>
						<input type="text" name="slide_text_subtitle" id="slide_text_subtitle" class="large-text" value="<?php echo esc_html( $slide_text_subtitle ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_text_content"><?php _e( 'Content', 'foyer' ); ?></label>
					</th>
					<td>
						<textarea name="slide_text_content" id="slide_text_content" class="large-text" rows="8"><?php echo esc_html( $slide_text_content ); ?></textarea>
					</td>
				</tr>
			</tbody>
		</table><?php
	}
}
