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
class Foyer_Slides {

	static function get_slide_format_by_slug( $slug ) {
		
		foreach( self::get_slide_formats() as $slide_format_key => $slide_format_data ) {
			if ($slug == $slide_format_key) {
				return $slide_format_data;
			}
		}
		
		return false;
	}

	static function get_slide_format_for_slide( $post_id ) {

		$slide_format = get_post_meta( $post_id, 'slide_format', true );
		
		$slide_format_keys = array_keys( self::get_slide_formats() );
		
		if (empty ($slide_format) || !in_array( $slide_format, $slide_format_keys ) ) {
			$slide_format = $slide_format_keys[0];
		}
		
		return $slide_format;
	}

	static function get_slide_formats() {

		$slide_formats = array(
			'default' => array(
				'title' => __( 'Default', 'foyer'),
			),
		);
				
		$slide_formats = apply_filters( 'foyer/slides/formats', $slide_formats);
		
		return $slide_formats;
	}
	



}
