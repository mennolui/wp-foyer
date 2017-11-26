<?php

class Test_Foyer_Slide_Formats extends Foyer_UnitTestCase {

	function test_is_pdf_slide_format_registered() {
		$slide_format = Foyer_Slides::get_slide_format_by_slug( 'pdf' );
		$this->assertNotEmpty( $slide_format );
	}

	function test_is_production_slide_format_not_registered() {
		// Theater plugin is not loaded by default
		$slide_format = Foyer_Slides::get_slide_format_by_slug( 'production' );
		$this->assertEmpty( $slide_format );
	}

	/**
	 * @group theater
	 *
	 * Excluded when running phpunit by default. Use 'phpunit --group theater' to test.
	 */
	function test_is_production_slide_format_registered_when_theater_is_active() {

		// Load Theater plugin (if not loaded already)
		require_once dirname( dirname( __FILE__ ) ) . '/../../plugins/theatre/theater.php';

		$slide_format = Foyer_Slides::get_slide_format_by_slug( 'production' );
		$this->assertNotEmpty( $slide_format );
	}

	function test_is_video_slide_format_registered() {
		$slide_format = Foyer_Slides::get_slide_format_by_slug( 'video' );
		$this->assertNotEmpty( $slide_format );
	}

	function test_is_iframe_slide_format_registered() {
		$slide_format = Foyer_Slides::get_slide_format_by_slug( 'iframe' );
		$this->assertNotEmpty( $slide_format );
	}
}
