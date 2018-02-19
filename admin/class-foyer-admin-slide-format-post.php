<?php

/**
 * Adds admin functionality for the Post slide format.
 *
 * @since		1.5.0
 *
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Slide_Format_Post {

	/**
	 * Saves additional data for the Post slide format.
	 *
	 * @since	1.5.0
	 *
	 * @param	int		$post_id	The ID of the post being saved.
	 * @return	void
	 */
	static function save_slide( $post_id ) {
		$slide_post_post_id = intval( $_POST['slide_post_post_id'] );
		if ( empty( $slide_post_post_id ) ) {
			$slide_post_post_id = '';
		}

		$slide_post_display_thumbnail = '';
		if ( isset( $_POST['slide_post_display_thumbnail'] ) ) {
			$slide_post_display_thumbnail = intval( $_POST['slide_post_display_thumbnail'] );
			if ( empty( $slide_post_display_thumbnail ) ) {
				$slide_post_display_thumbnail = '';
			}
		}

		$slide_post_use_excerpt = '';
		if ( isset( $_POST['slide_post_use_excerpt'] ) ) {
			$slide_post_use_excerpt = intval( $_POST['slide_post_use_excerpt'] );
			if ( empty( $slide_post_use_excerpt ) ) {
				$slide_post_use_excerpt = '';
			}
		}

		update_post_meta( $post_id, 'slide_post_post_id', $slide_post_post_id );
		update_post_meta( $post_id, 'slide_post_display_thumbnail', $slide_post_display_thumbnail );
		update_post_meta( $post_id, 'slide_post_use_excerpt', $slide_post_use_excerpt );
	}

	/**
	 * Outputs the meta box for the Post slide format.
	 *
	 * @since	1.5.0
	 *
	 * @param	WP_Post	$post	The post of the current slide.
	 * @return	void
	 */
	static function slide_meta_box( $post ) {

		$args = array(
			'post_type' => 'post',
			'posts_per_page' => -1,
		);
		$posts = get_posts( $args );

		$slide_post_display_thumbnail = get_post_meta( $post->ID, 'slide_post_display_thumbnail', true );
		$slide_post_use_excerpt = get_post_meta( $post->ID, 'slide_post_use_excerpt', true );

		?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="slide_post_post_id"><?php _ex( 'Post', 'slide-format', 'foyer' ); ?></label>
					</th>
					<td>
						<select name="slide_post_post_id">
							<option value=""></option><?php

							foreach ( $posts as $post_option ) {
								?><option value="<?php echo intval( $post_option->ID ); ?>" <?php selected( get_post_meta( $post->ID, 'slide_post_post_id', true ), $post_option->ID, true ); ?>><?php echo esc_html( get_the_title( $post_option->ID ) ); ?></option><?php
							}
						?></select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_post_display_thumbnail"><?php _e( 'Display featured image?', 'foyer' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="slide_post_display_thumbnail" id="slide_post_display_thumbnail"
							value="1" <?php checked( $slide_post_display_thumbnail, 1 ); ?> />
						<span><?php _e( 'Yes, display the featured image.', 'foyer' ); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_post_use_excerpt"><?php _e( 'Use excerpt?', 'foyer' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="slide_post_use_excerpt" id="slide_post_use_excerpt"
							value="1" <?php checked( $slide_post_use_excerpt, 1 ); ?> />
						<span><?php _e( 'Yes, use the manual excerpt instead of the post content.', 'foyer' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table><?php
	}
}
