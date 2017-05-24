<?php

/**
 * The display admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since		1.0.0
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Display {

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
	 * Adds Default Channel and Active Channel columns to the Displays admin table.
	 * Removed the Date column.
	 *
	 * @since	1.0.0
	 * @param 	array	$columns	The current columns.
	 * @return	array				The new columns.
	 */
	function add_channel_columns($columns) {
		unset($columns['date']);
		return array_merge($columns,
			array(
				'default_channel' => __('Default channel', 'foyer'),
				'active_channel' => __('Active channel', 'foyer'),
			)
		);
	}

	/**
	 * Adds the channel editor meta box to the display admin page.
	 *
	 * @since	1.0.0
	 */
	public function add_channel_editor_meta_box() {
		add_meta_box(
			'foyer_channel_editor',
			__( 'Channel' , 'foyer' ),
			array( $this, 'channel_editor_meta_box' ),
			Foyer_Display::post_type_name,
			'normal',
			'high'
		);
	}

	/**
	 * Adds the channel scheduler meta box to the display admin page.
	 *
	 * @since	1.0.0
	 */
	public function add_channel_scheduler_meta_box() {
		add_meta_box(
			'foyer_channel_scheduler',
			__( 'Schedule temporary channel' , 'foyer' ),
			array( $this, 'channel_scheduler_meta_box' ),
			Foyer_Display::post_type_name,
			'normal',
			'high'
		);
	}

	/**
	 * Outputs the content of the channel editor meta box.
	 *
	 * @since	1.0.0
	 * @since	1.0.1				Sanitized the output.
	 *
	 * @param	WP_Post		$post	The post object of the current display.
	 */
	public function channel_editor_meta_box( $post ) {

		wp_nonce_field( Foyer_Display::post_type_name, Foyer_Display::post_type_name.'_nonce' );

		ob_start();

		?>
			<input type="hidden" id="foyer_channel_editor_<?php echo Foyer_Display::post_type_name; ?>"
				name="foyer_channel_editor_<?php echo Foyer_Display::post_type_name; ?>" value="<?php echo intval( $post->ID ); ?>">

			<table class="foyer_meta_box_form form-table foyer_channel_editor_form" data-display-id="<?php echo intval( $post->ID ); ?>">
				<tbody>
					<?php

						echo $this->get_default_channel_html( $post );

					?>
				</tbody>
			</table>

		<?php

		$html = ob_get_clean();

		echo $html;
	}

	/**
	 * Outputs the content of the channel scheduler meta box.
	 *
	 * @since	1.0.0
	 * @since	1.0.1				Sanitized the output.
	 *
	 * @param	WP_Post		$post	The post object of the current display.
	 */
	public function channel_scheduler_meta_box( $post ) {

		wp_nonce_field( Foyer_Display::post_type_name, Foyer_Display::post_type_name.'_nonce' );

		ob_start();

		?>
			<input type="hidden" id="foyer_channel_editor_<?php echo Foyer_Display::post_type_name; ?>"
				name="foyer_channel_editor_<?php echo Foyer_Display::post_type_name; ?>" value="<?php echo intval( $post->ID ); ?>">

			<table class="foyer_meta_box_form form-table foyer_channel_editor_form" data-display-id="<?php echo intval( $post->ID ); ?>">
				<tbody>
					<?php

						echo $this->get_scheduled_channel_html( $post );

					?>
				</tbody>
			</table>

		<?php

		$html = ob_get_clean();

		echo $html;
	}

	/**
	 * Outputs the Active Channel and Defaults Channel columns.
	 *
	 * @since	1.0.0
	 * @since	1.0.1				Escaped the output.
	 *
	 * @param 	string	$column		The current column that needs output.
	 * @param 	int 	$post_id 	The current display ID.
	 * @return	void
	 */
	function do_channel_columns( $column, $post_id ) {
	    switch ( $column ) {

		    case 'active_channel' :

				$display = new Foyer_Display( get_the_id() );
				$channel = new Foyer_Channel( $display->get_active_channel() );

				?><a href="<?php echo esc_url( get_edit_post_link( $channel->ID ) ); ?>"><?php
					echo esc_html( get_the_title( $channel->ID ) );
				?></a><?php

		        break;

		    case 'default_channel' :

				$display = new Foyer_Display( get_the_id() );
				$channel = new Foyer_Channel( $display->get_default_channel() );

				?><a href="<?php echo esc_url( get_edit_post_link( $channel->ID ) ); ?>"><?php
					echo esc_html( get_the_title( $channel->ID ) );
				?></a><?php

		        break;
	    }
	}

	/**
	 * Gets the defaults to be used in the channel scheduler.
	 *
	 * @since	1.0.0
	 * @return	string	The defaults to be used in the channel scheduler.
	 */
	public function get_channel_scheduler_defaults() {
		$language_parts = explode( '-', get_bloginfo( 'language' ) );

		$defaults = array(
			'datetime_format' => 'Y-m-d H:i',
			'duration' => 1 * 60 * 60, // one hour in seconds
			'locale' => $language_parts[0], // locale formatted as 'en' instead of 'en-US'
			'start_of_week' => get_option( 'start_of_week' ),
		);

		/**
		 * Filters the channel scheduler defaults.
		 *
		 * @since 1.0.0
		 *
		 * @param array $defaults	The current defaults to be used in the channel scheduler.
		 */
		return apply_filters( 'foyer/channel_scheduler/defaults', $defaults );
	}

	/**
	 * Gets the HTML that lists the default channel in the channel editor.
	 *
	 * @since	1.0.0
	 * @since	1.0.1			Escaped and sanitized the output.
	 *
	 * @access	public
	 * @param	WP_Post	$post
	 * @return	string	$html	The HTML that lists the default channel in the channel editor.
	 */
	public function get_default_channel_html( $post ) {

		$display = new Foyer_Display( $post );
		$default_channel = $display->get_default_channel();

		ob_start();

		?>
			<tr>
				<th>
					<label for="foyer_channel_editor_default_channel">
						<?php echo esc_html__( 'Default channel', 'foyer' ); ?>
					</label>
				</th>
				<td>
					<select id="foyer_channel_editor_default_channel" name="foyer_channel_editor_default_channel">
						<option value="">(<?php echo esc_html__( 'Select a channel', 'foyer' ); ?>)</option>
						<?php
							$channels = get_posts( array( 'post_type' => Foyer_Channel::post_type_name ) ); //@todo: move to class
							foreach ( $channels as $channel ) {
								$checked = '';
								if ( $default_channel == $channel->ID ) {
									$checked = 'selected="selected"';
								}
							?>
								<option value="<?php echo intval( $channel->ID ); ?>" <?php echo $checked; ?>><?php echo esc_html( $channel->post_title ); ?></option>
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
	 * Gets the HTML that lists the scheduled channels in the channel scheduler.
	 *
	 * Currently limited to only one scheduled channel.
	 *
	 * @since	1.0.0
	 * @since	1.0.1			Escaped and sanitized the output.
	 *
	 * @access	public
	 * @param	WP_Post	$post
	 * @return	string	$html	The HTML that lists the scheduled channels in the channel scheduler.
	 */
	public function get_scheduled_channel_html( $post ) {

		$display = new Foyer_Display( $post );
		$schedule = $display->get_schedule();

		if ( !empty( $schedule ) ) {
			$scheduled_channel = $schedule[0];
		}

		$channel_scheduler_defaults = $this->get_channel_scheduler_defaults();

		ob_start();

		?>
			<tr>
				<th>
					<label for="foyer_channel_editor_scheduled_channel">
						<?php echo esc_html__( 'Temporary channel', 'foyer' ); ?>
					</label>
				</th>
				<td>
					<select id="foyer_channel_editor_scheduled_channel" name="foyer_channel_editor_scheduled_channel">
						<option value="">(<?php echo esc_html__( 'Select a channel', 'foyer' ); ?>)</option>
						<?php
							$channels = get_posts( array( 'post_type' => Foyer_Channel::post_type_name ) ); //@todo: move to class
							foreach ( $channels as $channel ) {
								$checked = '';
								if ( ! empty( $scheduled_channel['channel'] ) && $scheduled_channel['channel'] == $channel->ID ) {
									$checked = 'selected="selected"';
								}
							?>
								<option value="<?php echo intval( $channel->ID ); ?>" <?php echo $checked; ?>><?php echo esc_html( $channel->post_title ); ?></option>
							<?php
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th>
					<label for="foyer_channel_editor_scheduled_channel_start">
						<?php echo esc_html__( 'Show from', 'foyer' ); ?>
					</label>
				</th>
				<td>
					<input type="text" id="foyer_channel_editor_scheduled_channel_start" name="foyer_channel_editor_scheduled_channel_start" value="<?php if ( ! empty( $scheduled_channel['start'] ) ) { echo esc_html( date_i18n( $channel_scheduler_defaults['datetime_format'], $scheduled_channel['start'] + get_option( 'gmt_offset' ) * 3600, true ) ); } ?>" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="foyer_channel_editor_scheduled_channel_end">
						<?php echo esc_html__( 'Until', 'foyer' ); ?>
					</label>
				</th>
				<td>
					<input type="text" id="foyer_channel_editor_scheduled_channel_end" name="foyer_channel_editor_scheduled_channel_end" value="<?php if ( ! empty( $scheduled_channel['end'] ) ) { echo esc_html( date_i18n( $channel_scheduler_defaults['datetime_format'], $scheduled_channel['end'] + get_option( 'gmt_offset' ) * 3600, true ) ); } ?>" />
				</td>
			</tr>
		<?php

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Localizes the JavaScript for the display admin area.
	 *
	 * @since	1.0.0
	 */
	public function localize_scripts() {

		$channel_scheduler_defaults = $this->get_channel_scheduler_defaults();
		wp_localize_script( $this->plugin_name, 'foyer_channel_scheduler_defaults', $channel_scheduler_defaults );
	}

	/**
	 * Saves all custom fields for a display.
	 *
	 * Triggered when a display is submitted from the display admin form.
	 *
	 * @since 	1.0.0
	 * @since	1.0.1				Improved validating & sanitizing of the user input.
	 *
	 * @param 	int		$post_id	The channel id.
	 * @return void
	 */
	public function save_display( $post_id ) {

		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		/* Check if our nonce is set */
		if ( ! isset( $_POST[Foyer_Display::post_type_name.'_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST[Foyer_Display::post_type_name.'_nonce'];

		/* Verify that the nonce is valid */
		if ( ! wp_verify_nonce( $nonce, Foyer_Display::post_type_name ) ) {
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

		/* Input validation */
		/* See: https://codex.wordpress.org/Data_Validation#Input_Validation */
		$channel = intval( $_POST['foyer_channel_editor_default_channel'] );
		$display_id = intval( $_POST['foyer_channel_editor_' . Foyer_Display::post_type_name] );

		if ( empty( $display_id ) ) {
			return $post_id;
		}

		if ( ! empty( $channel ) ) {
			update_post_meta( $display_id, Foyer_Channel::post_type_name, $channel );
		}
		else {
			delete_post_meta( $display_id, Foyer_Channel::post_type_name );
		}

		/**
		 * Save schedule for temporary channels.
		 */
		$this->save_schedule( $post_id );

	}

	/**
	 * Save all scheduled channels for this display.
	 *
	 * @access	private
	 * @since	1.0.0
	 * @since	1.0.1					Improved validating & sanitizing of the user input.
	 * @since	1.0.1					Removed the $values param that contained $_POST, to always be aware
	 * 									we're working with $_POST data.
	 *
	 * @param 	array	$values			All form values that were submitted from the display admin page.
	 * @param 	int		$display_id		The ID of the display that is being saved.
	 * @return 	void
	 */
	private function save_schedule( $display_id ) {

		delete_post_meta( $display_id, 'foyer_display_schedule' );

		$foyer_channel_editor_scheduled_channel = intval( $_POST['foyer_channel_editor_scheduled_channel'] );
		if ( empty( $foyer_channel_editor_scheduled_channel ) ) {
			return;
		}

		$foyer_channel_editor_scheduled_channel_start = sanitize_text_field( $_POST['foyer_channel_editor_scheduled_channel_start'] );
		if ( empty( $foyer_channel_editor_scheduled_channel_start ) ) {
			return;
		}

		$foyer_channel_editor_scheduled_channel_end = sanitize_text_field( $_POST['foyer_channel_editor_scheduled_channel_end'] );
		if ( empty( $foyer_channel_editor_scheduled_channel_end ) ) {
			return;
		}

		/**
		 * Store all scheduled channels.
		 * Currently only one scheduled channel is saved.
		 *
		 * Makes sure that start and end times are stored in UTC.
		 * Makes sure end time never equals or is before the start time.
		 */

		$start = strtotime( $foyer_channel_editor_scheduled_channel_start ) - get_option( 'gmt_offset' ) * 3600;
		$end = strtotime( $foyer_channel_editor_scheduled_channel_end ) - get_option( 'gmt_offset' ) * 3600;

		if ( $end <= $start ) {
			// End time is invalid, set based on start time and default duration
			$channel_scheduler_defaults = $this->get_channel_scheduler_defaults();
			$end = $start + $channel_scheduler_defaults['duration'];
		}

		$schedule = array(
			'channel' => $foyer_channel_editor_scheduled_channel,
			'start' => 	$start,
			'end' => $end,
		);

		add_post_meta( $display_id, 'foyer_display_schedule', $schedule, false );
	}
}
