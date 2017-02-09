<?php
class Foyer_Templates {

	static function get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		if ( is_array( $args ) && isset( $args ) ) :
			extract( $args );
		endif;
		$template_file = self::locate_template( $template_name, $template_path, $default_path );
		if ( ! file_exists( $template_file ) ) :
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
			return;
		endif;
		include $template_file;
	}

	static function locate_template( $template_name, $template_path = '', $default_path = '' ) {
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

	static function template_include( $template ) {
		
		$file = '';

		if ( is_singular( Foyer_Slide::post_type_name ) ) {
			$slide = new Foyer_Slide( get_the_id() );
			$file = 'slides/'.$slide->format().'.php';
		} else if ( is_singular( Foyer_Display::post_type_name ) ) {
			$file = 'display.php';
		} else {
			return $template;
		}
		
		if ( file_exists( self::locate_template( $file ) ) ) {
			$template = self::locate_template( $file );
		}

		return $template;		
	}
}