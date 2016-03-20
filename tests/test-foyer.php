<?php

class Foyer_Test extends WP_UnitTestCase {

	function setUp() {
		parent::setUp();

		$slide_args = array(
			'post_type' => Foyer_Slide::post_type_name,
		);
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
		);

		/* Create slides */
		$this->slide1 = $this->factory->post->create( $slide_args );
		$this->slide2 = $this->factory->post->create( $slide_args );
		$this->slide3 = $this->factory->post->create( $slide_args );

		/* Create channel with two slides */
		$this->channel1 = $this->factory->post->create( $channel_args );
		add_post_meta( $this->channel1, Foyer_Slide::post_type_name, $this->slide1 );
		add_post_meta( $this->channel1, Foyer_Slide::post_type_name, $this->slide2 );

		/* Create channel with one slide */
		$this->channel2 = $this->factory->post->create( $channel_args );
		add_post_meta( $this->channel2, Foyer_Slide::post_type_name, $this->slide1 );
	}


	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}


	function test_does_channel_have_slides() {

		$channel = new Foyer_Channel( $this->channel1 );
		$slides = $channel->get_slides();

		$this->assertCount( 2, $slides );
	}
}

