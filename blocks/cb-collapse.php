<?php
/**
 * Block template for CB Collapse.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$btitle  = get_field( 'title' );
$content = get_field( 'content' );

if ( ! $btitle ) {
	return;
}

$bg = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color has-background' : '';
$fg = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color has-text-color' : '';

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

$block_uid = 'cb-collapse-' . uniqid();
$classes   = trim( implode( ' ', array_filter( array( 'cb-collapse', $bg, $fg, $custom_classes ) ) ) );
?>
<section id="<?= esc_attr( $block_uid ); ?>" class="<?= esc_attr( $classes ); ?>">
	<div class="container">
		<div class="cb-collapse__item">
			<button class="cb-collapse__toggle collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= esc_attr( $block_uid ); ?>-body" aria-expanded="false" aria-controls="<?= esc_attr( $block_uid ); ?>-body">
				<?= esc_html( $btitle ); ?>
			</button>
			<div class="collapse" id="<?= esc_attr( $block_uid ); ?>-body">
				<div class="cb-collapse__body">
					<?= wp_kses_post( wpautop( (string) $content ) ); ?>
				</div>
			</div>
		</div>
	</div>
</section>
