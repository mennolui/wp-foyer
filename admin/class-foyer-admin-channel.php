<?php

/**
 * The channel admin-specific functionality of the plugin.
 *
 * @link       http://mennoluitjes.nl
 * @since      1.0.0
 *
 * @package    Foyer
 * @subpackage Foyer/admin
 */

/**
 * The channel admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Foyer
 * @subpackage Foyer/admin
 * @author     Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Channel {

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
	 * Localize the JavaScript for the channel admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * An instance of this class is passed to the run() function
		 * defined in Foyer_Loader as all of the hooks are defined
		 * in that particular class.
		 */

		/* Localize the script */
		$defaults = array( 'confirm_remove_message' => __( 'Are you sure you want to remove this slide from the channel?', 'foyer' ) );
		wp_localize_script( $this->plugin_name, 'foyer_slides_editor_defaults', $defaults );

		$security = array( 'nonce' => wp_create_nonce( 'foyer_slides_editor_ajax_nonce' ) );
		wp_localize_script( $this->plugin_name, 'foyer_slides_editor_security', $security );
	}


	/**
	 * Adds the slides editor meta box to the channel admin page.
	 *
	 * @since	1.0.0
	 */
	public function add_slides_editor_meta_box() {
		add_meta_box(
			'foyer_slides_editor',
			__( 'Slides' , 'foyer' ),
			array( $this, 'slides_editor_meta_box' ),
			Foyer_Channel::post_type_name,
			'normal',
			'high'
		);
	}


	/**
	 * Gets the HTML that lists all slides in the slides editor.
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	WP_Post	$post
	 * @return	string	$html	The HTML that lists all slides in the slides editor.
	 */
	public function get_slides_list_html( $post ) {

		$channel = new Foyer_Channel( $post );
		$slides = $channel->get_slides();

		ob_start();

		$i = 0;
		foreach( $slides as $slide ) {
			?>
				<tr data-slide-key="<?php echo $i; ?>">
					<th>
						<label for="foyer_slides_editor_slide_<?php echo $i; ?>">
							<?php echo __( 'Slide', 'foyer' ) . ' ' . ($i + 1); ?>
						</label>
					</th>
					<td>
						<input type="hidden" id="foyer_slides_editor_slide_<?php echo $i; ?>"
							name="foyer_slides_editor_slides[]" value="<?php echo $slide->ID; ?>">
						<?php echo get_the_title( $slide->ID ); ?>
						(<a href="#" class="foyer_slides_editor_form_action_remove"><?php echo __( 'Remove', 'foyer' ); ?></a>)
					</td>
				</tr>
			<?php
			$i++;
		}

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Gets the HTML to add a slide in the slides editor.
	 *
	 * @since	1.0.0
	 * @access	public
	 * @return	string	$html	The HTML to add a slide in the slides editor.
	 */
	public function get_add_slide_html() {

		ob_start();

		?>
			<tr>
				<th>
					<label for="foyer_slides_editor_slide_add">
						<?php echo __( 'Add slide', 'foyer' ); ?>
					</label>
				</th>
				<td>
					<select id="foyer_slides_editor_slide_add" name="foyer_slides_editor_slides[]">
						<option value="">(<?php echo __( 'Select a slide', 'foyer' ); ?>)</option>
						<?php
							$slides = get_posts( array( 'post_type' => Foyer_Slide::post_type_name ) ); //@todo: move to class
							foreach ( $slides as $slide ) {
							?>
								<option value="<?php echo $slide->ID; ?>"><?php echo $slide->post_title; ?></option>
							<?php
							}
						?>
					</select>
				</td>
			</tr>
		<?php

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Outputs the content of the slides editor meta box.
	 *
	 * @since	1.0.0
	 * @param	WP_Post		$post	The post object of the current channel.
	 */
	public function slides_editor_meta_box( $post ) {

		wp_nonce_field( Foyer_Channel::post_type_name, Foyer_Channel::post_type_name.'_nonce' );

		ob_start();

		?>
			<input type="hidden" id="foyer_slides_editor_<?php echo Foyer_Channel::post_type_name; ?>"
				name="foyer_slides_editor_<?php echo Foyer_Channel::post_type_name; ?>" value="<?php echo $post->ID; ?>">

			<table class="foyer_meta_box_form foyer_slides_editor_form" data-channel-id="<?php echo $post->ID; ?>">
				<tbody>
					<?php

						echo $this->get_slides_list_html( $post );
						echo $this->get_add_slide_html();

					?>
				</tbody>
			</table>

		<?php

		$html = ob_get_clean();

		echo $html;
	}


	/**
	 * Saves all custom fields for a channel.
	 *
	 * Triggered when a channel is submitted from the channel admin form.
	 *
	 * @since 	1.0.0
	 * @param 	int		$post_id	The channel id.
	 * @return void
	 */
	public function save_channel( $post_id ) {

		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		/* Check if our nonce is set */
		if ( ! isset( $_POST[Foyer_Channel::post_type_name.'_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST[Foyer_Channel::post_type_name.'_nonce'];

		/* Verify that the nonce is valid */
		if ( ! wp_verify_nonce( $nonce, Foyer_Channel::post_type_name ) ) {
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

		$slides = $_POST['foyer_slides_editor_slides'];

		/* Input validation */
		/* See: https://codex.wordpress.org/Data_Validation#Input_Validation */
		$slides = array_map( 'absint', $slides );
		$channel_id = intval( $_POST['foyer_slides_editor_' . Foyer_Channel::post_type_name] );

		/* Remove any empty positions */
		foreach ( $slides as $key => $slide ) {
			if ( empty ( $slide ) ) {
				unset ( $slides[$key] );
			}
		}

		if ( ! empty( $slides ) ) {
			update_post_meta( $channel_id, Foyer_Slide::post_type_name, $slides );
		}
		else {
			delete_post_meta( $channel_id, Foyer_Slide::post_type_name );
		}
	}

	public function remove_slide_over_ajax() {

		check_ajax_referer( 'foyer_slides_editor_ajax_nonce', 'nonce' , true );

		$channel_id = $_POST['channel_id'];
		$remove_slide_key = $_POST['slide_key'];

		/* Check if this post exists */
		if ( is_null( get_post( $channel_id  ) ) ) {
			wp_die();
		}

		$channel = new Foyer_Channel( $channel_id );
		$slides = $channel->get_slides();

		/* Check if the channel has slides */
		if ( empty( $slides ) ) {
			wp_die();
		}

		$new_slides = array();
		foreach( $slides as $slide ) {
			$new_slides[] = $slide->ID;
		}

		if ( ! isset( $new_slides[$remove_slide_key] ) ) {
			wp_die();
		}

		unset( $new_slides[$remove_slide_key] );
		update_post_meta( $channel_id, Foyer_Slide::post_type_name, $new_slides );

		echo $remove_slide_key;
		wp_die();
	}


}
