<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://mennoluitjes.nl
 * @since             1.0.0
 * @package           Foyer
 *
 * @wordpress-plugin
 * Plugin Name:       Foyer - Digital Signage for WordPress
 * Plugin URI:        http://mennoluitjes.nl
 * Description:       Create slideshows and show them off on your networked displays.
 * Version:           1.2.5
 * Author:            Menno Luitjes
 * Author URI:        http://mennoluitjes.nl
 * License:           GPL-3.0+
 * License URI:       https://www.gnu.org/licenses/gpl.html
 * Text Domain:       foyer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-foyer-activator.php
 */
function activate_foyer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-foyer-activator.php';
	Foyer_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-foyer-deactivator.php
 */
function deactivate_foyer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-foyer-deactivator.php';
	Foyer_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_foyer' );
register_deactivation_hook( __FILE__, 'deactivate_foyer' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-foyer.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_foyer() {

	$plugin = new Foyer();
	$plugin->run();

}
run_foyer();
