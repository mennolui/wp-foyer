<?php
/**
 * @group theater
 */
class Test_Foyer_Public_Templates_Slides_Upcoming_Productions extends Foyer_Theater_UnitTestCase {

	/**
	 * @since	1.X.X
	 */
	function test_are_all_upcoming_productions_slide_properties_included_in_slide() {

		$this->assume_role( 'administrator' );

		update_post_meta( $this->slide1, 'slide_format', 'upcoming-productions' );
		update_post_meta( $this->slide1, 'slide_background', '' );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template('partials/slide.php');
		$actual = ob_get_clean();

		$this->assertContains( get_the_title( $this->production1 ), $actual );
	}

	/**
	 * @since	1.X.X
	 */
	function test_are_productions_upcoming_only() {

		$this->assume_role( 'administrator' );

		update_post_meta( $this->slide1, 'slide_format', 'upcoming-productions' );
		update_post_meta( $this->slide1, 'slide_background', '' );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template('partials/slide.php');
		$actual = ob_get_clean();

		$this->assertContains( get_the_title( $this->production1 ), $actual ); // upcoming production
		$this->assertContains( get_the_title( $this->production2 ), $actual ); // upcoming production
		$this->assertNotContains( get_the_title( $this->production3 ), $actual ); // past production
	}

	/**
	 * @since	1.X.X
	 */
	function test_are_productions_filtered_by_category() {

		$this->assume_role( 'administrator' );

		update_post_meta( $this->slide1, 'slide_format', 'upcoming-productions' );
		update_post_meta( $this->slide1, 'slide_background', '' );
		update_post_meta( $this->slide1, 'slide_upcoming_productions_categories', array( $this->category_concert ) );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template('partials/slide.php');
		$actual = ob_get_clean();

		$this->assertContains( get_the_title( $this->production1 ), $actual ); // category concert
		$this->assertNotContains( get_the_title( $this->production2 ), $actual ); // category film
	}

	/**
	 * @since	1.X.X
	 */
	function test_are_productions_filtered_by_limit() {

		$this->assume_role( 'administrator' );

		update_post_meta( $this->slide1, 'slide_format', 'upcoming-productions' );
		update_post_meta( $this->slide1, 'slide_background', '' );
		update_post_meta( $this->slide1, 'slide_upcoming_productions_limit', 1 );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template('partials/slide.php');
		$actual = ob_get_clean();

		$this->assertContains( get_the_title( $this->production1 ), $actual ); // first upcoming
		$this->assertNotContains( get_the_title( $this->production2 ), $actual ); // second upcoming
	}
}

