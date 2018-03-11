<?php

/**
 * The image object model.
 *
 * @since		1.5.1
 *
 * @package		Foyer
 * @subpackage	Foyer/includes
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Image {

	public $ID;
	private $post;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	1.5.1
	 * @param	int or WP_Post	$ID		The id or the WP_Post object of the attachment.
	 */
	public function __construct( $ID = false ) {

		if ( $ID instanceof WP_Post ) {
			// $ID is a WP_Post object
			$this->post = $ID;
			$ID = $ID->ID;
		}

		$this->ID = $ID;
	}

	/**
	 * Gets the image HTML.
	 *
	 * @since	1.5.1
	 *
	 * @param 	string 	$size	The size of the attachment to get.
	 * @return	void
	 */
	public function get_html( $size = '' ) {

		$attachment_url = wp_get_attachment_url( $this->ID );

		if ( empty( $attachment_url ) ) {
			return;
		}

		if ( empty( $size ) || 'foyer_16_9' == $size ) {
			return $this->get_16_9_html();
		}

		return $this->get_custom_size_html( $size );
	}

	private function get_16_9_html() {

		$fhd_landscape_src = wp_get_attachment_image_src( $this->ID, 'foyer_fhd_landscape' );
		$fhd_portrait_src = wp_get_attachment_image_src( $this->ID, 'foyer_fhd_portrait' );

		$uhd4k_landscape_src = wp_get_attachment_image_src( $this->ID, 'foyer_uhd4k_landscape' );
		$uhd4k_portrait_src = wp_get_attachment_image_src( $this->ID, 'foyer_uhd4k_portrait' );

		$max_fhd = 1920;

		ob_start();
		?>
			<picture>
				<?php if ( ! empty( $uhd4k_portrait_src[0] ) ) { ?>
					<source srcset="<?php echo $uhd4k_portrait_src[0]; ?>" media="(orientation: portrait) and (min-height:<?php echo $max_fhd + 1; ?>px)">
					<?php for ( $i = 2; $i <= 4; $i++ ) { ?>
						<source srcset="<?php echo $uhd4k_portrait_src[0]; ?>" media="(orientation: portrait) and (min-height:<?php echo round( $max_fhd / $i ) + 1; ?>px) and (-webkit-min-device-pixel-ratio: <?php echo $i; ?>)">
						<source srcset="<?php echo $uhd4k_portrait_src[0]; ?>" media="(orientation: portrait) and (min-height:<?php echo round( $max_fhd / $i ) + 1; ?>px) and (webkit-min-device-pixel-ratio: <?php echo $i; ?>)">
					<?php } ?>
				<?php } ?>

				<?php if ( ! empty( $uhd4k_landscape_src[0] ) ) { ?>
					<source srcset="<?php echo $uhd4k_landscape_src[0]; ?>" media="(orientation: landscape) and (min-width:<?php echo $max_fhd + 1; ?>px)">
					<?php for ( $i = 2; $i <= 4; $i++ ) { ?>
						<source srcset="<?php echo $uhd4k_landscape_src[0]; ?>" media="(orientation: landscape) and (min-width:<?php echo round( $max_fhd / $i ) + 1; ?>px) and (-webkit-min-device-pixel-ratio: <?php echo $i; ?>)">
						<source srcset="<?php echo $uhd4k_landscape_src[0]; ?>" media="(orientation: landscape) and (min-width:<?php echo round( $max_fhd / $i ) + 1; ?>px) and (webkit-min-device-pixel-ratio: <?php echo $i; ?>)">
					<?php } ?>
				<?php } ?>

				<?php if ( ! empty( $fhd_portrait_src[0] ) ) { ?>
					<source srcset="<?php echo $fhd_portrait_src[0]; ?>" media="(orientation: portrait)">
				<?php } ?>

				<?php if ( ! empty( $fhd_landscape_src[0] ) ) { ?>
					<source srcset="<?php echo $fhd_landscape_src[0]; ?>" media="(orientation: landscape)">
				<?php } ?>

				<!-- fallback for browsers that do not support the picture element, or uploads that do not have Foyer image sizes -->
				<img src="<?php echo $attachment_url; ?>">
			</picture>
		<?php

		return ob_get_clean();
	}

	/**
	 * Outputs the image HTML for use in slide format template.
	 *
	 * @since	1.5.1
	 *
	 * @param 	string 	$size	The size of the attachment to output.
	 * @return	void
	 */
	public function html( $size = '' ) {
		echo $this->get_html( $size );
	}
}
