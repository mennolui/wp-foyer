<?php
class Test_Foyer_Public_Templates_Slides_Text extends Foyer_UnitTestCase {

	/**
	 * @since	1.5.0
	 */
	function test_are_all_text_slide_properties_included_in_slide() {

		$pretitle = 'Winning pre-title.';
		$title = 'Great title.';
		$subtitle = 'Best subtitle.';
		$content_line1 = '<strong>Some strong words.</strong>';
		$content_line2 = 'And a second paragraph.';
		$content = $content_line1 . "\n\n" . $content_line2;

		update_post_meta( $this->slide1, 'slide_format', 'text' );
		update_post_meta( $this->slide1, 'slide_background', '' );

		update_post_meta( $this->slide1, 'slide_text_pretitle', $pretitle );
		update_post_meta( $this->slide1, 'slide_text_title', $title );
		update_post_meta( $this->slide1, 'slide_text_subtitle', $subtitle );
		update_post_meta( $this->slide1, 'slide_text_content', $content );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template( 'partials/slide.php' );
		$actual = ob_get_clean();

		$this->assertContains( $pretitle, $actual );
		$this->assertContains( $title, $actual );
		$this->assertContains( $subtitle, $actual );
		$this->assertContains( '<p>' . $content_line1 . '</p>', $actual );
		$this->assertContains( '<p>' . $content_line2 . '</p>', $actual );
	}

	/**
	 * @since	1.5.0
	 */
	function test_are_all_text_slide_field_elements_included_in_slide() {

		$pretitle = 'Winning pre-title.';
		$title = 'Great title.';
		$subtitle = 'Best subtitle.';
		$content = 'Strong content.';

		update_post_meta( $this->slide1, 'slide_format', 'text' );
		update_post_meta( $this->slide1, 'slide_background', '' );

		update_post_meta( $this->slide1, 'slide_text_pretitle', $pretitle );
		update_post_meta( $this->slide1, 'slide_text_title', $title );
		update_post_meta( $this->slide1, 'slide_text_subtitle', $subtitle );
		update_post_meta( $this->slide1, 'slide_text_content', $content );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template( 'partials/slide.php' );
		$actual = ob_get_clean();

		$this->assertContains( '<div class="foyer-slide-field foyer-slide-field-pretitle', $actual );
		$this->assertContains( '<div class="foyer-slide-field foyer-slide-field-title', $actual );
		$this->assertContains( '<div class="foyer-slide-field foyer-slide-field-subtitle', $actual );
		$this->assertContains( '<div class="foyer-slide-field foyer-slide-field-content', $actual );
	}

	/**
	 * @since	1.5.0
	 */
	function test_is_pretitle_element_not_included_in_text_slide_when_empty() {

		$pretitle = '';
		$title = 'Great title.';
		$subtitle = 'Best subtitle.';
		$content = 'Strong content.';

		update_post_meta( $this->slide1, 'slide_format', 'text' );
		update_post_meta( $this->slide1, 'slide_background', '' );

		update_post_meta( $this->slide1, 'slide_text_title', $title );
		update_post_meta( $this->slide1, 'slide_text_subtitle', $subtitle );
		update_post_meta( $this->slide1, 'slide_text_content', $content );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template( 'partials/slide.php' );
		$actual = ob_get_clean();

		$this->assertNotContains( '<div class="foyer-slide-field foyer-slide-field-pretitle', $actual );
	}

	/**
	 * @since	1.5.0
	 */
	function test_is_title_element_not_included_in_text_slide_when_empty() {

		$pretitle = 'Winning pre-title.';
		$title = '';
		$subtitle = 'Best subtitle.';
		$content = 'Strong content.';

		update_post_meta( $this->slide1, 'slide_format', 'text' );
		update_post_meta( $this->slide1, 'slide_background', '' );

		update_post_meta( $this->slide1, 'slide_text_title', $title );
		update_post_meta( $this->slide1, 'slide_text_subtitle', $subtitle );
		update_post_meta( $this->slide1, 'slide_text_content', $content );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template( 'partials/slide.php' );
		$actual = ob_get_clean();

		$this->assertNotContains( '<div class="foyer-slide-field foyer-slide-field-title', $actual );
	}

	/**
	 * @since	1.5.0
	 */
	function test_is_subtitle_element_not_included_in_text_slide_when_empty() {

		$pretitle = 'Winning pre-title.';
		$title = 'Great title.';
		$subtitle = '';
		$content = 'Strong content.';

		update_post_meta( $this->slide1, 'slide_format', 'text' );
		update_post_meta( $this->slide1, 'slide_background', '' );

		update_post_meta( $this->slide1, 'slide_text_title', $title );
		update_post_meta( $this->slide1, 'slide_text_subtitle', $subtitle );
		update_post_meta( $this->slide1, 'slide_text_content', $content );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template( 'partials/slide.php' );
		$actual = ob_get_clean();

		$this->assertNotContains( '<div class="foyer-slide-field foyer-slide-field-subtitle', $actual );
	}

	/**
	 * @since	1.5.0
	 */
	function test_is_content_element_not_included_in_text_slide_when_empty() {

		$pretitle = 'Winning pre-title.';
		$title = 'Great title.';
		$subtitle = 'Best subtitle.';
		$content = '';

		update_post_meta( $this->slide1, 'slide_format', 'text' );
		update_post_meta( $this->slide1, 'slide_background', '' );

		update_post_meta( $this->slide1, 'slide_text_title', $title );
		update_post_meta( $this->slide1, 'slide_text_subtitle', $subtitle );
		update_post_meta( $this->slide1, 'slide_text_content', $content );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template( 'partials/slide.php' );
		$actual = ob_get_clean();

		$this->assertNotContains( '<div class="foyer-slide-field foyer-slide-field-content', $actual );
	}
}

