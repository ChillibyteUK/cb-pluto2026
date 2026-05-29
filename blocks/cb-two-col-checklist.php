<?php
/**
 * Block template for CB Two Col Checklist.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$section_classes = array( 'cb-two-col-checklist' );
$section_style   = '';

if ( ! empty( $block['backgroundColor'] ) ) {
	$section_classes[] = 'has-' . $block['backgroundColor'] . '-background-color';
	$section_classes[] = 'has-background';
}
if ( ! empty( $block['textColor'] ) ) {
	$section_classes[] = 'has-' . $block['textColor'] . '-color';
	$section_classes[] = 'has-text-color';
}
$custom_bg = $block['style']['color']['background'] ?? '';
$custom_fg = $block['style']['color']['text'] ?? '';
if ( $custom_bg ) {
	$section_style .= 'background-color:' . $custom_bg . ';';
	$section_classes[] = 'has-background';
}
if ( $custom_fg ) {
	$section_style .= 'color:' . $custom_fg . ';';
	$section_classes[] = 'has-text-color';
}
if ( ! empty( $block['className'] ) ) {
	$section_classes[] = $block['className'];
}
?>
<section class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>"<?= $section_style ? ' style="' . esc_attr( $section_style ) . '"' : ''; ?>>
	<div class="container py-5">
		<?php if ( have_rows( 'checklist' ) ) : ?>
		<div class="row g-4">
			<?php while ( have_rows( 'checklist' ) ) : the_row(); ?>
			<div class="col-12 col-md-6 cb-two-col-checklist__item">
				<i class="fa-regular fa-circle-check cb-two-col-checklist__icon" aria-hidden="true"></i>
				<?php if ( $title = get_sub_field( 'title' ) ) : ?>
				<strong class="cb-two-col-checklist__title"><?= esc_html( $title ); ?></strong>
				<?php endif; ?>
				<?php if ( $content = get_sub_field( 'content' ) ) : ?>
				<div class="cb-two-col-checklist__content"><?= wp_kses_post( $content ); ?></div>
				<?php endif; ?>
			</div>
			<?php endwhile; ?>
		</div>
		<?php endif; ?>
	</div>
</section>
