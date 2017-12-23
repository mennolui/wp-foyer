<?php

class Test_Foyer_Updater extends Foyer_UnitTestCase {

	function test_is_database_version_updated_after_plugin_update() {
		// Set to really old version to trigger database update
		Foyer_Updater::update_db_version( '1.0.0' );

		$actual = Foyer_Updater::update();
		$expected = true;
		$this->assertEquals( $expected, $actual );

		$actual = Foyer_Updater::get_db_version();
		$expected = Foyer::get_version();
		$this->assertEquals( $expected, $actual );
	}

	function test_is_database_version_updated_after_plugin_update_with_no_database_version_set() {
		// Remove database version to trigger database update
		delete_option( 'foyer_plugin_version' );

		$actual = Foyer_Updater::update();
		$expected = true;
		$this->assertEquals( $expected, $actual );

		$actual = Foyer_Updater::get_db_version();
		$expected = Foyer::get_version();
		$this->assertEquals( $expected, $actual );
	}

	function test_is_database_update_skipped_when_database_is_up_to_date() {
		// Set ddatabase version to current plugin version
		Foyer_Updater::update_db_version( Foyer::get_version() );

		$actual = Foyer_Updater::update();
		$expected = false;
		$this->assertEquals( $expected, $actual );
	}

	function test_are_slides_correctly_converted_on_update_to_1_4_0() {
		$this->assume_role( 'administrator' );

		$file = dirname( __FILE__ ) . '/assets/Kip-400x400.jpg';
		$default_image_id = $this->factory->attachment->create_upload_object( $file );
		$production_image_id = $this->factory->attachment->create_upload_object( $file );

		$video_url = 'https://youtu.be/r9tbusKyvMY';
		$video_start = '35';
		$video_end = '60';
		$hold_slide = '1';

		$slide_args = array(
			'post_type' => Foyer_Slide::post_type_name,
		);

		/* Create slides */
		$slide_1_id = $this->factory->post->create( $slide_args );
		update_post_meta( $slide_1_id, 'slide_format', 'default' );
		update_post_meta( $slide_1_id, 'slide_default_image', $default_image_id );

		$slide_2_id = $this->factory->post->create( $slide_args );
		update_post_meta( $slide_2_id, 'slide_format', 'production' );
		update_post_meta( $slide_2_id, 'slide_production_image', $production_image_id );

		$slide_3_id = $this->factory->post->create( $slide_args );
		update_post_meta( $slide_3_id, 'slide_format', 'video' );
		update_post_meta( $slide_3_id, 'slide_video_video_url', $video_url );
		update_post_meta( $slide_3_id, 'slide_video_video_start', $video_start );
		update_post_meta( $slide_3_id, 'slide_video_video_end', $video_end );
		update_post_meta( $slide_3_id, 'slide_video_hold_slide', $hold_slide );

		$slide_4_id = $this->factory->post->create( $slide_args );
		update_post_meta( $slide_4_id, 'slide_format', 'default' );
		update_post_meta( $slide_4_id, 'slide_default_image', '' );

		$slide_5_id = $this->factory->post->create( $slide_args );
		update_post_meta( $slide_5_id, 'slide_format', 'video' );
		update_post_meta( $slide_5_id, 'slide_video_video_url', '' );

		// Run update to 1.4.0
		Foyer_Updater::update_to_1_4_0();

		// Default slide with image
		$actual = get_post_meta( $slide_1_id, 'slide_bg_image_image', true );
		$expected = $default_image_id;
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_1_id, 'slide_format', true );
		$expected = 'default';
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_1_id, 'slide_background', true );
		$expected = 'image';
		$this->assertEquals( $expected, $actual );

		// Production slide with image
		$actual = get_post_meta( $slide_2_id, 'slide_bg_image_image', true );
		$expected = $production_image_id;
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_2_id, 'slide_format', true );
		$expected = 'production';
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_2_id, 'slide_background', true );
		$expected = 'image';
		$this->assertEquals( $expected, $actual );

		// Video slide with video
		$actual = get_post_meta( $slide_3_id, 'slide_bg_video_video_url', true );
		$expected = $video_url;
		$this->assertEquals( $expected, $actual );
		$actual = get_post_meta( $slide_3_id, 'slide_bg_video_video_start', true );
		$expected = $video_start;
		$this->assertEquals( $expected, $actual );
		$actual = get_post_meta( $slide_3_id, 'slide_bg_video_video_end', true );
		$expected = $video_end;
		$this->assertEquals( $expected, $actual );
		$actual = get_post_meta( $slide_3_id, 'slide_bg_video_hold_slide', true );
		$expected = $hold_slide;
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_3_id, 'slide_format', true );
		$expected = 'default';
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_3_id, 'slide_background', true );
		$expected = 'video';
		$this->assertEquals( $expected, $actual );

		// Default slide without image
		$actual = get_post_meta( $slide_4_id, 'slide_bg_image_image', true );
		$expected = false;
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_4_id, 'slide_format', true );
		$expected = 'default';
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_4_id, 'slide_background', true );
		$expected = 'default';
		$this->assertEquals( $expected, $actual );

		// Video slide without video
		$actual = get_post_meta( $slide_5_id, 'slide_bg_video_video_url', true );
		$expected = false;
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_5_id, 'slide_format', true );
		$expected = 'default';
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_5_id, 'slide_background', true );
		$expected = 'default';
		$this->assertEquals( $expected, $actual );

		// Old meta is deleted
		$actual = get_post_meta( $slide_1_id, 'slide_default_image', true );
		$expected = false;
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_4_id, 'slide_default_image', true );
		$expected = false;
		$this->assertEquals( $expected, $actual );

	}
}
