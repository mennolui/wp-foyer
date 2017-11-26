<?php

class Test_Foyer_Public_Templates_Slides_PDF extends Foyer_UnitTestCase {

	function test_are_all_pdf_pages_included_in_slide() {

		$this->assume_role( 'administrator' );

		/* Create PDF attachment */
		$file = dirname( __FILE__ ) . '/assets/IJSBEERpaspoort-3p.pdf';
		$pdf_attachment_id = $this->factory->attachment->create_upload_object( $file );

		Foyer_Admin_Slide_Format_PDF::add_pdf_images_to_attachment( $pdf_attachment_id );

		update_post_meta( $this->slide1, 'slide_format', 'pdf' );
		update_post_meta( $this->slide1, 'slide_pdf_file', $pdf_attachment_id );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Foyer_Templates::get_template('partials/slide.php');
		$actual = ob_get_clean();

		$this->assertRegExp( '/IJSBEERpaspoort-3p.*\-p1-pdf.png/', $actual );
		$this->assertRegExp( '/IJSBEERpaspoort-3p.*\-p2-pdf.png/', $actual );
		$this->assertRegExp( '/IJSBEERpaspoort-3p.*\-p3-pdf.png/', $actual );
	}
}

