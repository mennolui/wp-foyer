<?php


class Test_Foyer_Admin_Channel extends Foyer_UnitTestCase {

	function get_meta_boxes_for_channel( $channel_id ) {
		$this->assume_role( 'author' );
		set_current_screen( Foyer_Channel::post_type_name );

		do_action( 'add_meta_boxes', Foyer_Channel::post_type_name );
		ob_start();
		do_meta_boxes( Foyer_Channel::post_type_name, 'normal', get_post( $channel_id ) );
		$meta_boxes = ob_get_clean();

		return $meta_boxes;
	}

	function test_does_channel_have_slides() {

		$channel = new Foyer_Channel( $this->channel1 );
		$slides = $channel->get_slides();

		$this->assertCount( 2, $slides );
	}

	function test_slides_editor_is_displayed_on_channel_admin_page() {

		$meta_boxes = $this->get_meta_boxes_for_channel( $this->channel1 );

		$this->assertContains( '<div class="foyer_meta_box foyer_slides_editor"', $meta_boxes );
	}

	function test_add_slide_html_is_displayed_on_channel_admin_page() {

		$foyer_admin_channel = new Foyer_Admin_Channel( 1, 1 ); //@todo
		$add_slide_html = $foyer_admin_channel->get_add_slide_html();

		$meta_boxes = $this->get_meta_boxes_for_channel( $this->channel1 );

		$this->assertContains( $add_slide_html, $meta_boxes );
	}

	function test_slides_list_html_is_displayed_on_channel_admin_page() {

		$foyer_admin_channel = new Foyer_Admin_Channel( 1, 1 ); //@todo
		$slides_list_html = $foyer_admin_channel->get_slides_list_html( get_post( $this->channel1 ) );

		$meta_boxes = $this->get_meta_boxes_for_channel( $this->channel1 );

		$this->assertContains( $slides_list_html, $meta_boxes );
	}
}

/**
 * Test case for the Ajax callbacks.
 *
 * @group ajax
 */
class Test_Foyer_Admin_Channel_Ajax extends Foyer_Ajax_UnitTestCase {

	function add_slide_to_channel( $slide_id, $channel_id ) {

		$this->_setRole( 'administrator' );

		$_POST['action'] = 'foyer_slides_editor_add_slide';
		$_POST['channel_id'] = $channel_id;
		$_POST['slide_id'] = $slide_id;
		$_POST['nonce'] = wp_create_nonce( 'foyer_slides_editor_ajax_nonce' );

		try {
			$this->_handleAjax( 'foyer_slides_editor_add_slide' );
		}
		catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		}
		catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}
	}

	function remove_slide_from_channel( $slide_key, $channel_id ) {

		$this->_setRole( 'administrator' );

		$_POST['action'] = 'foyer_slides_editor_remove_slide';
		$_POST['channel_id'] = $channel_id;
		$_POST['slide_key'] = $slide_key;
		$_POST['nonce'] = wp_create_nonce( 'foyer_slides_editor_ajax_nonce' );

		try {
			$this->_handleAjax( 'foyer_slides_editor_remove_slide' );
		}
		catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		}
		catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}
	}

	function test_slide_is_added_with_ajax_on_channel_admin_page() {

		$channel = new Foyer_Channel( $this->channel1 );
		$slides_before = $channel->get_slides();

		// create new slide
		$slide_args = array(
			'post_type' => Foyer_Slide::post_type_name,
		);
		$new_slide_id = $this->factory->post->create( $slide_args );

		// add new slide to channel with two slides
		$this->add_slide_to_channel( $new_slide_id, $this->channel1 );

		$channel = new Foyer_Channel( $this->channel1 );
		$slides_after = $channel->get_slides();

		$slide_ids_after = array();
		foreach ( $slides_after as $slide ) {
			$slide_ids_after[] = $slide->ID;
		}

		$this->assertContains( $new_slide_id, $slide_ids_after );
	}

	function test_slide_is_removed_with_ajax_on_channel_admin_page() {

		$channel = new Foyer_Channel( $this->channel1 );
		$slides_before = $channel->get_slides();

		// remove second slide from channel with two slides
		$remove_slide_key = 1;
		$this->remove_slide_from_channel( $remove_slide_key, $this->channel1 );

		$channel = new Foyer_Channel( $this->channel1 );
		$slides_after = $channel->get_slides();

		$slide_ids_after = array();
		foreach ( $slides_after as $slide ) {
			$slide_ids_after[] = $slide->ID;
		}

		$removed_slide_id = $slides_before[$remove_slide_key]->ID;

		$this->assertNotContains( $removed_slide_id, $slide_ids_after );
	}

	function test_first_slide_is_removed_with_ajax_on_channel_admin_page() {

		$channel = new Foyer_Channel( $this->channel1 );
		$slides_before = $channel->get_slides();

		// remove first slide from channel with two slides
		$remove_slide_key = 0;
		$this->remove_slide_from_channel( $remove_slide_key, $this->channel1 );

		$channel = new Foyer_Channel( $this->channel1 );
		$slides_after = $channel->get_slides();

		$slide_ids_after = array();
		foreach ( $slides_after as $slide ) {
			$slide_ids_after[] = $slide->ID;
		}

		$removed_slide_id = $slides_before[$remove_slide_key]->ID;

		$this->assertNotContains( $removed_slide_id, $slide_ids_after );
	}
}