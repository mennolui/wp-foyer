<?php

class Test_Foyer_Admin_Display extends Foyer_UnitTestCase {

	function get_meta_boxes_for_display( $display_id ) {
		$this->assume_role( 'author' );
		set_current_screen( Foyer_Display::post_type_name );

		do_action( 'add_meta_boxes', Foyer_Display::post_type_name );
		ob_start();
		do_meta_boxes( Foyer_Display::post_type_name, 'normal', get_post( $display_id ) );
		$meta_boxes = ob_get_clean();

		return $meta_boxes;
	}

	function test_channel_editor_meta_box_is_displayed_on_display_admin_page() {

		$meta_boxes = $this->get_meta_boxes_for_display( $this->display1 );

		$this->assertContains( '<div id="foyer_channel_editor" class="postbox', $meta_boxes );
	}

	function test_channel_scheduler_meta_box_is_displayed_on_display_admin_page() {

		$meta_boxes = $this->get_meta_boxes_for_display( $this->display1 );

		$this->assertContains( '<div id="foyer_channel_scheduler" class="postbox', $meta_boxes );
	}

	function test_is_default_channel_saved() {

		$this->assume_role( 'administrator' );

		$default_channel = $this->channel2;

		$_POST[ Foyer_Display::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Display::post_type_name );
		$_POST['foyer_channel_editor_' . Foyer_Display::post_type_name] = $this->display1;
		$_POST['foyer_channel_editor_default_channel'] = $default_channel;
		$_POST['foyer_channel_editor_scheduled_channel'] = '';
		$_POST['foyer_channel_editor_scheduled_channel_start'] = '';
		$_POST['foyer_channel_editor_scheduled_channel_end'] = '';

		Foyer_Admin_Display::save_display( $this->display1 );

		$updated_display = new Foyer_Display( $this->display1 );

		$actual = $updated_display->get_default_channel();
		$this->assertEquals( $default_channel, $actual );
	}

	function test_is_schedule_saved() {

		$this->assume_role( 'administrator' );

		$scheduled_channel = $this->channel1;
		$schedule_start = date( 'Y-m-d H:i', strtotime( '-10 minutes' ) );
		$schedule_end = date( 'Y-m-d H:i', strtotime( '+10 minutes' ) );
		$schedule_start_timestamp = strtotime( $schedule_start );
		$schedule_end_timestamp = strtotime( $schedule_end );

		$_POST[ Foyer_Display::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Display::post_type_name );
		$_POST['foyer_channel_editor_' . Foyer_Display::post_type_name] = $this->display1;
		$_POST['foyer_channel_editor_default_channel'] = '';
		$_POST['foyer_channel_editor_scheduled_channel'] = $scheduled_channel;
		$_POST['foyer_channel_editor_scheduled_channel_start'] = $schedule_start;
		$_POST['foyer_channel_editor_scheduled_channel_end'] = $schedule_end;

		Foyer_Admin_Display::save_display( $this->display1 );

		$updated_display = new Foyer_Display( $this->display1 );

		$actual = $updated_display->get_schedule();
		$actual = $actual[0]['channel'];
		$this->assertEquals( $scheduled_channel, $actual );

		$actual = $updated_display->get_schedule();
		$actual = $actual[0]['start'];
		$this->assertEquals( $schedule_start_timestamp, $actual );
	}

	function test_is_default_channel_column_empty_when_no_default_channel() {

		$this->assume_role( 'administrator' );

		/* Create display without a default channel */
		$display_args = array(
			'post_type' => Foyer_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );

		ob_start();
		Foyer_Admin_Display::do_channel_columns( 'default_channel', $display_id );
		$actual = ob_get_clean();

		$this->assertEquals( 'None', $actual );
	}

	function test_is_active_channel_column_empty_when_no_default_channel() {

		$this->assume_role( 'administrator' );

		/* Create display without a default channel */
		$display_args = array(
			'post_type' => Foyer_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );

		ob_start();
		Foyer_Admin_Display::do_channel_columns( 'active_channel', $display_id );
		$actual = ob_get_clean();

		$this->assertEquals( 'None', $actual );
	}

	function test_default_channel_column_contains_link_to_default_channel() {

		$this->assume_role( 'administrator' );

		$channel_title = 'Plain default channel';

		/* Create channel */
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
			'post_title' => $channel_title,
		);

		$channel_id = $this->factory->post->create( $channel_args );

		/* Create display with our channel as default */
		$display_args = array(
			'post_type' => Foyer_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );
		add_post_meta( $display_id, Foyer_Channel::post_type_name, $channel_id );

		ob_start();
		Foyer_Admin_Display::do_channel_columns( 'default_channel', $display_id );
		$actual = ob_get_clean();

		$this->assertEquals( '<a href="' . esc_url( get_edit_post_link( $channel_id ) ) . '">' . $channel_title . '</a>', $actual );
	}

	function test_active_channel_column_contains_link_to_active_channel() {

		$this->assume_role( 'administrator' );

		$channel_title = 'Plain default channel';

		/* Create channel */
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
			'post_title' => $channel_title,
		);

		$channel_id = $this->factory->post->create( $channel_args );

		/* Create display with our channel as default */
		$display_args = array(
			'post_type' => Foyer_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );
		add_post_meta( $display_id, Foyer_Channel::post_type_name, $channel_id );

		ob_start();
		Foyer_Admin_Display::do_channel_columns( 'active_channel', $display_id );
		$actual = ob_get_clean();

		$this->assertEquals( '<a href="' . esc_url( get_edit_post_link( $channel_id ) ) . '">' . $channel_title . '</a>', $actual );
	}

	function test_default_channel_html_contains_all_channels() {

		/* Create many channels */
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
		);
		$this->factory->post->create_many( 15, $channel_args );

		$actual = Foyer_Admin_Display::get_default_channel_html( get_post( $this->display1 ) );

		$args = array(
			'post_type' => Foyer_Channel::post_type_name,
			'posts_per_page' => -1,
		);
		$channels = get_posts( $args );

		foreach ( $channels as $channel ) {
			$this->assertContains( $channel->post_title . '</option>', $actual );
		}
	}

	function test_scheduled_channel_html_contains_all_channels() {

		/* Create many channels */
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
		);
		$this->factory->post->create_many( 15, $channel_args );

		$actual = Foyer_Admin_Display::get_scheduled_channel_html( get_post( $this->display1 ) );

		$args = array(
			'post_type' => Foyer_Channel::post_type_name,
			'posts_per_page' => -1,
		);
		$channels = get_posts( $args );

		foreach ( $channels as $channel ) {
			$this->assertContains( $channel->post_title . '</option>', $actual );
		}
	}
}
