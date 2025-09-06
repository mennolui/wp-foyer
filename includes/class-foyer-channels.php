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

    /**
     * Gets channel posts marked as favorite.
     *
     * @since 1.8.0
     *
     * @param array $args Additional args for get_posts().
     * @return array of WP_Post The favorite channel posts.
     */
    static function get_favorites( $args = array() ) {
        $meta_query = array(
            array(
                'key'   => 'foyer_channel_is_favorite',
                'value' => '1',
            )
        );

        $args = wp_parse_args( $args, array(
            'post_type'      => Foyer_Channel::post_type_name,
            'posts_per_page' => -1,
            'meta_query'     => $meta_query,
        ) );

        return get_posts( $args );
    }
}
