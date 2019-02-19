<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://mennoluitjes.nl
 * @since             1.0.0
 * @package           Foyer
 *
 * @wordpress-plugin
 * Plugin Name:       Foyer - Digital Signage for WordPress
 * Plugin URI:        https://mennoluitjes.nl
 * Description:       Create slideshows and show them off on your networked displays.
 * Version:           1.7.1
 * Author:            Menno Luitjes
 * Author URI:        https://mennoluitjes.nl
 * License:           GPL-3.0+
 * License URI:       https://www.gnu.org/licenses/gpl.html
 * Text Domain:       foyer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! function_exists( 'foyer_fs' ) ) {
    // Create a helper function for easy SDK access.
    function foyer_fs() {
        global $foyer_fs;

        if ( ! isset( $foyer_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $foyer_fs = fs_dynamic_init( array(
                'id'                  => '2853',
                'slug'                => 'foyer',
                'type'                => 'plugin',
                'public_key'          => 'pk_86d35013a3140a70d2554f2a74188',
                'is_premium'          => false,
                'has_addons'          => true,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'slug'           => 'foyer',
                    'contact'        => false,
                    'support'        => false,
                ),
            ) );
        }

        return $foyer_fs;
    }

    // Init Freemius.
    foyer_fs();
    // Signal that SDK was initiated.
    do_action( 'foyer_fs_loaded' );
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
 * @since	1.0.0
 * @since	1.3.2	Defined some named constants to be used throughout the plugin.
 */
function run_foyer() {

	define( 'FOYER_PLUGIN_VERSION', '1.7.1' ); // do not access directly
	define( 'FOYER_PLUGIN_NAME', 'foyer' ); // do not access directly
	define( 'FOYER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
	define( 'FOYER_PLUGIN_URL', trailingslashit( plugins_url( '', __FILE__ ) ) );
	define( 'FOYER_PLUGIN_FILE', __FILE__ );

	Foyer::init();
}

run_foyer();
