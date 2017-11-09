<?php

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
	 * Adds image sizes used throughout the front-end of the plugin.
	 *
	 * See https://en.wikipedia.org/wiki/Display_resolution for a list of display resolutions and their names.
	 *
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function add_image_sizes() {

		// Full HD (1920 x 1080) square
		add_image_size( 'foyer_fhd_square', 1920, 1920, true );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since	1.0.0
	 * @since	1.2.5	Added a 'foyer/public/enqueue_styles' action.
	 * @since	1.2.5	Register styles before they are enqueued.
	 *					Makes it possible to enqueue foyer styles outside of the foyer plugin.
	 *
	 * @return	void
	 */
	public function enqueue_styles() {

		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/foyer-public.css', array(), $this->version, 'all' );

		if ( ! is_singular( array( Foyer_Display::post_type_name, Foyer_Channel::post_type_name, Foyer_Slide::post_type_name) ) ) {
			return;
		}

		wp_enqueue_style( $this->plugin_name );

		/*
		 * Runs after the Foyer public styles are enqueued.
		 *
		 * @since	1.2.5
		*/
		do_action( 'foyer/public/enqueue_styles' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since	1.0.0
	 * @since	1.2.5	Added a 'foyer/public/enqueue_scripts' action.
	 * @since	1.2.5	Register scripts before they are enqueued.
	 *					Makes it possible to enqueue foyer scripts outside of the foyer plugin.
	 *
	 * @return	void
	 */
	public function enqueue_scripts() {

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/foyer-public-min.js', array( 'jquery' ), $this->version, false );

		if ( ! is_singular( array( Foyer_Display::post_type_name, Foyer_Channel::post_type_name, Foyer_Slide::post_type_name) ) ) {
			return;
		}

		wp_enqueue_script( $this->plugin_name );

		/*
		 * Runs after the Foyer public scripts are enqueued.
		 *
		 * @since	1.2.5
		*/
		do_action( 'foyer/public/enqueue_scripts' );
	}
}
