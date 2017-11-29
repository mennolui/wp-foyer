<?php

class Test_Foyer_Admin extends Foyer_UnitTestCase {

	function test_are_scripts_and_styles_enqueued_on_foyer_admin_screen() {

		$this->assume_role( 'administrator' );

		set_current_screen( 'edit.php?post_type=foyer_display' );

		$actual = get_echo( 'wp_head' );

//		@todo: make this work
//		$this->assertContains( 'foyer-admin-min.js', $actual );
//		$this->assertContains( 'foyer-admin.css', $actual );
	}
}
