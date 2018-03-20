<?php

/**
 * The class that holds all activation functionality.
 *
 * @since		1.0.0
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Activator {

	/**
	 * Does some housekeeping at plugin activation.
	 *
	 * Fired during plugin activation. Though when network activated only for the primary site.
	 *
	 * @since	1.0.0
	 * @since	1.5.3	Flushes the rewrite rules to make sure pretty permalinks for our custom post types
	 *					work properly after plugin is activated. Fixes #19 for new installs.
	 *
	 * @return	void
	 */
	public static function activate() {

		// Make sure our custom post types are registered
		Foyer_Setup::register_post_types();

		// Re-build rewrite rules, including those for our custom post types
		flush_rewrite_rules();
	}
}
