<?php

/**
 * Defines the public-specific functionality of the plugin.
 *
 * @since		1.0.0
 * @since		1.3.2	Refactored class from object to static methods.
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
	 * @since	1.3.2
	 * @since	1.5.4	Added a wp_head action to add the Web App manifest to displays.
	 */
	static function init() {
		self::load_dependencies();

		/* Foyer_Public */
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'init', array( __CLASS__, 'add_image_sizes' ) );
		add_action( 'wp_head', array( __CLASS__, 'add_web_app_manifest' ) );

		/* Foyer_Templates */
		add_action( 'template_include', array( 'Foyer_Templates', 'template_include' ) );
	}

	/**
	 * Adds image sizes used throughout the front-end of the plugin.
	 *
	 * See https://en.wikipedia.org/wiki/Display_resolution for a list of display resolutions and their names.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 * @since	1.5.2	Moved away from hard cropped images, instead introduced the soft cropped 'foyer' image size,
	 *					ready for responsive images and higher resolutions.
	 *
	 * @return	void
	 */
	static function add_image_sizes() {

		/*
		 * To be used in templates.
		 */
		add_image_size( 'foyer', 1920, 1920, false ); // to be set to the same dimensions as the largest internal image size

		/*
		 * Internal image sizes, to force cropping of different intermediate sizes.
		 */
		add_image_size( 'foyer_fhd', 1920, 1920, false ); // soft crop: scaled down to fit within 1920x1920 (Full HD)
//		add_image_size( 'foyer_4kuhd', 3840, 3840, false ); // soft crop: scaled down to fit within 3840x3840 (4K Ultra HD)
//		4K UHD is disabled for now.

		/*
		 * @deprecated	1.5.2
		 * Use 'foyer' instead.
		 */
		add_image_size( 'foyer_fhd_square', 1920, 1920, true ); // hard cropped to 1920x1920
	}


	/**
	 * Adds the Web App manifest to the head of Foyer displays.
	 *
	 * Only for users that are not logged in.
	 *
	 * @since	1.5.4
	 *
	 * @return	void
	 */
	static function add_web_app_manifest() {
		if ( ! is_singular( Foyer_Display::post_type_name ) ) {
			return;
		}

		if ( is_user_logged_in() ) {
			return;
		}

		?><link rel="manifest" href="<?php echo FOYER_PLUGIN_URL; ?>public/assets/manifest.json"><?php
	}

	/**
	 * Register the stylesheets for the public-facing side of the plugin.
	 *
	 * @since	1.0.0
	 * @since	1.2.5	Added a 'foyer/public/enqueue_styles' action.
	 * @since	1.2.5	Register styles before they are enqueued.
	 *					Makes it possible to enqueue foyer styles outside of the foyer plugin.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return	void
	 */
	static function enqueue_styles() {

		wp_register_style( Foyer::get_plugin_name(), plugin_dir_url( __FILE__ ) . 'css/foyer-public.css', array(), Foyer::get_version(), 'all' );

		if ( ! is_singular( array( Foyer_Display::post_type_name, Foyer_Channel::post_type_name, Foyer_Slide::post_type_name) ) ) {
			return;
		}

		wp_enqueue_style( Foyer::get_plugin_name() );

		/*
		 * Runs after the Foyer public styles are enqueued.
		 *
		 * @since	1.2.5
		*/
		do_action( 'foyer/public/enqueue_styles' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the plugin.
	 *
	 * @since	1.0.0
	 * @since	1.2.5	Added a 'foyer/public/enqueue_scripts' action.
	 * @since	1.2.5	Register scripts before they are enqueued.
	 *					Makes it possible to enqueue foyer scripts outside of the foyer plugin.
	 * @since	1.3.2	Changed method to static.
	 * @since	1.7.4	Added a 'foyer/public/enqueue_scripts/before' action hook that is triggered before the
	 *					Foyer scripts are enqueued, so add-on plugins can bind events before Foyer does.
	 *
	 * @return	void
	 */
	static function enqueue_scripts() {

		wp_register_script( Foyer::get_plugin_name(), plugin_dir_url( __FILE__ ) . 'js/foyer-public-min.js', array( 'jquery' ), Foyer::get_version(), false );

		if ( ! is_singular( array( Foyer_Display::post_type_name, Foyer_Channel::post_type_name, Foyer_Slide::post_type_name) ) ) {
			return;
		}

		/*
		 * Runs before the Foyer public scripts are enqueued.
		 *
		 * @since	1.7.4
		 */
		do_action( 'foyer/public/enqueue_scripts/before' );

		wp_enqueue_script( Foyer::get_plugin_name() );

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
	 * @since	1.3.2
	 * @access	private
	 */
	private static function load_dependencies() {
		require_once FOYER_PLUGIN_PATH . 'public/class-foyer-templates.php';

	}
}
