<?php
/**
 * Block template for CB Ticker x3.
 *
 * Three count-up statistics (title / prefix / number / suffix) shown side by
 * side. Numbers animate from 0 to their target value when the block scrolls
 * into view (count-up handler lives in src/js/custom-javascript.js, keyed off
 * the `.cb-ticker-x3__stat-value` class and `data-stat-target` attribute).
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$block_id = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-ticker-x3-' );

$request_uri     = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL );
$request_uri     = is_string( $request_uri ) ? wp_unslash( $request_uri ) : '/';
$current_path    = wp_parse_url( $request_uri, PHP_URL_PATH );
$current_path    = is_string( $current_path ) ? $current_path : '/';
$normalized_path = trailingslashit( $current_path );

$the_class = '';
if ( '/' === $current_path ) {
	$the_class = '';
} elseif ( 0 === strpos( $normalized_path, '/property-finance/' ) ) {
	$the_class = 'cb-ticker-x3--pf';
} elseif ( 0 === strpos( $normalized_path, '/investors/' ) ) {
	$the_class = 'cb-ticker-x3--inv';
}

$stats = array();

for ( $index = 1; $index <= 3; $index++ ) {
	$stats[] = array(
		'title'     => get_field( 'title_' . $index ),
		'prefix'    => get_field( 'prefix_' . $index ),
		'value'     => get_field( 'number_' . $index ),
		'suffix'    => get_field( 'suffix_' . $index ),
		'posttitle' => get_field( 'post_title_' . $index ),
	);
}

// Bail if nothing has been entered in any stat.
$has_content = array_reduce(
	$stats,
	function ( $carry, $stat ) {
		return $carry
			|| '' !== (string) $stat['title']
			|| '' !== (string) $stat['prefix']
			|| '' !== (string) $stat['value']
			|| '' !== (string) $stat['suffix']
			|| '' !== (string) $stat['posttitle'];
	},
	false
);

if ( ! $has_content ) {
	return;
}
?>
<section id="<?= esc_attr( $block_id ); ?>" class="cb-ticker-x3 <?= esc_attr( $the_class ); ?>">
	<div class="container">
		<div class="row justify-content-center">
			<?php
			foreach ( $stats as $stat ) {
				?>
			<div class="col-md-4 p-5">
				<div class="cb-ticker-x3__item text-center">
					<?php
					if ( '' !== (string) $stat['title'] ) {
						?>
						<div class="cb-ticker-x3__title"><?= esc_html( $stat['title'] ); ?></div>
						<?php
					}
					?>
					<div class="cb-ticker-x3__stat">
						<?php
						if ( '' !== (string) $stat['prefix'] ) {
							?>
							<span class="cb-ticker-x3__stat-prefix"><?= esc_html( $stat['prefix'] ); ?></span>
							<?php
						}
						?>
						<span class="cb-ticker-x3__stat-value" data-stat-target="<?= esc_attr( is_numeric( $stat['value'] ) ? $stat['value'] : 0 ); ?>">0</span>
						<?php
						if ( '' !== (string) $stat['suffix'] ) {
							?>
							<span class="cb-ticker-x3__stat-suffix"><?= esc_html( $stat['suffix'] ); ?></span>
							<?php
						}
						?>
					</div>
					<?php
					if ( '' !== (string) $stat['posttitle'] ) {
						?>
						<div class="cb-ticker-x3__posttitle"><?= esc_html( $stat['posttitle'] ); ?></div>
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
