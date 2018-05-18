<?php

/**
 * The class that holds all shared RevSlider functionality.
 *
 * @since		1.6.0
 *
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Revslider {

	/**
	 * Checks if the RevSlider plugin is activated.
	 *
	 * @since	1.6.0
	 *
	 * @return	bool
	 */
	static function is_revslider_activated() {
		return class_exists( 'RevSlider' );
	}


	/**
	 * Gets all RevSlider sliders.
	 *
	 * @since	1.6.0
	 *
	 * @return	array	All RevSlider sliders as array (ID => Title).
	 */
	static function get_sliders() {
		$revslider = new RevSlider();

		if ( empty( $revslider ) || ! method_exists( $revslider, 'getArrSlidersShort' ) ) {
			return false;
		}

		// Returns array of ID => Title
		return $revslider->getArrSlidersShort();
	}

	/**
	 * Outputs a RevSlider by ID.
	 *
	 * @since	1.6.0
	 *
	 * @param	int		$slider_id	The ID of the RevSlider to output.
	 * @return	void
	 */
	static function output_slider( $slider_id ) {
		if ( ! function_exists( 'putRevSlider' ) ) {
			return false;
		}

		// Output HTML
		putRevSlider( $slider_id );
	}
}
