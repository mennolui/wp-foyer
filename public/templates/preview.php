<?php
/**
 * Preview template for Displays, Channels and Slides.
 *
 * @since	1.0.0
 */

?><html>
	<head><?php
		wp_head();
	?>
	</head>
	<body <?php body_class(); ?>><?php
		
		$preview_url = get_permalink( get_the_id() );
		$preview_url = add_query_arg( 'preview', 1, $preview_url);
	
		?><iframe src="<?php echo $preview_url; ?>" class="foyer-preview foyer-preview-9-16"></iframe>
		<div class="foyer-preview-actions">
			<button value="orientation-9-16" class="active"><?php _e('portrait', 'foyer'); ?></button>
			<button value="orientation-16-9"><?php _e('landscape', 'foyer'); ?></button>
		</div><?php		
		wp_footer();		
	?></body>
</html>
