<?php
/**
 * Extends the WP_Image_Editor_Imagick class to allow processing of individual pages in PDF files.
 *
 * @since		1.1.0
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Image_Editor_Imagick extends WP_Image_Editor_Imagick {

	/**
	 * Get the number of pages in the pdf file.
	 *
	 * @since	1.1.0
	 *
	 * @return	int|WP_Error		Number of pages or WP_Error on failure.
	 */
	public function pdf_get_number_of_pages() {

		try {
			$number_of_pages = $this->image->getNumberImages();
		}
		catch ( Exception $e ) {
			return new WP_Error( 'invalid_image', __( 'Could not read number of pages in image.' ), $this->file );
		}

		return $number_of_pages;
	}

	/**
	 * Prepares a specific page so it can be loaded.
	 *
	 * Stores the page number to be used on load().
	 * Ddestroys the previously used Imagick object and resets the file pointer to enable loading of a new page.
	 *
	 * @since	1.1.0
	 *
	 * @param	int		$page_number	The number of the page to prepare for loading.
	 * @return	void
	 */
	public function pdf_prepare_page_for_load( $page_number ) {

		// Destroy Imagick object, otherwise load() would not load a new page but just return the existing object.
		if ( $this->image instanceof Imagick ) {
			$this->image->clear();
			$this->image->destroy();
			$this->image = null;
		}

		// Restore to initial clean file path without page specifier, otherwise load() would fail because file not found.
		$this->file = $this->pdf_file;

		// Store page number to be used on load().
		$this->pdf_page_number = $page_number;
	}
	/**
	 * Sets up Imagick for PDF processing.
	 *
	 * Overrides WP_Image_Editor_Imagick default PDF setup, since WP 4.7, to allow for loading of individual pages in PDF files.
	 *
	 * @since	1.1.0
	 * @since	1.3.1	Changed access to public, to allow invoking PDF setup on WP < 4.7.
	 *
	 * @return	string|WP_Error		File to load or WP_Error on failure.
	 */
	public function pdf_setup() {

		try {
			// By default, PDFs are rendered in 72 DPI.
			// This will generate an output file that has the same width and height in pixels as the PDF input.
			$this->image->setResolution( 72, 72 );

			// Store the clean file path without page specifier, for later use.
			if ( ! isset( $this->pdf_file ) ) {
				$this->pdf_file = $this->file;
			}

			// If no page number is set, assume the entire PDF should be loaded.
			if ( ! isset( $this->pdf_page_number ) ) {
				$this->pdf_page_number = 0;
				return $this->pdf_file;
			}

			// Add page specifier to file path, to load this specific page.
			return $this->pdf_file . '[' . $this->pdf_page_number . ']';
		}
		catch ( Exception $e ) {
			return new WP_Error( 'pdf_setup_failed', $e->getMessage(), $this->file );
		}
	}
}
