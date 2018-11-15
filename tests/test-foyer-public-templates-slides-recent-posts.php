<?php
class Test_Foyer_Public_Templates_Slides_Recent_Posts extends Foyer_UnitTestCase {

	function setUp() {
		parent::setUp();

		/* Set up categories */
		$this->category_boring = wp_create_category( 'boring-stuff' );
		$this->category_spectacular = wp_create_category( 'spectacular-news' );

		/* Create post */
		$post_args = array(
			'post_type' => 'post',
		);

		$this->post1_date = time() - 3 * DAY_IN_SECONDS;
		$this->post1_content = 'Some juicy content.';
		$this->post1_excerpt = 'Sometimes an excerpt is better.';
		$post_args['post_date'] = date( 'Y-m-d H:i:s', $this->post1_date );
		$post_args['post_content'] = $this->post1_content;
		$post_args['post_excerpt'] = $this->post1_excerpt;
		$this->post1 = $this->factory->post->create( $post_args );
		wp_set_post_categories( $this->post1, array( $this->category_spectacular ) );

		$this->post2_date = time() - 2 * DAY_IN_SECONDS;
		$this->post2_content = 'Again juicy content.';
		$this->post2_excerpt = 'Tiny excerpt.';
		$post_args['post_date'] = date( 'Y-m-d H:i:s', $this->post2_date );
		$post_args['post_content'] = $this->post2_content;
		$post_args['post_excerpt'] = $this->post2_excerpt;
		$this->post2 = $this->factory->post->create( $post_args );
		wp_set_post_categories( $this->post2, array( $this->category_boring ) );
	}

	/**
	 * @since	1.X.X
	 */
	function test_are_all_default_recent_posts_slide_properties_included_in_slide() {

		$this->assume_role( 'administrator' );

		update_post_meta( $this->slide1, 'slide_format', 'recent-posts' );
		update_post_meta( $this->slide1, 'slide_background', '' );
		update_post_meta( $this->slide1, 'slide_recent_posts_limit', 1 ); // show only most recent, post2
		update_post_meta( $this->slide1, 'slide_recent_posts_categories', '' );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template('partials/slide.php');
		$actual = ob_get_clean();

		$this->assertContains( get_the_title( $this->post2 ), $actual );
		$this->assertContains( date_i18n( get_option( 'date_format' ), $this->post2_date ), $actual );
		$this->assertContains( $this->post2_content, $actual );
	}

	/**
	 * @since	1.X.X
	 */
	function test_are_posts_filtered_by_category() {

		$this->assume_role( 'administrator' );

		update_post_meta( $this->slide1, 'slide_format', 'recent-posts' );
		update_post_meta( $this->slide1, 'slide_background', '' );
		update_post_meta( $this->slide1, 'slide_recent_posts_limit', '' );
		update_post_meta( $this->slide1, 'slide_recent_posts_categories', array( $this->category_spectacular ) );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template('partials/slide.php');
		$actual = ob_get_clean();

		$this->assertContains( get_the_title( $this->post1 ), $actual ); // category spectacular-news
		$this->assertNotContains( get_the_title( $this->post2 ), $actual ); // category boring-stuff
	}

	/**
	 * @since	1.X.X
	 */
	function test_are_posts_filtered_by_limit() {

		$this->assume_role( 'administrator' );

		update_post_meta( $this->slide1, 'slide_format', 'recent-posts' );
		update_post_meta( $this->slide1, 'slide_background', '' );
		update_post_meta( $this->slide1, 'slide_recent_posts_limit', 1 );
		update_post_meta( $this->slide1, 'slide_recent_posts_categories', '' );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template('partials/slide.php');
		$actual = ob_get_clean();

		$this->assertContains( get_the_title( $this->post2 ), $actual ); // most recent
		$this->assertNotContains( get_the_title( $this->post1 ), $actual ); // second most recent
	}

	/**
	 * @since	1.X.X
	 */
	function test_is_thumbnail_included_in_recent_posts_slide_with_display_thumbnail_set() {

		/* Create image attachment */
		$file = dirname( __FILE__ ) . '/assets/Kip-400x400.jpg';
		$image_attachment_id = $this->factory->attachment->create_upload_object( $file );
		set_post_thumbnail( $this->post2, $image_attachment_id );

		update_post_meta( $this->slide1, 'slide_format', 'recent-posts' );
		update_post_meta( $this->slide1, 'slide_background', '' );
		update_post_meta( $this->slide1, 'slide_recent_posts_limit', 1 ); // show only most recent, post2
		update_post_meta( $this->slide1, 'slide_recent_posts_categories', '' );
		update_post_meta( $this->slide1, 'slide_recent_posts_display_thumbnail', 1 );
		update_post_meta( $this->slide1, 'slide_recent_posts_use_excerpt', '' );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template( 'partials/slide.php' );
		$actual = ob_get_clean();

		$this->assertRegExp( '/Kip-400x400.*\.jpg/', $actual );
	}

