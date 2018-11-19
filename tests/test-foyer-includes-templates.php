<?php

class Test_Foyer_Templates extends Foyer_UnitTestCase {

	/**
	 * @since	1.7.2
	 */
	function test_are_plugin_template_paths_registered() {

		$plugin_template_path1 = '/just/a/path';
		Foyer_Templates::register_plugin_template_path( $plugin_template_path1 );

		$plugin_template_path2 = '/just/another/path';
		Foyer_Templates::register_plugin_template_path( $plugin_template_path2 );

		$plugin_template_paths = Foyer_Templates::get_plugin_template_paths();

		$this->assertContains( $plugin_template_path1, $plugin_template_paths );
		$this->assertContains( $plugin_template_path2, $plugin_template_paths );
	}
}
