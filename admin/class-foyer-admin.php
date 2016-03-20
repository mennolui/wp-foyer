<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://mennoluitjes.nl
 * @since      1.0.0
 *
 * @package    Foyer
 * @subpackage Foyer/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Foyer
 * @subpackage Foyer/admin
 * @author     Menno Luitjes <menno@mennoluitjes.nl>
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
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	function admin_menu() {
		add_menu_page( __( 'Foyer', 'foyer' ), __( 'Foyer', 'foyer' ), 'edit_posts', 'foyer', array(), 'dashicons-welcome-view-site', 31 );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Foyer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Foyer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/foyer-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Foyer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Foyer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/foyer-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Registers the custom post type for slides and channels.
	 *
	 * @since 	1.0.0
	 *
	 * @return void
	 */
	public function register_post_types() {

		register_post_type( Foyer_Channel::post_type_name,
			array(
				'labels' => array(
					'name' => __( 'Channels', 'foyer' ),
					'singular_name' => __( 'Channel', 'foyer'),
					'add_new' =>  _x( 'Add New', 'channel', 'foyer'),
					'new_item' => __( 'New channel', 'foyer' ),
					'add_new_item' => __( 'Add new channel', 'foyer' ),
					'edit_item' => __( 'Edit channel', 'foyer' ),
				),
				'public' => true,
				'has_archive' => false,
				'show_in_menu' => 'foyer',
				'show_in_admin_bar' => true,
	  			'supports' => array( 'title' ),
	  			'taxonomies' => array(),
	  			'rewrite' => false,
			)
		);

		register_post_type( Foyer_Slide::post_type_name,
			array(
				'labels' => array(
					'name' => __( 'Slides', 'foyer' ),
					'singular_name' => __( 'Slide', 'foyer' ),
					'add_new' =>  _x( 'Add New', 'slide', 'foyer'),
					'new_item' => __( 'New slide', 'foyer' ),
					'add_new_item' => __( 'Add new slide', 'foyer' ),
					'edit_item' => __( 'Edit slide', 'foyer' ),
				),
				'public' => true,
				'has_archive' => false,
				'show_in_menu' => 'foyer',
				'show_in_admin_bar' => true,
	  			'supports' => array( 'title', 'editor', 'thumbnail' ),
	  			'taxonomies' => array(),
	  			'rewrite' => false,
			)
		);

	}

}
