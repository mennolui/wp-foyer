<?php
/**
 * @group theater
 */
class Test_Foyer_Admin_Slide_Format_Upcoming_Productions extends Foyer_Theater_UnitTestCase {

	/**
	 * @since	1.7.0
	 */
	function test_are_all_upcoming_productions_slide_properties_saved() {

		$this->assume_role( 'administrator' );

		$limit = '2';
		$categories = array( 33, 44 );

		$_POST[ Foyer_Slide::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Slide::post_type_name );
		$_POST['slide_format'] = 'upcoming-productions';
		$_POST['slide_background'] = 'default';

		$_POST['slide_upcoming_productions_limit'] = $limit;
		$_POST['slide_upcoming_productions_categories'] = $categories;

		Foyer_Admin_Slide::save_slide( $this->slide1 );

		$actual = get_post_meta( $this->slide1, 'slide_upcoming_productions_limit', true );
		$this->assertEquals( $limit, $actual );

		$actual = get_post_meta( $this->slide1, 'slide_upcoming_productions_categories', true );
		$this->assertEquals( $categories, $actual );
	}
}