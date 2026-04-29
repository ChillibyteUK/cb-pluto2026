<?php
/**
 * Block template for CB Feature Title.
 *
 * Renders an h2 with a side-coloured dot marker and a 1px line that extends
 * leftward off the viewport edge. The dot/line variant (orange / teal) is
 * chosen from the URL path: /property-finance/ → orange, /investors/ → teal.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$title   = (string) get_field( 'title' );
$content = (string) get_field( 'content' );

// Custom classes (strip core wp-* helpers ACF injects).
$custom_classes = '';
if ( isset( $block['className'] ) ) {
	$class_array    = explode( ' ', $block['className'] );
	$filtered       = array_filter(
		$class_array,
		function ( $item ) {
			return ! preg_match( '/^wp-/', $item );
		}
	);
	$custom_classes = implode( ' ', $filtered );
}

// Side detection from URL path. Mirrors cb-topic-home-hero.
$context       = cb_get_site_context();
$variant_class = '' !== $context ? 'cb-feature-title--' . $context : '';

$classes = trim( 'cb-feature-title ' . $variant_class . ' ' . $custom_classes );

if ( '' === trim( $title ) && '' === trim( wp_strip_all_tags( $content ) ) ) {
	if ( ! empty( $block['data']['_is_preview'] ) || ( function_exists( 'acf_is_block_editor' ) && false ) ) {
		echo '<p class="text-muted"><em>' . esc_html__( 'Add a title and content.', 'cb-pluto2026' ) . '</em></p>';
	}
	return;
}
?>
<section class="<?= esc_attr( $classes ); ?>">
	<div class="container py-5">
		<?php if ( '' !== trim( $title ) ) : ?>
			<h2 class="cb-feature-title__heading"><span class="cb-feature-title__heading-text"><?= esc_html( $title ); ?></span></h2>
		<?php endif; ?>
		<?php if ( '' !== trim( wp_strip_all_tags( $content ) ) ) : ?>
			<div class="cb-feature-title__content"><?= wp_kses_post( $content ); ?></div>
		<?php endif; ?>
	</div>
</section>
