<?php

/**
 * The class that holds all channel functionality.
 *
 * @link       http://mennoluitjes.nl
 * @since      1.0.0
 *
 * @package    Foyer
 * @subpackage Foyer/includes
 */

/**
 * The class that holds all channel functionality.
 *
 * @package    Foyer
 * @subpackage Foyer/includes
 * @author     Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Channel {

	/**
	 * The Foyer Channel post type name.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $post_type_name    The Foyer Channel post type name.
	 */
	const post_type_name = 'foyer_channel';

	public $ID;
	private $post;

	/**
	 * The slides of this channel.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $slides    The slides of this channel.
	 */
	private $slides;

	/**
	 * The slides duration setting of this channel.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $slides    The slides duration setting of this channel.
	 */
	private $slides_duration;

	/**
	 * The slides transition setting of this channel.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $slides    The slides transition setting of this channel.
	 */
	private $slides_transition;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	1.0.0
	 * @param	int or WP_Post	$ID		The id or the WP_Post object of the channel.
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
	 * Get slides for this channel.
	 *
	 * @since	1.0.0
	 * @access	public
	 * @return	array of Foyer_Slide	The slides for this channel.
	 */
	public function get_slides() {

		if ( ! isset( $this->slides ) ) {

			$slides = array();

			$posts = get_post_meta( $this->ID, Foyer_Slide::post_type_name, true );

			if (!empty($posts)) {
				foreach ( $posts as $post ) {
					$slide = new Foyer_Slide( $post );
					$slides[] = $slide;
				}
			}

			$this->slides = $slides;
		}

		return $this->slides;
	}

	/**
	 * Get slides duration setting for this channel.
	 *
	 * @since	1.0.0
	 * @access	public
	 * @return	int		The slides duration setting for this channel.
	 */
	public function get_slides_duration() {

		if ( ! isset( $this->slides_duration ) ) {

			$slides_duration = get_post_meta( $this->ID, Foyer_Channel::post_type_name . '_slides_duration', true );
			$this->slides_duration = $slides_duration;
		}

		return $this->slides_duration;
	}

	/**
	 * Get slides transition setting for this channel.
	 *
	 * @since	1.0.0
	 * @access	public
	 * @return	int		The slides transition setting for this channel.
	 */
	public function get_slides_transition() {

		if ( ! isset( $this->slides_transition ) ) {

			$slides_transition = get_post_meta( $this->ID, Foyer_Channel::post_type_name . '_slides_transition', true );
			$this->slides_transition = $slides_transition;
		}

		return $this->slides_transition;
	}

}
