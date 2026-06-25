<?php
/**
 * Block template for CB Animated Map.
 *
 * Two-column layout mirroring cb-text-image, with authored WYSIWYG content on
 * the left and an animated inline SVG map on the right. The flourish is always
 * present and picks up the site colour variant automatically.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$context = cb_get_site_context();

// Flourish utility classes, matching the brand variants used elsewhere.
$flourish_classes = '';

// Extract custom classes (filter out wp-generated ones).
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

// Support Gutenberg colour picker.
$bg = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';

$block_uid = $block['anchor'] ?? ( 'animated-map-' . uniqid() );
$svg_id    = $block_uid . '-svg';

$section_style = '';
$circle_fill   = '';

if ( ! empty( $block['style']['color']['background'] ) ) {
	$circle_fill    = (string) $block['style']['color']['background'];
	$section_style .= 'background-color:' . $circle_fill . ';';
	$section_style .= '--cb-animated-map-circle-fill:' . $circle_fill . ';';
} elseif ( ! empty( $block['backgroundColor'] ) && function_exists( 'wp_get_global_settings' ) ) {
	$palette = wp_get_global_settings( array( 'color', 'palette', 'theme' ) );
	if ( empty( $palette ) ) {
		$palette = wp_get_global_settings( array( 'color', 'palette', 'default' ) );
	}

	foreach ( (array) $palette as $color ) {
		if ( empty( $color['slug'] ) || empty( $color['color'] ) ) {
			continue;
		}

		if ( sanitize_key( (string) $block['backgroundColor'] ) === sanitize_key( (string) $color['slug'] ) ) {
			$circle_fill    = (string) $color['color'];
			$section_style .= '--cb-animated-map-circle-fill:' . $circle_fill . ';';
			break;
		}
	}
}

$map_svg      = '';
$map_svg_path = get_theme_file_path( 'img/map.svg' );
if ( file_exists( $map_svg_path ) ) {
	$map_svg = file_get_contents( $map_svg_path );
	if ( false !== $map_svg ) {
		$replacements = array(
			'id="Layer_1-2"'          => 'id="' . $svg_id . '__layer-1-2"',
			'id="line"'               => 'id="' . $svg_id . '__line-group"',
			'id="outer-line"'         => 'id="' . $svg_id . '__outer-line"',
			'id="padding-path"'       => 'id="' . $svg_id . '__padding-path"',
			'id="luminosity-noclip"'  => 'id="' . $svg_id . '__luminosity-noclip"',
			'id="linear-gradient"'    => 'id="' . $svg_id . '__linear-gradient"',
			'id="mask"'               => 'id="' . $svg_id . '__mask"',
			'url(#mask)'              => 'url(#' . $svg_id . '__mask)',
			'url(#luminosity-noclip)' => 'url(#' . $svg_id . '__luminosity-noclip)',
			'url(#linear-gradient)'   => 'url(#' . $svg_id . '__linear-gradient)',
			'class="cls-1"'           => 'class="cb-animated-map__svg-country--highlight"',
			'class="cls-2"'           => 'class="cb-animated-map__svg-country--base"',
			'class="cls-3"'           => 'class="cb-animated-map__svg-mask"',
			'class="cls-4"'           => 'class="cb-animated-map__svg-none"',
			'class="cls-5"'           => 'class="cb-animated-map__svg-line"',
			'class="cls-6"'           => 'class="cb-animated-map__svg-gradient"',
			'class="cls-7"'           => 'class="cb-animated-map__svg-filter"',
			'.cls-1'                  => '.cb-animated-map__svg-country--highlight',
			'.cls-2'                  => '.cb-animated-map__svg-country--base',
			'.cls-3'                  => '.cb-animated-map__svg-mask',
			'.cls-4'                  => '.cb-animated-map__svg-none',
			'.cls-5'                  => '.cb-animated-map__svg-line',
			'.cls-6'                  => '.cb-animated-map__svg-gradient',
			'.cls-7'                  => '.cb-animated-map__svg-filter',
		);

		$map_svg = str_replace( array_keys( $replacements ), array_values( $replacements ), $map_svg );
	}
}

$stats = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$group   = get_field( 'stat_' . $i );
	$stats[] = is_array( $group ) ? $group : array();
}
?>

	<section
		id="<?= esc_attr( $block_uid ); ?>"
		class="<?= esc_attr( trim( $flourish_classes . ' cb-animated-map ' . $bg . ' ' . $custom_classes ) ); ?>"
		<?= '' !== trim( $section_style ) ? ' style="' . esc_attr( trim( $section_style ) ) . '"' : ''; ?>
	>
	<div class="container pb-5">
		<div class="row gy-5 gx-4 gx-lg-5 align-items-center">
			<div class="col-xl-6 cb-animated-map__content pe-lg-5" data-aos="fade">
				<div class="cb-animated-map__body">
					<?= wp_kses_post( get_field( 'content' ) ); ?>
				</div>
			</div>

			<div class="col-xl-6 cb-animated-map__visual" data-aos="fade-left">
				<div class="cb-animated-map__stage">
					<?php if ( '' !== trim( $map_svg ) ) : ?>
						<div
							class="cb-animated-map__svg-wrap"
							data-cb-animated-map
						>
							<?= $map_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>

					<div class="cb-animated-map__stats" aria-label="Animated map statistics">
						<?php foreach ( $stats as $index => $stat_group ) : ?>
							<?php
							$before  = isset( $stat_group['before'] ) ? trim( (string) $stat_group['before'] ) : '';
							$prefix  = isset( $stat_group['prefix'] ) ? trim( (string) $stat_group['prefix'] ) : '';
							$stat    = isset( $stat_group['stat'] ) ? trim( (string) $stat_group['stat'] ) : '';
							$suffix  = isset( $stat_group['suffix'] ) ? trim( (string) $stat_group['suffix'] ) : '';
							$after   = isset( $stat_group['after'] ) ? trim( (string) $stat_group['after'] ) : '';
							$stat_id = $svg_id . '__stat-' . ( $index + 1 );
							?>
							<div class="cb-animated-map__stat" data-cb-animated-map-stat>
								<svg class="cb-animated-map__stat-ring-svg" viewBox="0 0 200 200" aria-hidden="true" focusable="false">
									<circle class="cb-animated-map__stat-fill" cx="100" cy="100" r="92"></circle>
									<circle class="cb-animated-map__stat-ring" cx="100" cy="100" r="92"></circle>
								</svg>
								<div class="cb-animated-map__stat-copy" id="<?= esc_attr( $stat_id ); ?>">
									<div class="cb-animated-map__stat-before"><?= wp_kses_post( $before ); ?></div>
									<div class="cb-animated-map__stat-main">
										<?php if ( '' !== $prefix ) : ?>
											<span class="cb-animated-map__stat-prefix"><?= esc_html( $prefix ); ?></span>
										<?php endif; ?>
										<?php if ( '' !== $stat ) : ?>
											<?php $stat_decimal_places = false !== strpos( (string) $stat, '.' ) ? strlen( substr( (string) $stat, strpos( (string) $stat, '.' ) + 1 ) ) : 0; ?>
											<span class="cb-animated-map__stat-value" data-stat-target="<?= esc_attr( $stat ); ?>" data-stat-decimals="<?= esc_attr( $stat_decimal_places ); ?>"><?= esc_html( $stat ); ?></span>
										<?php endif; ?>
										<?php if ( '' !== $suffix ) : ?>
											<span class="cb-animated-map__stat-suffix"><?= esc_html( $suffix ); ?></span>
										<?php endif; ?>
									</div>
									<div class="cb-animated-map__stat-after"><?= wp_kses_post( $after ); ?></div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		(() => {
			const root = document.getElementById(<?= wp_json_encode( $block_uid ); ?>);
			if (!root) return;

			const init = () => {
				if (!window.gsap) {
					return false;
				}

				const map = root.querySelector('[data-cb-animated-map]');
				const line = root.querySelector('#<?= esc_js( $svg_id ); ?>__outer-line');
				const highlights = root.querySelectorAll('.cb-animated-map__svg-country--highlight');
				const statCards = root.querySelectorAll('[data-cb-animated-map-stat]');
				const statFills = root.querySelectorAll('.cb-animated-map__stat-fill');
				const statRings = root.querySelectorAll('.cb-animated-map__stat-ring');
				const statValues = root.querySelectorAll('.cb-animated-map__stat-value');
				if (!map || !line || !highlights.length) {
					return false;
				}

				const countryDuration = 1.5;
				const staggerStep = 0.14;
				const duration = countryDuration + (staggerStep * Math.max(highlights.length - 1, 0));
				const lineDuration = duration * 0.7;
				const baseFill = '#d4e2e2';
				const highlightFill = '#225933';
				const countUpDuration = 1.2;
				const countUpStart = lineDuration;
				const highlightTweenDuration = Math.max(0.1, countUpDuration - (staggerStep * Math.max(highlights.length - 1, 0)));

				const lineLength = line.getTotalLength ? line.getTotalLength() : 0;
				if (lineLength) {
					window.gsap.set(line, {
						strokeDasharray: lineLength,
						strokeDashoffset: lineLength,
					});
				}
				window.gsap.set(highlights, { fill: baseFill });
				if (statRings.length) {
					statRings.forEach((ring) => {
						const length = ring.getTotalLength ? ring.getTotalLength() : 0;
						if (length) {
							window.gsap.set(ring, {
								strokeDasharray: length,
								strokeDashoffset: length,
							});
						}
					});
					window.gsap.set(statCards, { autoAlpha: 0, y: 12 });
					window.gsap.set(statFills, { autoAlpha: 0 });
					window.gsap.set(statValues, { textContent: 0 });
				}

				const animateCountUps = () => {
					statValues.forEach((valueEl) => {
						const targetRaw = parseFloat(valueEl.getAttribute('data-stat-target') || '0');
						const decimals = parseInt(valueEl.getAttribute('data-stat-decimals') || '0', 10) || 0;
						const formatter = new Intl.NumberFormat(undefined, {
							minimumFractionDigits: decimals,
							maximumFractionDigits: decimals,
						});
						const state = { value: 0 };
						window.gsap.to(state, {
							value: targetRaw,
							duration: countUpDuration,
							ease: 'power2.out',
							onUpdate: () => {
								valueEl.textContent = formatter.format(state.value);
							},
							onComplete: () => {
								valueEl.textContent = formatter.format(targetRaw);
							},
						});
					});
				};

				const tl = window.gsap.timeline({ paused: true });
				tl.to(line, {
					strokeDashoffset: 0,
					duration: lineDuration,
					ease: 'power2.out',
				}, 0);
				tl.to(highlights, {
					fill: highlightFill,
					duration: highlightTweenDuration,
					ease: 'back.out(3.2)',
					stagger: {
						each: staggerStep,
						from: 'start',
					},
				}, countUpStart);

				if (statRings.length) {
					tl.to(statCards, {
						autoAlpha: 1,
						y: 0,
						duration,
						ease: 'power2.out',
					}, 0);

					tl.to(statFills, {
						autoAlpha: 1,
						duration,
						ease: 'power2.out',
					}, 0);

					tl.to(statRings, {
						strokeDashoffset: 0,
						duration,
						ease: 'power2.out',
					}, 0);
				}

					tl.add(animateCountUps, countUpStart);

				if (window.IntersectionObserver) {
					const observer = new IntersectionObserver((entries) => {
						entries.forEach((entry) => {
							if (entry.isIntersecting) {
								tl.play();
								observer.disconnect();
							}
						});
					}, { threshold: 0.25 });
					observer.observe(root);
				} else {
					tl.play();
				}

				return true;
			};

			const waitForGsap = () => {
				if (!init()) {
					window.setTimeout(waitForGsap, 50);
				}
			};

			if (document.readyState === 'complete') {
				waitForGsap();
			} else {
				window.addEventListener('load', waitForGsap, { once: true });
			}
		})();
	</script>
</section>
