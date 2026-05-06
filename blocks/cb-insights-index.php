<?php
/**
 * Block template for CB Insights Index.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$block_id = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-insights-index-' );
$context  = cb_get_site_context();

if ( 'pf' === $context ) {
	$post_type = 'pf_insight';
} elseif ( 'inv' === $context ) {
	$post_type = 'investor_insight';
} else {
	return;
}

$query = new WP_Query(
	array(
		'post_type'           => $post_type,
		'posts_per_page'      => -1,
		'ignore_sticky_posts' => true,
	)
);

if ( ! $query->have_posts() ) {
	return;
}

$posts = $query->posts;
$lead  = array_shift( $posts );

$lead_has_image = has_post_thumbnail( $lead );
$lead_classes   = 'cb-insights-index__featured-card cb-news-card';
if ( ! $lead_has_image ) {
	$lead_classes .= ' cb-insights-index__featured-card--no-image';
}
?>
<section id="<?= esc_attr( $block_id ); ?>" class="cb-insights-index">
	<div class="container pb-5">
		<div class="row g-4">
			<div class="col-12">
				<a href="<?= esc_url( get_permalink( $lead ) ); ?>" class="<?= esc_attr( $lead_classes ); ?>">
					<?php if ( $lead_has_image ) : ?>
						<div class="cb-insights-index__featured-image">
							<?= get_the_post_thumbnail( $lead, 'large' ); ?>
						</div>
					<?php endif; ?>
					<div class="cb-insights-index__featured-content">
						<h2 class="cb-insights-index__title"><?= esc_html( get_the_title( $lead ) ); ?></h2>
						<div class="cb-insights-index__excerpt"><?= wp_kses_post( get_the_excerpt( $lead ) ); ?></div>
						<div class="cb-news-card__link">Learn more</div>
					</div>
				</a>
			</div>

			<?php foreach ( $posts as $post_item ) : ?>
				<div class="col-md-4">
					<a href="<?= esc_url( get_permalink( $post_item ) ); ?>" class="cb-insights-index__card cb-news-card">
						<?php if ( has_post_thumbnail( $post_item ) ) : ?>
							<div class="cb-news-card__image cb-news-card__image--16-9">
								<?= get_the_post_thumbnail( $post_item, 'medium_large' ); ?>
							</div>
						<?php endif; ?>
						<h3 class="cb-news-card__title"><?= esc_html( get_the_title( $post_item ) ); ?></h3>
						<div class="cb-news-card__excerpt"><?= wp_kses_post( get_the_excerpt( $post_item ) ); ?></div>
						<div class="cb-news-card__link">Learn more</div>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();
