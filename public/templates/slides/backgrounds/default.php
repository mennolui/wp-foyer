<?php
/**
 * Default slide background template.
 *
 * @since	1.4.0
 * @since	1.5.7	Added support for passing on template args.
 */

$slide = new Foyer_Slide( get_the_id() );

$slide->default_background( $template_args );
