<?php

/**
 * Adds admin functionality for the Video slide format.
 *
 * @since		1.2.0
 *
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Slide_Format_Video {

	/**
	 * Saves additional data for the Video slide format.
	 *
	 * @since	1.2.0
	 * @since	1.2.3	Fixed an Undefined index PHP Notice.
	 *
	 * @param	int		$post_id	The ID of the post being saved.
	 * @return	void
	 */
	static function save_slide_video( $post_id ) {
		$slide_video_video_url = sanitize_text_field( $_POST['slide_video_video_url'] );

		$slide_video_video_start = intval( $_POST['slide_video_video_start'] );
		if ( empty( $slide_video_video_start ) ) {
			$slide_video_video_start = '';
		}

		$slide_video_video_end = intval( $_POST['slide_video_video_end'] );
		if ( empty( $slide_video_video_end ) ) {
			$slide_video_video_end = '';
		}

		$slide_video_hold_slide = '';
		if ( isset( $_POST['slide_video_hold_slide'] ) ) {
			$slide_video_hold_slide = intval( $_POST['slide_video_hold_slide'] );
			if ( empty( $slide_video_hold_slide ) ) {
				$slide_video_hold_slide = '';
			}
		}

		update_post_meta( $post_id, 'slide_video_video_url', $slide_video_video_url );
		update_post_meta( $post_id, 'slide_video_video_start', $slide_video_video_start );
		update_post_meta( $post_id, 'slide_video_video_end', $slide_video_video_end );
		update_post_meta( $post_id, 'slide_video_hold_slide', $slide_video_hold_slide );
	}

	/**
	 * Outputs the meta box for the Video slide format.
	 *
	 * @since	1.2.0
	 *
	 * @param	WP_Post	$post	The post of the current slide.
	 * @return	void
	 */
	static function slide_video_meta_box( $post ) {
		$slide_video_video_url = get_post_meta( $post->ID, 'slide_video_video_url', true );
		$slide_video_video_start = get_post_meta( $post->ID, 'slide_video_video_start', true );
		$slide_video_video_end = get_post_meta( $post->ID, 'slide_video_video_end', true );
		$slide_video_hold_slide = get_post_meta( $post->ID, 'slide_video_hold_slide', true );

		?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="slide_video_video_url"><?php _e('YouTube video URL', 'foyer'); ?></label>
					</th>
					<td>
						<input type="hidden" name="slide_video_video_id" id="slide_video_video_id" value="" />
						<input type="text" name="slide_video_video_url" id="slide_video_video_url" class="all-options"
							value="<?php echo $slide_video_video_url; ?>" />
						<p class="wp-ui-text-notification hidden" id="slide_video_video_url_notification">
							<?php _e( 'Not a valid YouTube video URL, eg. https://youtu.be/MlQunle406U', 'foyer'); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_video_video_start"><?php _e('Start at', 'foyer'); ?></label>
					</th>
					<td>
						<input type="text" name="slide_video_video_start" id="slide_video_video_start" class="small-text"
							value="<?php echo $slide_video_video_start; ?>" />
						<span><?php _e( 'seconds', 'foyer' ); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_video_video_end"><?php _e('End at', 'foyer'); ?></label>
					</th>
					<td>
						<input type="text" name="slide_video_video_end" id="slide_video_video_end" class="small-text"
							value="<?php echo $slide_video_video_end; ?>" />
						<span><?php _e( 'seconds', 'foyer' ); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_video_hold_slide"><?php _e('Hold slide until end?', 'foyer'); ?></label>
					</th>
					<td>
						<input type="checkbox" name="slide_video_hold_slide" id="slide_video_hold_slide"
							value="1" <?php checked( $slide_video_hold_slide, 1 ); ?> />
						<span><?php _e('Yes, hold the slide until the end of the video.', 'foyer'); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_video_video_preview"><?php _e('Preview', 'foyer'); ?></label>
					</th>
					<td>
						<div class="youtube-video-container" id="foyer-admin-video-preview"></div>
					</td>
				</tr>
			</tbody>
		</table><?php
	}
}
