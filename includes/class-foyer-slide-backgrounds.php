<?php

/**
 * The class that holds all shared slide background functionality.
 *
 * @since		1.4.0
 *
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Slide_Backgrounds {

	/**
	 * Adds the Default slide background.
	 *
	 * @since	1.4.0
	 *
	 * @param 	array	$slide_backgrounds	The current slide backgrounds.
	 * @return	array						The slide backgrounds with the Default slide background added.
	 */
	static function add_default_slide_background( $slide_backgrounds ) {

		$slide_backgrounds['default'] = array(
			'title' => _x( 'No background', 'foyer', 'slide-background' ),
			'meta_box' => array( 'Foyer_Admin_Slide_Background_Default', 'slide_background_meta_box' ),
			'save_post' => array( 'Foyer_Admin_Slide_Background_Default', 'save_slide_background' ),
		);
		return $slide_backgrounds;
	}

	/**
	 * Adds the Image slide background.
	 *
	 * @since	1.4.0
	 *
	 * @param 	array	$slide_backgrounds	The current slide backgrounds.
	 * @return	array						The slide backgrounds with the Image slide background added.
	 */
	static function add_image_slide_background( $slide_backgrounds ) {

		$slide_backgrounds['image'] = array(
			'title' => _x( 'Image', 'foyer', 'slide-background' ),
			'meta_box' => array( 'Foyer_Admin_Slide_Background_Image', 'slide_background_meta_box' ),
			'save_post' => array( 'Foyer_Admin_Slide_Background_Image', 'save_slide_background' ),
		);
		return $slide_backgrounds;
	}
}
