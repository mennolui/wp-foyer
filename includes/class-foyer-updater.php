<?php

/**
 * Updates the database to the latest plugin version, if plugin is updated.
 *
 * @since		1.4.0
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Updater {

	/**
	 * Adds an action on init that flushes the rewrite rules.
	 *
	 * @since	1.5.4
	 * @since	1.5.6	Changed the callback from the non-existing method Foyer_Updater::flush_rewrite_rules()
	 *					to the WordPress core method flush_rewrite_rules(). Fixes #26.
	 *
	 * @return	void
	 */
	static function add_flush_rewrite_rules_action() {

		/*
		 * Flush the rewrite rules on init, right after custom post types are registered.
		 *
		 * Fired on the first page load after plugin update.
		 * When network activated fired for each site.
		 * When network activated fired when a new site is created on the multisite network.
		 *
		 * When network deactivated and later network activated again this is _not_ fired.
		 * In this situation rewrite rules could go missing for all sites other than the primary site.
		 */
		add_action( 'init', 'flush_rewrite_rules', 6 );
	}

	/**
	 * Gets the database version.
	 *
	 * @since    1.4.0
	 *
	 * @return	string	The plugin version string stored in the database.
	 */
	static function get_db_version() {
		return get_option( 'foyer_plugin_version' );
	}

	/**
	 * Renames a meta_key for a post.
	 *
	 * @since    1.4.0
	 *
	 * @param	WP_Post	$post		The post to rename the meta_key for.
	 * @param	string	$old_key	The meta_key to rename.
	 * @param	string	$new_key	The new name for the meta_key.
	 * @return	bool				False if meta_key does not exist or value is empty, true otherwise.
	 */
	static function rename_meta_key_for_post( $post, $old_key, $new_key ) {

		$value = get_post_meta( $post->ID, $old_key, true );

		if ( empty( $value ) ) {
			// Post meta does not exist or contains an empty string, delete just to be sure
			delete_post_meta( $post->ID, $old_key, $value );
			return false;
		}

		update_post_meta( $post->ID, $new_key, $value );
		delete_post_meta( $post->ID, $old_key, $value );

		return true;
	}

	/**
	 * Resets all displays for certain updates.
	 *
	 * @since	1.5.4
	 * @since	1.5.5	Added 1.5.5 to the list of versions that need displays to reset.
	 * @since	1.6.0	Added 1.6.0 to the list of versions that need displays to reset.
	 * @since	1.7.0	Added 1.7.0 to the list of versions that need displays to reset.
	 * @since	1.7.1	Added 1.7.1 to the list of versions that need displays to reset.
	 *
	 * @param	string	$db_version		The current database version.
	 * @return	void
	 */
	static function reset_displays_for_certain_updates( $db_version ) {

		$reset_displays = false;

		$reset_displays_versions = array(
			'1.4.0',
			'1.5.0',
			'1.5.1',
			'1.5.5',
			'1.6.0',
			'1.7.0',
			'1.7.1',
		);

		foreach( $reset_displays_versions as $reset_displays_version ) {
			if ( version_compare( $db_version, $reset_displays_version, '<' ) ) {
				$reset_displays = true;
			}
		}

		if ( $reset_displays ) {
			// Update contains changes that require CSS/JS to be reloaded, reset displays
			Foyer_Displays::reset_all_displays();
		}
	}

	/**
	 * Updates the database to the latest plugin version, if plugin was updated.
	 *
	 * Triggered at 'plugins_loaded', before 'init', thus before custom post types are registered.
	 *
	 * @since	1.4.0
	 * @since	1.5.0	Added update code for 1.5.0.
	 * @since	1.5.1	Added update code for 1.5.1.
	 * @since	1.5.3	Made sure the rewrite rules are flushed after each update. Fixes #19 for existing installs.
	 * @since	1.5.4	Made sure no update scripts are run for fresh installs.
	 *					Added a call to a new method that resets displays for certain versions, and removed
	 *					two calls to update methods for versions that only needed to reset displays.
	 *					Moved hooking of flush rewrite rules to its own method.
	 *
	 * @return	bool	True if database was updated, false otherwise.
	 */
	static function update() {
		$db_version = self::get_db_version();

	    if ( $db_version === Foyer::get_version() ) {
			// No update needed, bail
		    return false;
	    }

		if ( empty( $db_version ) ) {
			// Fresh install, make sure all update code is skipped,
			// but still continue to flush the rewrite rules and update the db version
			$db_version = Foyer::get_version();
		}

		if ( version_compare( $db_version, '1.4.0', '<' ) ) {
			// Initial db version is lower than 1.4.0, and this requires some update code

			// Run update to 1.4.0
			self::update_to_1_4_0();

			// Update db version
			self::update_db_version( '1.4.0' );
		}

		// Reset displays for certain updates only
		self::reset_displays_for_certain_updates( $db_version );

		// All updates were successful, update db version to current plugin version
		self::update_db_version( Foyer::get_version() );

		// Flush rewrite rules
		self::add_flush_rewrite_rules_action();

		return true;
	}

	/**
	 * Updates the database version.
	 *
	 * @since    1.4.0
	 *
	 * @param	string	$version	The plugin version string to be stored in the database.
	 * @return	void
	 */
	static function update_db_version( $version ) {
		update_option( 'foyer_plugin_version', $version );
	}

	/**
	 * Updates the database to version 1.4.0.
	 *
	 * All slides in the database are converted to the new slide formats and slide backgrounds introduced in 1.4.0.
	 * Additionally resets all displays.
	 *
	 * @since    1.4.0
	 *
	 * @return	bool	True, update is always successful.
	 */
	static function update_to_1_4_0() {

		$args = array(
			'post_status' => array( 'any', 'trash' ),
		 );
		$slides = Foyer_Slides::get_posts( $args );

		// Loop over all slides and convert them to new slide formats and slide backgrounds
		foreach ( $slides as $slide ) {

			$slide_format = get_post_meta( $slide->ID, 'slide_format', true );

			if ( 'default' == $slide_format ) {
				$renamed = self::rename_meta_key_for_post( $slide, 'slide_default_image', 'slide_bg_image_image' );

				// Always set background to 'image'
				update_post_meta( $slide->ID, 'slide_background', 'image' );
			}
			elseif ( 'production' == $slide_format ) {
				$renamed = self::rename_meta_key_for_post( $slide, 'slide_production_image', 'slide_bg_image_image' );
				if ( $renamed ) {
					// Post meta was not empty, set background to 'image'
					update_post_meta( $slide->ID, 'slide_background', 'image' );
				}
			}
			elseif ( 'video' == $slide_format ) {
				$renamed = self::rename_meta_key_for_post( $slide, 'slide_video_video_url', 'slide_bg_video_video_url' );
				self::rename_meta_key_for_post( $slide, 'slide_video_video_start', 'slide_bg_video_video_start' );
				self::rename_meta_key_for_post( $slide, 'slide_video_video_end', 'slide_bg_video_video_end' );
				self::rename_meta_key_for_post( $slide, 'slide_video_hold_slide', 'slide_bg_video_hold_slide' );

				// Always set background to 'video'
				update_post_meta( $slide->ID, 'slide_background', 'video' );

				// Set slide format to 'default'
				update_post_meta( $slide->ID, 'slide_format', 'default' );
			}

			$slide_background = get_post_meta( $slide->ID, 'slide_background', true );

			if ( empty( $slide_background ) ) {
				// Slide background is empty, set to 'default'
				update_post_meta( $slide->ID, 'slide_background', 'default' );
			}
		}

		return true;
	}
}
