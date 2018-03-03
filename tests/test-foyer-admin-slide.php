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

	/**
	 * @since	1.?
	 * @since	1.4.0	Updated to work with slide backgrounds.
	 */
	function test_is_slide_format_default_saved() {

		$this->assume_role( 'administrator' );

		// Set slide_format to an existing value, not default
		update_post_meta( $this->slide1, 'slide_format', 'pdf' );

		$slide_format = 'default';

		$_POST[ Foyer_Slide::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Slide::post_type_name );
		$_POST['slide_format'] = $slide_format;
		$_POST['slide_background'] = 'image';
		$_POST['slide_bg_image_image'] = '';

		Foyer_Admin_Slide::save_slide( $this->slide1 );

		$updated_slide = new Foyer_Slide( $this->slide1 );

		$actual = $updated_slide->get_format();
		$this->assertEquals( $slide_format, $actual );
	}

	/**
	 * @since	1.?
	 * @since	1.4.0	Updated to work with slide backgrounds.
	 */
	function test_is_slide_format_pdf_saved() {

		$this->assume_role( 'administrator' );

		$slide_format = 'pdf';

		$_POST[ Foyer_Slide::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Slide::post_type_name );
		$_POST['slide_format'] = $slide_format;
		$_POST['slide_background'] = 'default';
		$_POST['slide_pdf_file'] = '';

		Foyer_Admin_Slide::save_slide( $this->slide1 );

		$updated_slide = new Foyer_Slide( $this->slide1 );

		$actual = $updated_slide->get_format();
		$this->assertEquals( $slide_format, $actual );
	}

	/**
	 * @since	1.4.0
	 */
	function test_is_slide_background_default_saved() {

		$this->assume_role( 'administrator' );

		// Set slide_background to an existing value, not default
		update_post_meta( $this->slide1, 'slide_background', 'image' );

		$slide_background = 'default';

		$_POST[ Foyer_Slide::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Slide::post_type_name );
		$_POST['slide_format'] = 'iframe';
		$_POST['slide_background'] = $slide_background;
		$_POST['slide_iframe_website_url'] = '';

		Foyer_Admin_Slide::save_slide( $this->slide1 );

		$updated_slide = new Foyer_Slide( $this->slide1 );

		$actual = $updated_slide->get_background();
		$this->assertEquals( $slide_background, $actual );
	}

	/**
	 * @since	1.4.0
	 */
	function test_is_slide_background_image_saved() {

		$this->assume_role( 'administrator' );

		$slide_background = 'image';

		$_POST[ Foyer_Slide::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Slide::post_type_name );
		$_POST['slide_format'] = 'default';
		$_POST['slide_background'] = $slide_background;
		$_POST['slide_bg_image_image'] = '';

		Foyer_Admin_Slide::save_slide( $this->slide1 );

		$updated_slide = new Foyer_Slide( $this->slide1 );

		$actual = $updated_slide->get_background();
		$this->assertEquals( $slide_background, $actual );
	}

	/**
	 * @since	1.4.0
	 */
	function test_is_slide_not_saved_when_saving_slide_background_default_for_slide_format_default() {

		$this->assume_role( 'administrator' );

		$old_slide_background = 'video';

		// Set slide_background to an existing value, not default
		update_post_meta( $this->slide1, 'slide_background', $old_slide_background );

		// Try to save slide_format default and background default, to try to create an unwanted situation
		// (slide format default has no background default)
		$_POST[ Foyer_Slide::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Slide::post_type_name );
		$_POST['slide_format'] = 'default';
		$_POST['slide_background'] = 'default';

		Foyer_Admin_Slide::save_slide( $this->slide1 );

		$updated_slide = new Foyer_Slide( $this->slide1 );

		$actual = $updated_slide->get_background();
		$this->assertEquals( $old_slide_background, $actual );
	}

	/**
	 * @since	1.4.0
	 */
	function test_slide_format_select_is_displayed_on_slide_admin_page() {

		$meta_boxes = $this->get_meta_boxes_for_slide( $this->slide1 );

		$this->assertContains( '<div class="foyer_slide_select_format"', $meta_boxes );
	}

	/**
	 * @since	1.4.0
	 */
	function test_slide_background_select_is_displayed_on_slide_admin_page() {

		$meta_boxes = $this->get_meta_boxes_for_slide( $this->slide1 );

		$this->assertContains( '<div class="foyer_slide_select_background"', $meta_boxes );
	}

	/**
	 * @since	1.4.0
	 */
	function test_all_slide_format_options_are_displayed_on_slide_admin_page() {

		$meta_boxes = $this->get_meta_boxes_for_slide( $this->slide1 );

		$slide_formats = Foyer_Slides::get_slide_formats();
		foreach ( $slide_formats as $key => $slide_format ) {
			$this->assertContains( '<option value="' . $key . '"', $meta_boxes );
		}
	}

	/**
	 * @since	1.4.0
	 */
	function test_all_slide_background_options_are_displayed_on_slide_admin_page() {

		$meta_boxes = $this->get_meta_boxes_for_slide( $this->slide1 );

		$slide_backgrounds = Foyer_Slides::get_slide_backgrounds();
		foreach ( $slide_backgrounds as $key => $slide_background ) {
			$this->assertContains( '<option value="' . $key . '"', $meta_boxes );
		}
	}

	/**
	 * @since	1.4.0
	 */
	function test_all_slide_format_admin_panels_are_displayed_on_slide_admin_page() {

		$meta_boxes = $this->get_meta_boxes_for_slide( $this->slide1 );

		$slide_formats = Foyer_Slides::get_slide_formats();
		foreach ( $slide_formats as $key => $slide_format ) {
			$this->assertContains( '<div id="foyer_slide_format_' . $key . '"', $meta_boxes );
		}
	}

	/**
	 * @since	1.4.0
	 */
	function test_all_slide_background_admin_panels_are_displayed_on_slide_admin_page() {

		$meta_boxes = $this->get_meta_boxes_for_slide( $this->slide1 );

		$slide_backgrounds = Foyer_Slides::get_slide_backgrounds();
		foreach ( $slide_backgrounds as $key => $slide_background ) {
			$this->assertContains( '<div id="foyer_slide_background_' . $key . '"', $meta_boxes );
		}
	}

	/**
	 * @since	1.5.0
	 */
	function test_all_single_slide_options_are_displayed_for_their_optgroup() {

		ob_start();
		Foyer_Admin_Slide::slide_format_options_html( new Foyer_Slide( $this->slide1 ), false );
		$actual = ob_get_clean();

		$this->assertContains( '<option value="default"', $actual );
		$this->assertContains( '<option value="iframe"', $actual );

		$this->assertNotContains( '<option value="pdf"', $actual );
	}

	/**
	 * @since	1.5.0
	 */
	function test_all_slide_stack_options_are_displayed_for_their_optgroup() {

		ob_start();
		Foyer_Admin_Slide::slide_format_options_html( new Foyer_Slide( $this->slide1 ), true );
		$actual = ob_get_clean();

		$this->assertContains( '<option value="pdf"', $actual );

		$this->assertNotContains( '<option value="default"', $actual );
		$this->assertNotContains( '<option value="iframe"', $actual );
	}

	/**
	 * @since	1.5.1
	 */
	function test_slide_format_column_contains_format_and_background() {

		$this->assume_role( 'administrator' );

		/* Create slide */
		$slide_args = array(
			'post_type' => Foyer_Slide::post_type_name,
		);

		$slide_id = $this->factory->post->create( $slide_args );

		update_post_meta( $slide_id, 'slide_format', 'post' );
		update_post_meta( $slide_id, 'slide_background', 'image' );

		ob_start();
		Foyer_Admin_Slide::do_slide_format_column( 'slide_format', $slide_id );
		$actual = ob_get_clean();

		$this->assertEquals( 'Post<br />Image', $actual );
	}
}
