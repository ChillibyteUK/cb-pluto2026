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

// Inner-container utility classes come from the block's standard
// "Advanced → Additional CSS class(es)" sidebar field. Default to pb-5.
$container_classes = 'pb-5';
if ( ! empty( $block['className'] ) ) {
	$filtered = array_filter(
		explode( ' ', $block['className'] ),
		function ( $item ) {
			return '' !== $item && ! preg_match( '/^wp-/', $item );
		}
	);
	if ( $filtered ) {
		$container_classes = implode( ' ', $filtered );
	}
}

// Side detection from URL path.
$context       = cb_get_site_context();
$variant_class = '' !== $context ? 'cb-feature-title--' . $context : '';

// Gutenberg colour-picker support (preset slugs + custom hex).
$colour_classes = array();
$colour_styles  = array();

$bg_slug = $block['backgroundColor'] ?? '';
$bg_hex  = $block['style']['color']['background'] ?? '';
if ( $bg_slug ) {
	$colour_classes[] = 'has-background';
	$colour_classes[] = 'has-' . sanitize_html_class( $bg_slug ) . '-background-color';
} elseif ( $bg_hex ) {
	$colour_classes[] = 'has-background';
	$colour_styles[]  = 'background-color:' . $bg_hex;
}

$text_slug = $block['textColor'] ?? '';
$text_hex  = $block['style']['color']['text'] ?? '';
if ( $text_slug ) {
	$colour_classes[] = 'has-text-color';
	$colour_classes[] = 'has-' . sanitize_html_class( $text_slug ) . '-color';
} elseif ( $text_hex ) {
	$colour_classes[] = 'has-text-color';
	$colour_styles[]  = 'color:' . $text_hex;
}

$link_hex = $block['style']['color']['link'] ?? '';
if ( $link_hex ) {
	$colour_classes[] = 'has-link-color';
	$colour_styles[]  = '--wp--style--link-color:' . $link_hex;
}

$classes = trim( 'cb-feature-title ' . $variant_class . ' ' . implode( ' ', $colour_classes ) );
$style   = $colour_styles ? implode( ';', $colour_styles ) : '';
?>
<section class="<?= esc_attr( $classes ); ?>"<?= $style ? ' style="' . esc_attr( $style ) . '"' : ''; ?>>
	<div class="container <?= esc_attr( $container_classes ); ?>">
		<h2 class="cb-feature-title__heading"><span class="cb-feature-title__heading-text"><?= esc_html( get_field( 'title' ) ); ?></span></h2>
		<div class="cb-feature-title__content"><?= wp_kses_post( get_field( 'content' ) ); ?></div>
	</div>
</section>
