<?php

/**
 * The slide admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since		1.0.0
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Slide {

	/**
	 * The ID of this plugin.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @var		string		$plugin_name	The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @var		string		$version		The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	1.0.0
	 * @param	string		$plugin_name	The name of this plugin.
	 * @param	string		$version		The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Adds a Slide Format column to the Slides admin table, just after the title column.
	 *
	 * @since	1.0.0
	 * @param 	array	$columns	The current columns.
	 * @return	array				The new columns.
	 */
	function add_slide_format_column( $columns ) {
		$new_columns = array();

		foreach( $columns as $key => $title ) {
			$new_columns[$key] = $title;

			if ( 'title' == $key ) {
				// Add slides count column after the title column
				$new_columns['slide_format'] = __( 'Slide format', 'foyer' );
			}
		}
		return $new_columns;
	}

	/**
	 * Adds the channel editor meta box to the display admin page.
	 *
	 * @since	1.0.0
	 */
	public function add_slide_editor_meta_boxes() {
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

	/**
	 * Outputs the Slide Format column.
	 *
	 * @since	1.0.0
	 * @param 	string	$column		The current column that needs output.
	 * @param 	int 	$post_id 	The current display ID.
	 * @return	void
	 */
	function do_slide_format_column( $column, $post_id ) {
		if ( 'slide_format' == $column ) {

			$slide = new Foyer_Slide( get_the_id() );
			$format_slug = $slide->get_format();
			$format = Foyer_Slides::get_slide_format_by_slug( $format_slug );
			echo $format['title'];
	    }
	}

	/**
	 * Localizes the JavaScript for the slide admin area.
	 *
	 * @since	1.0.0
	 * @since	1.0.1			Escaped the output.
	 */
	public function localize_scripts() {
		$slide_format_default = array(
			'photo' => intval( get_post_meta( get_the_id(), 'slide_default_image', true ) ),
			'text_select_photo' => esc_html__( 'Select an image', 'foyer' ),
			'text_use_photo' => esc_html__( 'Use this image', 'foyer' ),
		);
		wp_localize_script( $this->plugin_name, 'foyer_slide_format_default', $slide_format_default );

	}

	/**
	 * Removes the sample permalink from the Slide edit screen.
	 *
	 * @since	1.0.0
	 * @param 	string	$sample_permalink
	 * @return 	string
	 */
	public function remove_sample_permalink( $sample_permalink ) {

		$screen = get_current_screen();

		// Bail if not on Channel edit screen.
		if ( empty( $screen ) || Foyer_Slide::post_type_name != $screen->post_type ) {
			return $sample_permalink;
		}

		return '';
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

	/**
	 * Saves the additional data of the default slide format.
	 *
	 * @since	1.0.0
	 * @since	1.0.1				Improved validating & sanitizing of the user input.
	 *
	 * @param 	int		$post_id	The Post ID of the slide being saved.
	 * @return 	void
	 */
	function save_slide_default( $post_id ) {
		$slide_default_subtitle = sanitize_text_field( $_POST['slide_default_subtitle'] );

		$slide_default_image = intval( $_POST['slide_default_image'] );
		if ( empty( $slide_default_image ) ) {
			$slide_default_image = '';
		}

		update_post_meta( $post_id, 'slide_default_subtitle', $slide_default_subtitle );
		update_post_meta( $post_id, 'slide_default_image', $slide_default_image );
	}

	/**
	 * Outputs the meta box for the default slide format.
	 *
	 * @since	1.0.0
	 * @since	1.0.1			Escaped and sanitized the output.
	 *
	 * @param 	WP_Post	$post	The post of the slide that is being edited.
	 * @return 	void
	 */
	public function slide_default_meta_box( $post ) {

		wp_enqueue_media();

		$slide_default_image = get_post_meta( $post->ID, 'slide_default_image', true );

		?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="slide_default_subtitle"><?php esc_html_e('Background image', 'foyer'); ?></label>
					</th>
					<td>
						<div class="slide_image_field<?php if ( empty( $slide_default_image ) ) { ?> empty<?php } ?>">
							<div class="image-preview-wrapper">
								<img class="slide_image_preview" src="<?php echo esc_url( wp_get_attachment_url( $slide_default_image ) ); ?>" height="100">
							</div>

							<input type="button" class="button slide_image_upload_button" value="<?php esc_html_e( 'Upload image', 'foyer' ); ?>" />
							<input type="button" class="button slide_image_delete_button" value="<?php esc_html_e( 'Remove image', 'foyer' ); ?>" />
							<input type="hidden" name="slide_default_image" class="slide_image_value" value='<?php echo intval( $slide_default_image ); ?>'>
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
	 * @since	1.0.1			Escaped and sanitized the output.
	 *
	 * @param	WP_Post		$post	The post object of the current display.
	 */
	public function slide_format_meta_box( $post ) {

		wp_nonce_field( Foyer_Slide::post_type_name, Foyer_Slide::post_type_name.'_nonce' );

		$slide = new Foyer_Slide( $post->ID );

		?><input type="hidden" id="foyer_slide_editor_<?php echo Foyer_Slide::post_type_name; ?>"
			name="foyer_slide_editor_<?php echo Foyer_Slide::post_type_name; ?>" value="<?php echo intval( $post->ID ); ?>"><?php

		foreach( Foyer_Slides::get_slide_formats() as $slide_format_key => $slide_format_data ) {
			?><label>
				<input type="radio" value="<?php echo esc_attr( $slide_format_key ); ?>" name="slide_format" <?php checked( $slide->get_format(), $slide_format_key, true ); ?> />
				<span><?php echo esc_html( $slide_format_data['title'] ); ?></span>
			</label><?php
		}
	}
}
