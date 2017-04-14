<?php

class Test_Foyer_Slide_Formats extends Foyer_UnitTestCase {

	function test_is_pdf_slide_format_registered() {
		$video_slide_format = Foyer_Slides::get_slide_format_by_slug( 'pdf' );
		$this->assertNotEmpty( $video_slide_format );
	}

	function test_is_production_slide_format_not_registered() {
		$video_slide_format = Foyer_Slides::get_slide_format_by_slug( 'production' );
		$this->assertEmpty( $video_slide_format );
	}

	function test_is_production_slide_format_registered_when_theater_is_active() {

		// Load Theater plugin
		require dirname( dirname( __FILE__ ) ) . '/../../plugins/theatre/theater.php';

		$video_slide_format = Foyer_Slides::get_slide_format_by_slug( 'production' );
		$this->assertNotEmpty( $video_slide_format );
	}

	function test_is_video_slide_format_registered() {
		$video_slide_format = Foyer_Slides::get_slide_format_by_slug( 'video' );
		$this->assertNotEmpty( $video_slide_format );
	}
}