<?php

/**
 * The class that holds all helper functions for slides.
 *
 * @since		1.0.0
 *
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
	 * Gets all slide posts.
	 *
	 * @since	1.4.0
	 *
	 * @param	array				$args	Additional args for get_posts().
	 * @return	array of WP_Post			The slide posts.
	 */
	static function get_posts( $args = array() ) {
		$defaults = array(
			'post_type' => Foyer_Slide::post_type_name,
			'posts_per_page' => -1,
		);

		$args = wp_parse_args( $args, $defaults );

		return get_posts( $args );
	}

	/**
	 * Gets a slide background by its slug.
	 *
	 * @since	1.4.0
	 * @param	string	$slug	The slug of the background to get.
	 * @return	array			The slide background properties.
	 */
	static function get_slide_background_by_slug( $slug ) {

		$slide_backgrounds = self::get_slide_backgrounds();

		if ( empty( $slide_backgrounds[$slug] ) ) {
			return false;
		}

		return $slide_backgrounds[$slug];
	}

	/**
	 * Gets the available slide backgrounds for a slide format, by its slug.
	 *
	 * Only returns slide format backgrounds that are registered according to Foyer_Slides::get_slide_backgrounds().
	 *
	 * @since	1.4.0
	 * @param	string	$slug	The slug of the slide format to get the slide backgrounds for.
	 * @return	array			The slide backgrounds with their properties.
	 */
	static function get_slide_format_backgrounds_by_slug( $slug ) {

		$slide_format = self::get_slide_format_by_slug( $slug );

		if ( ! empty( $slide_format['slide_backgrounds'] ) ) {
			$slide_backgrounds = $slide_format['slide_backgrounds'];
		}
		else {
			// Each slide format should have at least one background, use 'default'
			$slide_backgrounds = array( 'default' );
		}

		$slide_format_backgrounds = array();

		foreach ( $slide_backgrounds as $slide_background_slug ) {
			$slide_background_data = self::get_slide_background_by_slug( $slide_background_slug );
			if ( ! empty( $slide_background_data ) ) {
				// Only add to backgrounds if this background is registered
				$slide_format_backgrounds[$slide_background_slug] = $slide_background_data;
			}
		}

		return $slide_format_backgrounds;
	}

	/**
	 * Gets a slide background by its slug, for a specific slide format.
	 *
	 * Only returns a slide background if it is registered to the slide format.
	 *
	 * @since	1.4.0
	 * @param	string	$slide_background_slug	The slug of the slide background to get.
	 * @param	string	$slide_format_slug		The slug of the slide format to get the slide background for.
	 * @return	array							The slide background properties.
	 */
	static function get_slide_background_by_slug_for_slide_format( $slide_background_slug, $slide_format_slug ) {
		$slide_format_backgrounds = self::get_slide_format_backgrounds_by_slug( $slide_format_slug );
		if ( empty( $slide_format_backgrounds[$slide_background_slug] ) ) {
			return false;
		}

		return $slide_format_backgrounds[$slide_background_slug];
	}

	/**
	 * Gets all available slide backgrounds.
	 *
	 * Slide backgrounds are added through filters.
	 *
	 * @since	1.4.0
	 * @return	array	All backgrounds with their properties.
	 */
	static function get_slide_backgrounds() {

		$slide_backgrounds = array();

		/**
		 * Filter available slide backgrounds.
		 *
		 * @see Foyer_Slide_Backgrounds::add_image_slide_background() for an example.
		 *
		 * @since	1.4.0
		 * @param	array	$slide_backgrounds	The currently available slide backgrounds.
		 */
		$slide_backgrounds = apply_filters( 'foyer/slides/backgrounds', $slide_backgrounds );

		return $slide_backgrounds;
	}

	/**
	 * Gets a slide format by its slug.
	 *
	 * @since	1.0.0
	 * @param	string	$slug	The slug of the format to get.
	 * @return	array			The slide format properties.
	 */
	static function get_slide_format_by_slug( $slug ) {

		foreach( self::get_slide_formats() as $slide_format_key => $slide_format_data ) {
			if ( $slug == $slide_format_key ) {
				return $slide_format_data;
			}
		}

		return false;
	}

	/**
	 * Gets all available slide formats.
	 *
	 * Slide formats are added through filters.
	 *
	 * @since	1.0.0
	 * @since	1.4.0	Default slide format is now also added through filter, instead of here.
	 *
	 * @return	array	All formats with their properties.
	 */
	static function get_slide_formats() {

		$slide_formats = array();

		/**
		 * Filter available slide formats.
		 *
		 * @see Foyer_Slide_Formats::add_production_slide_format() for an example.
		 *
		 * @since	1.0.0
		 * @param	array	$slide_formats	The currently available slide formats.
		 */
		$slide_formats = apply_filters( 'foyer/slides/formats', $slide_formats );

		return $slide_formats;
	}

	/**
	 * Gets the available slide backgrounds for each available slide format.
	 *
	 * @since	1.4.0
	 * @return	array			The slide formats with their backgrounds with their properties.
	 */
	static function get_slide_formats_backgrounds() {
		$slide_formats_backgrounds = array();

		foreach( self::get_slide_formats() as $slide_format_key => $slide_format_data ) {
			$slide_formats_backgrounds[$slide_format_key] = self::get_slide_format_backgrounds_by_slug( $slide_format_key );
		}

		return $slide_formats_backgrounds;
	}

	/**
	 * Checks if the slide format has a default background template.
	 *
	 * @since	1.4.0
	 *
	 * @param	string	$slug	The slug of the slide format.
	 * @return	bool			True if the slide format is known to have a default background template, false otherwise.
	 */
	static function slide_format_has_default_background_template( $slug ) {
		$slide_format_data = self::get_slide_format_by_slug( $slug );

		if ( empty( $slide_format_data['default_background_template'] ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Checks if the slide format results in a stack of slides.
	 *
	 * @since	1.5.0
	 *
	 * @param	string	$slug	The slug of the slide format.
	 * @return	bool			True if the slide format is known to result in a stack of slides, false otherwise.
	 */
	static function slide_format_is_stack( $slug ) {
		$slide_format_data = self::get_slide_format_by_slug( $slug );

		if ( empty( $slide_format_data['stack'] ) ) {
			return false;
		}
		return true;
	}
}
