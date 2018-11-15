<?php
class Test_Foyer_Admin_Slide_Format_Recent_Posts extends Foyer_UnitTestCase {

	/**
	 * @since	1.X.X
	 */
	function test_are_all_recent_posts_slide_properties_saved() {

		$this->assume_role( 'administrator' );

		$limit = '2';
		$categories = array( 33, 44 );
		$display_thumbnail = '1';
		$use_excerpt = '1';

		$_POST[ Foyer_Slide::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Slide::post_type_name );
		$_POST['slide_format'] = 'recent-posts';
		$_POST['slide_background'] = 'default';

		$_POST['slide_recent_posts_limit'] = $limit;
		$_POST['slide_recent_posts_categories'] = $categories;
		$_POST['slide_recent_posts_display_thumbnail'] = $display_thumbnail;
		$_POST['slide_recent_posts_use_excerpt'] = $use_excerpt;

		Foyer_Admin_Slide::save_slide( $this->slide1 );

		$actual = get_post_meta( $this->slide1, 'slide_recent_posts_limit', true );
		$this->assertEquals( $limit, $actual );

		$actual = get_post_meta( $this->slide1, 'slide_recent_posts_categories', true );
		$this->assertEquals( $categories, $actual );

		$actual = get_post_meta( $this->slide1, 'slide_recent_posts_display_thumbnail', true );
		$this->assertEquals( $display_thumbnail, $actual );

		$actual = get_post_meta( $this->slide1, 'slide_recent_posts_use_excerpt', true );
		$this->assertEquals( $use_excerpt, $actual );
	}
}