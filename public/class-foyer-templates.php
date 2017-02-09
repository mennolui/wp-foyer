<?php
class Foyer_Templates {

	function locate_template( $template_name, $template_path = '', $default_path = '' ) {
		// Set variable to search in woocommerce-plugin-templates folder of theme.
		if ( ! $template_path ) :
			$template_path = 'foyer/';
		endif;
		// Set default plugin templates path.
		if ( ! $default_path ) :
			$default_path = plugin_dir_path( __FILE__ ) . 'templates/'; // Path to the template folder
		endif;
		// Search template file in theme folder.
		$template = locate_template( array(
			$template_path . $template_name,
			$template_name
		) );
		// Get plugins template file.
		if ( ! $template ) :
			$template = $default_path . $template_name;
		endif;
		return apply_filters( 'foyer/templates/template', $template, $template_name, $template_path, $default_path );
	}

	function template_include( $template ) {
		
		$file = '';

		if ( is_singular( Foyer_Slide::post_type_name ) ) {
			$slide_format = Foyer_Slides::get_slide_format_for_slide( get_the_id() );
			$file = 'slides/'.$slide_format.'.php';
		} else {
			return $template;
		}
		
		if ( file_exists( $this->locate_template( $file ) ) ) {
			$template = $this->locate_template( $file );
		}

		return $template;		
	}
}