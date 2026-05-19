<?php
/**
 * Block template for CB Portfolio Index.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$context = cb_get_site_context();

if ( 'pf' === $context ) {
	$cpt_type         = 'pf_portfolio';
} elseif ( 'inv' === $context ) {
	$cpt_type         = 'investor_portfolio';
} else {
	// Block is only meaningful inside a silo; render nothing elsewhere.
	return;
}

$query = new WP_Query(
	array(
		'post_type'      => $cpt_type,
		'posts_per_page' => -1,
		'no_found_rows'  => true,
	)
);

if ( ! $query->have_posts() ) {
	return;
}

?>
<div class="cb-portfolio-index">
	<div class="container">
		<div class="row">
			<?php
			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id          = get_the_ID();
				$gallery_images   = get_field( 'images', get_the_ID() );
				$featured_image_id = (int) get_post_thumbnail_id( $post_id );
				$card_image_id     = $featured_image_id > 0
					? $featured_image_id
					: ( is_array( $gallery_images ) && ! empty( $gallery_images ) ? (int) $gallery_images[0] : 0 );
				$card_highlights  = (string) get_field( 'card_highlights', $post_id );
				$has_highlights   = '' !== trim( wp_strip_all_tags( $card_highlights ) );

				$subtitle             = (string) get_field( 'subtitle', $post_id );
				$project_description  = (string) get_field( 'project_description', $post_id );
				$map_image_id         = (int) get_field( 'map', $post_id );
				$sidebar_highlights   = get_field( 'sidebar_highlights', $post_id );
				$has_sidebar_details  = is_array( $sidebar_highlights ) && ! empty( $sidebar_highlights );
				$has_portfolio_fields =
					'' !== trim( wp_strip_all_tags( $subtitle ) ) ||
					'' !== trim( wp_strip_all_tags( $project_description ) ) ||
					$map_image_id > 0 ||
					$has_sidebar_details ||
					! empty( $gallery_images );

				$should_link = $has_highlights && $has_portfolio_fields;
				?>
			<div class="col-md-6 col-lg-4 mb-4">
				<?php if ( $should_link ) : ?>
					<a class="portfolio__card" href="<?= esc_url( get_permalink( $post_id ) ); ?>">
				<?php else : ?>
					<div class="portfolio__card">
				<?php endif; ?>
					<div class="portfolio__card-image-wrapper">
						<?= wp_get_attachment_image( $card_image_id, 'medium_large', false, array( 'class' => 'portfolio__card-image' ) ); ?>
					</div>
					<div class="portfolio__card-inner">
						<h2 class="portfolio__card-title"><?= esc_html( get_the_title() ); ?></h2>
						<ul class="portfolio__highlights">
							<?= wp_kses_post( cb_list( $card_highlights ) ); ?>
						</ul>
					</div>
				<?php if ( $should_link ) : ?>
					</a>
				<?php else : ?>
					</div>
				<?php endif; ?>
			</div>
				<?php
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</div>
