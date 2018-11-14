<?php
/**
 * @group theater
 */
class Test_Foyer_Public_Templates_Slides_Backgrounds_Default_Upcoming_Productions extends Foyer_Theater_UnitTestCase {

	/**
	 * @since	1.7.0
	 */
	function test_are_all_default_background_properties_of_upcoming_productions_slide_included_in_slide() {

		$this->assume_role( 'administrator' );

		/* Create image attachment and set as production thumbnail */
		$file = dirname( __FILE__ ) . '/assets/Kip-400x400.jpg';
		$image_attachment_id = $this->factory->attachment->create_upload_object( $file );
		set_post_thumbnail( $this->production1, $image_attachment_id );

		update_post_meta( $this->slide1, 'slide_format', 'upcoming-productions' );
		update_post_meta( $this->slide1, 'slide_background', 'default' );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template('partials/slide.php');
		$actual = ob_get_clean();

		$this->assertRegExp( '/Kip-400x400.*\.jpg/', $actual );
	}
}

