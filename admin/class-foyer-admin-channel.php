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
	 * Adds a slide over AJAX and outputs the updated slides list HTML.
	 *
	 * @since	1.0.0
	 * @access public
	 * @return void
	 */
	public function add_slide_over_ajax() {

		check_ajax_referer( 'foyer_slides_editor_ajax_nonce', 'nonce' , true );

		$channel_id = $_POST['channel_id'];
		$add_slide_id = $_POST['slide_id'];

		/* Check if this post exists */
		if ( is_null( get_post( $channel_id  ) ) ) {
			wp_die();
		}

		$channel = new Foyer_Channel( $channel_id );
		$slides = $channel->get_slides();

		$new_slides = array();
		foreach( $slides as $slide ) {
			$new_slides[] = $slide->ID;
		}

		$new_slides[] = $add_slide_id;

		update_post_meta( $channel_id, Foyer_Slide::post_type_name, $new_slides );

		echo $this->get_slides_list_html( get_post( $channel_id ) );
		wp_die();
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
	 * Adds the settings meta box to the channel admin page.
	 *
	 * @since	1.0.0
	 */
	public function add_slides_settings_meta_box() {
		add_meta_box(
			'foyer_slides_settings',
			__( 'Slideshow settings' , 'foyer' ),
			array( $this, 'slides_settings_meta_box' ),
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

		?>
			<div class="foyer_slides_editor_slides">
				<?php

					$i = 0;
					foreach( $slides as $slide ) {

						$slide_url = get_permalink( $slide->ID );
						$slide_url = add_query_arg( 'preview', 1, $slide_url );

						?>
							<div class="foyer_slides_editor_slides_slide"
								data-slide-id="<?php echo $slide->ID; ?>"
								data-slide-key="<?php echo $i; ?>"
							>
								<div class="foyer_slides_editor_slides_slide_iframe_container">
									<iframe src="<?php echo $slide_url; ?>" width="1080" height="1920"></iframe>
								</div>
								<div class="foyer_slides_editor_slides_slide_caption">
									<?php echo __( 'Slide', 'foyer' ) . ' ' . ($i + 1); ?>
									(<a href="#" class="foyer_slides_editor_slides_slide_remove"><?php echo __( 'x', 'foyer' ); ?></a>)

								</div>
							</div>
						<?php

						$i++;
					}
				?>
			</div>
		<?php

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
			<div class="foyer_slides_editor_add">
				<label for="foyer_slides_editor_add">
					<?php echo __( 'Add slide', 'foyer' ); ?>
				</label>

				<select id="foyer_slides_editor_add" class="foyer_slides_editor_add_select">
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
			</div>
		<?php

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Gets the HTML to set the slides duration in the slides settings metabox.
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	WP_Post	$post	The post object of the current display.
	 * @return	string	$html	The HTML to set the slides duration in the slides settings metabox.
	 */
	public function get_set_duration_html( $post ) {

		$duration_options = $this->get_slides_duration_options();
		$default_duration = Foyer_Slides::get_default_slides_duration();

		$default_option_name = '(' . __( 'Default', 'foyer' );
		if ( ! empty( $duration_options[ $default_duration ] ) ) {
			$default_option_name .= ' [' . $duration_options[ $default_duration ] . ']';
		}
		$default_option_name .= ')';

		$channel = new Foyer_Channel( $post );
		$selected_duration = $channel->get_saved_slides_duration();

		ob_start();

		?>
			<tr>
				<th>
					<label for="foyer_slides_settings_duration">
						<?php echo __( 'Duration', 'foyer' ); ?>
					</label>
				</th>
				<td>
					<select id="foyer_slides_settings_duration" name="foyer_slides_settings_duration">
						<option value=""><?php echo $default_option_name; ?></option>
						<?php
							foreach ( $duration_options as $key => $name ) {
								$selected = '';
								if ( $selected_duration == $key ) {
									$selected = 'selected="selected"';
								}
								?>
									<option value="<?php echo $key; ?>" <?php echo $selected; ?>>
										<?php echo $name; ?>
									</option>
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
	 * Gets the HTML to set the slides transition in the slides settings metabox.
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	WP_Post	$post	The post object of the current display.
	 * @return	string	$html	The HTML to set the slides transition in the slides settings metabox.
	 */
	public function get_set_transition_html( $post ) {

		$transition_options = $this->get_slides_transition_options();
		$default_transition = Foyer_Slides::get_default_slides_transition();

		$default_option_name = '(' . __( 'Default', 'foyer' );
		if ( ! empty( $transition_options[ $default_transition ] ) ) {
			$default_option_name .= ' [' . $transition_options[ $default_transition ] . ']';
		}
		$default_option_name .= ')';

		$channel = new Foyer_Channel( $post );
		$selected_transition = $channel->get_saved_slides_transition();

		ob_start();

		?>
			<tr>
				<th>
					<label for="foyer_slides_settings_transition">
						<?php echo __( 'Transition', 'foyer' ); ?>
					</label>
				</th>
				<td>
					<select id="foyer_slides_settings_transition" name="foyer_slides_settings_transition">
						<option value=""><?php echo $default_option_name; ?></option>
						<?php
							foreach ( $transition_options as $key => $name ) {
								$selected = '';
								if ( $selected_transition == $key ) {
									$selected = 'selected="selected"';
								}
								?>
									<option value="<?php echo $key; ?>" <?php echo $selected; ?>>
										<?php echo $name; ?>
									</option>
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
	 * Gets the slides duration options.
	 *
	 * @since	1.0.0
	 * @return	array		The slides duration options.
	 */
	public function get_slides_duration_options() {

		$slides_duration_options = array();
		for ( $sec = 2; $sec <= 20; $sec++ ) {
			$slides_duration_options[ $sec ] = $sec . ' ' . _n( 'second', 'seconds', $sec, 'foyer' );
		}

		/**
		 * Filter available slides duration options.
		 *
		 * @since	1.0.0
		 * @param	array	$slides_duration_options	The currently available slides duration options.
		 */
		$slides_duration_options = apply_filters( 'foyer/slides/duration/options', $slides_duration_options );

		return $slides_duration_options;
	}

	/**
	 * Gets the slides transition options.
	 *
	 * @since	1.0.0
	 * @return	array		The slides transition options.
	 */
	public function get_slides_transition_options() {

		$slides_transition_options = array( 'fade' => __( 'Fade', 'foyer' ), 'slide' => __( 'Slide', 'foyer' ) );

		/**
		 * Filter available slides transition options.
		 *
		 * @since	1.0.0
		 * @param	array	$slides_transition_options	The currently available slides transition options.
		 */
		$slides_transition_options = apply_filters( 'foyer/slides/transition/options', $slides_transition_options );

		return $slides_transition_options;
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
			<div class="foyer_meta_box foyer_slides_editor" data-channel-id="<?php echo $post->ID; ?>">

				<?php
					echo $this->get_slides_list_html( $post );
					echo $this->get_add_slide_html();
				?>

			</div>
		<?php

		$html = ob_get_clean();

		echo $html;
	}

	/**
	 * Outputs the content of the slides settings meta box.
	 *
	 * @since	1.0.0
	 * @param	WP_Post		$post	The post object of the current channel.
	 */
	public function slides_settings_meta_box( $post ) {

		wp_nonce_field( Foyer_Channel::post_type_name, Foyer_Channel::post_type_name.'_nonce' );

		ob_start();

		?>
			<table class="foyer_meta_box_form form-table foyer_slides_settings_form">
				<tbody>
					<?php

						echo $this->get_set_duration_html( $post );
						echo $this->get_set_transition_html( $post );

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

		if (
			! isset( $_POST['foyer_slides_settings_duration'] ) ||
			! isset( $_POST['foyer_slides_settings_transition'] )
		) {
			return $post_id;
		}

		update_post_meta( $post_id, Foyer_Channel::post_type_name . '_slides_duration' , $_POST['foyer_slides_settings_duration'] );
		update_post_meta( $post_id, Foyer_Channel::post_type_name . '_slides_transition' , $_POST['foyer_slides_settings_transition'] );
	}

	/**
	 * Removes a slide over AJAX and outputs the updated slides list HTML.
	 *
	 * @since	1.0.0
	 * @access public
	 * @return void
	 */
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

		echo $this->get_slides_list_html( get_post( $channel_id ) );
		wp_die();
	}

	/**
	 * Reorders slides over AJAX and outputs the updated slides list HTML.
	 *
	 * @since	1.0.0
	 * @access public
	 * @return void
	 */
	public function reorder_slides_over_ajax() {

		check_ajax_referer( 'foyer_slides_editor_ajax_nonce', 'nonce' , true );

		$channel_id = $_POST['channel_id'];
		$slide_ids = $_POST['slide_ids'];

		/* Check if this post exists */
		if ( is_null( get_post( $channel_id  ) ) ) {
			wp_die();
		}

		$new_slides = array();
		foreach( $slide_ids as $slide_id ) {
			$new_slides[] = $slide_id;
		}

		update_post_meta( $channel_id, Foyer_Slide::post_type_name, $new_slides );

		echo $this->get_slides_list_html( get_post( $channel_id ) );
		wp_die();
	}

}
