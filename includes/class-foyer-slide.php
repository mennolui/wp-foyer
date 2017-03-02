<?php

/**
 * The class that holds all slide functionality.
 *
 * @link       http://mennoluitjes.nl
 * @since      1.0.0
 *
 * @package    Foyer
 * @subpackage Foyer/includes
 */

/**
 * The class that holds all slide functionality.
 *
 * @package    Foyer
 * @subpackage Foyer/includes
 * @author     Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Slide {

	/**
	 * The Foyer Slide post type name.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $post_type_name    The Foyer Slide post type name.
	 */
	const post_type_name = 'foyer_slide';

	public $ID;
	private $post;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	1.0.0
	 * @param	int or WP_Post	$ID		The id or the WP_Post object of the slide.
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
	 * Gets the format of the slide.
	 *
	 * @since	1.0.0
	 * @return	string	The format key.
	 */
	public function format() {

		$slide_format = get_post_meta( $this->ID, 'slide_format', true );

		$slide_format_keys = array_keys( Foyer_Slides::get_slide_formats() );

		if (empty ($slide_format) || !in_array( $slide_format, $slide_format_keys ) ) {
			$slide_format = $slide_format_keys[0];
		}

		return $slide_format;

	}

	/**
	 * Gets the URL of the slide image.
	 *
	 *
	 *
	 * @since	1.0.0
	 * @return	string	The URL of the slide image.
	 */
	public function image() {
		$attachment_id = get_post_meta( $this->ID, 'slide_default_image', true );
		$attachment_src = wp_get_attachment_image_src( $attachment_id, 'foyer_fhd_square' );
		if ( empty ( $attachment_src[0] ) ) {
			return false;
		}

		return $attachment_src[0];
	}
}
