<?php

/**
 * The class that holds all deactivation functionality.
 *
 * @since		1.0.0
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Deactivator {

	/**
	 * Does some housekeeping at plugin deactivation.
	 *
	 * Fired during plugin deactivation. Though when network activated only for the primary site.
	 *
	 * @since	1.0.0
	 * @since	1.5.3	Flushes the rewrite rules to make sure our rewrite rules are removed.
	 *
	 * @return	void
	 */
	public static function deactivate() {

		// Our custom post types are not registered at this point
		// Re-building rewrite rules, excluding those for our custom post types
		flush_rewrite_rules();
	}
}
