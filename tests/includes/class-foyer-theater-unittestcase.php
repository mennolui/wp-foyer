<?php

class Foyer_Theater_UnitTestCase extends Foyer_UnitTestCase {

	function setUp() {

		// Load Theater plugin (if not loaded already)
		global $wp_theatre;
		require_once dirname( dirname( __FILE__ ) ) . '/../../../plugins/theatre/theater.php';

		parent::setUp();

		$production_args = array(
			'post_type' => WPT_Production::post_type_name,
		);
		$event_args = array(
			'post_type' => WPT_Event::post_type_name,
		);

		/* Set up categories */
		$this->category_concert = wp_create_category( 'concert' );
		$this->category_film = wp_create_category( 'film' );

		/* Create production 1 with upcoming event, category 'concert' & 'film' */
		$this->production1 = $this->factory->post->create( $production_args );
		$event_id = $this->factory->post->create( $event_args );
		add_post_meta( $event_id, WPT_Production::post_type_name, $this->production1, true );
		add_post_meta( $event_id, 'event_date', date( 'Y-m-d H:i:s', time() + ( 2 * DAY_IN_SECONDS ) ) );
		wp_set_post_categories( $this->production1, array( $this->category_concert, $this->category_film ) );

		/* Create production 2 with upcoming event, category 'film' */
		$this->production2 = $this->factory->post->create( $production_args );
		$event_id = $this->factory->post->create( $event_args );
		add_post_meta( $event_id, WPT_Production::post_type_name, $this->production2, true );
		add_post_meta( $event_id, 'event_date', date( 'Y-m-d H:i:s', time() + ( 3 * DAY_IN_SECONDS ) ) );
		wp_set_post_categories( $this->production2, array( $this->category_film ) );

		/* Create production 3 with past event, category 'concert' */
		$this->production3 = $this->factory->post->create( $production_args );
		$event_id = $this->factory->post->create( $event_args );
		add_post_meta( $event_id, WPT_Production::post_type_name, $this->production3, true );
		add_post_meta( $event_id, 'event_date', date( 'Y-m-d H:i:s', time() - ( 2 * DAY_IN_SECONDS ) ) );
		wp_set_post_categories( $this->production3, array( $this->category_concert ) );
	}
}