<?php

class Test_Foyer_Public_Templates_Slides_Default extends Foyer_UnitTestCase {

	function test_are_all_default_slide_properties_included_in_slide() {

		$this->assume_role( 'administrator' );

		/* Create image attachment */
		$file = dirname( __FILE__ ) . '/assets/Kip-400x400.jpg';
		$image_attachment_id = $this->factory->attachment->create_upload_object( $file );

		update_post_meta( $this->slide1, 'slide_format', 'default' );
		update_post_meta( $this->slide1, 'slide_default_image', $image_attachment_id );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template('partials/slide.php');
		$actual = ob_get_clean();

		$this->assertRegExp( '/Kip-400x400.*\.jpg/', $actual );
	}
}

