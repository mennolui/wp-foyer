<?php

class Test_Foyer_Admin_Slide_Format_Production extends Foyer_UnitTestCase {

	function test_are_all_production_slide_properties_saved() {

		// Load Theater plugin (if not loaded already)
		require_once dirname( dirname( __FILE__ ) ) . '/../../plugins/theatre/theater.php';

		$this->assume_role( 'administrator' );

		$production_id = '222';

		/* Create image attachment */
		$file = dirname( __FILE__ ) . '/assets/Kip-400x400.jpg';
		$image_attachment_id = $this->factory->attachment->create_upload_object( $file );

		$_POST[ Foyer_Slide::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Slide::post_type_name );
		$_POST['slide_format'] = 'production';

		$_POST['slide_production_production_id'] = $production_id;
		$_POST['slide_production_image'] = $image_attachment_id;

		$admin_slide = new Foyer_Admin_Slide( 'foyer', '9.9.9' );
		$admin_slide->save_slide( $this->slide1 );

		$actual = get_post_meta( $this->slide1, 'slide_production_production_id', true );
		$this->assertEquals( $production_id, $actual );

		$actual = get_post_meta( $this->slide1, 'slide_production_image', true );
		$this->assertEquals( $image_attachment_id, $actual );
	}
}