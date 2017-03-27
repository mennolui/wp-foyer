<?php

/**
 * The class that holds all helper functions for slides.
 *
 * @since		1.0.0
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Slides {

	/**
	 * Gets the default slides duration.
	 *
	 * @since	1.0.0
	 * @return	int			The default slides duration.
	 */
	static function get_default_slides_duration() {
		$default_slides_duration = 8;

		/**
		 * Filter the default slides duration.
		 *
		 * @since	1.0.0
		 * @param	int		$default_slides_duration	The current default slides duration.
		 */
		$default_slides_duration = apply_filters( 'foyer/slides/duration/default', $default_slides_duration );

		return $default_slides_duration;
	}

	/**
	 * Gets the default slides transition.
	 *
	 * @since	1.0.0
	 * @return	int			The default slides transition.
	 */
	static function get_default_slides_transition() {
		$default_slides_transition = 'fade';

		/**
		 * Filter the default slides transition.
		 *
		 * @since	1.0.0
		 * @param	int		$default_slides_transition	The current default slides transition.
		 */
		$default_slides_transition = apply_filters( 'foyer/slides/transition/default', $default_slides_transition );

		return $default_slides_transition;
	}


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
