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

			$posts = get_post_meta( $this->ID, Foyer_Slide::post_type_name, true );

			$slides = array();

			foreach ( $posts as $post ) {
				$slide = new Foyer_Slide( $post );
				$slides[] = $slide;
			}

			$this->slides = $slides;
		}

		return $this->slides;
	}

}
