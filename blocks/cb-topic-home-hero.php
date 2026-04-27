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

if ( $hero_bg_url ) {
	$hero_classes[] = 'topic-home-hero--has-background-image';
	$hero_style     = sprintf( '--topic-home-hero-bg: url(%s);', esc_url_raw( $hero_bg_url ) );
}


?>
<section id="<?= esc_attr( $hero_id ); ?>" class="<?= esc_attr( implode( ' ', $hero_classes ) ); ?>"<?= $hero_style ? ' style="' . esc_attr( $hero_style ) . '"' : ''; ?>>
	<div class="topic-home-hero__overlay" aria-hidden="true">
		<div class="topic-home-hero__overlay-rect"></div>
		<div class="topic-home-hero__overlay-cap"></div>
	</div>
	<div class="container my-auto">
		<div class="row">
			<div class="col-md-6">
				<h1 class="text-balance"><?= esc_html( get_field( 'title' ) ); ?></h1>
				<ul class="topic-home-hero__intro"><?= wp_kses_post( cb_list( get_field( 'usps' ) ) ); ?></ul>
			</div>
		</div>
	</div>
</section>

<?php
if ( $hero_bg_url ) {
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
	<?php
}