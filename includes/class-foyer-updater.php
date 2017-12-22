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

	static function get_db_version() {
		return get_option( 'foyer_plugin_version' );
	}

	static function rename_slide_meta_key( $old_key, $new_key ) {

		$args = array(
			'post_type' => Foyer_Slide::post_type_name,
			'meta_query' => array(
				array(
					'key' => $old_key,
					'compare' => 'EXISTS',
				)
			)
		 );
		$slides = get_posts( $args );

		// Rename meta_key slide_default_image to slide_bg_image_image
		foreach ( $slides as $slide ) {
			$image = get_post_meta( $slide->ID, $old_key, true );
			if ( empty( $image ) ) {
				continue;
			}
			if ( update_post_meta( $slide->ID, $new_key, $image ) ) {
				delete_post_meta( $slide->ID, $old_key, $image );
			}
		}
	}

	/**
	 * Updates the database to the latest plugin version, if plugin was updated.
	 *
	 * @since    1.4.0
	 */
	static function update() {
		$db_version = self::get_db_version();

	    if ( $db_version === Foyer::get_version() ) {
			// No update needed, bail
		    return;
	    }

		if ( version_compare( $db_version, '1.4.0', '<' ) ) {
			// Current db version is lower than 1.4.0, run update
			if ( ! self::update_to_1_4_0() ) {
				// Update failed, bail
				return;
			}
			// Update succesfull, update db version
			self::update_db_version( '1.4.0' );
		}

		// Update to 1.5.0 here

		// All updates were succesfull, update db version to current plugin version
		self::update_db_version( Foyer::get_version() );

		return;
	}

	static function update_db_version( $version ) {
		return update_option( 'foyer_plugin_version', $version );
	}

	/**
	 * Updates the database to version 1.4.0.
	 *
	 * @since    1.4.0
	 */
	static function update_to_1_4_0() {
		// @todo: preload all slides

		self::rename_slide_meta_key( 'slide_default_image', 'slide_bg_image_image' );
		self::rename_slide_meta_key( 'slide_production_image', 'slide_bg_image_image' );
		self::rename_slide_meta_key( 'slide_video_video_url', 'slide_bg_video_video_url' );
		self::rename_slide_meta_key( 'slide_video_video_start', 'slide_bg_video_video_start' );
		self::rename_slide_meta_key( 'slide_video_video_end', 'slide_bg_video_video_end' );
		self::rename_slide_meta_key( 'slide_video_hold_slide', 'slide_bg_video_hold_slide' );

		return true;
	}
}
