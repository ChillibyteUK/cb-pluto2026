<?php
/**
 * Block template for CB Latest Posts.
 *
 * Renders the most recent posts from the appropriate insights CPT, with the
 * visual treatment chosen automatically from the current URL:
 *
 *   /property-finance/...  → Newsroom preset, 3 × pf_insight (1-1-1 → 1-1
 *                            row with the third card as a 16:9 feature).
 *   /investors/...         → Insights preset, 4 × investor_insight, all
 *                            with circular thumbnails.
 *   anywhere else          → block renders nothing (the section is only
 *                            meaningful inside one of the two silos).
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$block_id = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-latest-posts-' );

// URL-based preset detection (mirrors cb-text-image.php / cb-ticker-x3.php).
$context = cb_get_site_context();

if ( 'pf' === $context ) {
	$preset           = 'newsroom';
	$insight_type     = 'pf_insight';
	$count            = 3;
	$heading          = 'PLUTO NEWSROOM';
	$cta_label        = 'View all news';
	$cta_url          = '/property-finance/insights/';
	$flourish_variant = 'full-flourish--lending';
} elseif ( 'inv' === $context ) {
	$preset           = 'insights';
	$insight_type     = 'investor_insight';
	$count            = 4;
	$heading          = 'PLUTO INSIGHTS';
	$cta_label        = 'View all insights';
	$cta_url          = '/investors/insights/';
	$flourish_variant = 'full-flourish--investors';
} else {
	// Block is only meaningful inside a silo; render nothing elsewhere.
	return;
}

$query = new WP_Query(
	array(
		'post_type'      => $insight_type,
		'posts_per_page' => $count,
		'no_found_rows'  => true,
	)
);

if ( ! $query->have_posts() ) {
	return;
}

$section_classes = array( 'full-flourish', 'full-flourish--flip', $flourish_variant, 'cb-latest-posts', 'cb-latest-posts--' . $preset );
?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="container py-5">
		<div class="cb-latest-posts__header d-flex flex-wrap align-items-center justify-content-between mb-5">
			<h2 class="text-center"><?= esc_html( $heading ); ?></h2>
			<a href="<?= esc_url( $cta_url ); ?>" class="cb-latest-posts__cta"><?= esc_html( $cta_label ); ?></a>
		</div>

		<?php if ( 'newsroom' === $preset ) : ?>
			<div class="row">
				<?php
				$cols = 'col-md-3';
				while ( $query->have_posts() ) {
					$query->the_post();
					$is_feature = ( 2 === $query->current_post );
					if ( $is_feature ) {
						$cols = 'col-md-6';
					}
					?>
					<div class="<?= esc_attr( $cols ); ?>">
						<a href="<?php the_permalink(); ?>" class="cb-news-card">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="cb-news-card__image cb-news-card__image--<?= $is_feature ? '16-9' : '4-3'; ?>">
									<?php the_post_thumbnail( 'large' ); ?>
								</div>
							<?php endif; ?>
							<h3 class="cb-news-card__title"><?php the_title(); ?></h3>
							<div class="cb-news-card__excerpt"><?php the_excerpt(); ?></div>
							<div class="cb-news-card__link">Learn more</div>
						</a>
					</div>
					<?php
				}
				?>
			</div>
		<?php else : ?>
			<div class="row g-5">
				<?php
				while ( $query->have_posts() ) {
					$query->the_post();
					?>
					<div class="col-md-3">
						<a href="<?php the_permalink(); ?>" class="cb-news-card">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="cb-news-card__image cb-news-card__image--circle">
									<?php the_post_thumbnail( 'medium_large' ); ?>
								</div>
							<?php endif; ?>
							<div class="cb-news-card__date"><?= esc_html( get_the_date( 'jS F, Y' ) ); ?></div>
							<h3 class="cb-news-card__title"><?php the_title(); ?></h3>
							<div class="cb-news-card__link">Learn more</div>
						</a>
					</div>
					<?php
				}
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
wp_reset_postdata();
