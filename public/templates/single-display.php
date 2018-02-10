<?php
/**
 * Display template.
 *
 * @since	1.0.0
 */

global $post;

$display = new Foyer_Display( get_the_id() );
$channel = new Foyer_Channel( $display->get_active_channel() );

?><html>
	<head><?php
		wp_head();
	?>
	</head>
	<body <?php body_class(); ?>><?php
		?><div<?php $display->classes(); ?>><?php

			$post = get_post( $channel->ID );
			setup_postdata( $post );

			Foyer_Templates::get_template('partials/channel.php');

			wp_reset_postdata();
		?></div><?php

		wp_footer();

	?></body>
</html>
