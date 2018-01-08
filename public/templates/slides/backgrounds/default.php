<?php
/**
 * Default slide background template.
 *
 * @since	1.4.0
 */

$slide = new Foyer_Slide( get_the_id() );

?><div<?php $slide->background_classes(); ?><?php $slide->background_data_attr();?>></div><?php
