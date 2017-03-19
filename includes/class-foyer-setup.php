<?php

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
					'name' => _x( 'Displays', 'display cpt', 'foyer' ),
					'singular_name' => _x( 'Display', 'display cpt', 'foyer'),
					'add_new' =>  _x( 'Add New', 'display cpt', 'foyer'),
					'new_item' => _x( 'New display', 'display cpt', 'foyer' ),
					'view_item' => _x( 'View display', 'display cpt', 'foyer' ),
					'add_new_item' => _x( 'Add new display', 'display cpt', 'foyer' ),
					'edit_item' => _x( 'Edit display', 'display cpt', 'foyer' ),
				),
				'public' => true,
				'has_archive' => false,
				'show_in_menu' => 'foyer',
				'show_in_admin_bar' => true,
	  			'supports' => array( 'title' ),
	  			'taxonomies' => array(),
	  			'rewrite' => array( 'slug' => 'foyer' ),
			)
		);

		register_post_type( Foyer_Channel::post_type_name,
			array(
				'labels' => array(
					'name' => _x( 'Channels', 'channel cpt', 'foyer' ),
					'singular_name' => _x( 'Channel', 'channel cpt', 'foyer'),
					'add_new' =>  _x( 'Add New', 'channel cpt', 'foyer'),
					'new_item' => _x( 'New channel', 'channel cpt', 'foyer' ),
					'view_item' => _x( 'View channel', 'channel cpt', 'foyer' ),
					'add_new_item' => _x( 'Add new channel', 'channel cpt', 'foyer' ),
					'edit_item' => _x( 'Edit channel', 'channel cpt', 'foyer' ),
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
					'name' => _x( 'Slides', 'slide cpt', 'foyer' ),
					'singular_name' => _x( 'Slide', 'slide cpt', 'foyer' ),
					'add_new' =>  _x( 'Add New', 'slide cpt', 'foyer'),
					'new_item' => _x( 'New slide', 'slide cpt', 'foyer' ),
					'view_item' => _x( 'View slide', 'slide cpt', 'foyer' ),
					'add_new_item' => _x( 'Add new slide', 'slide cpt', 'foyer' ),
					'edit_item' => _x( 'Edit slide', 'slide cpt', 'foyer' ),
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