	/**
	 * @since	1.X.X
	 */
	function test_is_thumbnail_not_included_in_recent_posts_slide_with_display_thumbnail_not_set() {

		/* Create image attachment */
		$file = dirname( __FILE__ ) . '/assets/Kip-400x400.jpg';
		$image_attachment_id = $this->factory->attachment->create_upload_object( $file );
		set_post_thumbnail( $this->post2, $image_attachment_id );

		update_post_meta( $this->slide1, 'slide_format', 'recent-posts' );
		update_post_meta( $this->slide1, 'slide_background', '' );
		update_post_meta( $this->slide1, 'slide_recent_posts_limit', 1 ); // show only most recent, post2
		update_post_meta( $this->slide1, 'slide_recent_posts_categories', '' );
		update_post_meta( $this->slide1, 'slide_recent_posts_display_thumbnail', '' );
		update_post_meta( $this->slide1, 'slide_recent_posts_use_excerpt', '' );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template( 'partials/slide.php' );
		$actual = ob_get_clean();

		$this->assertNotRegExp( '/Kip-400x400.*\.jpg/', $actual );
	}

	/**
	 * @since	1.X.X
	 */
	function test_is_excerpt_included_in_recent_posts_slide_with_use_excerpt_set() {

		update_post_meta( $this->slide1, 'slide_format', 'recent-posts' );
		update_post_meta( $this->slide1, 'slide_background', '' );
		update_post_meta( $this->slide1, 'slide_recent_posts_limit', 1 ); // show only most recent, post2
		update_post_meta( $this->slide1, 'slide_recent_posts_categories', '' );
		update_post_meta( $this->slide1, 'slide_recent_posts_display_thumbnail', '' );
		update_post_meta( $this->slide1, 'slide_recent_posts_use_excerpt', 1 );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template( 'partials/slide.php' );
		$actual = ob_get_clean();

		$this->assertContains( $this->post2_excerpt, $actual );
		$this->assertNotContains( $this->post2_content, $actual );
	}

	/**
	 * @since	1.X.X
	 */
	function test_are_all_text_slide_field_elements_included_in_slide() {

		update_post_meta( $this->slide1, 'slide_format', 'recent-posts' );
		update_post_meta( $this->slide1, 'slide_background', '' );
		update_post_meta( $this->slide1, 'slide_recent_posts_limit', 1 ); // show only most recent, post2
		update_post_meta( $this->slide1, 'slide_recent_posts_categories', '' );
		update_post_meta( $this->slide1, 'slide_recent_posts_display_thumbnail', '' );
		update_post_meta( $this->slide1, 'slide_recent_posts_use_excerpt', '' );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template( 'partials/slide.php' );
		$actual = ob_get_clean();

		$this->assertContains( '<div class="foyer-slide-field foyer-slide-field-title', $actual );
		$this->assertContains( '<div class="foyer-slide-field foyer-slide-field-date', $actual );
		$this->assertContains( '<div class="foyer-slide-field foyer-slide-field-content', $actual );
	}

	/**
	 * @since	1.X.X
	 */
	function test_is_content_element_not_included_in_recent_posts_slide_when_empty() {

		/* Create post */
		$post_args = array(
			'post_type' => 'post',
			'post_content' => '',
		);

		$post_id = $this->factory->post->create( $post_args );

		update_post_meta( $this->slide1, 'slide_format', 'recent-posts' );
		update_post_meta( $this->slide1, 'slide_background', '' );
		update_post_meta( $this->slide1, 'slide_recent_posts_limit', 1 ); // show only most recent, post2
		update_post_meta( $this->slide1, 'slide_recent_posts_categories', '' );
		update_post_meta( $this->slide1, 'slide_recent_posts_display_thumbnail', '' );
		update_post_meta( $this->slide1, 'slide_recent_posts_use_excerpt', '' );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template( 'partials/slide.php' );
		$actual = ob_get_clean();

		$this->assertNotContains( '<div class="foyer-slide-field foyer-slide-field-content', $actual );

		wp_delete_post( $post_id ); // makes sure post2 is again the most recent post
	}
}

