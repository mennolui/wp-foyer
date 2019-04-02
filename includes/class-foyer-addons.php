<?php

/**
 * The class that holds all functionality to support add-ons.
 *
 * @since		1.7.2
 *
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Addons {

	private static $licensed_addons = array();

	/**
	 * Gets all registered licensed add-ons.
	 *
	 * @since	1.X.X
	 *
	 * @return	array	The registered licensed add-ons.
	 */
	static function get_licensed_addons() {
		if ( empty( self::$licensed_addons ) ) {

			$licensed_addons = array();

			/**
			 * Filter the licensed add-ons.
			 *
			 * @since	1.X.X
			 * @param	array	$licensed_addons	The currently registered licensed add-ons.
			 */
			self::$licensed_addons = apply_filters( 'foyer/addons/licensed_addons', $licensed_addons );
		}

		return self::$licensed_addons;
	}

	/**
	 * Registers an add-on with Foyer.
	 *
	 * Registers a plugin template path (optional) and a licensed add-on (optional).
	 *
	 * @since 	1.X.X
	 *
	 * @return	void
	 */
	static function register_addon( $addon ) {
		if ( ! empty( $addon['template_path'] ) ) {
			Foyer_Templates::register_plugin_template_path( $addon['template_path'] );
		}

		if ( ! empty( $addon['licensed_addon'] ) ) {
			self::register_licensed_addon( $addon['licensed_addon'] );
		}
	}

	/**
	 * Registers a licensed add-on.
	 *
	 * @since 	1.X.X
	 *
	 * @param	array	$updater		The add-on data for the EDD Plugin Updater.
	 * @return	void
	 */
	static function register_licensed_addon( $updater ) {
		add_filter(
			'foyer/addons/licensed_addons',
			function( $licensed_addons ) use ( $updater ) {
				$licensed_addons[] = $updater;
				return $licensed_addons;
			}
		);
	}

	/**
	 * Triggers the 'foyer_loaded' action so add-ons can initialize.
	 *
	 * @since 	1.7.2
	 *
	 * @return	void
	 */
	static function trigger_foyer_loaded() {
		do_action( 'foyer_loaded' );
	}
}
