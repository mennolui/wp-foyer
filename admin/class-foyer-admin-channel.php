<?php

/**
 * The channel admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since		1.0.0
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Channel {

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
	 * Adds a Slide Count column to the Channels admin table, just after the title column.
	 *
	 * @since	1.0.0
	 * @param 	array	$columns	The current columns.
	 * @return	array				The new columns.
	 */
	function add_slides_count_column( $columns ) {
		$new_columns = array();

		foreach( $columns as $key => $title ) {
			$new_columns[$key] = $title;

			if ( 'title' == $key ) {
				// Add slides count column after the title column
				$new_columns['slides_count'] = __( 'Number of slides', 'foyer' );
			}
		}
		return $new_columns;
	}

	/**
	 * Adds a slide over AJAX and outputs the updated slides list HTML.
	 *
	 * @since	1.0.0
	 * @since	1.0.1			Validated & sanitized the user input.
	 *
	 * @access public
	 * @return void
	 */
	public function add_slide_over_ajax() {

		check_ajax_referer( 'foyer_slides_editor_ajax_nonce', 'nonce' , true );

		$channel_id = intval( $_POST['channel_id'] );
		$add_slide_id = intval( $_POST['slide_id'] );

		if ( empty( $channel_id ) || empty( $add_slide_id ) ) {
			wp_die();
		}

		/* Check if the channel post exists */
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
			_x( 'Slides', 'slide cpt', 'foyer' ),
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
	 * Outputs the Slides Count column.
	 *
	 * @since	1.0.0
	 * @param 	string	$column		The current column that needs output.
	 * @param 	int 	$post_id 	The current display ID.
	 * @return	void
	 */
	function do_slides_count_column( $column, $post_id ) {
		if ( 'slides_count' == $column ) {

			$channel = new Foyer_Channel( get_the_id() );

			echo count( $channel->get_slides() );
	    }
	}

	/**
	 * Gets the HTML to add a slide in the slides editor.
	 *
	 * @since	1.0.0
	 * @since	1.0.1			Escaped and sanitized the output.
	 * @since	1.1.0			Fix: List of slides was limited to 5 items.
	 *
	 * @access	public
	 * @return	string	$html	The HTML to add a slide in the slides editor.
	 */
	public function get_add_slide_html() {

		ob_start();

		?>
			<div class="foyer_slides_editor_add">
				<table class="form-table">
					<tbody>
						<th>
							<label for="foyer_slides_editor_add">
								<?php echo esc_html__( 'Add slide', 'foyer' ); ?>
							</label>
						</th>
						<td>
							<select id="foyer_slides_editor_add" class="foyer_slides_editor_add_select">
								<option value="">(<?php echo esc_html__( 'Select a slide', 'foyer' ); ?>)</option>
								<?php
									$slides = get_posts( array(
										'post_type' => Foyer_Slide::post_type_name,
										'posts_per_page' => -1,
									) ); //@todo: move to class
									foreach ( $slides as $slide ) {
									?>
										<option value="<?php echo intval( $slide->ID ); ?>"><?php echo esc_html( $slide->post_title ); ?></option>
									<?php
									}
								?>
							</select>
						</td>
					</tbody>
				</table>
			</div>
		<?php

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Gets the HTML to set the slides duration in the slides settings metabox.
	 *
	 * @since	1.0.0
	 * @since	1.0.1			Escaped the output.
	 *
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
						<?php echo esc_html__( 'Duration', 'foyer' ); ?>
					</label>
				</th>
				<td>
					<select id="foyer_slides_settings_duration" name="foyer_slides_settings_duration">
						<option value=""><?php echo esc_html( $default_option_name ); ?></option>
						<?php
							foreach ( $duration_options as $key => $name ) {
								$selected = '';
								if ( $selected_duration == $key ) {
									$selected = 'selected="selected"';
								}
								?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php echo $selected; ?>>
										<?php echo esc_html( $name ); ?>
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
	 * @since	1.0.1			Escaped the output.
	 *
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
						<?php echo esc_html__( 'Transition', 'foyer' ); ?>
					</label>
				</th>
				<td>
					<select id="foyer_slides_settings_transition" name="foyer_slides_settings_transition">
						<option value=""><?php echo esc_html( $default_option_name ); ?></option>
						<?php
							foreach ( $transition_options as $key => $name ) {
								$selected = '';
								if ( $selected_transition == $key ) {
									$selected = 'selected="selected"';
								}
								?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php echo $selected; ?>>
										<?php echo esc_html( $name ); ?>
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
	 * @since	1.2.4		Added longer slide durations, up to 120 seconds.
	 *
	 * @return	array		The slides duration options.
	 */
	public function get_slides_duration_options() {

		for ( $sec = 2; $sec <= 20; $sec++ ) {
			$secs[] = $sec;
		}
		for ( $sec = 25; $sec <= 60; $sec += 5 ) {
			$secs[] = $sec;
		}
		for ( $sec = 90; $sec <= 120; $sec += 30 ) {
			$secs[] = $sec;
		}

		$slides_duration_options = array();
		foreach ( $secs as $sec ) {
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
	 * Gets the HTML that lists all slides in the slides editor.
	 *
	 * @since	1.0.0
	 * @since	1.0.1			Escaped and sanitized the output.
	 *
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

					if ( empty( $slides ) ) {
						?><p>
							<?php echo esc_html__( 'No slides in this channel yet.', 'foyer' ); ?>
						</p><?php
					}
					else {

						$i = 0;
						foreach( $slides as $slide ) {

							$slide_url = get_permalink( $slide->ID );
							$slide_url = add_query_arg( 'preview', 1, $slide_url );

							?>
								<div class="foyer_slides_editor_slides_slide"
									data-slide-id="<?php echo intval( $slide->ID ); ?>"
									data-slide-key="<?php echo $i; ?>"
								>
									<div class="foyer_slides_editor_slides_slide_iframe_container">
										<iframe src="<?php echo esc_url( $slide_url ); ?>" width="1080" height="1920"></iframe>
									</div>
									<div class="foyer_slides_editor_slides_slide_caption">
										<?php echo esc_html_x( 'Slide', 'slide cpt', 'foyer' ) . ' ' . ($i + 1); ?>
										(<a href="#" class="foyer_slides_editor_slides_slide_remove"><?php echo esc_html__( 'x', 'foyer' ); ?></a>)

									</div>
								</div>
							<?php

							$i++;
						}
					}
				?>
			</div>
		<?php

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Gets the slides transition options.
	 *
	 * @since	1.0.0
	 * @since	1.2.4		Added a ‘No transition’ option.
	 *
	 * @return	array		The slides transition options.
	 */
	public function get_slides_transition_options() {

		$slides_transition_options = array(
			'fade' => __( 'Fade', 'foyer' ),
			'slide' => __( 'Slide', 'foyer' ),
			'none' => __( 'No transition', 'foyer' ),
		);

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
	 * Localizes the JavaScript for the channel admin area.
	 *
	 * @since	1.0.0
	 * @since	1.0.1			Escaped the output.
	 */
	public function localize_scripts() {

		$defaults = array( 'confirm_remove_message' => esc_html__( 'Are you sure you want to remove this slide from the channel?', 'foyer' ) );
		wp_localize_script( $this->plugin_name, 'foyer_slides_editor_defaults', $defaults );

		$security = array( 'nonce' => wp_create_nonce( 'foyer_slides_editor_ajax_nonce' ) );
		wp_localize_script( $this->plugin_name, 'foyer_slides_editor_security', $security );
	}

	/**
	 * Removes the sample permalink from the Channel edit screen.
	 *
	 * @since	1.0.0
	 * @param 	string	$sample_permalink
	 * @return 	string
	 */
	public function remove_sample_permalink( $sample_permalink ) {

		$screen = get_current_screen();

		// Bail if not on Channel edit screen.
		if ( empty( $screen ) || Foyer_Channel::post_type_name != $screen->post_type ) {
			return $sample_permalink;
		}

		return '';
	}

	/**
	 * Removes a slide over AJAX and outputs the updated slides list HTML.
	 *
	 * @since	1.0.0
	 * @since	1.0.1			Validated & sanitized the user input.
	 * @since	1.2.4			You can now remove the first slide (slide_key 0) of a channel. Fixes #1.
	 *
	 * @return	void
	 */
	public function remove_slide_over_ajax() {

		check_ajax_referer( 'foyer_slides_editor_ajax_nonce', 'nonce' , true );

		$channel_id = intval( $_POST['channel_id'] );
		$remove_slide_key = intval( $_POST['slide_key'] );

		if ( empty( $channel_id ) ) {
			wp_die();
		}

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
	 * @since	1.0.1			Validated & sanitized the user input.
	 *
	 * @access public
	 * @return void
	 */
	public function reorder_slides_over_ajax() {

		check_ajax_referer( 'foyer_slides_editor_ajax_nonce', 'nonce' , true );

		$channel_id = intval( $_POST['channel_id'] );
		$slide_ids = array_map( 'intval', $_POST['slide_ids'] );

		if ( empty( $channel_id ) || empty( $slide_ids ) ) {
			wp_die();
		}

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

	/**
	 * Saves all custom fields for a channel.
	 *
	 * Triggered when a channel is submitted from the channel admin form.
	 *
	 * @since 	1.0.0
	 * @since	1.0.1			Validated & sanitized the user input.
	 *
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

		/* Check if slides settings are included (empty or not) in form */
		if (
			! isset( $_POST['foyer_slides_settings_duration'] ) ||
			! isset( $_POST['foyer_slides_settings_transition'] )
		) {
			return $post_id;
		}

		$foyer_slides_settings_duration = intval( $_POST['foyer_slides_settings_duration'] );
		if ( empty( $foyer_slides_settings_duration ) ) {
			$foyer_slides_settings_duration = '';
		}

		$foyer_slides_settings_transition = sanitize_title( $_POST['foyer_slides_settings_transition'] );
		if ( empty( $foyer_slides_settings_transition ) ) {
			$foyer_slides_settings_transition = '';
		}

		update_post_meta( $post_id, Foyer_Channel::post_type_name . '_slides_duration' , $foyer_slides_settings_duration );
		update_post_meta( $post_id, Foyer_Channel::post_type_name . '_slides_transition' , $foyer_slides_settings_transition );
	}

	/**
	 * Outputs the content of the slides editor meta box.
	 *
	 * @since	1.0.0
	 * @since	1.0.1			Sanitized the output.
	 *
	 * @param	WP_Post		$post	The post object of the current channel.
	 */
	public function slides_editor_meta_box( $post ) {

		wp_nonce_field( Foyer_Channel::post_type_name, Foyer_Channel::post_type_name.'_nonce' );

		ob_start();

		?>
			<div class="foyer_meta_box foyer_slides_editor" data-channel-id="<?php echo intval( $post->ID ); ?>">

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
}
