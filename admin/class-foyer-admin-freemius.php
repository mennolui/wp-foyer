<?php

/**
 * The class that holds all shared Freemius functionality.
 *
 * @since		1.X.X
 *
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Freemius {

	static function init() {

		if ( ! function_exists( 'foyer_fs' ) ) {
			return;
		}

		foyer_fs()->add_filter( 'connect_message_on_update', array( __CLASS__, 'custom_connect_message_on_update' ), 10, 6 );
		foyer_fs()->add_filter( 'connect_message', array( __CLASS__, 'custom_connect_message' ), 10, 6 );
	}

	static function custom_connect_message(
		$message,
		$user_first_name,
		$plugin_title,
		$user_login,
		$site_link,
		$freemius_link
	) {
		return sprintf(
			__( 'Hi there, thanks for installing %2$s!', 'foyer' ) . '<br><br>' .
			__( 'Would you help me improve this plugin?', 'foyer' ) . '<br><br>' .
			__( 'If you opt-in, some data about your usage of %2$s will be sent to %5$s. And I will be able to send you important security and feature update notifications.', 'foyer' ) . '<br><br>' .
			__( 'If you don\'t want this, that\'s okay too! Just skip and everything will still work just fine.', 'foyer' ),
			$user_first_name,
			'<b>' . $plugin_title . '</b>',
			'<b>' . $user_login . '</b>',
			$site_link,
			$freemius_link
		);
	}

	static function custom_connect_message_on_update(
		$message,
		$user_first_name,
		$plugin_title,
		$user_login,
		$site_link,
		$freemius_link
	) {
		return sprintf(
			__( 'Hi there, thanks for using %2$s!', 'foyer' ) . '<br><br>' .
			__( 'Would you help me improve this plugin?', 'foyer' ) . '<br><br>' .
			__( 'If you opt-in, some data about your usage of %2$s will be sent to %5$s. And I will be able to send you important security and feature update notifications.', 'foyer' ) . '<br><br>' .
			__( 'If you don\'t want this, that\'s okay too! Just skip and everything will still work just fine.', 'foyer' ),
			$user_first_name,
			'<b>' . $plugin_title . '</b>',
			'<b>' . $user_login . '</b>',
			$site_link,
			$freemius_link
		);
	}
}
