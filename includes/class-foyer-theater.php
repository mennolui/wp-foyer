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

	private function is_theater_activated() {
		return class_exists( 'WP_Theatre' );
	}

	function slide_production_meta_box( $post ) {
		
		global $wp_theatre;
		
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
			</tbody>
		</table><?php
	}
	
	function save_slide_production( $post_id ) {
		update_post_meta( $post_id, 'slide_production_production_id', sanitize_text_field( $_POST['slide_production_production_id'] ) );	
	}
}
