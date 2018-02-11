<?php

/**
 * The display object model.
 *
 * @since		1.0.0
 *
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Display {

	/**
	 * The Foyer Display post type name.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $post_type_name    The Foyer Display post type name.
	 */
	const post_type_name = 'foyer_display';

	public $ID;
	private $post;

	/**
	 * The currently active channel of this display.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $channel    The currently active channel of this display.
	 */
	private $active_channel;

	/**
	 * The default channel of this display.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $channel    The default channel of this display.
	 */
	private $default_channel;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	1.0.0
	 * @param	int or WP_Post	$ID		The id or the WP_Post object of the display.
	 */
	public function __construct( $ID = false ) {

		if ( $ID instanceof WP_Post ) {
			// $ID is a WP_Post object
			$this->post = $ID;
			$ID = $ID->ID;
		}

		$this->ID = $ID;
	}

	/**
	 * Adds a request for the display to be reset.
	 *
	 * @since	1.4.0
	 *
	 * @return 	void
	 */
	public function add_reset_request() {
		update_post_meta( $this->ID, 'foyer_reset_display', 1 );
	}

	/**
	 * Outputs the display classes for use in the template.
	 *
	 * The output is escaped, so this method can be used in templates without further escaping.
	 *
	 * @since	1.4.0
	 *
	 * @param 	array 	$classes
	 * @return 	void
	 */
	public function classes( $classes = array() ) {

		$classes[] = 'foyer-display';

		if ( $this->is_reset_requested() && empty( $_GET['foyer-preview'] ) ) {
			// Reset is requested and we are not previewing, add class to invoke reset
			$classes[] = 'foyer-reset-display';

			// Display will be reset, delete reset request
			$this->delete_reset_request();
		}

		if ( empty( $classes ) ) {
			return;
		}

		?> class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" <?php
	}

	/**
	 * Deletes the request for the display to be reset.
	 *
	 * @since	1.4.0
	 *
	 * @return 	void
	 */
	public function delete_reset_request() {
		delete_post_meta( $this->ID, 'foyer_reset_display' );
	}

	/**
	 * Get the currently active channel for this display.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Only uses a schedule if the schedule's channel is set and published.
	 *
	 * @access	public
	 * @return	Foyer_Channel	The currently active channel for this display.
	 */
	public function get_active_channel() {

		if ( ! isset( $this->active_channel ) ) {

			$active_channel = $this->get_default_channel();

			$this->active_channel = $active_channel;

			/**
			 * Check if a temporary channel is scheduled.
			 */
			$schedule = $this->get_schedule();

			// Nothing scheduled at all. Return the default channel.
			if ( empty( $schedule ) ) {
				return $this->active_channel;
			}

			// Return the first scheduled channel that matches the current time, has a channel set, and channel is published.
			foreach ( $schedule as $scheduled_channel ) {

				if ( $scheduled_channel['start'] > time() ) {
					continue;
				}

				if ( $scheduled_channel['end'] < time() ) {
					continue;
				}

				if ( empty( $scheduled_channel['channel'] ) ) {
					continue;
				}

				// Only use channel with post status 'publish'
				if ( 'publish' != get_post_status( $scheduled_channel['channel'] ) ) {
					continue;
				}

				$this->active_channel = $scheduled_channel['channel'];

			}
		}

		return $this->active_channel;
	}


	/**
	 * Get the default channel for this display.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Only returns a channel if it is published.
	 *
	 * @access	public
	 * @return	Foyer_Channel	The default channel for this display.
	 */
	public function get_default_channel() {

		if ( ! isset( $this->default_channel ) ) {

			$default_channel = get_post_meta( $this->ID, Foyer_Channel::post_type_name, true );

			// Only use channel with post status 'publish'
			if ( 'publish' != get_post_status( $default_channel ) ) {
				$this->default_channel = false;
			}
			else {
				$this->default_channel = $default_channel;
			}
		}

		return $this->default_channel;
	}

	/**
	 * Gets all scheduled channels for this display.
	 *
	 * @since	1.0.0
	 * @return 	array|string	All scheduled channels or an empty string if no channels are scheduled.
	 */
	public function get_schedule() {
		$schedule = array();

		$schedule = get_post_meta( $this->ID, 'foyer_display_schedule', false );

		return $schedule;
	}

	/**
	 * Checks if a reset is requested for this display.
	 *
	 * @since	1.4.0
	 *
	 * @return 	bool	True if reset is requested for this display, false otherwise.
	 */
	private function is_reset_requested() {
		return (bool) get_post_meta( $this->ID, 'foyer_reset_display', true );
	}
}
