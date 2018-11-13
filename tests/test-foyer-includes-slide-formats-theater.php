<?php
/**
 * @group theater
 *
 * Excluded when running phpunit by default. Use 'phpunit --group theater' to test.
 */
class Test_Foyer_Slide_Formats_Theater extends Foyer_Theater_UnitTestCase {

	/**
	 * @since	1.?
	 */
	function test_is_production_slide_format_registered_when_theater_is_active() {

		$slide_format = Foyer_Slides::get_slide_format_by_slug( 'production' );
		$this->assertNotEmpty( $slide_format );
	}
}
