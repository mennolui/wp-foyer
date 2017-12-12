<?php

/**
 * The class that holds all shared slide format functionality.
 *
 * @since		1.1.0
 *
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Slide_Formats {

	/**
	 * Adds the Default slide format.
	 *
	 * @since	1.4.0	Default slide format is now also added through filter, instead of in Foyer_Slides.
	 * 					Added appropriate slide backgrounds to the properties of this slide format.
	 *
	 * @param 	array	$slide_formats	The current slide formats.
	 * @return	array					The slide formats with the Default slide format added.
	 */
	static function add_default_slide_format( $slide_formats ) {

		$slide_format_backgrounds = array( 'default', 'image', 'video' );

		/**
		 * Filter available slide backgrounds for this slide format.
		 *
		 * @since	1.4.0
		 * @param	array	$slide_format_backgrounds	The currently available slide backgrounds for this slide format.
		 */
		$slide_format_backgrounds = apply_filters( 'foyer/slides/backgrounds/format=default', $slide_format_backgrounds );

		$slide_formats['default'] = array(
			'title' => _x( 'Default', 'slide-format', 'foyer' ),
//			'meta_box' => array( 'Foyer_Admin_Slide_Format_Default', 'slide_default_meta_box' ),
//			'save_post' => array( 'Foyer_Admin_Slide_Format_Default', 'save_slide_default' ),
			'slide_backgrounds' => $slide_format_backgrounds,
		);
		return $slide_formats;
	}

	/**
	 * Adds the Iframe slide format.
	 *
	 * @since	1.3.0
	 *
	 * @param 	array	$slide_formats	The current slide formats.
	 * @return	array					The slide formats with the Iframe slide format added.
	 */
	static function add_iframe_slide_format( $slide_formats ) {

		$slide_formats['iframe'] = array(
			'title' => _x( 'External web page', 'slide-format', 'foyer' ),
			'meta_box' => array( 'Foyer_Admin_Slide_Format_Iframe', 'slide_meta_box' ),
			'save_post' => array( 'Foyer_Admin_Slide_Format_Iframe', 'save_slide' ),
		);
		return $slide_formats;
	}

	/**
	 * Adds the PDF slide format.
	 *
	 * @since	1.1.0
	 *
	 * @param 	array	$slide_formats	The current slide formats.
	 * @return	array					The slide formats with the PDF slide format added.
	 */
	static function add_pdf_slide_format( $slide_formats ) {

		$slide_formats['pdf'] = array(
			'title' => _x( 'PDF', 'slide-format', 'foyer' ),
			'meta_box' => array( 'Foyer_Admin_Slide_Format_PDF', 'slide_pdf_meta_box' ),
			'save_post' => array( 'Foyer_Admin_Slide_Format_PDF', 'save_slide_pdf' ),
		);
		return $slide_formats;
	}

	/**
	 * Adds the Production slide format.
	 *
	 * @since	1.0.0
	 * @since	1.1.0	Moved here from Foyer_Theater, and changed to static.
	 * @since	1.2.6	Changed the displayed name from Production to Event, same terminology as in Theater for WordPress.
	 * @since	1.4.0	Added appropriate slide backgrounds to the properties of this slide format.
	 *
	 * @param 	array	$slide_formats	The current slide formats.
	 * @return	array					The slide formats with the Production slide format added.
	 */
	static function add_production_slide_format( $slide_formats ) {

		if ( Foyer_Theater::is_theater_activated() ) {

			$slide_format_backgrounds = array( 'default', 'image', 'video' );

			/**
			 * Filter available slide backgrounds for this slide format.
			 *
			 * @since	1.4.0
			 * @param	array	$slide_format_backgrounds	The currently available slide backgrounds for this slide format.
			 */
			$slide_format_backgrounds = apply_filters( 'foyer/slides/backgrounds/format=production', $slide_format_backgrounds );

			$slide_formats['production'] = array(
				'title' => _x( 'Event', 'slide-format', 'foyer' ),
				'meta_box' => array( 'Foyer_Admin_Slide_Format_Production', 'slide_production_meta_box' ),
				'save_post' => array( 'Foyer_Admin_Slide_Format_Production', 'save_slide_production' ),
				'slide_backgrounds' => $slide_format_backgrounds,
			);

		}
		return $slide_formats;
	}

	/**
	 * Adds the Video slide format.
	 *
	 * @since	1.2.0
	 *
	 * @param 	array	$slide_formats	The current slide formats.
	 * @return	array					The slide formats with the Video slide format added.
	 */
	static function add_video_slide_format( $slide_formats ) {

		$slide_formats['video'] = array(
			'title' => _x( 'Video', 'foyer', 'slide-format' ),
			'meta_box' => array( 'Foyer_Admin_Slide_Format_Video', 'slide_video_meta_box' ),
			'save_post' => array( 'Foyer_Admin_Slide_Format_Video', 'save_slide_video' ),
		);
		return $slide_formats;
	}
}
