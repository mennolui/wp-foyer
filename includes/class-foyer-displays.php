<?php

/**
 * The class that holds all helper functions for displays.
 *
 * @since		1.4.0
 *
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Displays {

	/**
	 * Gets all display posts.
	 *
	 * @since	1.4.0
	 *
	 * @param	array				$args	Additional args for get_posts().
	 * @return	array of WP_Post			The display posts.
	 */
	static function get_posts( $args = array() ) {
		$defaults = array(
			'post_type' => Foyer_Display::post_type_name,
			'posts_per_page' => -1,
		);

		$args = wp_parse_args( $args, $defaults );

		return get_posts( $args );
	}

	/**
	 * Adds a request for each display to be reset.
	 *
	 * @return void
	 */
	static function reset_all_displays() {
		$display_posts = self::get_posts();

		foreach ( $display_posts as $display_post ) {
			$display = new Foyer_Display( $display_post );
			$display->add_reset_request();
		}
	}
}
