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

$btitle    = (string) get_field( 'title' );
$address   = function_exists( 'get_field' ) ? (string) get_field( 'address', 'option' ) : '';
$map_url   = function_exists( 'get_field' ) ? (string) get_field( 'map_url', 'option' ) : '';
$address_2 = function_exists( 'get_field' ) ? (string) get_field( 'address_2', 'option' ) : '';
$map_url_2 = function_exists( 'get_field' ) ? (string) get_field( 'map_url_2', 'option' ) : '';
$email     = function_exists( 'get_field' ) ? (string) get_field( 'contact_email', 'option' ) : '';
$phone     = function_exists( 'get_field' ) ? (string) get_field( 'contact_phone', 'option' ) : '';
$linkedin  = function_exists( 'get_field' ) ? (string) get_field( 'linkedin_url', 'option' ) : '';

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
$context          = cb_get_site_context();
$flourish_classes = '';

$block_uid = 'cb-contact-map-' . uniqid();
$classes   = trim( implode( ' ', array_filter( array( 'cb-contact-map', $flourish_classes, $bg, $fg, $custom_classes ) ) ) );
?>
<section
	id="<?= esc_attr( $block_uid ); ?>"
	class="<?= esc_attr( $classes ); ?>"
	<?= $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : ''; ?>
>
	<div class="container py-5">
		<?php
		if ( '' !== trim( $btitle ) ) {
			?>
		<h2 class="cb-contact-map__title text-center"><?= esc_html( $btitle ); ?></h2>
			<?php
		}
		?>
		<div class="d-flex flex-wrap justify-content-center gap-4 mt-4">
			<a class="contact-block__detail" href="tel:<?= esc_attr( parse_phone( $phone ) ); ?>">
				<strong>T:</strong>
				<?= esc_html( $phone ); ?>
			</a>
			<a class="contact-block__detail" href="mailto:<?= esc_attr( antispambot( $email ) ); ?>">
				<strong>E:</strong>
				<?= esc_html( antispambot( $email ) ); ?>
			</a>
			<a class="contact-block__detail" href="<?= esc_url( $linkedin ); ?>" target="_blank" rel="noopener">
				Find us on <strong>LinkedIn</strong>
			</a>
		</div>
		<div class="contact-block__offices row mt-5">
			<article class="contact-block__office contact-block__office--primary col-lg-7 col-md-12">
				<h3>London</h3>
				<address><?= esc_html( wp_strip_all_tags( $address ) ); ?></address>
				<?php
				if ( '' !== trim( $map_url ) ) {
					?>
				<div class="cb-contact-map__map">
					<iframe
						src="<?= esc_url( $map_url ); ?>"
						title="<?php esc_attr_e( 'Office location map', 'cb-pluto2026' ); ?>"
						loading="lazy"
						referrerpolicy="no-referrer-when-downgrade"
						allowfullscreen
					></iframe>
				</div>
					<?php
				}
				?>

			</article>

			<article class="contact-block__office col-lg-5 col-md-12">
				<h3>Edinburgh</h3>
				<address><?= esc_html( wp_strip_all_tags( $address_2 ) ); ?></address>
				<?php
				if ( '' !== trim( $map_url_2 ) ) {
					?>
				<div class="cb-contact-map__map">
					<iframe
						src="<?= esc_url( $map_url_2 ); ?>"
						title="<?php esc_attr_e( 'Office location map', 'cb-pluto2026' ); ?>"
						loading="lazy"
						referrerpolicy="no-referrer-when-downgrade"
						allowfullscreen
					></iframe>
				</div>
					<?php
				}
				?>
			</article>
		</div>

	</div>
</section>
