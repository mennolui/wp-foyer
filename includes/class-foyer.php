<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, general hooks, admin-specific hooks, and
 * public-facing hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since		1.0.0
 * @since		1.3.2	Refactored class from object to static methods.
 *						Switched from using a central Foyer_Loader class to registering hooks directly
 *						on init of Foyer, Foyer_Admin and Foyer_Public.
 *
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer {

	/**
	 * Initializes the plugin.
	 *
	 * Loads dependencies, defines the locale and registers all of the hooks related to the
	 * general functionality of the plugin (not public/admin specific).
	 *
	 * @since	1.3.2	Changed method to static.
	 * @since	1.4.0	Registered hooks for slide backgrounds.
	 *					Changed priority of slide format filters to make sure they are triggered before
	 *					filters with default priority.
	 */
	static function init() {

		self::load_dependencies();

		/* Foyer_i18n */
		add_action( 'plugins_loaded', array( 'Foyer_i18n', 'load_plugin_textdomain' ) );

		/* Foyer_Setup */
		add_action( 'init', array( 'Foyer_Setup', 'register_post_types' ) );

		/* Foyer_Slide_Backgrounds */
		add_filter( 'foyer/slides/backgrounds', array( 'Foyer_Slide_Backgrounds', 'add_default_slide_background' ), 5 );
		add_filter( 'foyer/slides/backgrounds', array( 'Foyer_Slide_Backgrounds', 'add_image_slide_background' ), 5 );
		add_filter( 'foyer/slides/backgrounds', array( 'Foyer_Slide_Backgrounds', 'add_video_slide_background' ), 5 );

		/* Foyer_Slide_Formats */
		add_filter( 'foyer/slides/formats', array( 'Foyer_Slide_Formats', 'add_default_slide_format' ), 5 );
		add_filter( 'foyer/slides/formats', array( 'Foyer_Slide_Formats', 'add_pdf_slide_format' ), 5 );
		add_filter( 'foyer/slides/formats', array( 'Foyer_Slide_Formats', 'add_video_slide_format' ), 5 );
		add_filter( 'foyer/slides/formats', array( 'Foyer_Slide_Formats', 'add_iframe_slide_format' ), 5 );
		add_filter( 'foyer/slides/formats', array( 'Foyer_Slide_Formats', 'add_production_slide_format' ), 5 );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *					Now uses a named constant.
	 *
	 * @return	string	The name of the plugin.
	 */
	static function get_plugin_name() {
		return FOYER_PLUGIN_NAME;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 * 					Now uses a named constant.
	 *
	 * @return	string	The version of the plugin.
	 */
	static function get_version() {
		return FOYER_PLUGIN_VERSION;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Includes the following files that make up the plugin:
	 *
	 * - All general (not public/admin) classes.
	 * - Foyer_Admin: Defines all functionality for the admin area and registers its hooks.
	 * - Foyer_Public: Defines all functionality for the public side of the site and registers its hooks.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 * @since	1.4.0	Included includes/class-foyer-slide-backgrounds.php.
	 *
	 * @access	private
	 */
	private static function load_dependencies() {

		/**
		 * ------ General (not public/admin) ------
		 */

		/* Display, channel and slide models. */
		require_once FOYER_PLUGIN_PATH . 'includes/class-foyer-display.php';
		require_once FOYER_PLUGIN_PATH . 'includes/class-foyer-channel.php';
		require_once FOYER_PLUGIN_PATH . 'includes/class-foyer-slide.php';

		/* Setup of internationalization. */
		require_once FOYER_PLUGIN_PATH . 'includes/class-foyer-i18n.php';

		/* General (not public/admin) setup actions. */
		require_once FOYER_PLUGIN_PATH . 'includes/class-foyer-setup.php';

		/* Slides helper functions. */
		require_once FOYER_PLUGIN_PATH . 'includes/class-foyer-slides.php';

		/* Slide backgrounds. */
		require_once FOYER_PLUGIN_PATH . 'includes/class-foyer-slide-backgrounds.php';

		/* Slide formats. */
		require_once FOYER_PLUGIN_PATH . 'includes/class-foyer-slide-formats.php';

		/* Theater for WordPress helper functions. */
		require_once FOYER_PLUGIN_PATH . 'includes/class-foyer-theater.php';


		/**
		 * ------ Admin ------
		 */

		require_once FOYER_PLUGIN_PATH . 'admin/class-foyer-admin.php';
		Foyer_Admin::init();

		/**
		 * ------ Public ------
		 */

		require_once FOYER_PLUGIN_PATH . 'public/class-foyer-public.php';
		Foyer_Public::init();
	}
}
