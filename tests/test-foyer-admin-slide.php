<?php

class Test_Foyer_Admin_Slide extends Foyer_UnitTestCase {

	function get_meta_boxes_for_slide( $slide_id ) {
		$this->assume_role( 'author' );
		set_current_screen( Foyer_Slide::post_type_name );

		do_action( 'add_meta_boxes', Foyer_Slide::post_type_name );
		ob_start();
		do_meta_boxes( Foyer_Slide::post_type_name, 'normal', get_post( $slide_id ) );
		$meta_boxes = ob_get_clean();

		return $meta_boxes;
	}

	function test_slide_format_meta_box_is_displayed_on_slide_admin_page() {

		$meta_boxes = $this->get_meta_boxes_for_slide( $this->slide1 );

		$this->assertContains( '<div id="foyer_slide_format" class="postbox', $meta_boxes );
	}

	function test_all_slide_format_radios_are_displayed_on_slide_admin_page() {

		$meta_boxes = $this->get_meta_boxes_for_slide( $this->slide1 );

		$slide_formats = Foyer_Slides::get_slide_formats();
		foreach ( $slide_formats as $key => $slide_format ) {
			$this->assertContains( '<input type="radio" value="' . $key . '" name="slide_format"', $meta_boxes );
		}
	}

	function test_all_slide_format_meta_boxes_are_displayed_on_slide_admin_page() {

		$meta_boxes = $this->get_meta_boxes_for_slide( $this->slide1 );

		$slide_formats = Foyer_Slides::get_slide_formats();
		foreach ( $slide_formats as $key => $slide_format ) {
			$this->assertContains( '<div id="foyer_slide_format_' . $key . '" class="postbox', $meta_boxes );
		}
	}

	function test_is_slide_format_default_saved() {

		$this->assume_role( 'administrator' );

		$slide_format = 'default';

		$_POST[ Foyer_Slide::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Slide::post_type_name );
		$_POST['slide_format'] = $slide_format;
		$_POST['slide_default_image'] = '';

		$admin_slide = new Foyer_Admin_Slide( 'foyer', '9.9.9' );
		$admin_slide->save_slide( $this->slide1 );

		$updated_slide = new Foyer_Slide( $this->slide1 );

		$actual = $updated_slide->get_format();
		$this->assertEquals( $slide_format, $actual );
	}

	function test_is_slide_format_pdf_saved() {

		$this->assume_role( 'administrator' );

		$slide_format = 'pdf';

		$_POST[ Foyer_Slide::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Slide::post_type_name );
		$_POST['slide_format'] = $slide_format;
		$_POST['slide_pdf_file'] = '';

		$admin_slide = new Foyer_Admin_Slide( 'foyer', '9.9.9' );
		$admin_slide->save_slide( $this->slide1 );

		$updated_slide = new Foyer_Slide( $this->slide1 );

		$actual = $updated_slide->get_format();
		$this->assertEquals( $slide_format, $actual );
	}
}
