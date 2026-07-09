<?php
/**
 * Block template for CB Topic Home Hero.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$hero_id       = wp_unique_id( 'topic-home-hero-' );
$hero_classes  = array( 'topic-home-hero', 'd-flex', 'align-items-center' );
$hero_style    = '';
$hero_thumb_id = get_field( 'background_image' );
$hero_bg_url   = $hero_thumb_id ? wp_get_attachment_image_url( $hero_thumb_id, 'full' ) : '';

$mp4_field  = get_field( 'background_video_mp4' );
$webm_field = get_field( 'background_video_webm' );

$primary_cta   = get_field( 'primary_cta' );
$secondary_cta = get_field( 'secondary_cta' );

$mp4_url  = is_array( $mp4_field ) ? $mp4_field['url'] : '';
$webm_url = is_array( $webm_field ) ? $webm_field['url'] : '';

$has_video      = '' !== $webm_url || '' !== $mp4_url;
$has_video_webm = '' !== $webm_url;
$has_video_mp4  = ! $has_video_webm && '' !== $mp4_url;

if ( $has_video ) {
	$hero_classes[] = 'topic-home-hero--has-video';
}

if ( ! $has_video && $hero_bg_url ) {
	$hero_classes[] = 'topic-home-hero--has-background-image';
	$hero_style     = sprintf( '--topic-home-hero-bg: url(%s);', esc_url_raw( $hero_bg_url ) );
}

$context   = cb_get_site_context();
$the_class = '';
if ( 'pf' === $context ) {
	$hero_classes[] = 'topic-home-hero--pf';
} elseif ( 'inv' === $context ) {
	$hero_classes[] = 'topic-home-hero--inv';
}


?>
<section id="<?= esc_attr( $hero_id ); ?>" class="<?= esc_attr( implode( ' ', $hero_classes ) ); ?>"<?= $hero_style ? ' style="' . esc_attr( $hero_style ) . '"' : ''; ?>>
	<?php if ( $has_video ) { ?>
	<video class="topic-home-hero__video" autoplay muted loop playsinline>
		<?php if ( $has_video_webm ) { ?>
		<source src="<?= esc_url( $webm_url ); ?>" type="video/webm">
		<?php } ?>
		<?php if ( $has_video_mp4 ) { ?>
		<source src="<?= esc_url( $mp4_url ); ?>" type="video/mp4">
		<?php } ?>
	</video>
	<?php } ?>
	<div class="topic-home-hero__overlay" aria-hidden="true">
		<div class="topic-home-hero__overlay-rect"></div>
		<div class="topic-home-hero__overlay-cap"></div>
	</div>
	<div class="container my-auto">
		<div class="row">
			<div class="col-lg-6">
				<h1 class="text-balance"><?= wp_kses_post( get_field( 'title' ) ); ?></h1>
				<?php
				// Render the USP lines as a list only when there is more than one
				// line. A single line stays as plain intro text.
				$usps_raw   = strip_tags( (string) get_field( 'usps' ), '<br />' );
				$usps_lines = preg_split( "/\r\n|\n|\r/", $usps_raw );
				$usps_lines = array_values( array_filter( array_map( 'trim', is_array( $usps_lines ) ? $usps_lines : array() ) ) );
				$usps_count = count( $usps_lines );

				$usp_index = 0;
				if ( $usps_count > 1 ) :
					?>
					<ul class="topic-home-hero__intro">
						<?php
						// Each <li> gets a staggered AOS fade. Delays are multiples of
						// 100 so AOS's stylesheet honours them.
						foreach ( $usps_lines as $usp_line ) {
							printf(
								'<li data-aos="fade" data-aos-delay="%d">%s</li>',
								(int) ( $usp_index * 100 ),
								esc_html( $usp_line )
							);
							++$usp_index;
						}
						?>
					</ul>
					<?php
				elseif ( 1 === $usps_count ) :
					?>
					<div class="topic-home-hero__intro" data-aos="fade"><?= esc_html( $usps_lines[0] ); ?></div>
					<?php
				endif;
				?>
				<?php
				$cta_delay = $usp_index > 0 ? ( ( $usp_index - 1 ) * 100 ) + 600 : 0;
				if ( $primary_cta || $secondary_cta ) {
					?>
					<div class="topic-home-hero__ctas d-flex flex-wrap align-items-center gap-4 mt-4" data-aos="fade" data-aos-delay="<?= esc_attr( $cta_delay ); ?>">
						<?php
						if ( $primary_cta ) {
							$primary_target = $primary_cta['target'] ? $primary_cta['target'] : '_self';
							?>
							<a class="btn btn-primary" href="<?= esc_url( $primary_cta['url'] ); ?>" target="<?= esc_attr( $primary_target ); ?>"<?= '_blank' === $primary_target ? ' rel="noopener"' : ''; ?>><?= esc_html( $primary_cta['title'] ); ?></a>
							<?php
						}

						if ( $secondary_cta ) {
							$secondary_target = $secondary_cta['target'] ? $secondary_cta['target'] : '_self';
							?>
							<a class="cb-link-dot has-white-color" href="<?= esc_url( $secondary_cta['url'] ); ?>" target="<?= esc_attr( $secondary_target ); ?>"<?= '_blank' === $secondary_target ? ' rel="noopener"' : ''; ?>><?= esc_html( $secondary_cta['title'] ); ?></a>
							<?php
						}
						?>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</section>

<?php
if ( $has_video || $hero_bg_url ) {
	?>
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
			section.style.setProperty('--topic-home-hero-parallax-y', translateY.toFixed(1) + 'px');
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
	<?php
}
