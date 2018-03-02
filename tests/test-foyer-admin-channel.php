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

		$add_slide_html = Foyer_Admin_Channel::get_add_slide_html();

		$meta_boxes = $this->get_meta_boxes_for_channel( $this->channel1 );

		$this->assertContains( $add_slide_html, $meta_boxes );
	}

	function test_slides_list_html_is_displayed_on_channel_admin_page() {

		$slides_list_html = Foyer_Admin_Channel::get_slides_list_html( get_post( $this->channel1 ) );

		$meta_boxes = $this->get_meta_boxes_for_channel( $this->channel1 );

		$this->assertContains( $slides_list_html, $meta_boxes );
	}

	function test_are_slideshow_settings_saved() {

		$this->assume_role( 'administrator' );

		$duration = '3';
		$transition = 'none';

		$_POST[ Foyer_Channel::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Channel::post_type_name );
		$_POST['foyer_slides_settings_duration'] = $duration;
		$_POST['foyer_slides_settings_transition'] = $transition;

		Foyer_Admin_Channel::save_channel( $this->channel1 );

		$updated_channel = new Foyer_Channel( $this->channel1 );

		$actual = $updated_channel->get_slides_duration();
		$this->assertEquals( $duration, $actual );

		$actual = $updated_channel->get_slides_transition();
		$this->assertEquals( $transition, $actual );
	}

	/**
	 * @since	1.4.0
	 */
	function test_add_slide_html_contains_all_slides() {

		/* Create many slides */
		$slide_args = array(
			'post_type' => Foyer_Slide::post_type_name,
		);
		$this->factory->post->create_many( 15, $slide_args );

		$actual = Foyer_Admin_Channel::get_add_slide_html( get_post( $this->channel1 ) );

		$args = array(
			'post_type' => Foyer_Slide::post_type_name,
			'posts_per_page' => -1,
		);
		$slides = get_posts( $args );

		foreach ( $slides as $slide ) {
			$this->assertContains( $slide->post_title . '</option>', $actual );
		}
	}

	function test_is_stack_class_included_in_channel_admin_page_for_slide_stack() {

		/* Create a slide */
		$slide_args = array(
			'post_type' => Foyer_Slide::post_type_name,
		);

		$slide_stack_id = $this->factory->post->create( $slide_args );
		update_post_meta( $slide_stack_id, 'slide_format', 'pdf' );

		/* Create a channel */
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
		);

		$channel_id = $this->factory->post->create( $channel_args );
		update_post_meta( $channel_id, Foyer_Slide::post_type_name, array( $slide_stack_id ) );

		$meta_boxes = $this->get_meta_boxes_for_channel( $channel_id );

		$this->assertContains( '<div class="foyer_slides_editor_slides_slide foyer-slide-is-stack', $meta_boxes );
	}

	function test_is_stack_class_not_included_in_channel_admin_page_for_single_slide() {

		/* Create a slide */
		$slide_args = array(
			'post_type' => Foyer_Slide::post_type_name,
		);

		$slide_single_id = $this->factory->post->create( $slide_args );
		update_post_meta( $slide_single_id, 'slide_format', 'default' );

		/* Create a channel */
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
		);

		$channel_id = $this->factory->post->create( $channel_args );
		update_post_meta( $channel_id, Foyer_Slide::post_type_name, array( $slide_single_id ) );

		$meta_boxes = $this->get_meta_boxes_for_channel( $channel_id );

		$this->assertContains( '<div class="foyer_slides_editor_slides_slide', $meta_boxes );
		$this->assertNotContains( '<div class="foyer_slides_editor_slides_slide foyer-slide-is-stack', $meta_boxes );
	}

	function test_is_overlay_with_info_about_slide_included_in_channel_admin_page() {

		$title = 'Just another slide';

		/* Create a slide */
		$slide_args = array(
			'post_type' => Foyer_Slide::post_type_name,
			'post_title' => $title
		);

		$slide_id = $this->factory->post->create( $slide_args );
		update_post_meta( $slide_id, 'slide_format', 'default' );
		update_post_meta( $slide_id, 'slide_background', 'image' );

		/* Create a channel */
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
		);

		$channel_id = $this->factory->post->create( $channel_args );
		update_post_meta( $channel_id, Foyer_Slide::post_type_name, array( $slide_id ) );

		$meta_boxes = $this->get_meta_boxes_for_channel( $channel_id );

		$this->assertContains( '<div class="foyer_slides_editor_slides_slide_iframe_container_overlay', $meta_boxes );
		$this->assertContains( '<h4>' . $title . '</h4>', $meta_boxes );
		$this->assertContains( '<dd>Default</dd>', $meta_boxes );
		$this->assertContains( '<dd>Image</dd>', $meta_boxes );
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