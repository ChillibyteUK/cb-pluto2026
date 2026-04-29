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

$ftitle  = (string) get_field( 'title' );
$content = (string) get_field( 'content' );

// Inner-container utility classes come from the block's standard
// "Advanced → Additional CSS class(es)" sidebar field ($block['className']).
// We strip the wp-* helpers ACF injects and default to pb-5 when empty so
// feature titles stack flush against the following block.
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

// Side detection from URL path. Mirrors cb-topic-home-hero.
$context       = cb_get_site_context();
$variant_class = '' !== $context ? 'cb-feature-title--' . $context : '';

// Gutenberg colour-picker support. ACF surfaces preset slugs via
// $block['backgroundColor'] / ['textColor'] and custom hex picks via
// $block['style']['color']['background'|'text']. Translate both into
// the standard core-block class + inline-style pattern so theme.json
// presets stay editable from the sidebar.
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

if ( '' === trim( $ftitle ) && '' === trim( wp_strip_all_tags( $content ) ) ) {
	if ( ! empty( $block['data']['_is_preview'] ) || ( function_exists( 'acf_is_block_editor' ) && false ) ) {
		echo '<p class="text-muted"><em>' . esc_html__( 'Add a title and content.', 'cb-pluto2026' ) . '</em></p>';
	}
	return;
}
?>
<section class="<?= esc_attr( $classes ); ?>"<?= $style ? ' style="' . esc_attr( $style ) . '"' : ''; ?>>
	<div class="container <?= esc_attr( $container_classes ); ?>">
		<?php if ( '' !== trim( $ftitle ) ) : ?>
			<h2 class="cb-feature-title__heading"><span class="cb-feature-title__heading-text"><?= esc_html( $ftitle ); ?></span></h2>
		<?php endif; ?>
		<?php if ( '' !== trim( wp_strip_all_tags( $content ) ) ) : ?>
			<div class="cb-feature-title__content"><?= wp_kses_post( $content ); ?></div>
		<?php endif; ?>
	</div>
</section>
