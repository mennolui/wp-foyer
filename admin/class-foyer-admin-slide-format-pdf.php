<?php

/**
 * Adds admin functionality for the PDF slide format.
 *
 * @since		1.1.0
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Slide_Format_PDF {

	/**
	 * Adds our own Foyer_Image_Editor_Imagick image editor to the list of available image editors.
	 *
	 * @since	1.1.0
	 *
	 * @param	array		The current list of image editors.
	 * @return	array		The list of image editors with our own Foyer_Image_Editor_Imagick added.
	 */
	static function add_foyer_imagick_image_editor( $editors ) {

		// Image Editor classes are lazy loaded, so we can't just extend them on init
		// Include our own image editor now and extend WP_Image_Editor_Imagick
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-foyer-image-editor-imagick.php';

		$editors[] = 'Foyer_Image_Editor_Imagick';
		return $editors;
	}

	/**
	 * Saves all pages in a PDF as seperate PNG images.
	 *
	 * Uses the Foyer_Image_Editor_Imagick image editor to convert PDF pages to PNG images.
	 *
	 * @return	array		The file paths of all saved PNG images.
	 */
	static function save_pdf_pages_as_images() {
		$png_files = array();

		$pdf_file = plugin_dir_path( dirname( __FILE__ ) ) . 'includes/test.pdf';

		// Load our own Foyer_Image_Editor_Imagick by requesting methods that only exists in our image editor
		$editor = wp_get_image_editor( $pdf_file, array( 'methods' => array( 'pdf_get_number_of_pages', 'pdf_prepare_page_for_load' ) ) );
		if ( is_wp_error( $editor ) ) {
			return $editor;
		}

		// Get the number of pages in the PDF
		$number_of_pages = $editor->pdf_get_number_of_pages();
		if ( is_wp_error( $number_of_pages ) ) {
			return $number_of_pages;
		}

		// Loop over all pages
		for ( $p = 0; $p < $number_of_pages; $p++ ) {

			$editor->pdf_prepare_page_for_load( $p );
			$loaded = $editor->load();

			if ( is_wp_error( $loaded ) ) {
				return $loaded;
			}

			// Created a unique filename that will not overwrite any PNG images that already exist
			$dirname = dirname( $pdf_file ) . '/';
			$ext = '.' . pathinfo( $pdf_file, PATHINFO_EXTENSION );
			$png_file = $dirname . wp_unique_filename( $dirname, wp_basename( $pdf_file, $ext ) . '-p' . ( $p + 1 ) . '-pdf.png' );

			$saved = $editor->save( $png_file, 'image/png' );

			if ( is_wp_error( $saved ) ) {
				return $saved;
			}

			// Store file path of the saved PNG image for this page
			$png_files[] = $saved['path'];
		}

		unset( $editor );
		var_dump( $png_files ); exit;
	}
}
