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
	 */
	static function init() {
		self::load_dependencies();

		/* Foyer_Public */
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'init', array( __CLASS__, 'add_image_sizes' ) );

		add_filter( 'wp_get_attachment_image_attributes', array( __CLASS__, 'add_cropped_images_to_foyer_fhd_square_srcset' ), 10, 3 );

		/* Foyer_Templates */
		add_action( 'template_include', array( 'Foyer_Templates', 'template_include' ) );
	}



	/**
	 * Adds landscape and portrait cropped images to the srcset of foyer_fhd_square images.
	 *
	 * @since	1.5.1
	 *
	 * @param	array	$attr			The attachment attributes.
	 * @param	WP_Post	$attachment		The attachment.
	 * @param 	string	$size			The attachment size.
	 * @return	array					The attachment attributes with the srcset and sizes set to include a mobile image.
	 */
	static function add_cropped_images_to_foyer_fhd_square_srcset( $attr, $attachment, $size ) {

		if ( 'foyer_fhd_square' == $size ) {
			$attachment_landscape = wp_get_attachment_image_src( $attachment->ID, 'foyer_fhd_landscape' );
			$attachment_portrait = wp_get_attachment_image_src( $attachment->ID, 'foyer_fhd_portrait' );
			$attr['srcset'] = '';
			$attr['srcset'] .= $attachment_portrait[0] . ' ' . $attachment_portrait[1] . 'w, ';
			$attr['srcset'] .= $attachment_landscape[0] . ' ' . $attachment_landscape[1] . 'w'; // src width

			$attr['sizes'] = '';
			$attr['sizes'] .= '(orientation: portrait) ' . $attachment_portrait[1] / 2 . 'px, ';
			$attr['sizes'] .= '(orientation: landscape) ' . $attachment_landscape[1] / 2 . 'px, ';
		}

		return $attr;
	}


	/**
	 * Adds image sizes used throughout the front-end of the plugin.
	 *
	 * See https://en.wikipedia.org/wiki/Display_resolution for a list of display resolutions and their names.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 * @since	1.5.1	Added additional image sizes to enable correct orientation aware cropping.
	 *
	 * @return	void
	 */
	static function add_image_sizes() {

		// Full HD (1920 x 1080) square
		add_image_size( 'foyer_fhd_square', 1920, 1920, true );

		// Full HD landscape (1920 x 1080)
		add_image_size( 'foyer_fhd_landscape', 1920, 1080, true );

		// Full HD portrait (1080 x 1920)
		add_image_size( 'foyer_fhd_portrait', 1080, 1920, true );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since	1.0.0
	 * @since	1.2.5	Added a 'foyer/public/enqueue_scripts' action.
	 * @since	1.2.5	Register scripts before they are enqueued.
	 *					Makes it possible to enqueue foyer scripts outside of the foyer plugin.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return	void
	 */
	static function enqueue_scripts() {

		wp_register_script( Foyer::get_plugin_name(), plugin_dir_url( __FILE__ ) . 'js/foyer-public-min.js', array( 'jquery' ), Foyer::get_version(), false );

		if ( ! is_singular( array( Foyer_Display::post_type_name, Foyer_Channel::post_type_name, Foyer_Slide::post_type_name) ) ) {
			return;
		}

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
