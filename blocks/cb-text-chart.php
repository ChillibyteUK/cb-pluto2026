<?php
/**
 * Block template for CB Text Chart.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$col_order       = get_field( 'order' ) ? get_field( 'order' ) : 'Text Chart';
$split           = get_field( 'split' ) ? get_field( 'split' ) : '50 50';
$full_bleed      = (bool) get_field( 'full_bleed' );
$flourish        = (bool) get_field( 'flourish' );
$aspect          = get_field( 'aspect' ) ? get_field( 'aspect' ) : 'native';
$rounded_raw     = get_field( 'rounded' );
$rounded         = ( null === $rounded_raw ) ? true : (bool) $rounded_raw;
$chart_key_label = get_field( 'chart_key_label' );
$chart_items     = get_field( 'chart_items' );

$flourish_classes = '';
if ( $flourish ) {
	$flourish_classes = 'full-flourish';
	$context          = cb_get_site_context();
	if ( 'pf' === $context ) {
		$flourish_classes .= ' full-flourish--lending';
	} elseif ( 'inv' === $context ) {
		$flourish_classes .= ' full-flourish--investors';
	}
	if ( 'Chart Text' === $col_order ) {
		$flourish_classes .= ' full-flourish--flip';
	}
}

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

$chart_side = ( 'Chart Text' === $col_order ) ? 'left' : 'right';
$modifiers  = array( 'cb-text-chart--chart-' . $chart_side );

$image_aos = ( 'left' === $chart_side ) ? 'fade-right' : 'fade-left';
$text_aos  = 'fade';
if ( $full_bleed ) {
	$modifiers[] = 'cb-text-chart--full-bleed';
	$modifiers[] = 'cb-text-chart--split-' . str_replace( ' ', '-', $split );
	if ( $rounded ) {
		$modifiers[] = 'cb-text-chart--rounded';
	}
} elseif ( 'native' !== $aspect ) {
	$modifiers[] = 'cb-text-chart--aspect-' . $aspect;
}
$modifier_classes = implode( ' ', $modifiers );

$block_uid = 'text-chart-' . uniqid();

if ( '60 40' === $split ) {
	$text_col_n  = 7;
	$image_col_n = 5;
} elseif ( '40 60' === $split ) {
	$text_col_n  = 5;
	$image_col_n = 7;
} else {
	$text_col_n  = 6;
	$image_col_n = 6;
}

$chart_uid = 'donut-' . uniqid();

$render_text = function () use ( $chart_key_label, $chart_items, $chart_uid ) {
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
	</div>
	<?php if ( ! empty( $chart_items ) ) : ?>
	<div class="cb-text-chart__chart-wrap">
		<canvas id="<?= esc_attr( $chart_uid ); ?>" width="400" height="400"></canvas>
		<?php if ( $chart_key_label || ! empty( $chart_items ) ) : ?>
		<div class="cb-text-chart__key">
			<?php if ( $chart_key_label ) : ?>
			<div class="cb-text-chart__key-label"><?= esc_html( $chart_key_label ); ?></div>
			<?php endif; ?>
			<ul class="cb-text-chart__key-list">
				<?php foreach ( $chart_items as $item ) : ?>
				<li>
					<span class="cb-text-chart__key-swatch" style="background-color: <?= esc_attr( $item['colour'] ?: '#000' ); ?>"></span>
					<?= esc_html( $item['title'] ); ?>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>
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
	colors.push('<?= esc_js( $item['colour'] ?: '#000' ); ?>');
	<?php endforeach; ?>
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
			cutout: '30%',
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
});
</script>
	<?php
};
?>
<section id="<?= esc_attr( $block_uid ); ?>" class="<?= esc_attr( trim( $flourish_classes . ' cb-text-chart ' . $modifier_classes . ' ' . $bg . ' ' . $fg . ' ' . $classes ) ); ?>">

	<?php if ( $full_bleed ) : ?>

		<?php
		$text_col_classes = 'col-lg-' . $text_col_n;
		if ( 'left' === $chart_side ) {
			$text_col_classes .= ' offset-lg-' . ( 12 - $text_col_n );
			$text_col_classes .= ' ps-lg-5';
		} else {
			$text_col_classes .= ' pe-lg-5';
		}
		?>
		<div class="cb-text-chart__inner">
			<div class="container">
				<div class="row">
					<div class="<?= esc_attr( $text_col_classes ); ?>" data-aos="<?= esc_attr( $text_aos ); ?>">
						<?php $render_text(); ?>
					</div>
				</div>
			</div>
			<div class="cb-text-chart__image" data-aos="<?= esc_attr( $image_aos ); ?>">
				<?= wp_get_attachment_image( get_field( 'image' ), 'full', false, array() ); ?>
			</div>
		</div>

	<?php else : ?>

		<?php
		$text_col_order  = ( 'Chart Text' === $col_order ) ? 'order-2 order-lg-2' : 'order-lg-1';
		$image_col_order = ( 'Chart Text' === $col_order ) ? 'order-1 order-lg-1' : 'order-lg-2';
		?>
		<div class="container">
			<div class="row gy-5 gx-4 gx-lg-5 align-items-center">
				<div class="col-lg-<?= esc_attr( $text_col_n ); ?> <?= esc_attr( $text_col_order ); ?> <?= esc_attr( 'Chart Text' === $col_order ? 'pe-lg-5' : 'ps-lg-5' ); ?>" data-aos="<?= esc_attr( $text_aos ); ?>">
					<?php $render_text(); ?>
				</div>
				<div class="col-lg-<?= esc_attr( $image_col_n ); ?> <?= esc_attr( $image_col_order ); ?> cb-text-chart__image text-center" data-aos="<?= esc_attr( $image_aos ); ?>">
					<?= wp_get_attachment_image( get_field( 'image' ), 'full', false, array() ); ?>
				</div>
			</div>
		</div>

	<?php endif; ?>

</section>
<?php $render_chart_script(); ?>
