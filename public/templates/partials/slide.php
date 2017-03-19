<?php
/**
 * Partial that outputs a slide.
 *
 * Used in the single-slide template and the channel partial.
 *
 * @since	1.0.0
 */

$slide = new Foyer_Slide( get_the_id() );
Foyer_Templates::get_template('slides/'.$slide->get_format().'.php');
