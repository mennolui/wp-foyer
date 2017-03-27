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
	 * Gets the file path relative to the uploads base.
	 *
	 * Eg. 2017/03/upload_file.pdf
	 *
	 * @since	1.1.0
	 *
	 * @param	string	$file_path	The full file path to get the relative path for.
	 * @return	string				The file path relative to the uploads base.
	 */
	static function get_file_path_relative_to_uploads_base( $file_path ) {
		$uploads = wp_upload_dir( null, false );
		$relative_file_path = str_replace( trailingslashit( $uploads['basedir'] ), '', $file_path );

		return $relative_file_path;
	}

	/**
	 * Saves all pages in a PDF as seperate PNG images.
	 *
	 * Uses the Foyer_Image_Editor_Imagick image editor to convert PDF pages to PNG images.
	 *
	 * @since	1.1.0
	 *
	 * @param	string		The file path of the PDF file to convert.
	 * @return	array		The file paths of all saved PNG images.
	 */
	static function save_pdf_pages_as_images( $pdf_file ) {
		if ( empty( $pdf_file ) ) {
			return false;
		}

		$png_files = array();

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

		return $png_files;
	}

	/**
	 * Saves additional data for the PDF slide format.
	 *
	 * Converts newly selected PDF file to images.
	 *
	 * @since	1.1.0
	 *
	 * @param	int		$post_id	The ID of the post being saved.
	 * @return	void
	 */
	static function save_slide_pdf( $post_id ) {
		$slide_pdf_file = intval( $_POST['slide_pdf_file'] );
		if ( empty( $slide_pdf_file ) ) {
			$slide_pdf_file = '';
		}

		$old_slide_pdf_file = get_post_meta( $post_id, 'slide_pdf_file', true );
		if ( $old_slide_pdf_file == $slide_pdf_file ) {
			// PDF file didn't change, no need for saving or converting
			return;
		}

		// New PDF was selected, convert to images
		$pdf_file_path = get_attached_file( $slide_pdf_file );
		$slide_pdf_images = self::save_pdf_pages_as_images( $pdf_file_path );

		if ( is_wp_error( $slide_pdf_images ) ) {
			return $slide_pdf_images;
		}

		// Convert full paths to paths relative to uploads base, eg. 2017/03/upload_file.pdf
		$slide_pdf_images = array_map(
			array( __CLASS__, 'get_file_path_relative_to_uploads_base' ),
			$slide_pdf_images
		);

		update_post_meta( $post_id, 'slide_pdf_file', $slide_pdf_file );
		update_post_meta( $post_id, 'slide_pdf_images', $slide_pdf_images );
	}

	/**
	 * Outputs the meta box for the Production slide format.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped & sanitized the output.
	 * @since	1.1.0	Moved here from Foyer_Theater, and changed to static.
	 *
	 * @param	WP_Post	$post	The post of the current slide.
	 * @return	void
	 */
	static function slide_pdf_meta_box( $post ) {

		$slide_pdf_file_preview_url = '';

		$slide_pdf_file = get_post_meta( $post->ID, 'slide_pdf_file', true );
		$slide_pdf_file_src = wp_get_attachment_image_src( $slide_pdf_file, 'full' );
		if ( ! empty( $slide_pdf_file_src ) ) {
			$slide_pdf_file_preview_url = $slide_pdf_file_src[0];
		}

		?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="slide_pdf_file"><?php _e( 'PDF file', 'foyer' ); ?></label>
					</th>
					<td>
						<div class="slide_image_field<?php if ( empty( $slide_pdf_file ) ) { ?> empty<?php } ?>">
							<div class="image-preview-wrapper">
								<img class="slide_image_preview" src="<?php echo esc_url( $slide_pdf_file_preview_url ); ?>" height="100">
							</div>

							<input type="button" class="button slide_image_upload_button" value="<?php _e( 'Upload PDF file', 'foyer' ); ?>" />
							<input type="button" class="button slide_image_delete_button" value="<?php _e( 'Remove PDF file', 'foyer' ); ?>" />
							<input type="hidden" name="slide_pdf_file" class="slide_image_value" value='<?php echo intval( $slide_pdf_file ); ?>'>
						</div>
					</td>
				</tr>
			</tbody>
		</table><?php
	}
}
