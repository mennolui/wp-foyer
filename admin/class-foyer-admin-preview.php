<?php

/**
 * The preview functionality for Displays, Channels and Slides.
 *
 * Defines the plugin name, version, and two hooks to
 * hie the admin bar and enqueue the admin-specific JavaScript.
 *
 * @since		1.0.0
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Preview {

	/**
	 * The ID of this plugin.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @var		string		$plugin_name	The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @var		string		$version		The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	1.0.0
	 * @param	string		$plugin_name	The name of this plugin.
	 * @param	string		$version		The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Enqueues the admin javascript when previewing a slide.
	 *
	 * @since	1.0.0
	 * return	void
	 */
	function enqueue_scripts() {

		if ( !is_user_logged_in(  ) ) {
			return;
		}

		if ( !empty( $_GET['preview'] ) ) {
			return;
		}

		if (!is_singular( array( Foyer_Display::post_type_name, Foyer_Channel::post_type_name, Foyer_Slide::post_type_name) ) ) {
			return;
		}

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/foyer-admin-min.js', array( 'jquery', 'jquery-ui-sortable' ), $this->version, false );

		wp_localize_script( $this->plugin_name, 'foyer_preview', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'object_id' => get_the_id(),
			'orientations' => self::get_orientations(),
		) );

	}

	/**
	 * Get the current user's orientation choice for a Display, Channel or Slide.
	 *
	 * @since	1.0.0
	 * @static
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
	 * @static
	 * @return	array
	 */
	static function get_orientations() {

		$orientations = array(
			'16-9' => __('landscape', 'foyer'),
			'9-16' => __('portrait', 'foyer'),
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
		if ( empty( $_GET['preview'] ) ) {
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
	 * @return	void
	 */
	function save_orientation_choice( ) {

		if ( !is_user_logged_in( ) ) {
			return;
		}

		if (empty( $_POST[ 'orientation' ] ) ) {
			return;
		}

		if (empty( $_POST[ 'object_id' ] ) ) {
			return;
		}

		$orientation_choices = get_user_meta( get_current_user_id( ), 'foyer_preview_orientation_choices', true );

		if (empty( $orientation_choices )) {
			$orientation_choices = array();
		}

		$orientation_choices[ intval( $_POST[ 'object_id' ] ) ] = sanitize_title( $_POST[ 'orientation' ] );

		update_user_meta( get_current_user_id( ), 'foyer_preview_orientation_choices', $orientation_choices );

		wp_die();

	}

}
