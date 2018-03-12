<?php

class Test_Foyer_Admin_Slide_Background_Video extends Foyer_UnitTestCase {

	/**
	 * @since	1.4.0
	 * @since	1.5.1	Now also tests if the new slide_bg_video_enable_sound option is saved.
	 */
	function test_are_all_slide_background_video_properties_saved() {

		$this->assume_role( 'administrator' );

		$video_url = 'https://youtu.be/r9tbusKyvMY';
		$video_start = '35';
		$video_end = '60';
		$hold_slide = '1';
		$enable_sound = '1';

		$_POST[ Foyer_Slide::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Slide::post_type_name );
		$_POST['slide_format'] = 'default';
		$_POST['slide_background'] = 'video';

		$_POST['slide_bg_video_video_url'] = $video_url;
		$_POST['slide_bg_video_video_start'] = $video_start;
		$_POST['slide_bg_video_video_end'] = $video_end;
		$_POST['slide_bg_video_hold_slide'] = $hold_slide;
		$_POST['slide_bg_video_enable_sound'] = $enable_sound;

		Foyer_Admin_Slide::save_slide( $this->slide1 );

		$actual = get_post_meta( $this->slide1, 'slide_bg_video_video_url', true );
		$this->assertEquals( $video_url, $actual );

		$actual = get_post_meta( $this->slide1, 'slide_bg_video_video_start', true );
		$this->assertEquals( $video_start, $actual );

		$actual = get_post_meta( $this->slide1, 'slide_bg_video_video_end', true );
		$this->assertEquals( $video_end, $actual );

		$actual = get_post_meta( $this->slide1, 'slide_bg_video_hold_slide', true );
		$this->assertEquals( $hold_slide, $actual );

		$actual = get_post_meta( $this->slide1, 'slide_bg_video_enable_sound', true );
		$this->assertEquals( $enable_sound, $actual );
	}
}