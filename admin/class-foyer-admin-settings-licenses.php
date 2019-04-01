<?php

/**
 * The admin settings functionality for licenses.
 *
 * @since		1.X.X
 *
 * @package		Foyer
 * @subpackage	Foyer
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Settings_Licenses {

	/**
	 * Adds the Licenses tab to the Foyer settings page.
	 *
	 * @since	1.X.X
	 *
	 * @param	array	$tabs	The current tabs.
	 * @return	array			The tabs with the Licenses tab added.
	 */
	static function register_settings_tab( $tabs ) {
		if ( ! empty( Foyer_Addons::get_licensed_addons() ) ) {
			$tabs['licenses'] = _x( 'Licenses', 'name of settings tab', 'foyer' );
		}

		return $tabs;
	}
}
