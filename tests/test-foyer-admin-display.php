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

		$admin_display = new Foyer_Admin_Display( 'foyer', '9.9.9' );
		$admin_display->save_display( $this->display1 );

		$updated_display = new Foyer_Display( $this->display1 );

		$actual = $updated_display->get_default_channel();
		$this->assertEquals( $default_channel, $actual );
	}

	function test_is_schedule_saved() {

		$this->assume_role( 'administrator' );

		$scheduled_channel = $this->channel1;
		$schedule_start = date_i18n( 'Y-m-d H:i', strtotime( '-10 minutes' ) );
		$schedule_end = date_i18n( 'Y-m-d H:i', strtotime( '+10 minutes' ) );
		$schedule_start_timestamp = strtotime( $schedule_start );
		$schedule_end_timestamp = strtotime( $schedule_end );

		$_POST[ Foyer_Display::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Display::post_type_name );
		$_POST['foyer_channel_editor_' . Foyer_Display::post_type_name] = $this->display1;
		$_POST['foyer_channel_editor_default_channel'] = '';
		$_POST['foyer_channel_editor_scheduled_channel'] = $scheduled_channel;
		$_POST['foyer_channel_editor_scheduled_channel_start'] = $schedule_start;
		$_POST['foyer_channel_editor_scheduled_channel_end'] = $schedule_end;

		$admin_display = new Foyer_Admin_Display( 'foyer', '9.9.9' );
		$admin_display->save_display( $this->display1 );

		$updated_display = new Foyer_Display( $this->display1 );

		$actual = $updated_display->get_schedule();
		$actual = $actual[0]['channel'];
		$this->assertEquals( $scheduled_channel, $actual );

		$actual = $updated_display->get_schedule();
		$actual = $actual[0]['start'];
		$this->assertEquals( $schedule_start_timestamp, $actual );
	}
}
