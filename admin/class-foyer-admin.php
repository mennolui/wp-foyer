<?php

/**
 * Defines the admin-specific functionality of the plugin.
 *
 * @since		1.0.0
 * @since		1.3.2	Refactored class from object to static methods.
 *						Switched from using a central Foyer_Loader class to registering hooks directly
 *						on init of Foyer, Foyer_Admin and Foyer_Public.
 *
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin {

	/**
	 * Loads dependencies and registers hooks for the admin-facing side of the plugin.
	 *
	 * @since	1.3.2
	 */
	static function init() {
		self::load_dependencies();

		/* Foyer_Admin */
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
		// Scheduler page submenu and save handler
		add_action( 'admin_menu', array( 'Foyer_Admin_Scheduler', 'admin_menu' ) );
		add_action( 'admin_post_foyer_save_scheduler', array( 'Foyer_Admin_Scheduler', 'handle_post' ) );
		add_action( 'admin_post_foyer_apply_scheduler_template', array( 'Foyer_Admin_Scheduler', 'handle_apply_template' ) );

		/* Foyer_Admin_Display */
		add_action( 'admin_enqueue_scripts', array( 'Foyer_Admin_Display', 'localize_scripts' ) );
		add_action( 'admin_notices', array( 'Foyer_Admin_Display', 'render_notices' ) );
		add_action( 'wp_ajax_foyer_validate_schedule', array( 'Foyer_Admin_Display', 'validate_schedule_over_ajax' ) );
		add_action( 'add_meta_boxes', array( 'Foyer_Admin_Display', 'add_channel_editor_meta_box' ) );
			// Only use the new multi-entry scheduler list UI
			add_action( 'add_meta_boxes', array( 'Foyer_Admin_Display', 'add_channel_scheduler_list_meta_box' ) );
		add_action( 'save_post', array( 'Foyer_Admin_Display', 'save_display' ) );
		add_filter( 'manage_'.Foyer_Display::post_type_name.'_posts_columns', array( 'Foyer_Admin_Display', 'add_channel_columns' ) );
		add_action( 'manage_'.Foyer_Display::post_type_name.'_posts_custom_column', array( 'Foyer_Admin_Display', 'do_channel_columns' ), 10, 2 );
		/* Foyer_Admin_Channel */
		add_action( 'admin_enqueue_scripts', array( 'Foyer_Admin_Channel', 'localize_scripts' ) );
		// Order favorites first in Channels list via SQL clause filter
		add_filter( 'posts_clauses', array( 'Foyer_Admin_Channel', 'order_favorites_first_clause' ), 10, 2 );
		add_action( 'add_meta_boxes', array( 'Foyer_Admin_Channel', 'add_slides_editor_meta_box' ), 20 );
		add_action( 'add_meta_boxes', array( 'Foyer_Admin_Channel', 'add_slides_settings_meta_box' ), 40 );
		add_action( 'save_post', array( 'Foyer_Admin_Channel', 'save_channel' ) );
			add_action( 'wp_ajax_foyer_slides_editor_add_slide', array( 'Foyer_Admin_Channel', 'add_slide_over_ajax' ) );
			add_action( 'wp_ajax_foyer_slides_editor_remove_slide', array( 'Foyer_Admin_Channel', 'remove_slide_over_ajax' ) );
			add_action( 'wp_ajax_foyer_slides_editor_reorder_slides', array( 'Foyer_Admin_Channel', 'reorder_slides_over_ajax' ) );
			add_action( 'wp_ajax_foyer_channel_set_slide_window', array( 'Foyer_Admin_Channel', 'set_slide_window_over_ajax' ) );
			add_action( 'wp_ajax_foyer_channel_toggle_favorite', array( 'Foyer_Admin_Channel', 'toggle_favorite_over_ajax' ) );
		add_filter( 'get_sample_permalink_html', array( 'Foyer_Admin_Channel', 'remove_sample_permalink' ) );
		add_filter( 'manage_'.Foyer_Channel::post_type_name.'_posts_columns', array( 'Foyer_Admin_Channel', 'add_slides_count_column' ) );
		add_action( 'manage_'.Foyer_Channel::post_type_name.'_posts_custom_column', array( 'Foyer_Admin_Channel', 'do_slides_count_column' ), 10, 2 );

		/* Foyer_Admin_Slide */
		add_action( 'admin_enqueue_scripts', array( 'Foyer_Admin_Slide', 'localize_scripts' ) );
		add_action( 'add_meta_boxes', array( 'Foyer_Admin_Slide', 'add_slide_editor_meta_boxes' ) );
		add_action( 'save_post', array( 'Foyer_Admin_Slide', 'save_slide' ) );
		add_filter( 'get_sample_permalink_html', array( 'Foyer_Admin_Slide', 'remove_sample_permalink' ) );
		add_filter( 'manage_'.Foyer_Slide::post_type_name.'_posts_columns', array( 'Foyer_Admin_Slide', 'add_slide_format_column' ) );
		add_action( 'manage_'.Foyer_Slide::post_type_name.'_posts_custom_column', array( 'Foyer_Admin_Slide', 'do_slide_format_column' ), 10, 2 );

		/* Foyer_Admin_Preview */
		add_action( 'wp_enqueue_scripts', array( 'Foyer_Admin_Preview', 'enqueue_scripts' ) );
		add_filter( 'show_admin_bar', array( 'Foyer_Admin_Preview', 'hide_admin_bar' ) );
		add_action( 'wp_ajax_foyer_preview_save_orientation_choice', array( 'Foyer_Admin_Preview', 'save_orientation_choice' ) );
		add_action( 'wp_ajax_nopriv_foyer_preview_save_orientation_choice', array( 'Foyer_Admin_Preview', 'save_orientation_choice' ) );

		/* Foyer_Admin_Slide_Format_PDF */
		add_filter( 'wp_image_editors', array( 'Foyer_Admin_Slide_Format_PDF', 'add_foyer_imagick_image_editor' ) );
		add_action( 'delete_attachment', array( 'Foyer_Admin_Slide_Format_PDF', 'delete_pdf_images_for_attachment' ) );
		add_action( 'admin_notices', array( 'Foyer_Admin_Slide_Format_PDF', 'display_admin_notice' ) );
	}

	/**
	 * Adds the top-level Foyer admin menu item.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *					Added context for translations.
	 * @since	1.5.1	Improved the context of the translatable string 'Foyer' to make translation easier.
	 */
	static function admin_menu() {
		add_menu_page(
			_x( 'Foyer', 'plugin name in admin menu', 'foyer' ),
			_x( 'Foyer', 'plugin name in admin menu', 'foyer' ),
			'edit_posts',
			'foyer',
			array(),
			'dashicons-welcome-view-site',
			31
		);
	}

	/**
	 * Enqueues the JavaScript for the admin area.
	 *
	 * @since	1.0.0
	 * @since	1.2.5	Register scripts before they are enqueued.
	 *					Makes it possible to enqueue Foyer scripts outside of the Foyer plugin.
	 *					Changed handle of script to {plugin_name}-admin.
	 * @since	1.3.2	Changed method to static.
	 */
	static function enqueue_scripts() {

		wp_register_script( Foyer::get_plugin_name() . '-admin', plugin_dir_url( __FILE__ ) . 'js/foyer-admin-min.js', array( 'jquery', 'jquery-ui-sortable' ), Foyer::get_version(), false );
		wp_enqueue_script( Foyer::get_plugin_name() . '-admin' );

		// Ensure datetimepicker does not normalize on blur, which can cause 1899 fallback dates
		$inline = "jQuery(function($){ try { if ($.fn && $.fn.foyer_datetimepicker && $.fn.foyer_datetimepicker.defaults) { $.fn.foyer_datetimepicker.defaults.validateOnBlur = false; } } catch(e){} });";
		wp_add_inline_script( Foyer::get_plugin_name() . '-admin', $inline, 'after' );
	}

	/**
	 * Enqueues the stylesheets for the admin area.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 */
	static function enqueue_styles() {

		wp_enqueue_style( Foyer::get_plugin_name(), plugin_dir_url( __FILE__ ) . 'css/foyer-admin.css', array(), Foyer::get_version(), 'all' );
	}

	/**
	 * Loads the required dependencies for the admin-facing side of the plugin.
	 *
	 * @since	1.3.2
	 * @since	1.4.0	Included admin/class-foyer-admin-slide-background-image.php.
	 *					Included admin/class-foyer-admin-slide-background-video.php.
	 *					Removed include admin/class-foyer-admin-slide-format-video.php.
	 * @since	1.6.0	Included the HTML5 Video slide background admin.
	 * @since	1.7.0	Included the Upcoming Productions slide background admin.
	 *
	 * @access	private
	 */
	private static function load_dependencies() {

		/**
		 * Admin area functionality for display, channel and slide.
		 */
		require_once FOYER_PLUGIN_PATH . 'admin/class-foyer-admin-display.php';
		require_once FOYER_PLUGIN_PATH . 'admin/class-foyer-admin-channel.php';
		require_once FOYER_PLUGIN_PATH . 'admin/class-foyer-admin-slide.php';
		require_once FOYER_PLUGIN_PATH . 'admin/class-foyer-admin-preview.php';

		/**
		 * Admin area functionality for specific slide backgrounds.
		 */
		require_once FOYER_PLUGIN_PATH . 'admin/class-foyer-admin-slide-background-image.php';
		require_once FOYER_PLUGIN_PATH . 'admin/class-foyer-admin-slide-background-video.php';
		require_once FOYER_PLUGIN_PATH . 'admin/class-foyer-admin-slide-background-html5-video.php';

		/**
		 * Admin area functionality for specific slide formats.
		 */
		require_once FOYER_PLUGIN_PATH . 'admin/class-foyer-admin-slide-format-iframe.php';
		require_once FOYER_PLUGIN_PATH . 'admin/class-foyer-admin-slide-format-pdf.php';
		require_once FOYER_PLUGIN_PATH . 'admin/class-foyer-admin-slide-format-post.php';
		require_once FOYER_PLUGIN_PATH . 'admin/class-foyer-admin-slide-format-production.php';
		require_once FOYER_PLUGIN_PATH . 'admin/class-foyer-admin-slide-format-recent-posts.php';
		require_once FOYER_PLUGIN_PATH . 'admin/class-foyer-admin-slide-format-text.php';
        require_once FOYER_PLUGIN_PATH . 'admin/class-foyer-admin-slide-format-upcoming-productions.php';
		// Scheduler admin page
		require_once FOYER_PLUGIN_PATH . 'admin/class-foyer-admin-scheduler.php';
	}
}
