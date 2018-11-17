<?php

/**
 * The class that holds all functionality to support add-ons.
 *
 * @since		1.X.X
 *
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Addons {

	/**
	 * Triggers the 'foyer_loaded' action so add-ons can initialize.
	 *
	 * @since 	1.X.X
	 *
	 * @return void
	 */
	static function trigger_foyer_loaded() {
		do_action( 'foyer_loaded' );
	}
}
