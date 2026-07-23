<?php
/**
 * Block template for CB Text Stats.
 *
 * Two-column layout: text content (left) and a stat grid (right)
 * following the cb-stat-bar format.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$btitle  = get_field( 'title' );
$content = get_field( 'content' );
$l       = get_field( 'link' );
$stats   = get_field( 'stats' );

$block_id        = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-text-stats-' );
$section_classes = array( 'cb-text-stats' );

if ( ! empty( $block['className'] ) ) {
	$section_classes[] = $block['className'];
}

$bg = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
if ( $bg ) {
	$section_classes[] = $bg;
}
?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="container">
		<div class="row gy-5 gx-4 gx-lg-5 align-items-start">
			<div class="col-md-6 cb-text-stats__text pe-lg-5 pt-3" data-aos="fade">
				<?php if ( $btitle ) { ?>
				<h2 class="cb-text-stats__title has-700-font-size mb-4"><?= wp_kses_post( $btitle ); ?></h2>
				<?php } ?>
				<?php if ( $content ) { ?>
				<div class="cb-text-stats__content"><?= wp_kses_post( $content ); ?></div>
				<?php } ?>
				<?php if ( $l ) { ?>
				<p class="mt-4"><a class="cb-link-dot" href="<?= esc_url( $l['url'] ); ?>" target="<?= esc_attr( $l['target'] ? $l['target'] : '_self' ); ?>"><?= esc_html( $l['title'] ); ?></a></p>
				<?php } ?>
			</div>
			<div class="col-md-6 cb-text-stats__stats-col">
				<?php
				if ( ! empty( $stats ) && is_array( $stats ) ) {
					?>
				<div class="row g-3 cb-text-stats__grid">
					<?php
					$total_stats = count( $stats );
					foreach ( $stats as $i => $item ) {
						$is_last    = ( $i + 1 ) === $total_stats;
						$wrap_class = 'cb-text-stats__stat-wrap';
						$stat_class = 'cb-text-stats__stat';
						if ( $is_last ) {
							$wrap_class .= ' col-12 cb-text-stats__stat-wrap--full';
							$stat_class .= ' cb-text-stats__stat--inline';
						} else {
							$wrap_class .= ' col-sm-6';
						}
						?>
					<div class="<?= esc_attr( $wrap_class ); ?>">
						<div class="<?= esc_attr( $stat_class ); ?>">
							<?php
							if ( ! empty( $item['stat_title'] ) ) {
								?>
							<div class="cb-text-stats__stat-title"><?= esc_html( $item['stat_title'] ); ?></div>
								<?php
							}
							if ( isset( $item['stat'] ) && '' !== $item['stat'] ) {
								$stat_size_class = mb_strlen( $item['stat'] ) < 15 ? 'cb-text-stats__stat-value--lg' : 'cb-text-stats__stat-value--sm';
								?>
							<div class="cb-text-stats__stat-value <?= esc_attr( $stat_size_class ); ?>"><?= wp_kses_post( nl2br( $item['stat'] ) ); ?></div>
								<?php
							}
							if ( ! empty( $item['stat_after'] ) ) {
								?>
							<div class="cb-text-stats__stat-after"><?= esc_html( $item['stat_after'] ); ?></div>
								<?php
							}
							?>
						</div>
					</div>
						<?php
					}
					?>
				</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</section>
