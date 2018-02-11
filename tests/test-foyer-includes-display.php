<?php

class Test_Foyer_Display extends Foyer_UnitTestCase {

	function test_is_default_channel_used_when_schedule_has_no_channel() {

		$channel_title = 'Plain default channel';

		/* Create channel */
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
			'post_title' => $channel_title,
		);

		$channel_id = $this->factory->post->create( $channel_args );

		/* Create display with our channel as default, and a faulty schedule without channel */
		$display_args = array(
			'post_type' => Foyer_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );
		add_post_meta( $display_id, Foyer_Channel::post_type_name, $channel_id );

		$schedule = array(
			'channel' => false,
			'start' => 	strtotime( '-1 day' ),
			'end' => strtotime( '+1 day' ),
		);
		add_post_meta( $display_id, 'foyer_display_schedule', $schedule, false );

		$display = new Foyer_Display( $display_id );

		$actual = $display->get_active_channel();

		$this->assertEquals( $channel_id, $actual );
	}

	function test_is_default_channel_used_when_schedule_has_not_published_channel() {

		/* Create published channel */
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
		);

		$channel_1_id = $this->factory->post->create( $channel_args );

		/* Create trashed channel */
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
			'post_status' => 'trash',
		);

		$channel_2_id = $this->factory->post->create( $channel_args );

		/* Create display with our published channel as default, and a schedule with trashed channel */
		$display_args = array(
			'post_type' => Foyer_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );
		add_post_meta( $display_id, Foyer_Channel::post_type_name, $channel_1_id );

		$schedule = array(
			'channel' => $channel_2_id,
			'start' => 	strtotime( '-1 day' ),
			'end' => strtotime( '+1 day' ),
		);
		add_post_meta( $display_id, 'foyer_display_schedule', $schedule, false );

		$display = new Foyer_Display( $display_id );

		$actual = $display->get_active_channel();

		$this->assertEquals( $channel_1_id, $actual );
	}

	function test_is_scheduled_channel_used_when_schedule_has_published_channel() {

		/* Create published channels */
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
		);

		$channel_1_id = $this->factory->post->create( $channel_args );
		$channel_2_id = $this->factory->post->create( $channel_args );

		/* Create display with our published channel as default, and a schedule with the other published channel */
		$display_args = array(
			'post_type' => Foyer_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );
		add_post_meta( $display_id, Foyer_Channel::post_type_name, $channel_1_id );

		$schedule = array(
			'channel' => $channel_2_id,
			'start' => 	strtotime( '-1 day' ),
			'end' => strtotime( '+1 day' ),
		);
		add_post_meta( $display_id, 'foyer_display_schedule', $schedule, false );

		$display = new Foyer_Display( $display_id );

		$actual = $display->get_active_channel();

		$this->assertEquals( $channel_2_id, $actual );
	}

	function test_is_default_channel_not_returned_when_channel_is_not_published() {

		/* Create trashed channel */
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
			'post_status' => 'trash',
		);

		$channel_id = $this->factory->post->create( $channel_args );

		/* Create display with our trashed channel as default */
		$display_args = array(
			'post_type' => Foyer_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );
		add_post_meta( $display_id, Foyer_Channel::post_type_name, $channel_id );

		$display = new Foyer_Display( $display_id );

		$actual = $display->get_active_channel();

		$this->assertEquals( false, $actual );
	}

	function test_is_default_channel_returned_when_channel_is_published() {

		/* Create published channel */
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
		);

		$channel_id = $this->factory->post->create( $channel_args );

		/* Create display with our published channel as default */
		$display_args = array(
			'post_type' => Foyer_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );
		add_post_meta( $display_id, Foyer_Channel::post_type_name, $channel_id );

		$display = new Foyer_Display( $display_id );

		$actual = $display->get_active_channel();

		$this->assertEquals( $channel_id, $actual );
	}

	function test_is_scheduled_channel_used_when_schedule_is_now() {

		$this->assume_role( 'administrator' );

		/* Create channels */
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
		);

		$channel_1_id = $this->factory->post->create( $channel_args );
		$channel_2_id = $this->factory->post->create( $channel_args );

		/* Create display */
		$display_args = array(
			'post_type' => Foyer_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );

		$default_channel = $channel_1_id;
		$scheduled_channel = $channel_2_id;
		$schedule_start = date( 'Y-m-d H:i', strtotime( '-10 minutes' ) ); // Convert to UTC
		$schedule_end = date( 'Y-m-d H:i', strtotime( '+10 minutes' ) ); // Convert to UTC

		$_POST[ Foyer_Display::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Display::post_type_name );
		$_POST['foyer_channel_editor_' . Foyer_Display::post_type_name] = $display_id;
		$_POST['foyer_channel_editor_default_channel'] = $default_channel;
		$_POST['foyer_channel_editor_scheduled_channel'] = $scheduled_channel;
		$_POST['foyer_channel_editor_scheduled_channel_start'] = $schedule_start;
		$_POST['foyer_channel_editor_scheduled_channel_end'] = $schedule_end;

		Foyer_Admin_Display::save_display( $display_id );

		$display = new Foyer_Display( $display_id );

		$actual = $display->get_active_channel();

		$this->assertEquals( $scheduled_channel, $actual );
	}

	function test_is_default_channel_used_when_schedule_is_not_now() {

		$this->assume_role( 'administrator' );

		/* Create channels */
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
		);

		$channel_1_id = $this->factory->post->create( $channel_args );
		$channel_2_id = $this->factory->post->create( $channel_args );

		/* Create display */
		$display_args = array(
			'post_type' => Foyer_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );

		$default_channel = $channel_1_id;
		$scheduled_channel = $channel_2_id;
		$schedule_start = date( 'Y-m-d H:i', strtotime( '+10 minutes' ) ); // Convert to UTC
		$schedule_end = date( 'Y-m-d H:i', strtotime( '+20 minutes' ) ); // Convert to UTC

		$_POST[ Foyer_Display::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Display::post_type_name );
		$_POST['foyer_channel_editor_' . Foyer_Display::post_type_name] = $display_id;
		$_POST['foyer_channel_editor_default_channel'] = $default_channel;
		$_POST['foyer_channel_editor_scheduled_channel'] = $scheduled_channel;
		$_POST['foyer_channel_editor_scheduled_channel_start'] = $schedule_start;
		$_POST['foyer_channel_editor_scheduled_channel_end'] = $schedule_end;

		Foyer_Admin_Display::save_display( $display_id );

		$display = new Foyer_Display( $display_id );

		$actual = $display->get_active_channel();

		$this->assertEquals( $default_channel, $actual );
	}

	function test_is_scheduled_channel_used_when_schedule_is_now_and_timezone_set() {
		$timezone_offset = 5;
		update_option( 'gmt_offset', $timezone_offset );

		$this->assume_role( 'administrator' );

		/* Create channels */
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
		);

		$channel_1_id = $this->factory->post->create( $channel_args );
		$channel_2_id = $this->factory->post->create( $channel_args );

		/* Create display */
		$display_args = array(
			'post_type' => Foyer_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );

		$default_channel = $channel_1_id;
		$scheduled_channel = $channel_2_id;
		$schedule_start = date( 'Y-m-d H:i', strtotime( '-10 minutes' ) + $timezone_offset * HOUR_IN_SECONDS ); // Convert to UTC
		$schedule_end = date( 'Y-m-d H:i', strtotime( '+10 minutes' ) + $timezone_offset * HOUR_IN_SECONDS ); // Convert to UTC

		$_POST[ Foyer_Display::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Display::post_type_name );
		$_POST['foyer_channel_editor_' . Foyer_Display::post_type_name] = $display_id;
		$_POST['foyer_channel_editor_default_channel'] = $default_channel;
		$_POST['foyer_channel_editor_scheduled_channel'] = $scheduled_channel;
		$_POST['foyer_channel_editor_scheduled_channel_start'] = $schedule_start;
		$_POST['foyer_channel_editor_scheduled_channel_end'] = $schedule_end;

		Foyer_Admin_Display::save_display( $display_id );

		$display = new Foyer_Display( $display_id );

		$actual = $display->get_active_channel();

		$this->assertEquals( $scheduled_channel, $actual );
	}

	function test_is_default_channel_used_when_schedule_is_not_now_and_timezone_set() {
		$timezone_offset = 5;
		update_option( 'gmt_offset', $timezone_offset );

		$this->assume_role( 'administrator' );

		/* Create channels */
		$channel_args = array(
			'post_type' => Foyer_Channel::post_type_name,
		);

		$channel_1_id = $this->factory->post->create( $channel_args );
		$channel_2_id = $this->factory->post->create( $channel_args );

		/* Create display */
		$display_args = array(
			'post_type' => Foyer_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );

		$default_channel = $channel_1_id;
		$scheduled_channel = $channel_2_id;
		$schedule_start = date( 'Y-m-d H:i', strtotime( '+10 minutes' ) + $timezone_offset * HOUR_IN_SECONDS ); // Convert to UTC
		$schedule_end = date( 'Y-m-d H:i', strtotime( '+20 minutes' ) + $timezone_offset * HOUR_IN_SECONDS ); // Convert to UTC

		$_POST[ Foyer_Display::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Display::post_type_name );
		$_POST['foyer_channel_editor_' . Foyer_Display::post_type_name] = $display_id;
		$_POST['foyer_channel_editor_default_channel'] = $default_channel;
		$_POST['foyer_channel_editor_scheduled_channel'] = $scheduled_channel;
		$_POST['foyer_channel_editor_scheduled_channel_start'] = $schedule_start;
		$_POST['foyer_channel_editor_scheduled_channel_end'] = $schedule_end;

		Foyer_Admin_Display::save_display( $display_id );

		$display = new Foyer_Display( $display_id );

		$actual = $display->get_active_channel();

		$this->assertEquals( $default_channel, $actual );
	}

	function test_is_reset_request_added() {

		/* Create display */
		$display_args = array(
			'post_type' => Foyer_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );

		/* Check that no reset request is present */
		$this->assertEmpty( get_post_meta( $display_id, 'foyer_reset_display' ), true );

		$display = new Foyer_Display( $display_id );
		$display->add_reset_request();

		/* Check that reset request was added */
		$this->assertNotEmpty( get_post_meta( $display_id, 'foyer_reset_display' ), true );
	}

	function test_is_reset_request_deleted() {

		/* Create display */
		$display_args = array(
			'post_type' => Foyer_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );

		$display = new Foyer_Display( $display_id );
		$display->add_reset_request();

		/* Check that reset request was added */
		$this->assertNotEmpty( get_post_meta( $display_id, 'foyer_reset_display' ), true );

		$display->delete_reset_request();

		/* Check that no reset request is present after delete */
		$this->assertEmpty( get_post_meta( $display_id, 'foyer_reset_display' ), true );
	}

	function test_is_foyer_reset_display_class_added_when_reset_is_requested() {

		/* Create display */
		$display_args = array(
			'post_type' => Foyer_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );

		$display = new Foyer_Display( $display_id );

		/* Check that no foyer-reset-display class is present by default */
		ob_start();
		$display->classes();
		$actual = ob_get_clean();

		$expected = 'foyer-reset-display';
		$this->assertNotContains( $expected, $actual );

		$display->add_reset_request();

		/* Check that foyer-reset-display class is added */
		ob_start();
		$display->classes();
		$actual = ob_get_clean();

		$expected = 'foyer-reset-display';
		$this->assertContains( $expected, $actual );
	}

	function test_is_foyer_reset_display_class_not_added_when_reset_is_requested_and_previewing() {

		// We are previewing
		$_GET['foyer-preview'] = 1;

		/* Create display */
		$display_args = array(
			'post_type' => Foyer_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );

		$display = new Foyer_Display( $display_id );
		$display->add_reset_request();

		/* Check that foyer-reset-display class is not added */
		ob_start();
		$display->classes();
		$actual = ob_get_clean();

		$expected = 'foyer-reset-display';
		$this->assertNotContains( $expected, $actual );
	}
}
