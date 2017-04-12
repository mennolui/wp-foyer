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
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'foyer';
		$this->version = '1.2.1';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_setup_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Foyer_Loader. Orchestrates the hooks of the plugin.
	 * - Foyer_i18n. Defines internationalization functionality.
	 * - Foyer_Admin. Defines all hooks for the admin area.
	 * - Foyer_Public. Defines all hooks for the public side of the site.
	 * - etc.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

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

		/**
		 * Admin area functionality for display, channel and slide.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-foyer-admin-display.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-foyer-admin-channel.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-foyer-admin-slide.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-foyer-admin-preview.php';

		/**
		 * Admin area functionality for specific slide formats.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-foyer-admin-slide-format-pdf.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-foyer-admin-slide-format-production.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-foyer-admin-slide-format-video.php';

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

		$this->setup = new Foyer_Setup( $this->get_plugin_name(), $this->get_version() );

		$this->admin = new Foyer_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->admin_channel = new Foyer_Admin_Channel( $this->get_plugin_name(), $this->get_version() );
		$this->admin_display = new Foyer_Admin_Display( $this->get_plugin_name(), $this->get_version() );
		$this->admin_slide = new Foyer_Admin_Slide( $this->get_plugin_name(), $this->get_version() );
		$this->admin_preview = new Foyer_Admin_Preview( $this->get_plugin_name(), $this->get_version() );

		$this->public = new Foyer_Public( $this->get_plugin_name(), $this->get_version() );
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Foyer_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Foyer_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		// Admin
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $this->admin, 'admin_menu' );

		// Admin Display
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin_display, 'localize_scripts' );
		$this->loader->add_action( 'add_meta_boxes', $this->admin_display, 'add_channel_editor_meta_box' );
		$this->loader->add_action( 'add_meta_boxes', $this->admin_display, 'add_channel_scheduler_meta_box' );
		$this->loader->add_action( 'save_post', $this->admin_display, 'save_display' );
		$this->loader->add_filter( 'manage_'.Foyer_Display::post_type_name.'_posts_columns', $this->admin_display, 'add_channel_columns' );
		$this->loader->add_action( 'manage_'.Foyer_Display::post_type_name.'_posts_custom_column', $this->admin_display, 'do_channel_columns', 10, 2 );

		// Admin Channel
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin_channel, 'localize_scripts' );
		$this->loader->add_action( 'add_meta_boxes', $this->admin_channel, 'add_slides_editor_meta_box', 20 );
		$this->loader->add_action( 'add_meta_boxes', $this->admin_channel, 'add_slides_settings_meta_box', 40 );
		$this->loader->add_action( 'save_post', $this->admin_channel, 'save_channel' );
		$this->loader->add_action( 'wp_ajax_foyer_slides_editor_add_slide', $this->admin_channel, 'add_slide_over_ajax' );
		$this->loader->add_action( 'wp_ajax_foyer_slides_editor_remove_slide', $this->admin_channel, 'remove_slide_over_ajax' );
		$this->loader->add_action( 'wp_ajax_foyer_slides_editor_reorder_slides', $this->admin_channel, 'reorder_slides_over_ajax' );
		$this->loader->add_filter( 'get_sample_permalink_html', $this->admin_channel, 'remove_sample_permalink' );
		$this->loader->add_filter( 'manage_'.Foyer_Channel::post_type_name.'_posts_columns', $this->admin_channel, 'add_slides_count_column' );
		$this->loader->add_action( 'manage_'.Foyer_Channel::post_type_name.'_posts_custom_column', $this->admin_channel, 'do_slides_count_column', 10, 2 );

		// Admin Slide
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin_slide, 'localize_scripts' );
		$this->loader->add_action( 'add_meta_boxes', $this->admin_slide, 'add_slide_editor_meta_boxes' );
		$this->loader->add_action( 'save_post', $this->admin_slide, 'save_slide' );
		$this->loader->add_filter( 'get_sample_permalink_html', $this->admin_slide, 'remove_sample_permalink' );
		$this->loader->add_filter( 'manage_'.Foyer_Slide::post_type_name.'_posts_columns', $this->admin_slide, 'add_slide_format_column' );
		$this->loader->add_action( 'manage_'.Foyer_Slide::post_type_name.'_posts_custom_column', $this->admin_slide, 'do_slide_format_column', 10, 2 );

		// Admin Preview
		$this->loader->add_action( 'wp_enqueue_scripts', $this->admin_preview, 'enqueue_scripts' );
		$this->loader->add_filter( 'show_admin_bar', $this->admin_preview, 'hide_admin_bar' );
		$this->loader->add_action( 'wp_ajax_foyer_preview_save_orientation_choice', $this->admin_preview, 'save_orientation_choice' );
		$this->loader->add_action( 'wp_ajax_nopriv_foyer_preview_save_orientation_choice', $this->admin_preview, 'save_orientation_choice' );

		// Admin slide formats
		$this->loader->add_filter( 'wp_image_editors', 'Foyer_Admin_Slide_Format_PDF', 'add_foyer_imagick_image_editor' );
		$this->loader->add_filter( 'delete_attachment', 'Foyer_Admin_Slide_Format_PDF', 'delete_pdf_images_for_attachment' );
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
		$this->loader->add_filter( 'foyer/slides/formats', 'Foyer_Slide_Formats', 'add_production_slide_format');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Foyer_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
