<?php

class Test_Foyer_Updater extends Foyer_UnitTestCase {

	/**
	 * @since	1.4.0
	 */
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

	/**
	 * @since	1.4.0
	 */
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

	/**
	 * @since	1.4.0
	 */
	function test_is_database_update_skipped_when_database_is_up_to_date() {
		// Set database version to current plugin version
		Foyer_Updater::update_db_version( Foyer::get_version() );

		$actual = Foyer_Updater::update();
		$expected = false;
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * @since	1.4.0
	 */
	function test_are_slides_correctly_converted_on_update_to_1_4_0() {
		$this->assume_role( 'administrator' );

		$file = dirname( __FILE__ ) . '/assets/Kip-400x400.jpg';
		$default_image_id = $this->factory->attachment->create_upload_object( $file );
		$production_image_id = $this->factory->attachment->create_upload_object( $file );

		$video_url = 'https://youtu.be/r9tbusKyvMY';
		$video_start = '35';
		$video_end = '60';
		$hold_slide = '1';

		$website_url = 'https://mennoluitjes.nl';

		$slide_args = array(
			'post_type' => Foyer_Slide::post_type_name,
		);

		$slide_draft_args = wp_parse_args( array(
			'post_status' => 'draft',
		), $slide_args );

		$slide_trash_args = wp_parse_args( array(
			'post_status' => 'trash',
		), $slide_args );


		/* Create a slide for each oldskool slide format */

		/* Default slide, with image */
		$slide_1_id = $this->factory->post->create( $slide_args );
		update_post_meta( $slide_1_id, 'slide_format', 'default' );
		update_post_meta( $slide_1_id, 'slide_default_image', $default_image_id );

		/* Production slide, with image */
		$slide_2_id = $this->factory->post->create( $slide_args );
		update_post_meta( $slide_2_id, 'slide_format', 'production' );
		update_post_meta( $slide_2_id, 'slide_production_image', $production_image_id );

		/* Video slide */
		$slide_3_id = $this->factory->post->create( $slide_args );
		update_post_meta( $slide_3_id, 'slide_format', 'video' );
		update_post_meta( $slide_3_id, 'slide_video_video_url', $video_url );
		update_post_meta( $slide_3_id, 'slide_video_video_start', $video_start );
		update_post_meta( $slide_3_id, 'slide_video_video_end', $video_end );
		update_post_meta( $slide_3_id, 'slide_video_hold_slide', $hold_slide );

		/* Default slide, without image */
		$slide_4_id = $this->factory->post->create( $slide_args );
		update_post_meta( $slide_4_id, 'slide_format', 'default' );

		/* Video slide, with empty fields */
		$slide_5_id = $this->factory->post->create( $slide_args );
		update_post_meta( $slide_5_id, 'slide_format', 'video' );
		update_post_meta( $slide_5_id, 'slide_video_video_url', '' );

		/* Production slide, without image */
		$slide_6_id = $this->factory->post->create( $slide_args );
		update_post_meta( $slide_6_id, 'slide_format', 'production' );

		/* Iframe slide */
		$slide_7_id = $this->factory->post->create( $slide_args );
		update_post_meta( $slide_7_id, 'slide_format', 'iframe' );
		update_post_meta( $slide_7_id, 'slide_iframe_website_url', $website_url );

		/* Draft default slide, with image */
		$slide_8_id = $this->factory->post->create( $slide_draft_args );
		update_post_meta( $slide_8_id, 'slide_format', 'default' );
		update_post_meta( $slide_8_id, 'slide_default_image', $default_image_id );

		/* Trashed default slide, with image */
		$slide_9_id = $this->factory->post->create( $slide_trash_args );
		update_post_meta( $slide_9_id, 'slide_format', 'default' );
		update_post_meta( $slide_9_id, 'slide_default_image', $default_image_id );

		// Run update to 1.4.0
		Foyer_Updater::update_to_1_4_0();

		// Check conversion of Default slide with image
		$actual = get_post_meta( $slide_1_id, 'slide_bg_image_image', true );
		$expected = $default_image_id;
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_1_id, 'slide_format', true );
		$expected = 'default';
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_1_id, 'slide_background', true );
		$expected = 'image';
		$this->assertEquals( $expected, $actual );

