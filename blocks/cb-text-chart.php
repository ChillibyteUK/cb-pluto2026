<?php
/**
 * Block template for CB Text Chart.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$col_order       = get_field( 'order' ) ? get_field( 'order' ) : 'Text Chart';
$chart_key_label = get_field( 'chart_key_label' );
$chart_items     = get_field( 'chart_items' );

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

$bg = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';

$block_uid = 'text-chart-' . uniqid();
$chart_uid = 'donut-' . uniqid();

// AOS intro animations: the chart slides in from its own side, the text fades.
$chart_side = ( 'Chart Text' === $col_order ) ? 'left' : 'right';
$chart_aos  = ( 'left' === $chart_side ) ? 'fade-right' : 'fade-left';
$text_aos   = 'fade';

$text_col_order  = ( 'Chart Text' === $col_order ) ? 'order-2 order-lg-2' : 'order-lg-1';
$chart_col_order = ( 'Chart Text' === $col_order ) ? 'order-1 order-lg-1' : 'order-lg-2';

/**
 * Render the text content (title + content + link + chart key). The chart
 * key is appended at the end of the content, not alongside the chart itself.
 */
$render_text = function () use ( $chart_key_label, $chart_items ) {
	if ( get_field( 'title' ) ) {
		echo '<h2 class="cb-text-chart__title has-700-font-size mb-4">' . wp_kses_post( get_field( 'title' ) ) . '</h2>';
	}
	?>
	<div class="cb-text-chart__content">
		<?= wp_kses_post( get_field( 'content' ) ); ?>
		<?php
		if ( get_field( 'link' ) ) {
			$l = get_field( 'link' );
			?>
			<p class="mt-4"><a class="cb-link-dot" href="<?= esc_url( $l['url'] ); ?>"
				target="<?= esc_attr( $l['target'] ? $l['target'] : '_self' ); ?>"><?= esc_html( $l['title'] ); ?></a>
			</p>
			<?php
		}
		?>
		<?php if ( ! empty( $chart_items ) ) : ?>
		<div class="cb-text-chart__key">
			<?php if ( $chart_key_label ) : ?>
			<div class="cb-text-chart__key-label"><?= esc_html( $chart_key_label ); ?></div>
			<?php endif; ?>
			<ul class="cb-text-chart__key-list">
				<?php foreach ( $chart_items as $item ) : ?>
				<li>
					<span class="cb-text-chart__key-swatch" style="background-color: <?= esc_attr( $item['colour'] ? $item['colour'] : '#000' ); ?>"></span>
					<span class="cb-text-chart__key-title"><?= esc_html( $item['title'] ); ?></span>
					<span class="cb-text-chart__key-value"><?= esc_html( $item['value'] ); ?>%</span>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>
	</div>
	<?php
};

$render_chart_script = function () use ( $chart_uid, $chart_items ) {
	if ( empty( $chart_items ) ) {
		return;
	}
	?>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var ctx = document.getElementById('<?= esc_js( $chart_uid ); ?>');
	if (!ctx) return;
	var labels = [], data = [], colors = [];
	<?php foreach ( $chart_items as $item ) : ?>
	labels.push('<?= esc_js( $item['title'] ); ?>');
	data.push(<?= (float) $item['value']; ?>);
	colors.push('<?= esc_js( $item['colour'] ? $item['colour'] : '#000' ); ?>');
	<?php endforeach; ?>
	var observer = new IntersectionObserver(function (entries) {
		entries.forEach(function (entry) {
			if (!entry.isIntersecting) return;
			new Chart(ctx, {
				type: 'doughnut',
				data: {
					labels: labels,
					datasets: [{
						data: data,
						backgroundColor: colors,
						borderWidth: 0
					}]
				},
				options: {
					responsive: true,
					cutout: '40%',
					plugins: {
						legend: { display: false },
						tooltip: {
							callbacks: {
								label: function (tooltipItem) {
									var total = tooltipItem.dataset.data.reduce(function (a, b) { return a + b; }, 0);
									var pct = ((tooltipItem.parsed / total) * 100).toFixed(1);
									return tooltipItem.label + ': ' + pct + '%';
								}
							}
						}
					}
				}
			});
			observer.unobserve(ctx);
		});
	}, { threshold: 0.3 });
	observer.observe(ctx);
});
</script>
	<?php
};
?>
<section id="<?= esc_attr( $block_uid ); ?>" class="<?= esc_attr( trim( 'cb-text-chart cb-text-chart--chart-' . $chart_side . ' ' . $bg . ' ' . $fg . ' ' . $classes ) ); ?>">
	<div class="container">
		<div class="row gy-5 gx-4 gx-lg-5 align-items-center">
			<div class="col-lg-6 <?= esc_attr( $text_col_order ); ?> <?= esc_attr( 'Chart Text' === $col_order ? 'pe-lg-5' : 'ps-lg-5' ); ?>" data-aos="<?= esc_attr( $text_aos ); ?>">
				<?php $render_text(); ?>
			</div>
			<div class="col-lg-6 <?= esc_attr( $chart_col_order ); ?> cb-text-chart__chart-col text-center" data-aos="<?= esc_attr( $chart_aos ); ?>">
				<?php if ( ! empty( $chart_items ) ) : ?>
				<canvas id="<?= esc_attr( $chart_uid ); ?>" class="mx-auto" width="400" height="400"></canvas>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php $render_chart_script(); ?>
