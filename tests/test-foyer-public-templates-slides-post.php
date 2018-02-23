<?php
class Test_Foyer_Public_Templates_Slides_Post extends Foyer_UnitTestCase {

	/**
	 * @since	1.5.0
	 */
	function test_are_all_default_post_slide_properties_included_in_slide() {

		$post_title = 'Hello world this is our post';
		$post_content = 'With a lot a lot a lot a lot a lot of content.';
		$args = array(
			'post_type' => 'post',
			'post_title' => $post_title,
			'post_content' => $post_content,
		);

		/* Create post */
		$post_id = $this->factory->post->create( $args );

		update_post_meta( $this->slide1, 'slide_format', 'post' );
		update_post_meta( $this->slide1, 'slide_background', '' );
		update_post_meta( $this->slide1, 'slide_post_post_id', $post_id );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template( 'partials/slide.php' );
		$actual = ob_get_clean();

		$this->assertContains( $post_title, $actual );
		$this->assertContains( date_i18n( get_option( 'date_format' ) ), $actual );
		$this->assertContains( $post_content, $actual );
	}

	/**
	 * @since	1.5.0
	 */
	function test_is_thumbnail_included_in_post_slide_with_display_thumbnail_set() {

		$post_title = 'Hello world this is our post';
		$args = array(
			'post_type' => 'post',
			'post_title' => $post_title,
		);

		/* Create post */
		$post_id = $this->factory->post->create( $args );

		/* Create image attachment */
		$file = dirname( __FILE__ ) . '/assets/Kip-400x400.jpg';
		$image_attachment_id = $this->factory->attachment->create_upload_object( $file );
		set_post_thumbnail( $post_id, $image_attachment_id );

		update_post_meta( $this->slide1, 'slide_format', 'post' );
		update_post_meta( $this->slide1, 'slide_background', '' );
		update_post_meta( $this->slide1, 'slide_post_post_id', $post_id );
		update_post_meta( $this->slide1, 'slide_post_display_thumbnail', '1' );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template( 'partials/slide.php' );
		$actual = ob_get_clean();

		$this->assertRegExp( '/Kip-400x400.*\.jpg/', $actual );
	}

	/**
	 * @since	1.5.0
	 */
	function test_is_thumbnail_not_included_in_post_slide_with_display_thumbnail_not_set() {

		$post_title = 'Hello world this is our post';
		$args = array(
			'post_type' => 'post',
			'post_title' => $post_title,
		);

		/* Create post */
		$post_id = $this->factory->post->create( $args );

		/* Create image attachment */
		$file = dirname( __FILE__ ) . '/assets/Kip-400x400.jpg';
		$image_attachment_id = $this->factory->attachment->create_upload_object( $file );
		set_post_thumbnail( $post_id, $image_attachment_id );

		update_post_meta( $this->slide1, 'slide_format', 'post' );
		update_post_meta( $this->slide1, 'slide_background', '' );
		update_post_meta( $this->slide1, 'slide_post_post_id', $post_id );
		update_post_meta( $this->slide1, 'slide_post_display_thumbnail', '' );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template( 'partials/slide.php' );
		$actual = ob_get_clean();

		$this->assertNotRegExp( '/Kip-400x400.*\.jpg/', $actual );
	}

	/**
	 * @since	1.5.0
	 */
	function test_is_excerpt_included_in_post_slide_with_use_excerpt_set() {

		$post_title = 'Hello world this is our post';
		$post_content = 'With a lot a lot a lot a lot a lot of content.';
		$post_excerpt = 'Or a tiny excerpt.';
		$args = array(
			'post_type' => 'post',
			'post_title' => $post_title,
			'post_content' => $post_content,
			'post_excerpt' => $post_excerpt,
		);

		/* Create post */
		$post_id = $this->factory->post->create( $args );

		update_post_meta( $this->slide1, 'slide_format', 'post' );
		update_post_meta( $this->slide1, 'slide_background', '' );
		update_post_meta( $this->slide1, 'slide_post_post_id', $post_id );
		update_post_meta( $this->slide1, 'slide_post_use_excerpt', '1' );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template( 'partials/slide.php' );
		$actual = ob_get_clean();

		$this->assertContains( $post_excerpt, $actual );
		$this->assertNotContains( $post_content, $actual );
	}

	/**
	 * @since	1.5.0
	 */
	function test_are_all_text_slide_field_elements_included_in_slide() {

		$post_title = 'Hello world this is our post';
		$post_content = 'With a lot a lot a lot a lot a lot of content.';
		$args = array(
			'post_type' => 'post',
			'post_title' => $post_title,
			'post_content' => $post_content,
		);

		/* Create post */
		$post_id = $this->factory->post->create( $args );

		update_post_meta( $this->slide1, 'slide_format', 'post' );
		update_post_meta( $this->slide1, 'slide_background', '' );
		update_post_meta( $this->slide1, 'slide_post_post_id', $post_id );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template( 'partials/slide.php' );
		$actual = ob_get_clean();

		$this->assertContains( '<div class="foyer-slide-field foyer-slide-field-title', $actual );
		$this->assertContains( '<div class="foyer-slide-field foyer-slide-field-date', $actual );
		$this->assertContains( '<div class="foyer-slide-field foyer-slide-field-content', $actual );
	}

	/**
	 * @since	1.5.0
	 */
	function test_is_content_element_not_included_in_post_slide_when_empty() {

		$post_title = 'Hello world this is our post';
		$args = array(
			'post_type' => 'post',
			'post_title' => $post_title,
			'post_content' => '',
		);

		/* Create post */
		$post_id = $this->factory->post->create( $args );

		update_post_meta( $this->slide1, 'slide_format', 'post' );
		update_post_meta( $this->slide1, 'slide_background', '' );
		update_post_meta( $this->slide1, 'slide_post_post_id', $post_id );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template( 'partials/slide.php' );
		$actual = ob_get_clean();

		$this->assertNotContains( '<div class="foyer-slide-field foyer-slide-field-content', $actual );
	}
}

