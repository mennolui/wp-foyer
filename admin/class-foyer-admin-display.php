<?php

/**
 * The display admin-specific functionality of the plugin.
 *
 * @link       http://mennoluitjes.nl
 * @since      1.0.0
 *
 * @package    Foyer
 * @subpackage Foyer/admin
 */

/**
 * The display admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Foyer
 * @subpackage Foyer/admin
 * @author     Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Display {

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
	 * Gets the HTML that lists the default channel in the channel editor.
	 *
	 * @since	1.0.0
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
						<?php echo __( 'Default channel', 'foyer' ); ?>
					</label>
				</th>
				<td>
					<select id="foyer_channel_editor_default_channel" name="foyer_channel_editor_default_channel">
						<option value="">(<?php echo __( 'Select a channel', 'foyer' ); ?>)</option>
						<?php
							$channels = get_posts( array( 'post_type' => Foyer_Channel::post_type_name ) ); //@todo: move to class
							foreach ( $channels as $channel ) {
								$checked = '';
								if ( $default_channel == $channel->ID ) {
									$checked = 'selected="selected"';
								}
							?>
								<option value="<?php echo $channel->ID; ?>" <?php echo $checked; ?>><?php echo $channel->post_title; ?></option>
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

		ob_start();

		?>
			<tr>
				<th>
					<label for="foyer_channel_editor_scheduled_channel">
						<?php echo __( 'Temporary channel', 'foyer' ); ?>
					</label>
				</th>
				<td>
					<select id="foyer_channel_editor_scheduled_channel" name="foyer_channel_editor_scheduled_channel">
						<option value="">(<?php echo __( 'Select a channel', 'foyer' ); ?>)</option>
						<?php
							$channels = get_posts( array( 'post_type' => Foyer_Channel::post_type_name ) ); //@todo: move to class
							foreach ( $channels as $channel ) {
								$checked = '';
								if ( !empty( $scheduled_channel['channel'] ) && $scheduled_channel['channel'] == $channel->ID ) {
									$checked = 'selected="selected"';
								}
							?>
								<option value="<?php echo $channel->ID; ?>" <?php echo $checked; ?>><?php echo $channel->post_title; ?></option>
							<?php
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th>
					<label for="foyer_channel_editor_scheduled_channel_start">
						<?php echo __( 'Show from', 'foyer' ); ?>
					</label>
				</th>
				<td>
					<input type="text" class="regular-text" id="foyer_channel_editor_scheduled_channel_start" name="foyer_channel_editor_scheduled_channel_start" value="<?php if ( !empty( $scheduled_channel['start'] ) ) { echo date_i18n( 'Y-m-d H:i:s', $scheduled_channel['start'] + get_option( 'gmt_offset' ) * 3600, true ); } ?>" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="foyer_channel_editor_scheduled_channel_end">
						<?php echo __( 'Until', 'foyer' ); ?>
					</label>
				</th>
				<td>
					<input type="text" class="regular-text" id="foyer_channel_editor_scheduled_channel_end" name="foyer_channel_editor_scheduled_channel_end" value="<?php if ( !empty( $scheduled_channel['end'] ) ) { echo date_i18n( 'Y-m-d H:i:s', $scheduled_channel['end'] + get_option( 'gmt_offset' ) * 3600, true ); } ?>" />
				</td>
			</tr>
		<?php

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Outputs the content of the channel editor meta box.
	 *
	 * @since	1.0.0
	 * @param	WP_Post		$post	The post object of the current display.
	 */
	public function channel_editor_meta_box( $post ) {

		wp_nonce_field( Foyer_Display::post_type_name, Foyer_Display::post_type_name.'_nonce' );

		ob_start();

		?>
			<input type="hidden" id="foyer_channel_editor_<?php echo Foyer_Display::post_type_name; ?>"
				name="foyer_channel_editor_<?php echo Foyer_Display::post_type_name; ?>" value="<?php echo $post->ID; ?>">

			<table class="foyer_meta_box_form foyer_channel_editor_form" data-display-id="<?php echo $post->ID; ?>">
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
	 * @param	WP_Post		$post	The post object of the current display.
	 */
	public function channel_scheduler_meta_box( $post ) {

		wp_nonce_field( Foyer_Display::post_type_name, Foyer_Display::post_type_name.'_nonce' );

		ob_start();

		?>
			<input type="hidden" id="foyer_channel_editor_<?php echo Foyer_Display::post_type_name; ?>"
				name="foyer_channel_editor_<?php echo Foyer_Display::post_type_name; ?>" value="<?php echo $post->ID; ?>">

			<table class="foyer_meta_box_form form-table foyer_channel_editor_form" data-display-id="<?php echo $post->ID; ?>">
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
	 * Saves all custom fields for a display.
	 *
	 * Triggered when a display is submitted from the display admin form.
	 *
	 * @since 	1.0.0
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

		if ( ! empty( $channel ) ) {
			update_post_meta( $display_id, Foyer_Channel::post_type_name, $channel );
		}
		else {
			delete_post_meta( $display_id, Foyer_Channel::post_type_name );
		}
		
		/**
		 * Save schedule for temporary channels.
		 */		
		$this->save_schedule( $_POST, $post_id );
		
	}
	
	/**
	 * Save all scheduled channels for this display.
	 * 
	 * @access	private
	 * @since	1.0.0
	 * @param 	array	$values			All form values that were submitted from the display admin page.
	 * @param 	int		$display_id		The ID of the display that is being saved.
	 * @return 	void
	 */
	private function save_schedule( $values, $display_id ) {

		delete_post_meta( $display_id, 'foyer_display_schedule' );
		
		if ( !is_numeric( $values['foyer_channel_editor_scheduled_channel'] ) ) {
			return;
		}
		
		if ( empty( $values['foyer_channel_editor_scheduled_channel_start'] ) ) {
			return;
		}
		
		if ( empty( $values['foyer_channel_editor_scheduled_channel_end'] ) ) {
			return;
		}

		/**
		 * Store all scheduled channels.
		 * Currently only one scheduled channel is saved.
		 * 
		 * Makes sure that start and end times are stored in UTC.
		 */
		
		$schedule = array(
			'channel' => $values['foyer_channel_editor_scheduled_channel'],
			'start' => 	strtotime( $values['foyer_channel_editor_scheduled_channel_start'] ) - get_option( 'gmt_offset' ) * 3600 ,
			'end' => strtotime( $values['foyer_channel_editor_scheduled_channel_end'] ) - get_option( 'gmt_offset' ) * 3600,
		);
		
		add_post_meta( $display_id, 'foyer_display_schedule', $schedule, false );
	}
}
