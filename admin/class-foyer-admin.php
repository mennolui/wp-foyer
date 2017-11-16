<?php

/**
 * The general admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since		1.0.0
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	1.0.0
	 * @param	string	$plugin_name	The name of this plugin.
	 * @param	string	$version		The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	function admin_menu() {
		add_menu_page( __( 'Foyer', 'foyer' ), __( 'Foyer', 'foyer' ), 'edit_posts', 'foyer', array(), 'dashicons-welcome-view-site', 31 );
	}

	/**
	 * Enqueues the JavaScript for the admin area.
	 *
	 * @since	1.0.0
	 * @since	1.2.5	Register scripts before they are enqueued.
	 *					Makes it possible to enqueue foyer scripts outside of the foyer plugin.
	 *					Changed handle of script to {plugin_name}_admin.
	 */
	public function enqueue_scripts() {

		wp_register_script( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'js/foyer-admin-min.js', array( 'jquery', 'jquery-ui-sortable' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '-admin' );
	}

	/**
	 * Enqueues the stylesheets for the admin area.
	 *
	 * @since	1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/foyer-admin.css', array(), $this->version, 'all' );
	}
}
