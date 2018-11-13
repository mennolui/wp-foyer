<?php
/**
 * @group theater
 */
class Test_Foyer_Admin_Slide_Format_Production extends Foyer_Theater_UnitTestCase {

	/**
	 * @since	1.?
	 * @since	1.4.0	Updated to work with slide backgrounds.
	 */
	function test_are_all_production_slide_properties_saved() {

		$this->assume_role( 'administrator' );

		$production_id = '222';


		$_POST[ Foyer_Slide::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Slide::post_type_name );
		$_POST['slide_format'] = 'production';
		$_POST['slide_background'] = 'default';

		$_POST['slide_production_production_id'] = $production_id;

		Foyer_Admin_Slide::save_slide( $this->slide1 );

		$actual = get_post_meta( $this->slide1, 'slide_production_production_id', true );
		$this->assertEquals( $production_id, $actual );
	}
}