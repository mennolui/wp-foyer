<?php

$slide = new Foyer_Slide( get_the_id() );
Foyer_Templates::get_template('slides/'.$slide->format().'.php');
