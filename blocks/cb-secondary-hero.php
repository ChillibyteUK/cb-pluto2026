<?php
/**
 * Block template for CB Secondary Hero.
 *
 * Centered hero with a thin parallax header-image strip and a white card
 * containing the title (h1) and content. The first paragraph of the content
 * is styled as a standfirst (selector: .cb-secondary-hero__content > p:first-child).
 *
 * Parallax: same scroll-driven --parallax-y custom property pattern as
 * cb-topic-home-hero / cb-image-cta.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$block_title = (string) get_field( 'title' );
$content     = (string) get_field( 'content' );
$image_id    = get_field( 'header_image' );

// Bail when nothing meaningful has been authored.
if ( '' === trim( $block_title ) && '' === trim( $content ) && ! $image_id ) {
	return;
}

$block_id  = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-secondary-hero-' );
$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';

$section_classes = array( 'cb-secondary-hero' );
$section_style   = '';
if ( $image_url ) {
	$section_classes[] = 'cb-secondary-hero--has-header-image';
	$section_style     = sprintf( '--cb-secondary-hero-bg: url(%s);', esc_url_raw( $image_url ) );
}
?>
<section
	id="<?= esc_attr( $block_id ); ?>"
	class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>"
	<?= $section_style ? ' style="' . esc_attr( $section_style ) . '"' : ''; ?>
>
	<?php if ( $image_url ) : ?>
		<div class="cb-secondary-hero__image" aria-hidden="true"></div>
	<?php endif; ?>

	<div class="cb-secondary-hero__body">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-9 col-xl-8 text-center">
					<?php if ( '' !== trim( $block_title ) ) : ?>
						<h1 class="cb-secondary-hero__title text-uppercase"><?= esc_html( $block_title ); ?></h1>
					<?php endif; ?>
					<?php if ( '' !== trim( $content ) ) : ?>
						<div class="cb-secondary-hero__content">
							<?= wp_kses_post( wpautop( $content ) ); ?>
						</div>
					<?php endif; ?>
				</div>
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
			var translateY = (percent - 0.5) * 160;
			section.style.setProperty('--cb-secondary-hero-parallax-y', translateY.toFixed(1) + 'px');
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
