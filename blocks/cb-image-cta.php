<?php
/**
 * Block template for CB Image CTA.
 *
 * Full-bleed call-to-action: a parallax background image (same scroll-driven
 * --parallax-y custom property pattern as cb-topic-home-hero), a dim overlay
 * for legibility, white title + content (fs-500) and a single link styled
 * with the rolling-dot connector used by .cb-news-card__link.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$site_title = (string) get_field( 'title' );
$content    = (string) get_field( 'content' );
$cta_link   = get_field( 'link' );
$image_id   = get_field( 'background_image' );

// Bail when nothing of substance has been authored.
if ( '' === trim( $site_title ) && '' === trim( wp_strip_all_tags( $content ) ) && empty( $cta_link ) && ! $image_id ) {
	return;
}

$block_id  = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-image-cta-' );
$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';

$section_classes = array( 'cb-image-cta' );
$section_style   = '';
if ( $image_url ) {
	$section_classes[] = 'cb-image-cta--has-background-image';
	$section_style     = sprintf( '--cb-image-cta-bg: url(%s);', esc_url_raw( $image_url ) );
}
?>
<section
	id="<?= esc_attr( $block_id ); ?>"
	class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>"
	<?= $section_style ? ' style="' . esc_attr( $section_style ) . '"' : ''; ?>
>
	<div class="cb-image-cta__overlay" aria-hidden="true"></div>
	<div class="container py-5">
		<div class="row justify-content-center">
			<div class="col-md-8 text-center">
				<?php if ( '' !== trim( $site_title ) ) : ?>
					<h2 class="cb-image-cta__title text-uppercase"><?= esc_html( $site_title ); ?></h2>
				<?php endif; ?>
				<?php if ( '' !== trim( wp_strip_all_tags( $content ) ) ) : ?>
					<div class="cb-image-cta__content"><?= wp_kses_post( $content ); ?></div>
				<?php endif; ?>
				<?php if ( ! empty( $cta_link ) && ! empty( $cta_link['url'] ) ) : ?>
					<a
						class="cb-image-cta__link"
						href="<?= esc_url( $cta_link['url'] ); ?>"
						target="<?= esc_attr( ! empty( $cta_link['target'] ) ? $cta_link['target'] : '_self' ); ?>"
					><?= esc_html( ! empty( $cta_link['title'] ) ? $cta_link['title'] : $cta_link['url'] ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

<?php if ( $image_url ) : ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var section = document.getElementById(<?= wp_json_encode( $block_id ); ?>);
	if (!section) return;

	var ticking = false;

	function update() {
		var rect = section.getBoundingClientRect();
		var windowHeight = window.innerHeight;

		if (rect.bottom > 0 && rect.top < windowHeight) {
			var percent = (windowHeight - rect.top) / (windowHeight + rect.height);
			percent = Math.max(0, Math.min(1, percent));
			var translateY = (percent - 0.5) * 240;
			section.style.setProperty('--cb-image-cta-parallax-y', translateY.toFixed(1) + 'px');
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
