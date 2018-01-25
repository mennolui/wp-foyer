<?php

class Test_Foyer_Admin_Slide_Format_Iframe extends Foyer_UnitTestCase {

	/**
	 * @since	1.?
	 * @since	1.4.0	Updated to work with slide backgrounds.
	 */
	function test_are_all_iframe_slide_properties_saved() {

		$this->assume_role( 'administrator' );

		$website_url = 'https://mennoluitjes.nl';

		$_POST[ Foyer_Slide::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Slide::post_type_name );
		$_POST['slide_format'] = 'iframe';
		$_POST['slide_background'] = 'default';

		$_POST['slide_iframe_website_url'] = $website_url;

		Foyer_Admin_Slide::save_slide( $this->slide1 );

		$actual = get_post_meta( $this->slide1, 'slide_iframe_website_url', true );
		$this->assertEquals( $website_url, $actual );
	}
}