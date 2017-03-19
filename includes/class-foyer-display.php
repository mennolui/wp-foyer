<?php

/**
 * The class that holds all display functionality.
 *
 * @package    Foyer
 * @subpackage Foyer/includes
 * @author     Menno Luitjes <menno@mennoluitjes.nl>
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
	 * Get the currently active channel for this display.
	 *
	 *
	 * @since	1.0.0
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

			// Return the first scheduled channel that matches the current time.
			foreach ( $schedule as $scheduled_channel ) {

				if ( $scheduled_channel['start'] > time() ) {
					continue;
				}

				if ( $scheduled_channel['end'] < time() ) {
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
	 * @access	public
	 * @return	Foyer_Channel	The default channel for this display.
	 */
	public function get_default_channel() {

		if ( ! isset( $this->default_channel ) ) {

			$default_channel = get_post_meta( $this->ID, Foyer_Channel::post_type_name, true );

			$this->default_channel = $default_channel;
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

}
