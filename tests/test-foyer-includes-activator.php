<?php

class Test_Foyer_Activator extends Foyer_UnitTestCase {

	function setUp() {
		global $wp_rewrite;

		parent::setUp();

		// Enable pretty permalinks for all Test_Foyer_Activator tests
		$wp_rewrite->set_permalink_structure('/%postname%/');
	}

	function has_foyer_rewrite_rule() {
		global $wp_rewrite;

		foreach ( $wp_rewrite->rewrite_rules() as $key => $value ) {
			if (
				false !== strpos( $key, 'foyer/([^/]+)/page/?([0-9]{1,})/?$' ) &&
				false !== strpos( $value, 'index.php?foyer_display=$matches[1]&paged=$matches[2]' )
			) {
				// Rewrite rule is present
				return true;
			}
		}

		return false;
	}

	/**
	 * @since	1.5.3
	 */
	function test_is_foyer_rewrite_rule_added_on_activation() {

		// Rewrite rule should not be present before activation
		$this->assertFalse( $this->has_foyer_rewrite_rule() );

		// Run activation code, normally run through register_activation_hook
		require_once dirname( dirname( __FILE__ ) ) . '/includes/class-foyer-activator.php';
		Foyer_Activator::activate();

		// Rewrite rule should be present after activation
		$this->assertTrue( $this->has_foyer_rewrite_rule() );
	}

	/**
	 * @since	1.5.3
	 */
	function test_is_foyer_rewrite_rule_removed_on_deactivation() {

		// Run activation code, normally run through register_activation_hook
		require_once dirname( dirname( __FILE__ ) ) . '/includes/class-foyer-activator.php';
		Foyer_Activator::activate();

		// Rewrite rule should be present after activation
		$this->assertTrue( $this->has_foyer_rewrite_rule() );

		// Pretend our Display cpt does not exist any more
		unregister_post_type( Foyer_Display::post_type_name );

		// Run deactivation code, normally run through register_deactivation_hook
		require_once dirname( dirname( __FILE__ ) ) . '/includes/class-foyer-deactivator.php';
		Foyer_Deactivator::deactivate();

		$this->markTestIncomplete( 'This test has not been implemented yet.' );
		// Rewrite rule should not be present after deactivation
//		$this->assertFalse( $this->has_foyer_rewrite_rule() );
//		@todo: make this work - for some reason foyer rewrite rules are still present after unregister_post_type

		// Re-register our Display cpt
		Foyer_Setup::register_post_types();
	}
}
