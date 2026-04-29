<?php
/**
 * Block template for CB Quote.
 *
 * Pull-quote layout: a 7/12 text column (quote, author, role) sits beside a
 * 4/12 image column (author portrait + transparent signature). The whole
 * section sits on a parallax background image (same scroll-driven
 * --cb-quote-parallax-y custom property pattern as cb-image-cta).
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$quote        = (string) get_field( 'quote' );
$author       = (string) get_field( 'quote_author' );
$arole        = (string) get_field( 'author_role' );
$author_image = get_field( 'author_image' );
$author_sig   = get_field( 'author_sig' );
$bg_image     = get_field( 'background_image' );

if ( '' === trim( wp_strip_all_tags( $quote ) ) && ! $author && ! $author_image ) {
	return;
}

$block_id = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-quote-' );
$bg_url   = $bg_image ? wp_get_attachment_image_url( $bg_image, 'full' ) : '';

$section_classes = array( 'cb-quote' );
$section_style   = '';
if ( $bg_url ) {
	$section_classes[] = 'cb-quote--has-background-image';
	$section_style    .= sprintf( '--cb-quote-bg: url(%s);', esc_url_raw( $bg_url ) );
}
if ( ! empty( $block['className'] ) ) {
	$section_classes[] = $block['className'];
}

// Quote: paragraph-ify on blank lines (textarea wpautop).
$quote_html = wpautop( $quote );
?>
<section
	id="<?= esc_attr( $block_id ); ?>"
	class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>"
	<?= $section_style ? ' style="' . esc_attr( $section_style ) . '"' : ''; ?>
>
	<?php if ( $bg_url ) : ?>
		<div class="cb-quote__overlay" aria-hidden="true"></div>
	<?php endif; ?>
	<div class="container py-5">
		<div class="row align-items-center gx-4 gx-lg-5">
			<div class="col-md-7 cb-quote__text">
				<?php if ( '' !== trim( wp_strip_all_tags( $quote_html ) ) ) : ?>
					<blockquote class="cb-quote__quote"><?= wp_kses_post( $quote_html ); ?></blockquote>
				<?php endif; ?>
				<?php if ( $author || $arole ) : ?>
					<p class="cb-quote__attribution">
						<?php if ( $author ) : ?>
							<span class="cb-quote__author"><?= esc_html( $author ); ?></span>
						<?php endif; ?>
						<?php if ( $arole ) : ?>
							<span class="cb-quote__role"><?= esc_html( $arole ); ?></span>
						<?php endif; ?>
					</p>
				<?php endif; ?>
			</div>
			<div class="col-md-4 offset-md-1 cb-quote__media">
				<?php if ( $author_image ) : ?>
					<div class="cb-quote__author-image mx-auto">
						<?= wp_get_attachment_image( $author_image, 'medium_large', false, array( 'alt' => esc_attr( $author ) ) ); ?>
					</div>
				<?php endif; ?>
				<?php if ( $author_sig ) : ?>
					<div class="cb-quote__author-sig mx-auto">
						<?= wp_get_attachment_image( $author_sig, 'medium', false, array( 'alt' => esc_attr( $author ? $author . ' signature' : 'signature' ) ) ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

<?php if ( $bg_url ) : ?>
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
			section.style.setProperty('--cb-quote-parallax-y', translateY.toFixed(1) + 'px');
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
