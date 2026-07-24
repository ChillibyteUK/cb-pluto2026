<?php
/**
 * Block template for CB Title Scroll Bullets.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$btitle  = get_field( 'title' );
$bullets = get_field( 'bullets' );

if ( empty( $btitle ) && empty( $bullets ) ) {
	return;
}

$block_uid = 'title-scroll-bullets-' . uniqid();

$bullet_lines = array();
if ( ! empty( $bullets ) ) {
	$bullet_lines = preg_split( '/\r\n|\r|\n/', $bullets );
	$bullet_lines = array_filter( array_map( 'trim', $bullet_lines ) );
}

$bg = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';

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
$classes = $custom_classes ? $custom_classes : '';
?>
<section id="<?= esc_attr( $block_uid ); ?>" class="<?= esc_attr( trim( 'cb-title-scroll-bullets py-6 ' . $bg . ' ' . $fg . ' ' . $classes ) ); ?>">
	<div class="container">
		<div class="row gx-5">
			<div class="col-lg-6 cb-title-scroll-bullets__title-col">
				<div class="cb-title-scroll-bullets__title-inner">
					<?php if ( $btitle ) : ?>
					<h2 class="cb-title-scroll-bullets__title"><?= wp_kses_post( $btitle ); ?></h2>
					<?php endif; ?>
				</div>
			</div>
			<div class="col-lg-6 cb-title-scroll-bullets__bullets-col">
				<?php if ( ! empty( $bullet_lines ) ) : ?>
				<ul class="cb-title-scroll-bullets__list">
					<?php foreach ( $bullet_lines as $bullet ) : ?>
					<li class="cb-title-scroll-bullets__item"><?= esc_html( $bullet ); ?></li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>


