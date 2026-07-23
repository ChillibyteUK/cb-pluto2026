<?php
/**
 * Block template for CB Text Video.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$btitle  = get_field( 'title' );
$content = get_field( 'content' );

$vid = get_field( 'vimeo_id' );

$col_order = get_field( 'order' ) ? get_field( 'order' ) : 'Text Video';

$block_id        = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-text-video-' );
$section_classes = array( 'cb-text-video' );

if ( ! empty( $block['className'] ) ) {
	$section_classes[] = $block['className'];
}

$bg = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
if ( $bg ) {
	$section_classes[] = $bg;
}

// AOS intro animations: the video slides in from its own side, the text fades.
$video_side = ( 'Video Text' === $col_order ) ? 'left' : 'right';
$video_aos  = ( 'left' === $video_side ) ? 'fade-right' : 'fade-left';
$text_aos   = 'fade';

$text_col_order  = ( 'Video Text' === $col_order ) ? 'order-2 order-md-2' : 'order-md-1';
$video_col_order = ( 'Video Text' === $col_order ) ? 'order-1 order-md-1' : 'order-md-2';
?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="container">
		<div class="row gy-5 gx-4 gx-lg-5 align-items-start">
			<div class="col-md-6 cb-text-video pe-lg-5 pt-3 <?= esc_attr( $text_col_order ); ?>" data-aos="<?= esc_attr( $text_aos ); ?>">
				<?php
				if ( $btitle ) {
					?>
				<h2 class="cb-text-video has-700-font-size mb-4"><?= wp_kses_post( $btitle ); ?></h2>
					<?php
				}
				if ( $content ) {
					?>
				<div class="cb-text-video"><?= wp_kses_post( $content ); ?></div>
					<?php
				}
				?>
			</div>
			<div class="col-md-6 cb-text-video-col <?= esc_attr( $video_col_order ); ?>" data-aos="<?= esc_attr( $video_aos ); ?>">
<!-- video thumbnail - click to open player in modal -->
			</div>
		</div>
	</div>
</section>