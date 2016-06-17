<?php


class Foyer_Test_Slides_Editor extends WP_UnitTestCase {


	function setUp() {
		parent::setUp();

		$slide_args = array(
			'post_type' => Foyer_Slide::post_type_name,
		);
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
		);

		/* Create slides */
		$this->slide1 = $this->factory->post->create( $slide_args );
		$this->slide2 = $this->factory->post->create( $slide_args );
		$this->slide3 = $this->factory->post->create( $slide_args );

		/* Create channel with two slides */
		$this->channel1 = $this->factory->post->create( $channel_args );
		add_post_meta( $this->channel1, Foyer_Slide::post_type_name, array( $this->slide1, $this->slide2 ) );

		/* Create channel with one slide */
		$this->channel2 = $this->factory->post->create( $channel_args );
		add_post_meta( $this->channel2, Foyer_Slide::post_type_name, array( $this->slide1 ) );
	}

	function assume_role( $role = 'author' ) {
		$user = new WP_User( $this->factory->user->create( array( 'role' => $role ) ) );
		wp_set_current_user( $user->ID );
		return $user;
	}

	function get_meta_boxes_for_channel( $channel_id ) {
		$this->assume_role( 'author' );
		set_current_screen( Foyer_Channel::post_type_name );

		do_action( 'add_meta_boxes', Foyer_Channel::post_type_name );
		ob_start();
		do_meta_boxes( Foyer_Channel::post_type_name, 'normal', get_post( $channel_id ) );
		$meta_boxes = ob_get_clean();

		return $meta_boxes;
	}

	function test_slides_editor_is_displayed_on_channel_admin_page() {

		$meta_boxes = $this->get_meta_boxes_for_channel( $this->channel1 );

		$this->assertContains( '<table class="foyer_meta_box_form foyer_slides_editor_form"', $meta_boxes );
	}

	function test_add_slide_html_is_displayed_on_channel_admin_page() {

		$foyer_admin = new Foyer_Admin( 1, 1 ); //@todo
		$add_slide_html = $foyer_admin->get_add_slide_html();

		$meta_boxes = $this->get_meta_boxes_for_channel( $this->channel1 );

		$this->assertContains( $add_slide_html, $meta_boxes );
	}

	function test_slides_list_html_is_displayed_on_channel_admin_page() {

		$foyer_admin = new Foyer_Admin( 1, 1 ); //@todo
		$slides_list_html = $foyer_admin->get_slides_list_html( get_post( $this->channel1 ) );

		$meta_boxes = $this->get_meta_boxes_for_channel( $this->channel1 );

		$this->assertContains( $slides_list_html, $meta_boxes );
	}

	function test_slide_is_added_on_channel_admin_page() {
	}
}

/**
 * Test case for the Ajax callbacks.
 *
 * @group ajax
 */
class Foyer_Test_Slides_Editor_Ajax extends WP_Ajax_UnitTestCase {

	function test_event_is_removed_with_ajax_on_production_page() {
	}

}