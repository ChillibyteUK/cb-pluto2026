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
				$gallery_images   = get_field( 'images', get_the_ID() );
				$card_image_id    = is_array( $gallery_images ) && ! empty( $gallery_images ) ? $gallery_images[0] : 0;
				?>
			<div class="col-md-6 col-lg-4 mb-4">
				<a class="portfolio__card" href="<?= esc_url( get_permalink() ); ?>">
					<div class="portfolio__card-image-wrapper">
						<?= wp_get_attachment_image( $card_image_id, 'medium_large', false, array( 'class' => 'portfolio__card-image' ) ); ?>
					</div>
					<div class="portfolio__card-inner">
						<h2 class="portfolio__card-title"><?= esc_html( get_the_title() ); ?></h2>
						<ul class="portfolio__highlights">
							<?= wp_kses_post( cb_list( get_field( 'card_highlights', get_the_ID() ) ) ); ?>
						</ul>
					</div>
				</a>
			</div>
				<?php
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</div>
