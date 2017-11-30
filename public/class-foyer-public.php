<?php

/**
 * Defines the public-specific functionality of the plugin.
 *
 * @since		1.0.0
 * @since		1.4.0	Refactored class from object to static methods.
 *						Switched from using a central Foyer_Loader class to registering hooks directly
 *						on init of Foyer, Foyer_Admin and Foyer_Public.
 *
 * @package		Foyer
 * @subpackage	Foyer/public
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Public {

	/**
	 * Loads dependencies and registers hooks for the public-facing side of the plugin.
	 *
	 * @since	1.4.0
	 */
	static function init() {
		self:load_dependencies();

		/* Foyer_Public */
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'init', array( __CLASS__, 'add_image_sizes' ) );

		/* Foyer_Templates */
		add_action( 'template_include', array( 'Foyer_Templates', 'template_include' ) );
	}

	/**
	 * Adds image sizes used throughout the front-end of the plugin.
	 *
	 * See https://en.wikipedia.org/wiki/Display_resolution for a list of display resolutions and their names.
	 *
	 * @since	1.0.0
	 * @since	1.4.0	Changed method to static.
	 *
	 * @return	void
	 */
	static function add_image_sizes() {

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
	 * @since	1.4.0	Changed method to static.
	 *
	 * @return	void
	 */
	static function enqueue_styles() {

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
	 * @since	1.4.0	Changed method to static.
	 *
	 * @return	void
	 */
	static function enqueue_scripts() {

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

	/**
	 * Loads the required dependencies for the public-facing side of the plugin.
	 *
	 * @since	1.4.0
	 * @access	private
	 */
	private static function load_dependencies() {
		require_once FOYER_PLUGIN_PATH . 'public/class-foyer-templates.php';

	}
}
