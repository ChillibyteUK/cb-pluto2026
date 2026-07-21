<?php
/**
 * Block template for CB Card Grid.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$cards = get_field( 'cards' );

if ( empty( $cards ) || ! is_array( $cards ) ) {
	return;
}

$block_id = $block['anchor'] ?? ( 'cb-card-grid-' . uniqid() );

$columns        = '3' === (string) get_field( 'columns' ) ? 3 : 2;
$column_class   = 3 === $columns ? 'col-md-6 col-lg-4' : 'col-lg-6';
$section_classes = array( 'cb-card-grid' );
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
	$section_style    .= 'background-color:' . $custom_bg . ';';
	$section_classes[] = 'has-background';
}
if ( $custom_fg ) {
	$section_style    .= 'color:' . $custom_fg . ';';
	$section_classes[] = 'has-text-color';
}

$custom_classes = 'py-5';
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

if ( '' !== trim( $custom_classes ) ) {
	$section_classes[] = trim( $custom_classes );
}

$classes = trim( implode( ' ', array_filter( $section_classes ) ) );

?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( $classes ); ?>"<?= $section_style ? ' style="' . esc_attr( $section_style ) . '"' : ''; ?>>
	<div class="container">
		<div class="row g-4 align-items-stretch">
			<?php
			foreach ( $cards as $card ) {
				?>
			<div class="<?= esc_attr( $column_class ); ?> cb-card-grid__col">
				<div class="cb-card-grid__card">
					<div class="d-flex gap-4 align-items-center mb-4">
				<?php
				$icon_id = $card['icon'] ?? 0;
				if ( $icon_id ) {
					echo wp_get_attachment_image( $icon_id, 'medium', '', array( 'class' => 'cb-card-grid__icon' ) );
				}
				if ( ! empty( $card['title'] ) ) {
					?>
				<h3 class="cb-card-grid__title"><?= esc_html( $card['title'] ); ?></h3>
					<?php
				}
				?>
			</div>
				<?php
				if ( ! empty( $card['content'] ) ) {
					?>
			<div class="cb-card-grid__content"><?= wp_kses_post( $card['content'] ); ?></div>
					<?php
				}
				?>
				</div>
			</div>
				<?php
			}
			?>
		</div>
	</div>
</section>
