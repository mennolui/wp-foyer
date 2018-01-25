<?php

class Test_Foyer_Admin_Slide_Background_Image extends Foyer_UnitTestCase {

	/**
	 * @since	1.4.0
	 */
	function test_are_all_slide_background_image_properties_saved() {

		$this->assume_role( 'administrator' );

		/* Create image attachment */
		$file = dirname( __FILE__ ) . '/assets/Kip-400x400.jpg';
		$image_attachment_id = $this->factory->attachment->create_upload_object( $file );

		$_POST[ Foyer_Slide::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Slide::post_type_name );
		$_POST['slide_format'] = 'default';
		$_POST['slide_background'] = 'image';

		$_POST['slide_bg_image_image'] = $image_attachment_id;

		Foyer_Admin_Slide::save_slide( $this->slide1 );

		$actual = get_post_meta( $this->slide1, 'slide_bg_image_image', true );
		$this->assertEquals( $image_attachment_id, $actual );
	}
}