<?php

/**
 * The preview functionality for Displays, Channels and Slides.
 *
 * @since		1.0.0
 * @since		1.3.2	Refactored class from object to static methods.
 *
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Preview {

	/**
	 * Enqueues the admin javascript when previewing a slide.
	 *
	 * @since	1.0.0
	 * @since	1.2.5	Register scripts before they are enqueued.
	 *					Makes it possible to enqueue foyer scripts outside of the foyer plugin.
	 * @since	1.3.2	Changed method to static.
	 *
	 * return	void
	 */
	static function enqueue_scripts() {

		wp_register_script( Foyer::get_plugin_name() . '-admin', plugin_dir_url( __FILE__ ) . 'js/foyer-admin-min.js', array( 'jquery', 'jquery-ui-sortable' ), Foyer::get_version(), false );

		wp_localize_script( Foyer::get_plugin_name() . '-admin', 'foyer_preview', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'object_id' => get_the_id(),
			'orientations' => self::get_orientations(),
		) );

		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( ! empty( $_GET['foyer-preview'] ) ) {
			return;
		}

		if ( ! is_singular( array( Foyer_Display::post_type_name, Foyer_Channel::post_type_name, Foyer_Slide::post_type_name) ) ) {
			return;
		}

		wp_enqueue_script( Foyer::get_plugin_name() . '-admin' );
	}

	/**
	 * Get the current user's orientation choice for a Display, Channel or Slide.
	 *
	 * @since	1.0.0
	 * @param 	int	$object_id
	 * @return	string
	 */
	static function get_orientation_choice( $object_id ) {

		$default_orientation_choice = '16-9';

		if ( !is_user_logged_in( ) ) {
			return $default_orientation_choice;
		}

		$orientation_choices = get_user_meta( get_current_user_id( ), 'foyer_preview_orientation_choices', true );

		if ( empty( $orientation_choices[ $object_id ] ) ) {
			return $default_orientation_choice;
		}

		return $orientation_choices[ $object_id ];
	}

	/**
	 * Gets all available preview orientations.
	 *
	 * @since	1.0.0
	 * @return	array
	 */
	static function get_orientations() {

		$orientations = array(
			'16-9' => __( 'Landscape', 'foyer' ),
			'9-16' => __( 'Portrait', 'foyer' ),
		);

		return $orientations;
	}

	/**
	 * Hides the admin bar when a Display, Channel of Slides is shown inside a preview iframe.
	 *
	 * @since	1.0.0
	 * @return	bool
	 */
	static function hide_admin_bar( $show_admin_bar ) {

		// Leave alone if admin bar is already hidden.
		if ( !$show_admin_bar ) {
			return $show_admin_bar;
		}

		// Don't hide if not inside preview iframe.
		if ( empty( $_GET['foyer-preview'] ) ) {
			return true;
		}

		// Don't hide if not viewing a Display, Channel of Slide.
		if (!is_singular( array( Foyer_Display::post_type_name, Foyer_Channel::post_type_name, Foyer_Slide::post_type_name) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Save a user's orientation choice for a Display, Channel of Slide.
	 *
	 * Hooked to orientation button via AJAX.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Improved validating & sanitizing of the user input.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return	void
	 */
	static function save_orientation_choice( ) {

		if ( !is_user_logged_in( ) ) {
			return;
		}

		$orientation = sanitize_title( $_POST[ 'orientation' ] );
		if ( empty(  $orientation ) ) {
			return;
		}

		$object_id = intval( $_POST[ 'object_id' ] );
		if ( empty(  $object_id ) ) {
			return;
		}

		$orientation_choices = get_user_meta( get_current_user_id( ), 'foyer_preview_orientation_choices', true );

		if (empty( $orientation_choices )) {
			$orientation_choices = array();
		}

		$orientation_choices[ $object_id ] = $orientation;

		update_user_meta( get_current_user_id( ), 'foyer_preview_orientation_choices', $orientation_choices );

		wp_die();
	}
}
