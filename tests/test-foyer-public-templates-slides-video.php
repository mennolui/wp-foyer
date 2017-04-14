<?php

class Test_Foyer_Public_Templates_Slides_Video extends Foyer_UnitTestCase {

	function test_are_all_video_slide_properties_included_in_slide() {

		$this->assume_role( 'administrator' );

		$video_id = 'r9tbusKyvMY';
		$video_url = 'https://youtu.be/' . $video_id;
		$video_start = '35';
		$video_end = '60';
		$hold_slide = '1';

		update_post_meta( $this->slide1, 'slide_format', 'video' );
		update_post_meta( $this->slide1, 'slide_video_video_url', $video_url );
		update_post_meta( $this->slide1, 'slide_video_video_start', $video_start );
		update_post_meta( $this->slide1, 'slide_video_video_end', $video_end );
		update_post_meta( $this->slide1, 'slide_video_hold_slide', $hold_slide );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template('partials/slide.php');
		$actual = ob_get_clean();

		$expected = 'data-foyer-video-id="' . $video_id . '"';
		$this->assertContains( $expected, $actual );

		$expected = 'data-foyer-video-start="' . $video_start . '"';
		$this->assertContains( $expected, $actual );

		$expected = 'data-foyer-video-end="' . $video_end . '"';
		$this->assertContains( $expected, $actual );

		$expected = 'data-foyer-hold-slide="' . $hold_slide . '"';
		$this->assertContains( $expected, $actual );
	}
}

