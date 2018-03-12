<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * This class can be removed as soon as the Requires at least: field in the readme.txt is set to 4.6.
 * See: https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#loading-text-domain
 *
 * @since		1.0.0
 * @since		1.3.2	Refactored class from object to static methods.
 * @since		1.5.2	Removed Dutch translation files. Translations are now fully handled by
 *						https://translate.wordpress.org/projects/wp-plugins/foyer.
 *
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 */
	static function load_plugin_textdomain() {

		load_plugin_textdomain(
			'foyer',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
