<?php

class Test_Foyer_Activator extends Foyer_UnitTestCase {

	/**
	 * @since	1.5.3
	 */
	function test_is_foyer_rewrite_rule_added_on_activation() {
		global $wp_rewrite;

		// Enable pretty permalinks
		$wp_rewrite->set_permalink_structure('/%postname%/');

		$display_slug = 'prettypermalink';

		/* Create display with title and slug 'prettypermalink' */
		$display_args = array(
			'post_type' => Foyer_Display::post_type_name,
			'post_title' => $display_slug,
		);
		$display_id = $this->factory->post->create( $display_args );

		// Display post should not be found
		$actual = url_to_postid( get_home_url( get_current_blog_id(), 'foyer/' . $display_slug ) );
		$this->assertNotEquals( $display_id, $actual );

		// Run activation code, normally run through register_activation_hook
		require_once dirname( dirname( __FILE__ ) ) . '/includes/class-foyer-activator.php';
		Foyer_Activator::activate();

		// Display post should be found after activation
		$actual = url_to_postid( get_home_url( get_current_blog_id(), 'foyer/' . $display_slug ) );
		$this->assertEquals( $display_id, $actual );
	}
}
