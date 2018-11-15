<?php

/**
 * Adds admin functionality for the Recent Posts slide format.
 *
 * @since		1.7.1
 *
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Slide_Format_Recent_Posts {

	/**
	 * Saves additional data for the Recent Posts slide format.
	 *
	 * @since	1.7.1
	 *
	 * @param	int		$post_id	The ID of the post being saved.
	 * @return	void
	 */
	static function save_slide( $post_id ) {
		$slide_recent_posts_limit = intval( $_POST['slide_recent_posts_limit'] );
		if ( empty( $slide_recent_posts_limit ) ) {
			$slide_recent_posts_limit = '';
		}

		$slide_recent_posts_categories = '';
		if (
			! empty( $_POST['slide_recent_posts_categories'] ) &&
			! empty( $_POST['slide_recent_posts_categories'][0] )
		) {
			$slide_recent_posts_categories = array_map( 'intval', $_POST['slide_recent_posts_categories'] );
		}

		$slide_recent_posts_display_thumbnail = '';
		if ( isset( $_POST['slide_recent_posts_display_thumbnail'] ) ) {
			$slide_recent_posts_display_thumbnail = intval( $_POST['slide_recent_posts_display_thumbnail'] );
			if ( empty( $slide_recent_posts_display_thumbnail ) ) {
				$slide_recent_posts_display_thumbnail = '';
			}
		}

		$slide_recent_posts_use_excerpt = '';
		if ( isset( $_POST['slide_recent_posts_use_excerpt'] ) ) {
			$slide_recent_posts_use_excerpt = intval( $_POST['slide_recent_posts_use_excerpt'] );
			if ( empty( $slide_recent_posts_use_excerpt ) ) {
				$slide_recent_posts_use_excerpt = '';
			}
		}

		update_post_meta( $post_id, 'slide_recent_posts_limit', $slide_recent_posts_limit );
		update_post_meta( $post_id, 'slide_recent_posts_categories', $slide_recent_posts_categories );
		update_post_meta( $post_id, 'slide_recent_posts_display_thumbnail', $slide_recent_posts_display_thumbnail );
		update_post_meta( $post_id, 'slide_recent_posts_use_excerpt', $slide_recent_posts_use_excerpt );
	}

	/**
	 * Outputs the meta box for the Recent Posts slide format.
	 *
	 * @since	1.7.1
	 *
	 * @param	WP_Post	$post	The post of the current slide.
	 * @return	void
	 */
	static function slide_meta_box( $post ) {

		$slide_recent_posts_limit = intval( get_post_meta( $post->ID, 'slide_recent_posts_limit', true ) );

		$slide_recent_posts_categories = get_post_meta( $post->ID, 'slide_recent_posts_categories', true );
		if ( empty( $slide_recent_posts_categories ) ) {
			$slide_recent_posts_categories = array();
		}

		$slide_recent_posts_display_thumbnail = get_post_meta( $post->ID, 'slide_recent_posts_display_thumbnail', true );
		$slide_recent_posts_use_excerpt = get_post_meta( $post->ID, 'slide_recent_posts_use_excerpt', true );

		?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="slide_recent_posts_limit"><?php _e( 'Display a maximum of', 'foyer' ); ?></label>
					</th>
					<td>
						<input type="number" min="0" step="1" id="slide_recent_posts_limit" name="slide_recent_posts_limit" class="small-text" value="<?php echo $slide_recent_posts_limit; ?>" /> <?php _e( 'posts', 'foyer' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_recent_posts_categories[]"><?php _e( 'Only display posts from categories', 'foyer' ); ?></label>
					</th>
					<td>
						<select name="slide_recent_posts_categories[]" multiple><?php
							foreach ( get_categories( array( 'hide_empty' => false ) ) as $cat ) {
								?><option value="<?php echo intval( $cat->term_id ); ?>" <?php if ( in_array( $cat->term_id, $slide_recent_posts_categories ) ) { ?>selected<?php } ?>><?php echo esc_html( $cat->name ); ?></option><?php
							}
						?></select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_recent_posts_display_thumbnail"><?php _e( 'Display featured image?', 'foyer' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="slide_recent_posts_display_thumbnail" id="slide_recent_posts_display_thumbnail"
							value="1" <?php checked( $slide_recent_posts_display_thumbnail, 1 ); ?> />
						<span><?php _e( 'Yes, display the featured image.', 'foyer' ); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_recent_posts_use_excerpt"><?php _e( 'Use excerpt?', 'foyer' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="slide_recent_posts_use_excerpt" id="slide_recent_posts_use_excerpt"
							value="1" <?php checked( $slide_recent_posts_use_excerpt, 1 ); ?> />
						<span><?php _e( 'Yes, use the manual excerpt instead of the post content.', 'foyer' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table><?php
	}
}
