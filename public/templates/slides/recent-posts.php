<?php
/**
 * Recent Posts slide format template.
 *
 * @since	1.7.1
 */

$slide = new Foyer_Slide( get_the_id() );

$slide_recent_posts_limit = intval( get_post_meta( $slide->ID, 'slide_recent_posts_limit', true ) );
$slide_recent_posts_categories = get_post_meta( $slide->ID, 'slide_recent_posts_categories', true );

$slide_recent_posts_display_thumbnail = get_post_meta( $slide->ID, 'slide_recent_posts_display_thumbnail', true );
$slide_recent_posts_use_excerpt = get_post_meta( $slide->ID, 'slide_recent_posts_use_excerpt', true );

// Prepare categories and limit for get_posts() query
$post_args = array();

if ( ! empty( $slide_recent_posts_categories ) ) {
	$post_args['category__in'] = array_map( 'intval', $slide_recent_posts_categories );
}

if ( ! empty( $slide_recent_posts_limit ) ) {
	$post_args['posts_per_page'] = $slide_recent_posts_limit;
}
else {
	$post_args['nopaging'] = true;
}

foreach ( get_posts( $post_args ) as $slide_post ) {

	if ( ! empty( $slide_recent_posts_use_excerpt ) ) {
		$content = apply_filters( 'the_content', $slide_post->post_excerpt );
	}
	else {
		$content = apply_filters( 'the_content', $slide_post->post_content );
	}

	?><div<?php $slide->classes(); ?><?php $slide->data_attr(); ?>>
		<div class="inner">
			<?php if ( ! empty( $slide_post->ID ) ) { ?>
				<?php if ( ! empty( $slide_recent_posts_display_thumbnail ) && $attachment_id = get_post_thumbnail_id( $slide_post->ID ) ) { ?>
					<figure>
						<?php echo wp_get_attachment_image( $attachment_id, 'foyer' ); ?>
					</figure>
				<?php } ?>
				<div class="foyer-slide-fields">
					<div class="foyer-slide-field foyer-slide-field-title"><span><?php echo get_the_title( $slide_post->ID ); ?></span></div>
					<div class="foyer-slide-field foyer-slide-field-date"><span><?php echo get_the_date( false, $slide_post->ID ); ?></span></div>
					<?php if ( ! empty( $content ) ) { ?>
						<div class="foyer-slide-field foyer-slide-field-content"><?php echo $content; ?></div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
		<?php $slide->background(); ?>
	</div><?php
}
