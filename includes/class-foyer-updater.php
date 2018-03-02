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
	 * Updates the database to the latest plugin version, if plugin was updated.
	 *
	 * @since    1.4.0
	 *
	 * @return	bool	True if database was updated, false otherwise.
	 */
	static function update() {
		$db_version = self::get_db_version();

	    if ( $db_version === Foyer::get_version() ) {
			// No update needed, bail
		    return false;
	    }

		if ( version_compare( $db_version, '1.4.0', '<' ) ) {
			// Initial db version is lower than 1.4.0, and this requires some update code

			// Run update to 1.4.0
			self::update_to_1_4_0();

			// Update db version
			self::update_db_version( '1.4.0' );
		}

		if ( version_compare( $db_version, '1.5.0', '<' ) ) {
			// Initial db version is lower than 1.5.0, and this requires some update code

			// Run update to 1.5.0
			self::update_to_1_5_0();

			// Update db version
			self::update_db_version( '1.5.0' );
		}

		// All updates were successful, update db version to current plugin version
		self::update_db_version( Foyer::get_version() );

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

		// Update contains changes that require CSS/JS to be reloaded, reset displays
		Foyer_Displays::reset_all_displays();

		return true;
	}

	/**
	 * Updates the database to version 1.5.0.
	 *
	 * No db changes are made, just resets all displays.
	 *
	 * @since    1.5.0
	 *
	 * @return	bool	True, update is always successful.
	 */
	static function update_to_1_5_0() {

		// Update contains changes that require CSS/JS to be reloaded, reset displays
		Foyer_Displays::reset_all_displays();

		return true;
	}
}
