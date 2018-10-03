<?php

class Test_Foyer_Slide_Backgrounds extends Foyer_UnitTestCase {

	/**
	 * @since	1.4.0
	 */
	function test_is_default_slide_background_registered() {
		$slide_background = Foyer_Slides::get_slide_background_by_slug( 'default' );
		$this->assertNotEmpty( $slide_background );
	}

	/**
	 * @since	1.4.0
	 */
	function test_is_image_slide_background_registered() {
		$slide_background = Foyer_Slides::get_slide_background_by_slug( 'image' );
		$this->assertNotEmpty( $slide_background );
	}

	/**
	 * @since	1.4.0
	 */
	function test_is_video_slide_background_registered() {
		$slide_background = Foyer_Slides::get_slide_background_by_slug( 'video' );
		$this->assertNotEmpty( $slide_background );
	}

	/**
	 * @since	1.X.X
	 */
	function test_is_html5_video_slide_background_registered() {
		$slide_background = Foyer_Slides::get_slide_background_by_slug( 'html5-video' );
		$this->assertNotEmpty( $slide_background );
	}
}
