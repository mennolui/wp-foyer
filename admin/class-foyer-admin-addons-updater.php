<?php

/**
 * The admin functionality for updating add-ons.
 *
 * @since		1.X.X
 *
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Addons_Updater {

	/**
	 * Creates an EDD Plugin Updater for every add-on.
	 *
	 * The EDD Plugin Updater periodically checks for valid license keys and available updates.
	 *
	 * @since	1.X.X
	 *
	 * @return 	void
	 */
	static function create_plugin_updaters() {
		foreach ( Foyer_Addons::get_licensed_addons() as $licensed_addon ) {
			$license_key = trim( get_option( $licensed_addon['slug'] . '_license_key' ) );
			$edd_updater = new Foyer_EDD_SL_Plugin_Updater( FOYER_EDD_STORE_URL, $licensed_addon['plugin_file'],
				array(
					'version' => $licensed_addon['version'],	// current version number
					'license' => $license_key,				// license key
					'item_id' => $licensed_addon['item_id'],	// ID of the product
					'author'  => $licensed_addon['author'],	// author of this plugin
					'beta'    => false,
				)
			);
		}
	}
}
