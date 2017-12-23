<?php

class Test_Foyer_Updater extends Foyer_UnitTestCase {

	function test_is_database_version_updated_after_plugin_update() {
		// Set to really old version to trigger database update
		Foyer_Updater::update_db_version( '1.0.0' );

		$actual = Foyer_Updater::update();
		$expected = true;
		$this->assertEquals( $expected, $actual );

		$actual = Foyer_Updater::get_db_version();
		$expected = Foyer::get_version();
		$this->assertEquals( $expected, $actual );
	}

	function test_is_database_version_updated_after_plugin_update_with_no_database_version_set() {
		// Remove database version to trigger database update
		delete_option( 'foyer_plugin_version' );

		$actual = Foyer_Updater::update();
		$expected = true;
		$this->assertEquals( $expected, $actual );

		$actual = Foyer_Updater::get_db_version();
		$expected = Foyer::get_version();
		$this->assertEquals( $expected, $actual );
	}

	function test_is_database_update_skipped_when_database_is_up_to_date() {
		// Set ddatabase version to current plugin version
		Foyer_Updater::update_db_version( Foyer::get_version() );

		$actual = Foyer_Updater::update();
		$expected = false;
		$this->assertEquals( $expected, $actual );
	}


}
