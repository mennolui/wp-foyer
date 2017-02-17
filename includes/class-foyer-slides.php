<?php

/**
 * The class that holds all helper function for slides.
 *
 * @link       http://mennoluitjes.nl
 * @since      1.0.0
 *
 * @package    Foyer
 * @subpackage Foyer/includes
 */

/**
 * The class that holds all helper function for slides.
 *
 * @package    Foyer
 * @subpackage Foyer/includes
 * @author     Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Slides {

	/**
	 * Gets a slide format by its slug.
	 * 
	 * @since	1.0.0
	 * @param	string	$slug
	 * @return	array			The slide format data.
	 */
	static function get_slide_format_by_slug( $slug ) {
		
		foreach( self::get_slide_formats() as $slide_format_key => $slide_format_data ) {
			if ($slug == $slide_format_key) {
				return $slide_format_data;
			}
		}
		
		return false;
	}

	
	/**
	 * Gets all available slide formats.
	 * 
	 * @since	1.0.0
	 * @return	array
	 */
	static function get_slide_formats() {

		$slide_formats = array(
			'default' => array(
				'title' => __( 'Default', 'foyer'),
			),
		);
				
		/**
		 * Filter available slide formats.
		 *
		 * @see Foyer_Theater::add_production_slide_format() for an example.
		 *
		 * @since	1.0.0
		 * @param	array	$slide_formats	The currently available slide formats.
		 */
		$slide_formats = apply_filters( 'foyer/slides/formats', $slide_formats);
		
		return $slide_formats;
	}
	



}
