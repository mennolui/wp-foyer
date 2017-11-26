<?php

class Test_Foyer_Public_Templates_Slides_Production extends Foyer_UnitTestCase {

	function test_are_all_production_slide_properties_included_in_slide() {

		// Load Theater plugin (if not loaded already)
		require_once dirname( dirname( __FILE__ ) ) . '/../../plugins/theatre/theater.php';

		$this->assume_role( 'administrator' );

		$production_title = 'Superduperproduction';
		$prod_args = array(
			'post_type' => WPT_Production::post_type_name,
			'post_title' => $production_title,
		);

		/* Create production */
		$production_id = $this->factory->post->create( $prod_args );

		/* Create image attachment */
		$file = dirname( __FILE__ ) . '/assets/Kip-400x400.jpg';
		$image_attachment_id = $this->factory->attachment->create_upload_object( $file );

		update_post_meta( $this->slide1, 'slide_format', 'production' );
		update_post_meta( $this->slide1, 'slide_production_production_id', $production_id );
		update_post_meta( $this->slide1, 'slide_production_image', $image_attachment_id );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template('partials/slide.php');
		$actual = ob_get_clean();

		$this->assertContains( $production_title, $actual );
		$this->assertRegExp( '/Kip-400x400.*\.jpg/', $actual );
	}
}

