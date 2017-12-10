<?php

/**
 * Adds admin functionality for the Iframe slide format.
 *
 * @since		1.3.0
 *
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Slide_Format_Iframe {

	/**
	 * Saves additional data for the Iframe slide format.
	 *
	 * @since	1.3.0
	 *
	 * @param	int		$post_id	The ID of the post being saved.
	 * @return	void
	 */
	static function save_slide( $post_id ) {
		$slide_iframe_website_url = sanitize_text_field( $_POST['slide_iframe_website_url'] );
		update_post_meta( $post_id, 'slide_iframe_website_url', $slide_iframe_website_url );
	}

	/**
	 * Outputs the meta box for the Iframe slide format.
	 *
	 * @since	1.3.0
	 *
	 * @param	WP_Post	$post	The post of the current slide.
	 * @return	void
	 */
	static function slide_meta_box( $post ) {
		$slide_iframe_website_url = get_post_meta( $post->ID, 'slide_iframe_website_url', true );

		$https = ( 0 === stripos( 'https://', get_permalink() ) );
		$placeholder = __( 'https://...', 'foyer' );

		?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="slide_iframe_website_url"><?php _e( 'Web page URL', 'foyer' ); ?></label>
					</th>
					<td>
						<input type="text" name="slide_iframe_website_url" id="slide_iframe_website_url" placeholder="<?php echo $placeholder; ?>" class="all-options"
							value="<?php echo esc_url( $slide_iframe_website_url ); ?>" />
						<?php if ( $https ) { ?>
							<p><?php _e( 'Be sure to use an https URL', 'foyer' ); ?></p>
						<?php } ?>
					</td>
				</tr>
			</tbody>
		</table><?php
	}
}
