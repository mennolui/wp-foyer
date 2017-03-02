<?php

/**
 * The preview functionality for Displays, Channels and Slides.
 *
 * @link       http://mennoluitjes.nl
 * @since      1.0.0
 *
 * @package    Foyer
 * @subpackage Foyer/admin
 */

/**
 * The preview functionality for Displays, Channels and Slides.
 *
 * Defines the plugin name, version, and two hooks to
 * hie the admin bar and enqueue the admin-specific JavaScript.
 *
 * @package    Foyer
 * @subpackage Foyer/admin
 * @author     Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Preview {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
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

}
