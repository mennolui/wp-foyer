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
}
