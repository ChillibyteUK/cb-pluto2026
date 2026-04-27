<?php
/**
 * Template Name: Home Page
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;
get_header();

$hero_id       = wp_unique_id( 'home-page-hero-' );
$hero_classes  = array( 'home-page__hero', 'd-flex', 'align-items-center' );
$hero_style    = '';
$hero_thumb_id = get_post_thumbnail_id( get_the_ID() );
$hero_bg_url   = $hero_thumb_id ? wp_get_attachment_image_url( $hero_thumb_id, 'full' ) : '';

if ( $hero_bg_url ) {
	$hero_classes[] = 'home-page__hero--has-background-image';
	$hero_style     = sprintf( '--home-page-hero-bg: url(%s);', esc_url_raw( $hero_bg_url ) );
}
?>
<main id="main" class="home-page">
    <section id="<?= esc_attr( $hero_id ); ?>" class="<?= esc_attr( implode( ' ', $hero_classes ) ); ?>"<?= $hero_style ? ' style="' . esc_attr( $hero_style ) . '"' : ''; ?>>
        <div class="home-page__hero-overlay" aria-hidden="true">
            <div class="home-page__hero-overlay-rect"></div>
            <div class="home-page__hero-overlay-cap"></div>
        </div>
        <div class="container my-auto">
            <h1><?= esc_html( get_field( 'title' ) ); ?></h1>
            <div class="home-page__intro"><?= esc_html( get_field( 'intro' ) ); ?></div>
            <div class="row g-4">
                <div class="col-md-6">
                    <a class="home-page__card home-page__card--lending" href="/property-finance/">
                        <h2 class="home-page__card-title"><?= cb_sanitise_svg( get_stylesheet_directory() . '/img/dot-green-orange-white.svg', null, 40, 40 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <span>PLUTO <strong>LENDING</strong></span></h2>
                        <div class="home-page__card-body">
                            <?= wp_kses_post( get_field( 'lending_intro' ) ); ?>
                        </div>
                        <div class="home-page__card-link">
                            Enter site
                            <?= cb_sanitise_svg( get_stylesheet_directory() . '/img/dot-orange-green-white.svg', null, 40, 40 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a class="home-page__card home-page__card--investing" href="/investors/">
                        <h2 class="home-page__card-title"><?= cb_sanitise_svg( get_stylesheet_directory() . '/img/dot-green-orange-white.svg', null, 40, 40 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <span>PLUTO <strong>INVESTING</strong></span></h2>
                        <div class="home-page__card-body">
                            <?= wp_kses_post( get_field( 'investing_intro' ) ); ?>
                        </div>
                        <div class="home-page__card-link">
                            Enter site
                            <?= cb_sanitise_svg( get_stylesheet_directory() . '/img/dot-teal-green-white.svg', null, 40, 40 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <section class="home-page__newsroom py-5">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-5">
                <h2 class="text-center">PLUTO NEWSROOM</h2>
                <a href="/property-finance/insights/" class="home-page__newsroom-link">View all news</a>
            </div>
            <?php
            $news_posts = new WP_Query(
                array(
                    'post_type'      => 'pf_insight',
                    'posts_per_page' => 3,
                )
            );
            if ( $news_posts ) {
                ?>
            <div class="row">
                <?php
                $cols = 'col-md-3';
                while ( $news_posts->have_posts() ) {
                    $news_posts->the_post();
                    if ( 2 === $news_posts->current_post ) {
                        $cols = 'col-md-6';
                    }
                    ?>
                    <?php $is_feature = ( 2 === $news_posts->current_post ); ?>
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
                wp_reset_postdata();
                ?>
            </div>
                <?php
            }
            ?>
        </div>
    </section>
    <section class="home-page__insights full-flourish">
        <div class="container py-5">
            <div class="d-flex flex-wrap align-items-center justify-content-between py-5">
                <h2 class="text-center">PLUTO INSIGHTS</h2>
                <a href="/investors/insights/" class="home-page__insights-link">View all insights</a>
            </div>
            <?php
            $insight_posts = new WP_Query(
                array(
                    'post_type'      => 'investor_insight',
                    'posts_per_page' => 4,
                )
            );
            if ( $insight_posts ) {
                ?>
            <div class="row g-5">
                <?php
                while ( $insight_posts->have_posts() ) {
                    $insight_posts->the_post();
                    ?>
                    <div class="col-md-3">
                        <a href="<?php the_permalink(); ?>" class="cb-news-card">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="cb-news-card__image cb-news-card__image--circle">
                                    <?php the_post_thumbnail( 'medium_large' ); ?>
                                </div>
                            <?php endif; ?>
                            <div class="cb-news-card__date"><?= get_the_date( 'jS F, Y' ); ?></div>
                            <h3 class="cb-news-card__title"><?php the_title(); ?></h3>
                            <div class="cb-news-card__link">Learn more</div>
                        </a>
                    </div>
                    <?php
                }
                wp_reset_postdata();
                ?>
            </div>
                <?php
            }
            ?>

        </div>
    </section>
</main>
<?php if ( $hero_bg_url ) : ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var section = document.getElementById(<?= wp_json_encode( $hero_id ); ?>);
	if (!section) return;

	var ticking = false;

	function update() {
		var rect = section.getBoundingClientRect();
		var windowHeight = window.innerHeight;

		if (rect.bottom > 0 && rect.top < windowHeight) {
			var percent = (windowHeight - rect.top) / (windowHeight + rect.height);
			percent = Math.max(0, Math.min(1, percent));
			var translateY = (percent - 0.5) * 240;
			section.style.setProperty('--home-page-hero-parallax-y', translateY.toFixed(1) + 'px');
		}

		ticking = false;
	}

	function onScroll() {
		if (!ticking) {
			window.requestAnimationFrame(update);
			ticking = true;
		}
	}

	window.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', onScroll);
	onScroll();
});
</script>
<?php endif; ?>
<?php
get_footer();
?>