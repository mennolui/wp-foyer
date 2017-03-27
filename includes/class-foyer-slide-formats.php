<?php

/**
 * The class that holds all shared slide format functionality.
 *
 * @since		1.1.0
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Slide_Formats {

	/**
	 * Adds the Production slide format.
	 *
	 * @since	1.0.0
	 * @since	1.1.0	Moved here from Foyer_Theater, and changed to static.
	 *
	 * @param 	array	$slide_formats	The current slide formats.
	 * @return	array					The slide formats with the Production slide format added.
	 */
	static function add_production_slide_format( $slide_formats ) {

		if ( Foyer_Theater::is_theater_activated() ) {

			$slide_formats['production'] = array(
				'title' => __( 'Production', 'wp_theatre' ),
				'meta_box' => array( 'Foyer_Admin_Slide_Format_Production', 'slide_production_meta_box' ),
				'save_post' => array( 'Foyer_Admin_Slide_Format_Production', 'save_slide_production' ),
			);

		}
		return $slide_formats;
	}
}
