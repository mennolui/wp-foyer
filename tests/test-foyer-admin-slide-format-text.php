<?php

class Test_Foyer_Admin_Slide_Format_Text extends Foyer_UnitTestCase {

	/**
	 * @since	1.5.0
	 */
	function test_are_all_text_slide_properties_saved() {

		$this->assume_role( 'administrator' );

		$title = 'Great title.';
		$subtitle = 'Best subtitle.';
		$content = '<strong>Some strong words.</strong>' . "\n\n" . 'And a second paragraph.';

		$_POST[ Foyer_Slide::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Slide::post_type_name );
		$_POST['slide_format'] = 'text';
		$_POST['slide_background'] = 'default';

		$_POST['slide_text_title'] = $title;
		$_POST['slide_text_subtitle'] = $subtitle;
		$_POST['slide_text_content'] = $content;

		Foyer_Admin_Slide::save_slide( $this->slide1 );

		$actual = get_post_meta( $this->slide1, 'slide_text_title', true );
		$this->assertEquals( $title, $actual );

		$actual = get_post_meta( $this->slide1, 'slide_text_subtitle', true );
		$this->assertEquals( $subtitle, $actual );

		$actual = get_post_meta( $this->slide1, 'slide_text_content', true );
		$this->assertEquals( $content, $actual );
	}
}