<?php

/**
 * Theater for WordPress integrations.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://mennoluitjes.nl
 * @since      1.0.0
 *
 * @package    Foyer
 * @subpackage Foyer/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Foyer
 * @subpackage Foyer/includes
 * @author     Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Theater {

	/**
	 * Adds the Production slide format.
	 * 
	 * @since	1.0.0
	 * @param 	array	$slide_formats
	 * @return	array
	 */
	function add_production_slide_format( $slide_formats ) {
		
		if ( $this->is_theater_activated() ) {
			
			$slide_formats['production'] = array(
				'title' => __('Production', 'wp_theatre'),
				'meta_box' => array( $this, 'slide_production_meta_box'),
				'save_post' => array($this, 'save_slide_production'),
			); 
			
		}
		
		return $slide_formats;
		
	}

	/**
	 * Checks if the Theater for Wordpress plugin is activated.
	 * 
	 * @since	1.0.0
	 * @return	bool
	 */
	private function is_theater_activated() {
		return class_exists( 'WP_Theatre' );
	}

	/**
	 * Outputs the meta box for the Production slide format.
	 * 
	 * @since	1.0.0
	 * @param	WP_Post	$post	The post of the current slide.
	 * @return	void
	 */
	function slide_production_meta_box( $post ) {
		
		global $wp_theatre;
		
		$slide_production_image = get_post_meta( $post->ID, 'slide_production_image', true );

		?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="slide_default_subtitle"><?php _e('Production', 'wp_theatre'); ?></label>
					</th>
					<td>
						<select name="slide_production_production_id">
							<option value=""></option><?php
						
							foreach ( $wp_theatre->productions->get() as $production ) {
								?><option value="<?php echo $production->ID; ?>" <?php selected( get_post_meta( $post->ID, 'slide_production_production_id', true ), $production->ID, true ); ?>><?php echo $production->title(); ?></option><?php
							}
						?></select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_default_subtitle"><?php _e('Background image', 'foyer'); ?></label>
					</th>
					<td>
						<div class="slide_image_field<?php if ( empty( $slide_production_image ) ) { ?> empty<?php } ?>">
							<div class="image-preview-wrapper">
								<img class="slide_image_preview" src="<?php echo wp_get_attachment_url( $slide_production_image ); ?>" height="100">
							</div>
							
							<input type="button" class="button slide_image_upload_button" value="<?php _e( 'Upload image', 'foyer' ); ?>" />
							<input type="button" class="button slide_image_delete_button" value="<?php _e( 'Remove image', 'foyer' ); ?>" />
							<input type="hidden" name="slide_production_image" class="slide_image_value" value='<?php echo $slide_production_image; ?>'>
						</div>	
					</td>
				</tr>
			</tbody>
		</table><?php
	}
	
	/**
	 * Saves additional data for the Production slide format.
	 * 
	 * @since	1.0.0
	 * @param	int	$post_id
	 * @return	void
	 */
	function save_slide_production( $post_id ) {
		update_post_meta( $post_id, 'slide_production_production_id', sanitize_text_field( $_POST['slide_production_production_id'] ) );		update_post_meta( $post_id, 'slide_production_image', intval( $_POST['slide_production_image'] ) );	
	}
}
