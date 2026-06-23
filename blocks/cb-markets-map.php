<?php
/**
 * Block template for CB Markets Map.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

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

$block_uid = ! empty( $block['anchor'] ) ? $block['anchor'] : ( 'markets-map-' . uniqid() );

$acf_markets = get_field( 'markets' );
$markets     = array();

if ( ! empty( $acf_markets ) && is_array( $acf_markets ) ) {
	foreach ( $acf_markets as $item ) {
		$key          = $item['market_key'] ?? '';
		$title        = $item['market_title'] ?? '';
		$body         = $item['market_body'] ?? '';
		$markets[ $key ] = array(
			'label' => $title,
			'title' => $title,
			'body'  => $body,
		);
	}
}

if ( empty( $markets ) ) {
	$markets = array(
		'GB'    => array(
			'label' => __( 'United Kingdom', 'cb-pluto2026' ),
			'title' => __( 'United Kingdom', 'cb-pluto2026' ),
			'body'  => __( 'Add market copy for the United Kingdom here.', 'cb-pluto2026' ),
		),
		'IE'    => array(
			'label' => __( 'Ireland', 'cb-pluto2026' ),
			'title' => __( 'Ireland', 'cb-pluto2026' ),
			'body'  => __( 'Add market copy for Ireland here.', 'cb-pluto2026' ),
		),
		'NL'    => array(
			'label' => __( 'Netherlands', 'cb-pluto2026' ),
			'title' => __( 'Netherlands', 'cb-pluto2026' ),
			'body'  => __( 'Add market copy for the Netherlands here.', 'cb-pluto2026' ),
		),
		'DE'    => array(
			'label' => __( 'Germany', 'cb-pluto2026' ),
			'title' => __( 'Germany', 'cb-pluto2026' ),
			'body'  => __( 'Add market copy for Germany here.', 'cb-pluto2026' ),
		),
		'ES_PT' => array(
			'label' => __( 'Spain and Portugal', 'cb-pluto2026' ),
			'title' => __( 'Spain and Portugal', 'cb-pluto2026' ),
			'body'  => __( 'Add market copy for Spain and Portugal here.', 'cb-pluto2026' ),
		),
	);
}

$country_keys = array(
	'GB' => 'GB',
	'IE' => 'IE',
	'NL' => 'NL',
	'DE' => 'DE',
	'ES' => 'ES_PT',
	'PT' => 'ES_PT',
);

$map_svg = '';
$map_path = get_theme_file_path( 'img/map.svg' );
if ( file_exists( $map_path ) ) {
	$raw = file_get_contents( $map_path );
	if ( false !== $raw ) {
		$map_svg = preg_replace( '/<defs>.*?<\/defs>/s', '', $raw );
		$map_svg = str_replace( 'class="cls-3"', '', $map_svg );
	}
}

foreach ( $country_keys as $country_id => $market_key ) {
	$market  = $markets[ $market_key ];
	$map_svg = str_replace(
		'id="' . $country_id . '" class="cls-1"',
		'id="' . $country_id . '" class="cb-markets-map__country" data-cb-market="' . esc_attr( $market_key ) . '" data-cb-market-title="' . esc_attr( $market['title'] ) . '" data-cb-market-body="' . esc_attr( $market['body'] ) . '"',
		$map_svg
	);
}
?>

<section id="<?= esc_attr( $block_uid ); ?>" class="<?= esc_attr( trim( 'cb-markets-map ' . $custom_classes ) ); ?>">
	<div class="container">
		<div class="row gy-4 gx-lg-5 align-items-lg-start">
			<div class="col-lg-5 cb-markets-map__content">
				<div class="cb-markets-map__panel">
					<p class="cb-markets-map__eyebrow"><?= esc_html__( 'Explore our markets', 'cb-pluto2026' ); ?></p>
					<h2 class="cb-markets-map__title" data-cb-markets-title><?= esc_html__( 'Explore our markets', 'cb-pluto2026' ); ?></h2>
					<div class="cb-markets-map__body" data-cb-markets-body>
						<p><?= esc_html__( 'Select a highlighted market on the map to learn more.', 'cb-pluto2026' ); ?></p>
					</div>
				</div>
			</div>

			<div class="col-lg-7 cb-markets-map__visual">
				<div class="cb-markets-map__stage">
					<?php if ( '' !== trim( $map_svg ) ) : ?>
						<?= $map_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<script>
		(() => {
			const script = document.currentScript;
			const root = script ? script.closest('.cb-markets-map') : document.getElementById(<?= wp_json_encode( $block_uid ); ?>);
			if (!root) return;

			const title = root.querySelector('[data-cb-markets-title]');
			const body = root.querySelector('[data-cb-markets-body]');
			const countries = root.querySelectorAll('.cb-markets-map__country');

			const setActive = (key) => {
				countries.forEach((el) => {
					el.classList.toggle('is-active', el.getAttribute('data-cb-market') === key);
				});
			};

			const setMarket = (el) => {
				if (!title || !body) return;
				title.textContent = el.getAttribute('data-cb-market-title') || '';
				body.innerHTML = '';

				const b = el.getAttribute('data-cb-market-body');
				if (b) {
					body.innerHTML = b;
				}

				setActive(el.getAttribute('data-cb-market'));
			};

			countries.forEach((el) => {
				el.addEventListener('mouseenter', () => {
					el.classList.add('is-hovered');
				});
				el.addEventListener('mouseleave', () => {
					el.classList.remove('is-hovered');
				});
				el.addEventListener('click', () => setMarket(el));
				el.addEventListener('keydown', (e) => {
					if (e.key !== 'Enter' && e.key !== ' ') return;
					e.preventDefault();
					setMarket(el);
				});
				el.setAttribute('role', 'button');
				el.setAttribute('tabindex', '0');
			});
		})();
	</script>
</section>
