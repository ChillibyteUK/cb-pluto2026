<?php
/**
 * Block template for CB Contact Map.
 *
 * Renders an authored title, an "Our offices are located at" intro that pulls
 * the address from Site-Wide Settings, and an embedded map iframe whose src
 * comes from the `map_url` option. The block carries the brand flourish along
 * its top edge; the colour variant (lending / investors) is chosen
 * automatically from the current URL, matching cb-text-image / cb-latest-posts.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$btitle  = (string) get_field( 'title' );
$address = function_exists( 'get_field' ) ? (string) get_field( 'address', 'option' ) : '';
$map_url = function_exists( 'get_field' ) ? (string) get_field( 'map_url', 'option' ) : '';

// Custom classes (filter wp-generated).
$custom_classes = '';
if ( ! empty( $block['className'] ) ) {
	$class_array    = explode( ' ', $block['className'] );
	$filtered       = array_filter(
		$class_array,
		function ( $item ) {
			return ! preg_match( '/^wp-/', $item );
		}
	);
	$custom_classes = implode( ' ', $filtered );
}

// Gutenberg colour support.
$bg = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color has-background' : '';
$fg = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color has-text-color' : '';

$inline_style = '';
if ( ! empty( $block['style']['color']['background'] ) ) {
	$inline_style .= 'background-color:' . $block['style']['color']['background'] . ';';
	$bg           .= ' has-background';
}
if ( ! empty( $block['style']['color']['text'] ) ) {
	$inline_style .= 'color:' . $block['style']['color']['text'] . ';';
	$fg           .= ' has-text-color';
}

// Determine the flourish colour variant from the URL, mirroring cb-text-image.
$request_uri     = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL );
$request_uri     = is_string( $request_uri ) ? wp_unslash( $request_uri ) : '/';
$current_path    = wp_parse_url( $request_uri, PHP_URL_PATH );
$current_path    = is_string( $current_path ) ? $current_path : '/';
$normalized_path = trailingslashit( $current_path );

$flourish_classes = 'full-flourish';
if ( 0 === strpos( $normalized_path, '/property-finance/' ) ) {
	$flourish_classes .= ' full-flourish--lending full-flourish--flip';
} elseif ( 0 === strpos( $normalized_path, '/investors/' ) ) {
	$flourish_classes .= ' full-flourish--investors full-flourish--flip';
}

$block_uid = 'cb-contact-map-' . uniqid();
$classes   = trim( implode( ' ', array_filter( array( 'cb-contact-map', $flourish_classes, $bg, $fg, $custom_classes ) ) ) );
?>
<section
	id="<?= esc_attr( $block_uid ); ?>"
	class="<?= esc_attr( $classes ); ?>"
	<?= $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : ''; ?>
>
	<div class="container py-5">
		<?php if ( '' !== trim( $btitle ) ) : ?>
			<h2 class="cb-contact-map__title text-center text-uppercase"><?= esc_html( $btitle ); ?></h2>
		<?php endif; ?>

		<?php if ( '' !== trim( wp_strip_all_tags( $address ) ) ) : ?>
			<p class="cb-contact-map__intro text-center">
				<?= esc_html__( 'Our offices are located at', 'cb-pluto2026' ); ?>
				<span class="cb-contact-map__address"><?= esc_html( wp_strip_all_tags( $address ) ); ?></span>
			</p>
		<?php endif; ?>

		<?php if ( '' !== trim( $map_url ) ) : ?>
			<div class="cb-contact-map__map">
				<iframe
					src="<?= esc_url( $map_url ); ?>"
					title="<?php esc_attr_e( 'Office location map', 'cb-pluto2026' ); ?>"
					loading="lazy"
					referrerpolicy="no-referrer-when-downgrade"
					allowfullscreen
				></iframe>
			</div>
		<?php endif; ?>
	</div>
</section>
