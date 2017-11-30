<?php

/**
 * The class that holds all shared Theater functionality.
 *
 * @since		1.0.0
 * @since		1.1.0	Moved most functionality from Foyer_Theater to Foyer_Slide_Formats
 *						and to Foyer_Admin_Slide_Format_Production.
 *
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Theater {

	/**
	 * Checks if the Theater for Wordpress plugin is activated.
	 *
	 * @since	1.0.0
	 * @since	1.1.0			Changed method to static.
	 *
	 * @return	bool
	 */
	static function is_theater_activated() {
		return class_exists( 'WP_Theatre' );
	}
}
