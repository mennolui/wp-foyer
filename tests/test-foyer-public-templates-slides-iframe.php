<?php

class Test_Foyer_Public_Templates_Slides_Iframe extends Foyer_UnitTestCase {

	/**
	 * @since	1.?
	 * @since	1.4.0	Updated to work with slide backgrounds.
	 */
	function test_are_all_iframe_slide_properties_included_in_slide() {

		$this->assume_role( 'administrator' );

		$website_url = 'https://mennoluitjes.nl';

		update_post_meta( $this->slide1, 'slide_format', 'iframe' );
		update_post_meta( $this->slide1, 'slide_background', '' );
		update_post_meta( $this->slide1, 'slide_iframe_website_url', $website_url );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template('partials/slide.php');
		$actual = ob_get_clean();

		$expected = '<iframe src="' . $website_url . '"';
		$this->assertContains( $expected, $actual );
	}
}

