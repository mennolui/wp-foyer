<?php

class Foyer_Theater_UnitTestCase extends Foyer_UnitTestCase {

	function setUp() {

		// Load Theater plugin (if not loaded already)
		global $wp_theatre;
		require_once dirname( dirname( __FILE__ ) ) . '/../../../plugins/theatre/theater.php';

		parent::setUp();
	}
}