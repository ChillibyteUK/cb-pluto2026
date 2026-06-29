<?php
/**
 * Block template for CB Stat Bar.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$stats = get_field( 'stats' );

$block_id        = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-stat-bar-' );
$section_classes = array( 'cb-stat-bar' );

if ( ! empty( $block['className'] ) ) {
    $section_classes[] = $block['className'];
}

if ( empty( $stats ) || ! is_array( $stats ) ) {
    return;
}
?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>">
    <div class="container">
        <div class="row justify-content-center">
            <?php foreach ( $stats as $item ) { ?>
            <div class="col-sm-6 col-lg-3 cb-stat-bar__item">
                <div class="cb-stat-bar__item-inner">
                    <?php if ( ! empty( $item['stat_title'] ) ) { ?>
                    <div class="cb-stat-bar__item-title"><?= esc_html( $item['stat_title'] ); ?></div>
                    <?php } ?>
                    <?php if ( isset( $item['stat'] ) && '' !== $item['stat'] ) { ?>
                    <div class="cb-stat-bar__item-stat"><?= wp_kses_post( nl2br( $item['stat'] ) ); ?></div>
                    <?php } ?>
                    <?php if ( ! empty( $item['stat_after'] ) ) { ?>
                    <div class="cb-stat-bar__item-after"><?= esc_html( $item['stat_after'] ); ?></div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</section>
