<?php

/**
 * The class that holds all general (not public/admin) setup functionality.
 *
 * @link       http://mennoluitjes.nl
 * @since      1.0.0
 *
 * @package    Foyer
 * @subpackage Foyer/includes
 */

/**
 * The class that holds all general (not public/admin) setup functionality.
 *
 * @package    Foyer
 * @subpackage Foyer/includes
 * @author     Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Setup {

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

	/**
	 * Registers the custom post type for slides and channels.
	 *
	 * @since 	1.0.0
	 *
	 * @return void
	 */
	public function register_post_types() {

		register_post_type( Foyer_Display::post_type_name,
			array(
				'labels' => array(
					'name' => __( 'Displays', 'foyer' ),
					'singular_name' => __( 'Display', 'foyer'),
					'add_new' =>  _x( 'Add New', 'display', 'foyer'),
					'new_item' => __( 'New display', 'foyer' ),
					'add_new_item' => __( 'Add new display', 'foyer' ),
					'edit_item' => __( 'Edit display', 'foyer' ),
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
	  			'supports' => array( 'title' ),
	  			'taxonomies' => array(),
	  			'rewrite' => false,
			)
		);

	}
}
