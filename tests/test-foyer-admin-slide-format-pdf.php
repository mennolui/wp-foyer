<?php

class Test_Foyer_Admin_Slide_Format_PDF extends Foyer_UnitTestCase {

	/**
	 * @since	1.?
	 * @since	1.4.0	Updated to work with slide backgrounds.
	 */
	function test_are_all_pdf_slide_properties_saved() {

		$this->assume_role( 'administrator' );

		/* Create PDF attachment */
		$file = dirname( __FILE__ ) . '/assets/IJSBEERpaspoort-3p.pdf';
		$pdf_attachment_id = $this->factory->attachment->create_upload_object( $file );

		$_POST[ Foyer_Slide::post_type_name.'_nonce' ] = wp_create_nonce( Foyer_Slide::post_type_name );
		$_POST['slide_format'] = 'pdf';
		$_POST['slide_background'] = '';

		$_POST['slide_pdf_file'] = $pdf_attachment_id;

		Foyer_Admin_Slide::save_slide( $this->slide1 );

		$actual = get_post_meta( $this->slide1, 'slide_pdf_file', true );
		$this->assertEquals( $pdf_attachment_id, $actual );
	}
}