<?php

/**
 * The class that handles template files for slides.
 *
 * @since		1.0.0
 *
 * @package		Foyer
 * @subpackage	Foyer/public
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Templates {

	/**
	 * Gets a template.
	 *
	 * Search for the template and include the file.
	 *
	 * Inspired by https://jeroensormani.com/how-to-add-template-files-in-your-plugin/
	 *
	 * @since	1.0.0
	 * @since	1.5.7	Renamed the overly generic $args to $template_args so it can be re-used within templates.
	 *
	 * @param	string 	$template_name			Template to load.
	 * @param	array 	$template_args			Args passed for the template file.
	 * @param	string 	$string $template_path	Path to templates.
	 * @param	string	$default_path			Default path to template files.
	 * @return	void
	 */
	static function get_template( $template_name, $template_args = array(), $template_path = '', $default_path = '' ) {
		if ( is_array( $template_args ) && isset( $template_args ) ) {
			extract( $template_args );
		}
		$template_file = self::locate_template( $template_name, $template_path, $default_path );
		if ( ! file_exists( $template_file ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), FOYER_PLUGIN_VERSION );
			return false;
		}
		include $template_file;
	}

	/**
	 * Gets all template paths registered by plugins.
	 *
	 * Add-ons can add their plugin template path using Foyer_Templates::register_plugin_template_path().
	 *
	 * @since	1.7.2
	 *
	 * @return	array	All registered plugin template paths.
	 */
	static function get_plugin_template_paths() {

		$plugin_template_paths = array();

		/**
		 * Filter the plugin template paths.
		 *
		 * @since	1.7.2
		 * @param	array	$plugin_template_paths	The currently registered plugin template paths.
		 */
		$plugin_template_paths = apply_filters( 'foyer/templates/plugin_template_paths', $plugin_template_paths );

		return $plugin_template_paths;
	}

	/**
	 * Locates a Foyer template.
	 *
	 * Search Order:
	 * 1. /themes/theme/foyer/$template_name
	 * 2. <registered plugin template paths>/$template_name.
	 * 3. /plugins/foyer/public/templates/$template_name.
	 *
	 * Inspired by https://jeroensormani.com/how-to-add-template-files-in-your-plugin/
	 *
	 * @since	1.0.0
	 * @since	1.7.2	Removed searching the /themes/theme/$template_name path as this is bound to cause
	 *					conflicts with generic template names.
	 * @since	1.7.2	Added searching registered plugin template paths. This adds add-on plugin support.
	 *
	 * @param 	string 	$template_name		Template to load.
	 * @param 	string 	$template_path		Path to templates.
	 * @param 	string	$default_path		Default path to template files.
	 * @return 	string 						Path to the template file.
	 */
	static function locate_template( $template_name, $template_path = '', $default_path = '' ) {

		// Set template path to foyer folder of theme / registered plugin.
		if ( ! $template_path ) {
			$template_path = 'foyer/';
		}
		// Set default path to templates folder of Foyer plugin.
		if ( ! $default_path ) {
			$default_path = plugin_dir_path( __FILE__ ) . 'templates/'; // Path to the template folder
		}

		// 1. Search template file in active (child)theme.
		$template = locate_template( array(
			$template_path . $template_name,
		) );

		// 2. Search template file in registered plugin template paths (if no template found in previous step).
		if ( ! $template && $plugin_paths = self::get_plugin_template_paths() ) {
			foreach ( $plugin_paths as $plugin_path ) {
				$full_path = trailingslashit( $plugin_path ) . $template_name;
				if ( file_exists( $full_path ) ) {
					$template = $full_path;
					break;
				}
			}
		}

		// 3. Fall back to template file in Foyer plugin (if no template found in previous step).
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		return apply_filters( 'foyer/templates/template', $template, $template_name, $template_path, $default_path );
	}

	/**
	 * Registers the template path for an add-on.
	 *
	 * @since 	1.7.2
	 *
	 * @param 	string 	$template_path		Path to templates for this plugin.
	 * @return	void
	 */
	static function register_plugin_template_path( $template_path ) {
		add_filter(
			'foyer/templates/plugin_template_paths',
			function( $plugin_template_paths ) use ( $template_path ) {
				$plugin_template_paths[] = $template_path;
				return $plugin_template_paths;
			},
			5
		);
	}

	/**
	 * Template loader.
	 *
	 * The template loader will check if WP is loading a template for a Foyer post type
	 * and will try to load the template from our 'templates' directory.
	 *
	 * @since	1.0.0
	 *
	 * @param	string	$template	Template file that is being loaded.
	 * @return	string				Template file that should be loaded.
	 */
	static function template_include( $template ) {

		$file = '';

		if (
			is_singular( array( Foyer_Slide::post_type_name, Foyer_Channel::post_type_name, Foyer_Display::post_type_name ) ) &&
			is_user_logged_in( ) &&
			empty( $_GET['foyer-preview'] )
		) {
			// Show inside preview iframe when logged in.
			$file = 'preview.php';
		}
		else if ( is_singular( Foyer_Slide::post_type_name ) ) {
			$file = 'single-slide.php';
		}
		else if ( is_singular( Foyer_Channel::post_type_name ) ) {
			$file = 'single-channel.php';
		}
		else if ( is_singular( Foyer_Display::post_type_name ) ) {
			$file = 'single-display.php';
		}
		else {
			return $template;
		}

		if ( file_exists( self::locate_template( $file ) ) ) {
			$template = self::locate_template( $file );
		}

		return $template;
	}
}
