<?php
class Test_Foyer_Public_Templates_Slides_Post extends Foyer_UnitTestCase {

	/**
	 * @since	1.5.0
	 */
	function test_are_all_post_slide_properties_included_in_slide() {

		$post_title = 'Hello world this is our post';
		$args = array(
			'post_type' => 'post',
			'post_title' => $post_title,
		);

		/* Create post */
		$post_id = $this->factory->post->create( $args );

		update_post_meta( $this->slide1, 'slide_format', 'post' );
		update_post_meta( $this->slide1, 'slide_background', '' );
		update_post_meta( $this->slide1, 'slide_post_post_id', $post_id );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template('partials/slide.php');
		$actual = ob_get_clean();

		$this->assertContains( $post_title, $actual );
		$this->assertContains( date_i18n( get_option( 'date_format' ) ), $actual );

		// @todo: featured image
	}
}

