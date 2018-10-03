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
			'title' => _x( 'Default / none', 'slide-background', 'foyer' ),
			'description' => __( 'Displays the default background for the chosen slide format, if any, or no background.', 'foyer' ),
		);
		return $slide_backgrounds;
	}

	/**
	 * Adds the HTML5 Video slide background.
	 *
	 * @since	1.6.0
	 *
	 * @param 	array	$slide_backgrounds	The current slide backgrounds.
	 * @return	array						The slide backgrounds with the HTML5 Video slide background added.
	 */
	static function add_html5_video_slide_background( $slide_backgrounds ) {

		$slide_backgrounds['html5-video'] = array(
			'title' => _x( 'Video', 'slide-background', 'foyer' ),
			'meta_box' => array( 'Foyer_Admin_Slide_Background_Html5_Video', 'slide_background_meta_box' ),
			'save_post' => array( 'Foyer_Admin_Slide_Background_Html5_Video', 'save_slide_background' ),
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
			'title' => _x( 'Image', 'slide-background', 'foyer' ),
			'meta_box' => array( 'Foyer_Admin_Slide_Background_Image', 'slide_background_meta_box' ),
			'save_post' => array( 'Foyer_Admin_Slide_Background_Image', 'save_slide_background' ),
		);
		return $slide_backgrounds;
	}

	/**
	 * Adds the YouTube Video slide background.
	 *
	 * @since	1.4.0
	 * @since	1.6.0	Renamed the slide background from 'Video' to 'YouTube', without changing internal names.
	 *
	 * @param 	array	$slide_backgrounds	The current slide backgrounds.
	 * @return	array						The slide backgrounds with the YouTube Video slide background added.
	 */
	static function add_video_slide_background( $slide_backgrounds ) {

		$slide_backgrounds['video'] = array(
			'title' => _x( 'YouTube', 'slide-background', 'foyer' ),
			'meta_box' => array( 'Foyer_Admin_Slide_Background_Video', 'slide_background_meta_box' ),
			'save_post' => array( 'Foyer_Admin_Slide_Background_Video', 'save_slide_background' ),
		);
		return $slide_backgrounds;
	}
}