		// Check conversion of Production slide with image
		$actual = get_post_meta( $slide_2_id, 'slide_bg_image_image', true );
		$expected = $production_image_id;
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_2_id, 'slide_format', true );
		$expected = 'production';
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_2_id, 'slide_background', true );
		$expected = 'image';
		$this->assertEquals( $expected, $actual );

		// Check conversion of Video slide with video
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

		// Check conversion of Default slide without image (result should still be image background)
		$actual = get_post_meta( $slide_4_id, 'slide_bg_image_image', true );
		$expected = false;
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_4_id, 'slide_format', true );
		$expected = 'default';
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_4_id, 'slide_background', true );
		$expected = 'image';
		$this->assertEquals( $expected, $actual );

		// Check conversion of Video slide without video (result should still be video background)
		$actual = get_post_meta( $slide_5_id, 'slide_bg_video_video_url', true );
		$expected = false;
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_5_id, 'slide_format', true );
		$expected = 'default';
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_5_id, 'slide_background', true );
		$expected = 'video';
		$this->assertEquals( $expected, $actual );

		// Check conversion of Production slide without image (result should be default background)
		$actual = get_post_meta( $slide_6_id, 'slide_bg_image_image', true );
		$expected = false;
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_6_id, 'slide_format', true );
		$expected = 'production';
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_6_id, 'slide_background', true );
		$expected = 'default';
		$this->assertEquals( $expected, $actual );

		// Check conversion of Iframe slide
		$actual = get_post_meta( $slide_7_id, 'slide_iframe_website_url', true );
		$expected = $website_url;
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_7_id, 'slide_format', true );
		$expected = 'iframe';
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_7_id, 'slide_background', true );
		$expected = 'default';
		$this->assertEquals( $expected, $actual );

		// Check conversion of DRAFT default slide with image
		$actual = get_post_meta( $slide_8_id, 'slide_bg_image_image', true );
		$expected = $default_image_id;
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_8_id, 'slide_format', true );
		$expected = 'default';
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_8_id, 'slide_background', true );
		$expected = 'image';
		$this->assertEquals( $expected, $actual );

		// Check conversion of TRASHED default slide with image
		$actual = get_post_meta( $slide_9_id, 'slide_bg_image_image', true );
		$expected = $default_image_id;
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_9_id, 'slide_format', true );
		$expected = 'default';
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_9_id, 'slide_background', true );
		$expected = 'image';
		$this->assertEquals( $expected, $actual );

		// Check old meta is deleted
		$actual = get_post_meta( $slide_1_id, 'slide_default_image', true );
		$expected = false;
		$this->assertEquals( $expected, $actual );

		$actual = get_post_meta( $slide_4_id, 'slide_default_image', true );
		$expected = false;
		$this->assertEquals( $expected, $actual );
	}

	/*
	 * @since	1.5.4
	 */
	function test_are_displays_reset_when_updating_from_1_0_0() {
		// Set to really old version to trigger database update
		Foyer_Updater::update_db_version( '1.0.0' );

		/* Check that reset request is not yet present */
		$this->assertEmpty( get_post_meta( $this->display1, 'foyer_reset_display', true ) );

		Foyer_Updater::update();

		/* Check that reset request was added */
		$this->assertNotEmpty( get_post_meta( $this->display1, 'foyer_reset_display', true ) );
	}

	/*
	 * @since	1.5.4
	 */
	function test_are_displays_reset_when_database_is_up_to_date() {
		// Set database version to current plugin version
		Foyer_Updater::update_db_version( Foyer::get_version() );

		/* Check that reset request is not yet present */
		$this->assertEmpty( get_post_meta( $this->display1, 'foyer_reset_display', true ) );

		Foyer_Updater::update();

		/* Check that reset request was not added */
		$this->assertEmpty( get_post_meta( $this->display1, 'foyer_reset_display', true ) );
	}
}
