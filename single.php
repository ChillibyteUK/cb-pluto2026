<?php
/**
 * Template for displaying single posts.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$context        = cb_get_site_context();
$fallback_image = get_stylesheet_directory_uri() . '/img/pluto-logo.png';

if ( 'inv' === $context ) {
	$home_url       = '/investors/';
	$home_label     = 'Home';
	$insights_url   = '/investors/insights/';
	$insights_label = 'Insights';
	$main_class     = 'cb-post cb-post--investor';
} else {
	$home_url       = '/property-finance/';
	$home_label     = 'Home';
	$insights_url   = '/property-finance/insights/';
	$insights_label = 'Insights';
	$main_class     = 'cb-post cb-post--pf';
}

get_header();
?>
<main id="main" class="<?= esc_attr( $main_class ); ?>">
	<div class="container pt-4 pb-5">
		<div id="breadcrumbs" class="mb-3">
			<a href="<?= esc_url( $home_url ); ?>"><?= esc_html( $home_label ); ?></a> &raquo; <a href="<?= esc_url( $insights_url ); ?>"><?= esc_html( $insights_label ); ?></a> &raquo; <?= esc_html( get_the_title() ); ?>
		</div>
		<div class="row g-5">
			<div class="col-lg-9">
				<div class="cb-post__hero">
					<?php if ( has_post_thumbnail() ) { ?>
					<?= get_the_post_thumbnail( get_the_ID(), 'full', array( 'class' => 'cb-post__hero-img' ) ); ?>
					<?php } else { ?>
					<img class="cb-post__hero-img" src="<?= esc_url( $fallback_image ); ?>" alt="<?= esc_attr( get_bloginfo( 'name' ) ); ?>">
					<?php } ?>
				</div>
				<h1 class="cb-post__title"><?= esc_html( get_the_title() ); ?></h1>
				<div class="cb-post__meta">
					<span><?= esc_html( get_the_date( 'j M Y' ) ); ?></span>
					<span><?= esc_html( estimate_reading_time_in_minutes( get_the_content() ) ); ?> min read</span>
				</div>
				<div class="cb-post__content">
					<?= wp_kses_post( get_the_content() ); ?>
				</div>
				<?php
				$prev = get_previous_post();
				$next = get_next_post();

				if ( $prev || $next ) {
					$justify = $prev && $next ? 'justify-content-between' : ( $next ? 'justify-content-end' : 'justify-content-start' );
					?>
				<nav class="cb-post__nav d-flex <?= esc_attr( $justify ); ?>" aria-label="Post navigation">
					<?php if ( $prev ) { ?>
					<a href="<?= esc_url( get_permalink( $prev ) ); ?>" class="button button--primary">&larr; Previous</a>
					<?php } ?>
					<?php if ( $next ) { ?>
					<a href="<?= esc_url( get_permalink( $next ) ); ?>" class="button button--primary">Next &rarr;</a>
					<?php } ?>
				</nav>
					<?php
				}
				?>
			</div>
			<div class="col-lg-3">
				<?php
				$q = new WP_Query(
					array(
						'post_type'      => 'post',
						'posts_per_page' => 5,
						'post__not_in'   => array( get_the_ID() ),
						'orderby'        => 'date',
						'order'          => 'DESC',
					)
				);
				if ( $q->have_posts() ) {
					?>
				<aside class="cb-post-sidebar">
					<h2 class="cb-post-sidebar__title has-underline">Latest <?= esc_html( $insights_label ); ?></h2>
					<?php
					while ( $q->have_posts() ) {
						$q->the_post();
						?>
					<a class="cb-post-sidebar__item" href="<?= esc_url( get_permalink() ); ?>">
						<div class="cb-post-sidebar__image-wrap">
							<?php if ( has_post_thumbnail() ) { ?>
							<?= get_the_post_thumbnail( get_the_ID(), 'medium', array( 'class' => 'cb-post-sidebar__image' ) ); ?>
							<?php } else { ?>
							<img class="cb-post-sidebar__image" src="<?= esc_url( $fallback_image ); ?>" alt="<?= esc_attr( get_bloginfo( 'name' ) ); ?>">
							<?php } ?>
						</div>
						<div class="cb-post-sidebar__body">
							<div class="cb-post-sidebar__meta">
								<span><?= esc_html( get_the_date( 'j M Y' ) ); ?></span>
							</div>
							<div class="cb-post-sidebar__post-title"><?= esc_html( get_the_title() ); ?></div>
						</div>
					</a>
					<?php } ?>
				</aside>
					<?php
					wp_reset_postdata();
				}
				?>
			</div>
		</div>
	</div>
</main>
<?php
get_footer();
