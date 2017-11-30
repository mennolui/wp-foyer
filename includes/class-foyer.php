<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since		1.0.0
 * @since		1.4.0	Refactored class from object to static methods.
 *						Switched from using a central Foyer_Loader class to adding actions and filters directly
 *						on init of Foyer, Foyer_Admin and Foyer_Public.
 *
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Foyer_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Load the dependencies and define the locale.
	 *
	 * @since	1.0.0
	 * @since	1.4.0	Changed method to static.
	 */
	static function init() {

		self::load_dependencies();
		self::set_locale();

		$this->define_setup_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Foyer_i18n. Defines internationalization functionality.
	 * - Foyer_Admin. Defines all hooks for the admin area.
	 * - Foyer_Public. Defines all hooks for the public side of the site.
	 * - etc.
	 *
	 * @since	1.0.0
	 * @since	1.4.0	Changed method to static.
	 * @access	private
	 */
	private static function load_dependencies() {

		// --- includes ---

		/**
		 * Helper functions for handling actions and filters of the core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-foyer-loader.php';

		/**
		 * Setup of internationalization.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-foyer-i18n.php';

		/**
		 * General (not public/admin) setup actions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-foyer-setup.php';

		/**
		 * Display, channel and slide models.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-foyer-display.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-foyer-channel.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-foyer-slide.php';

		/**
		 * Slides helper functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-foyer-slides.php';

		/**
		 * Theater for WordPress helper functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-foyer-theater.php';

		/**
		 * Register slide formats.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-foyer-slide-formats.php';

		// --- admin ---

		/**
		 * Setup of the admin area of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-foyer-admin.php';
		// --- public ---

		/**
		 * Setup of the public-facing side of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-foyer-public.php';

		/**
		 * Templating.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-foyer-templates.php';


		// Store a reference to some of the classes, to to enable defining hooks.
		$this->loader = new Foyer_Loader();

		$this->setup = new Foyer_Setup( self::get_plugin_name(), self::get_version() );

		$this->admin = new Foyer_Admin( self::get_plugin_name(), self::get_version() );
		$this->admin_channel = new Foyer_Admin_Channel( self::get_plugin_name(), self::get_version() );
		$this->admin_display = new Foyer_Admin_Display( self::get_plugin_name(), self::get_version() );
		$this->admin_slide = new Foyer_Admin_Slide( self::get_plugin_name(), self::get_version() );
		$this->admin_preview = new Foyer_Admin_Preview( self::get_plugin_name(), self::get_version() );

		$this->public = new Foyer_Public( self::get_plugin_name(), self::get_version() );
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Foyer_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since	1.0.0
	 * @since	1.4.0	Changed method to static.
	 *					Switched from using a Foyer_Loader class to defining hooks directly.
	 *
	 * @access	private
	 */
	private static function set_locale() {
		add_action( 'plugins_loaded', array( 'Foyer_i18n', 'load_plugin_textdomain' ) );
	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

	}

	/**
	 * Registers all of the hooks related to the public-facing functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$this->loader->add_action( 'wp_enqueue_scripts', $this->public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $this->public, 'add_image_sizes' );

		$this->loader->add_action( 'template_include', 'Foyer_Templates', 'template_include' );
	}

	/**
	 * Registers all of the hooks related to the general setup functionality of the plugin (not public/admin specific).
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_setup_hooks() {

		$this->loader->add_action( 'init', $this->setup, 'register_post_types' );
		$this->loader->add_filter( 'foyer/slides/formats', 'Foyer_Slide_Formats', 'add_pdf_slide_format');
		$this->loader->add_filter( 'foyer/slides/formats', 'Foyer_Slide_Formats', 'add_video_slide_format');
		$this->loader->add_filter( 'foyer/slides/formats', 'Foyer_Slide_Formats', 'add_iframe_slide_format');
		$this->loader->add_filter( 'foyer/slides/formats', 'Foyer_Slide_Formats', 'add_production_slide_format');
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since	1.0.0
	 * @since	1.4.0	Changed method to static.
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
	 * @since	1.4.0	Changed method to static.
	 * 					Now uses a named constant.
	 *
	 * @return	string	The version of the plugin.
	 */
	static function get_version() {
		return FOYER_PLUGIN_VERSION;
	}

}
