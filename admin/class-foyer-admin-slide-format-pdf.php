<?php

/**
 * Adds admin functionality for the PDF slide format.
 *
 * @since		1.1.0
 * @package		Foyer
 * @subpackage	Foyer/admin
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
	 * Adds PDF images to an attachment, for each page in a PDF.
	 *
	 * @since	1.1.0
	 *
	 * @param	int	$attachment_id	The ID of the attachment to add PDF images to.
	 * @return	WP_Error|void			Returns a WP error if generating of images failed, void otherwise.
	 */
	static function add_pdf_images_to_attachment( $attachment_id ) {

		$current_pdf_images = get_post_meta( $attachment_id, '_foyer_pdf_images', true );

		if ( ! empty( $current_pdf_images ) ) {
			// Images already added, no need to generate them
			return;
		}

		$pdf_file_path = get_attached_file( $attachment_id );
		$pdf_images = self::generate_images_for_pdf_pages( $pdf_file_path );

		if ( is_wp_error( $pdf_images ) ) {
			return $pdf_images;
		}

		// Convert full paths to paths relative to uploads base, eg. 2017/03/upload_file.pdf
		$pdf_images = array_map(
			array( __CLASS__, 'get_file_path_relative_to_uploads_base' ),
			$pdf_images
		);

		update_post_meta( $attachment_id, '_foyer_pdf_images', $pdf_images );
	}

	/**
	 * Deletes generated PDF images for an attachment.
	 *
	 * @since	1.1.0
	 *
	 * @param	int	$attachment_id	The ID of the attachment to delete PDF images for.
	 * @return	void
	 */
	static function delete_pdf_images_for_attachment( $attachment_id ) {

		$slide_images = get_post_meta( $attachment_id, '_foyer_pdf_images', true );

		if ( empty( $slide_images ) ) {
			// No images were generated, bail
			return;
		}

		$uploads = wp_upload_dir( null, false );

		foreach ( $slide_images as $slide_image ) {

			$slide_image_path = trailingslashit( $uploads['basedir'] ) . $slide_image;
			wp_delete_file( $slide_image_path );
		}
	}

	/**
	 * Displays an admin notice on the Slide edit screen if something went wrong during saving.
	 *
	 * Error is stored in a transient.
	 *
	 * @since	1.3.2
	 *
	 * @return void
	 */
	static function display_admin_notice() {

		// Bail if not on Slide edit screen.
		$screen = get_current_screen();
		if ( empty( $screen ) || Foyer_Slide::post_type_name != $screen->post_type ) {
			return;
		}

		if ( $error = get_transient( 'foyer_save_slide_pdf_notice_' . get_the_ID() . '_' . get_current_user_id() ) ) { ?>
			<div class="notice notice-error">
				<p><?php _e( 'Processing PDF pages failed.', 'foyer' ); ?></p>
				<p>
					<?php _e( 'Make sure you are on WordPress 4.7 or higher, and your webhosting has Imagick and Ghostscript installed.', 'foyer' ); ?><br />
					<?php _e( 'Error message:', 'foyer' ); ?> <?php echo $error->get_error_message(); ?>
				</p>
			</div><?php

			delete_transient( 'foyer_save_slide_pdf_notice_' . get_the_ID() . '_' . get_current_user_id() );
		}
	}

	/**
	 * Generates an image for each page in a PDF file.
	 *
	 * Uses the Foyer_Image_Editor_Imagick image editor to convert PDF pages to PNG images.
	 *
	 * @since	1.1.0
	 * @since	1.3.1	Now invokes Foyer_Image_Editor_Imagick::pdf_setup() for WP < 4.7,
	 *					to make PDF processing work on WP < 4.7.
	 * @since	1.3.2	Returns a WP_Error for WP < 4.7, instead of invoking Foyer_Image_Editor_Imagick::pdf_setup()
	 *					added in 1.3.1 as this proved to be unreliable.
	 *
	 * @param	string				The file path of the PDF file to generate images for.
	 * @return	array|WP_Error		The file paths of all generated images, or WP_Error if an error occured.
	 */
	static function generate_images_for_pdf_pages( $pdf_file ) {
		if ( empty( $pdf_file ) ) {
			// Not an error, just don't generate anything
			return false;
		}

		$file_extension = strtolower( pathinfo( $pdf_file, PATHINFO_EXTENSION ) );
		if ( 'pdf' != $file_extension ) {
			return new WP_Error( 'invalid_image', __( 'Not a PDF file.', 'foyer' ), $pdf_file );
		}

		$png_files = array();

		// Load our own Foyer_Image_Editor_Imagick by requesting methods that only exists in our image editor
		$editor = wp_get_image_editor( $pdf_file, array( 'methods' => array( 'pdf_get_number_of_pages', 'pdf_prepare_page_for_load' ) ) );
		if ( is_wp_error( $editor ) ) {
			return $editor;
		}

		// Check if WordPress install has WP_Image_Editor_Imagick PDF setup support (WP 4.7 and up)
		if ( ! method_exists( 'WP_Image_Editor_Imagick', 'pdf_setup' ) ) {
			return new WP_Error(
				'wp_image_editor_imagick_pdf_setup',
				__( 'Your WordPress version lacks support for PDF processing.', 'foyer' ),
				$editor
			);
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

		if ( empty( $slide_pdf_file ) ) {
			delete_post_meta( $post_id, 'slide_pdf_file' );
		}
		else {

			$added = self::add_pdf_images_to_attachment( $slide_pdf_file );
			if ( is_wp_error( $added ) ) {

				// Store our error in a transient so it can be displayed to the user on the Slide edit screen
				set_transient( 'foyer_save_slide_pdf_notice_' . $post_id . '_' . get_current_user_id(), $added, 45 );

				return $added;
			}

			update_post_meta( $post_id, 'slide_pdf_file', $slide_pdf_file );
		}
	}

	/**
	 * Outputs the meta box for the Production slide format.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped & sanitized the output.
	 * @since	1.1.0	Moved here from Foyer_Theater, and changed to static.
	 * @since	1.3.1	Added notifications when PDF processing is not supported (no Imagick/Ghostscript installed),
	 *					and when PDF file previews don’t work (PHP < 4.7).
	 * @since	1.3.2	Removed the notifications added in 1.3.1 as they proved to be unreliable.
	 * @since	1.6.0	Renamed everything slide_image_* to slide_file_*, and 'Upload PDF' to 'Select PDF'.
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
						<div class="slide_file_field file_type_pdf<?php if ( empty( $slide_pdf_file ) ) { ?> empty<?php } ?>">
							<div class="slide_file_preview_wrapper">
								<img class="slide_file_preview" src="<?php echo esc_url( $slide_pdf_file_preview_url ); ?>" height="100">
							</div>

							<input type="button" class="button slide_file_upload_button" value="<?php _e( 'Select PDF file', 'foyer' ); ?>" />
							<input type="button" class="button slide_file_delete_button" value="<?php _e( 'Remove PDF file', 'foyer' ); ?>" />
							<input type="hidden" name="slide_pdf_file" class="slide_file_value" value='<?php echo intval( $slide_pdf_file ); ?>'>
						</div>
					</td>
				</tr>
			</tbody>
		</table><?php
	}
}
