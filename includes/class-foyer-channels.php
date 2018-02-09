<?php

/**
 * The class that holds all helper functions for channels.
 *
 * @since		1.4.0
 *
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Channels {

	/**
	 * Gets all channel posts.
	 *
	 * @since	1.4.0
	 *
	 * @param	array				$args	Additional args for get_posts().
	 * @return	array of WP_Post			The channel posts.
	 */
	static function get_posts( $args = array() ) {
		$defaults = array(
			'post_type' => Foyer_Channel::post_type_name,
			'posts_per_page' => -1,
		);

		$args = wp_parse_args( $args, $defaults );

		return get_posts( $args );
	}
}
