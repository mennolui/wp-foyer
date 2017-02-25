<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://mennoluitjes.nl
 * @since      1.0.0
 *
 * @package    Foyer
 * @subpackage Foyer/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Foyer
 * @subpackage Foyer/public
 * @author     Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if ( is_singular( Foyer_Display::post_type_name ) || is_singular( Foyer_Channel::post_type_name ) ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/foyer-public.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( is_singular( Foyer_Display::post_type_name ) || is_singular( Foyer_Channel::post_type_name ) ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/foyer-public-min.js', array( 'jquery' ), $this->version, false );
		}
	}

	/**
	 * Suppress the WordPress admin bar when viewing a display, channel or slide.
	 *
	 * @since    1.0.0
	 */
	public function suppress_admin_bar() {

		if (
			is_singular( Foyer_Display::post_type_name ) ||
			is_singular( Foyer_Channel::post_type_name ) ||
			is_singular( Foyer_Slide::post_type_name )
		) {
			return false;
		}

		return true;
	}
}
