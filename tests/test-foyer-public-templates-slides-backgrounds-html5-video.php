<?php

class Test_Foyer_Public_Templates_Slides_Backgrounds_Html5_Video extends Foyer_UnitTestCase {

	/**
	 * @since	1.6.0
	 */
	function test_are_all_slide_background_html5_video_properties_included_in_slide() {

		$this->assume_role( 'administrator' );

		$video_url = 'http://techslides.com/demos/sample-videos/small.mp4';
		$video_start = '35';
		$video_end = '60';
		$hold_slide = '1';
		$enable_sound = '1';

		update_post_meta( $this->slide1, 'slide_format', '' );
		update_post_meta( $this->slide1, 'slide_background', 'html5-video' );
		update_post_meta( $this->slide1, 'slide_bg_html5_video_video_url', $video_url );
		update_post_meta( $this->slide1, 'slide_bg_html5_video_video_start', $video_start );
		update_post_meta( $this->slide1, 'slide_bg_html5_video_video_end', $video_end );
		update_post_meta( $this->slide1, 'slide_bg_html5_video_hold_slide', $hold_slide );
		update_post_meta( $this->slide1, 'slide_bg_html5_video_enable_sound', $enable_sound );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template('partials/slide.php');
		$actual = ob_get_clean();

		$expected = '<source src="' . $video_url . '"';
		$this->assertContains( $expected, $actual );

		$expected = 'data-foyer-video-start="' . $video_start . '"';
		$this->assertContains( $expected, $actual );

		$expected = 'data-foyer-video-end="' . $video_end . '"';
		$this->assertContains( $expected, $actual );

		$expected = 'data-foyer-hold-slide="' . $hold_slide . '"';
		$this->assertContains( $expected, $actual );

		$expected = 'data-foyer-output-sound="' . $enable_sound . '"';
		$this->assertContains( $expected, $actual );
	}
}

