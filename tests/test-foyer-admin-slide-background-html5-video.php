<?php

class Test_Foyer_Admin_Slide_Background_Html5_Video extends Foyer_UnitTestCase {

	/**
	 * @since	1.6.0
	 */
	function test_are_all_slide_background_html5_video_properties_saved() {

		$this->assume_role( 'administrator' );

		/* Create video attachment */
		$file = dirname( __FILE__ ) . '/assets/techslides-sample-video-small.mp4';
		$video_attachment_id = $this->factory->attachment->create_upload_object( $file );

		$video_url = 'http://techslides.com/demos/sample-videos/small.mp4';
		$video_start = '35';
		$video_end = '60';
		$hold_slide = '1';
		$enable_sound = '1';

		$_POST[ Foyer_Slide::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Slide::post_type_name );
		$_POST['slide_format'] = 'default';
		$_POST['slide_background'] = 'html5-video';

		$_POST['slide_bg_html5_video_video'] = $video_attachment_id;
		$_POST['slide_bg_html5_video_video_url'] = $video_url;
		$_POST['slide_bg_html5_video_video_start'] = $video_start;
		$_POST['slide_bg_html5_video_video_end'] = $video_end;
		$_POST['slide_bg_html5_video_hold_slide'] = $hold_slide;
		$_POST['slide_bg_html5_video_enable_sound'] = $enable_sound;

		Foyer_Admin_Slide::save_slide( $this->slide1 );

		$actual = get_post_meta( $this->slide1, 'slide_bg_html5_video_video', true );
		$this->assertEquals( $video_attachment_id, $actual );

		$actual = get_post_meta( $this->slide1, 'slide_bg_html5_video_video_url', true );
		$this->assertEquals( $video_url, $actual );

		$actual = get_post_meta( $this->slide1, 'slide_bg_html5_video_video_start', true );
		$this->assertEquals( $video_start, $actual );

		$actual = get_post_meta( $this->slide1, 'slide_bg_html5_video_video_end', true );
		$this->assertEquals( $video_end, $actual );

		$actual = get_post_meta( $this->slide1, 'slide_bg_html5_video_hold_slide', true );
		$this->assertEquals( $hold_slide, $actual );

		$actual = get_post_meta( $this->slide1, 'slide_bg_html5_video_enable_sound', true );
		$this->assertEquals( $enable_sound, $actual );
	}
}

