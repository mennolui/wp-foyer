<?php

/**
 * The slide admin-specific functionality of the plugin.
 *
 * @link       http://mennoluitjes.nl
 * @since      1.0.0
 *
 * @package    Foyer
 * @subpackage Foyer/admin
 */

/**
 * The slide admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Foyer
 * @subpackage Foyer/admin
 * @author     Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Slide {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Adds consulent variables for use in admin.js.
	 *
	 * @since	1.0
	 */
	function enqueue_scripts() {
		$consulent_array = array(
			'photo' => intval( get_post_meta( get_the_id(), 'slide_default_image', true ) ),
			'text_select_photo' => __( 'Select an image', 'foyer' ),
			'text_use_photo' => __( 'Use this image', 'foyer' ),
		);
		wp_localize_script( $this->plugin_name, 'foyer_slide_format_default', $consulent_array );
		
	}
	
	/**
	 * Adds the channel editor meta box to the display admin page.
	 *
	 * @since	1.0.0
	 */
	public function add_slide_editor_meta_boxes() {
		global $foyer;
		
		add_meta_box(
			'foyer_slide_format',
			__( 'Slide format' , 'foyer' ),
			array( $this, 'slide_format_meta_box' ),
			Foyer_Slide::post_type_name,
			'normal',
			'low'
		);
		
		foreach( Foyer_Slides::get_slide_formats() as $slide_format_key => $slide_format_data ) {
			
			if ( empty( $slide_format_data['meta_box'] ) ) {
				$meta_box_callback = array( $this, 'slide_default_meta_box');
			} else {
				$meta_box_callback = $slide_format_data['meta_box'];
			}
			add_meta_box(
				'foyer_slide_format_'.$slide_format_key,
				sprintf( __( 'Slide format: %s ', 'foyer'), $slide_format_data['title'] ),
				$meta_box_callback,
				Foyer_Slide::post_type_name,
				'normal',
				'low'
			);
		}
		
	}

	public function slide_default_meta_box( $post ) {

		wp_enqueue_media();

		$slide_default_image = get_post_meta( $post->ID, 'slide_default_image', true );

		?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="slide_default_subtitle"><?php _e('Background image', 'foyer'); ?></label>
					</th>
					<td>
						<div class="slide_image_field<?php if ( empty( $slide_default_image ) ) { ?> empty<?php } ?>">
							<div class="image-preview-wrapper">
								<img class="slide_image_preview" src="<?php echo wp_get_attachment_url( get_post_meta( $post->ID, 'slide_default_image', true ) ); ?>" height="100">
							</div>
							
							<input type="button" class="button slide_image_upload_button" value="<?php _e( 'Upload image', 'foyer' ); ?>" />
							<input type="button" class="button slide_image_delete_button" value="<?php _e( 'Remove image', 'foyer' ); ?>" />
							<input type="hidden" name="slide_default_image" class="slide_image_value" value='<?php echo get_post_meta( $post->ID, 'slide_default_image', true ); ?>'>
						</div>	
					</td>
				</tr>
			</tbody>
		</table><?php
	}
	
	/**
	 * Outputs the content of the channel editor meta box.
	 *
	 * @since	1.0.0
	 * @param	WP_Post		$post	The post object of the current display.
	 */
	public function slide_format_meta_box( $post ) {

		wp_nonce_field( Foyer_Slide::post_type_name, Foyer_Slide::post_type_name.'_nonce' );

		ob_start();

		?><input type="hidden" id="foyer_slide_editor_<?php echo Foyer_Slide::post_type_name; ?>"
			name="foyer_slide_editor_<?php echo Foyer_Slide::post_type_name; ?>" value="<?php echo $post->ID; ?>"><?php
		
		foreach( Foyer_Slides::get_slide_formats() as $slide_format_key => $slide_format_data ) {
			?><label>
				<input type="radio" value="<?php echo $slide_format_key; ?>" name="slide_format" <?php checked( Foyer_Slides::get_slide_format_for_slide( get_the_id() ), $slide_format_key, true ); ?> />
				<span><?php echo $slide_format_data['title']; ?></span>
			</label><?php			
		}

		$html = ob_get_clean();

		echo $html;
	}


	/**
	 * Saves all custom fields for a display.
	 *
	 * Triggered when a display is submitted from the display admin form.
	 *
	 * @since 	1.0.0
	 * @param 	int		$post_id	The channel id.
	 * @return void
	 */
	public function save_slide( $post_id ) {

		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		/* Check if our nonce is set */
		if ( ! isset( $_POST[Foyer_Slide::post_type_name.'_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST[Foyer_Slide::post_type_name.'_nonce'];

		/* Verify that the nonce is valid */
		if ( ! wp_verify_nonce( $nonce, Foyer_Slide::post_type_name ) ) {
			return $post_id;
		}

		/* If this is an autosave, our form has not been submitted, so we don't want to do anything */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		/* Check the user's permissions */
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$slide_format_slug = sanitize_title( $_POST['slide_format'] );
		$slide_format = Foyer_Slides::get_slide_format_by_slug( $slide_format_slug );

		if (!empty( $slide_format ) ) {
			update_post_meta( $post_id, 'slide_format', $slide_format_slug);
		}
		
		if (empty( $slide_format['save_post'] ) ) {
			$this->save_slide_default( $post_id );
		} else {
			call_user_func_array( $slide_format['save_post'], array( $post_id ) );
		}

	}
	
	function save_slide_default( $post_id ) {
		update_post_meta( $post_id, 'slide_default_subtitle', sanitize_text_field( $_POST['slide_default_subtitle'] ) );	
		update_post_meta( $post_id, 'slide_default_image', sanitize_text_field( $_POST['slide_default_image'] ) );	
	}
}
