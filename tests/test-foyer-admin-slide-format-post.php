<?php
class Test_Foyer_Admin_Slide_Format_Post extends Foyer_UnitTestCase {

	/**
	 * @since	1.5.0
	 */
	function test_are_all_post_slide_properties_saved() {

		$this->assume_role( 'administrator' );

		$post_id = '222';
		$slide_post_display_thumbnail = '1';
		$slide_post_use_excerpt = '1';

		$_POST[ Foyer_Slide::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Slide::post_type_name );
		$_POST['slide_format'] = 'post';
		$_POST['slide_background'] = 'default';

		$_POST['slide_post_post_id'] = $post_id;
		$_POST['slide_post_display_thumbnail'] = $slide_post_display_thumbnail;
		$_POST['slide_post_use_excerpt'] = $slide_post_use_excerpt;

		Foyer_Admin_Slide::save_slide( $this->slide1 );

		$actual = get_post_meta( $this->slide1, 'slide_post_post_id', true );
		$this->assertEquals( $post_id, $actual );

		$actual = get_post_meta( $this->slide1, 'slide_post_display_thumbnail', true );
		$this->assertEquals( $slide_post_display_thumbnail, $actual );

		$actual = get_post_meta( $this->slide1, 'slide_post_use_excerpt', true );
		$this->assertEquals( $slide_post_use_excerpt, $actual );
	}
}