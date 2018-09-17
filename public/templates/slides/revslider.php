<?php
/**
 * Revslider slide format template.
 *
 * @since	1.6.0
 */

$slide = new Foyer_Slide( get_the_id() );

$slider_id = get_post_meta( $slide->ID, 'slide_revslider_slider_id', true );

?><div<?php $slide->classes(); ?><?php $slide->data_attr();?>>
	<div class="inner">
		<?php if ( ! empty( $slider_id ) ) { ?>
			<div class="revslider-container" data-foyer-hold-slide="1">
				<?php Foyer_Revslider::output_slider( $slider_id ); ?>


<div class="slider manual" id="revolutionSlider2" data-plugin-revolution-slider data-plugin-options='{"startheight": 500}'>

			</div>
		<?php } ?>

<script type="text/javascript">
	// dit doet iets..?
    var revapi = jQuery(document).ready(function() {

        jQuery('#rev_slider_1_1').show().revolution({

            stopLoop: 'on',
            stopAfterLoops: 0,
            stopAtSlide: 3,

            /* SLIDER SETTINGS CONTINUED */

        });
    });

    // listen for when the "stopAtSlide" Slide set above is reached
    revapi.on('revolution.slide.onstop', function() {

        // Slider has reached the "stopAtSlide" (Slide #3)

    });

</script>

		<script>
			jQuery(document).ready(function() {
				var revapi = jQuery('#rev_slider_1_1').show().revolution({
					waitForInit: true,
				});

				revapi.revstart();

				revapi.on("revolution.slide.onstop",function (e,data) {
					console.log("slider stopped");
				});

				revapi.on("revolution.slide.onpause",function (e,data) {
					console.log("timer paused");
				});

				revapi.on("revolution.slide.slideatend",function (e,data) {
					console.log("slideatend");
				});

				revapi.on("revolution.slide.XXonafterswap",function (e,data) {
					console.log("Slider After Swap");

					console.log(revapi.revcurrentslide());
					console.log(revapi.revlastslide());

					if ( revapi.revcurrentslide() === revapi.revlastslide() ) {
						revapi.revpause();
						console.log('PAUSED!');
					}
				});

				//onload
				//revolution.slide.slideatend
				//revolution.nextslide.waiting

				//init met setting (js init? shortcode filter?) stop slider after 0 loops [last] slide
				//onstop.. next slide

				//altijd een laatste slide toevoegen aan het eind (bv 0 seconden), die overgaat in foyer

				//@todo:
				//get working with Wait for revapi1.revstart(), in theme
			});

		</script>

	</div>
	<?php $slide->background(); ?>
</div>