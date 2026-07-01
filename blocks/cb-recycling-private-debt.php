<?php
/**
 * Block template for CB Recycling Private Debt.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$block_id = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-rpd-' );
$context  = cb_get_site_context();
$modifier = '' !== $context ? 'cb-recycling-private-debt--' . $context : '';

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
?>
<section id="<?= esc_attr( $block_id ); ?>" class="cb-recycling-private-debt <?= esc_attr( $modifier . ' ' . $custom_classes ); ?>">
	<div class="container">
		<div class="cb-recycling-private-debt__inner">
			<svg class="cb-recycling-private-debt__svg" viewBox="0 0 1394 745" fill="none" xmlns="http://www.w3.org/2000/svg">
				<defs>
					<linearGradient id="<?= esc_attr( $block_id ); ?>-bar-top" gradientUnits="userSpaceOnUse" x1="238" y1="187.5" x2="1050" y2="187.5">
						<stop offset="0%" stop-color="#fff" />
						<stop offset="100%" stop-color="#81bcb4" />
					</linearGradient>
					<linearGradient id="<?= esc_attr( $block_id ); ?>-bar-bottom" gradientUnits="userSpaceOnUse" x1="239" y1="549.5" x2="1080" y2="549.5">
						<stop offset="0%" stop-color="#0b644a" />
						<stop offset="100%" stop-color="#c2dfdb" />
					</linearGradient>
					<linearGradient id="<?= esc_attr( $block_id ); ?>-bar-top-sm" gradientUnits="userSpaceOnUse" x1="238" y1="263.5" x2="469" y2="263.5">
						<stop offset="0%" stop-color="#fff" />
						<stop offset="100%" stop-color="#f3c588" />
					</linearGradient>
					<linearGradient id="<?= esc_attr( $block_id ); ?>-bar-bottom-sm" gradientUnits="userSpaceOnUse" x1="238" y1="624.5" x2="466" y2="624.5">
						<stop offset="0%" stop-color="#0b644a" />
						<stop offset="100%" stop-color="#e68300" />
					</linearGradient>

					<g id="<?= esc_attr( $block_id ); ?>-worker-a" fill="none" stroke="#114e25" stroke-width="1.5" stroke-miterlimit="10">
						<path d="M493.03,557.14c-4.49,0-8.13-2.14-6.77-8.13l2.71-6.32"/>
						<path d="M510.41,542.69l2.71,6.32c1.13,5.42-2.28,8.13-6.77,8.13"/>
						<rect x="485.47" y="537.05" width="28.45" height="6.1"/>
						<path d="M502.81,524.78c5.66,1.4,9.86,6.51,9.86,12.6"/>
						<path d="M486.71,537.38c0-6.09,4.2-11.2,9.85-12.6"/>
						<path d="M496.49,527.28v-3.78c0-.75.61-1.35,1.35-1.35h3.69c.75,0,1.36.61,1.36,1.35v3.78"/>
						<line x1="490.41" y1="528.13" x2="493.46" y2="532.19"/>
						<line x1="508.7" y1="528.13" x2="505.65" y2="532.19"/>
						<path d="M477.11,575.88v-12.19l10.39-7.68s.45,2.48,4.29,7.45c3.84,4.97,8.13,4.74,8.13,4.74"/>
						<polyline points="481.63 560.08 487.05 565.27 491.11 563.69"/>
						<path d="M522.72,575.88v-12.19l-10.39-7.68s-.45,2.48-4.29,7.45c-3.84,4.97-8.13,4.74-8.13,4.74"/>
						<polyline points="518.2 560.08 512.78 565.27 508.72 563.69"/>
						<line x1="499.91" y1="568.2" x2="499.91" y2="577.68"/>
						<path d="M509.62,542.92c0,9.01-4.45,16.32-9.93,16.32s-9.93-7.31-9.93-16.32"/>
					</g>

					<g id="<?= esc_attr( $block_id ); ?>-worker-b" fill="none" stroke="#114e25" stroke-width="1.5" stroke-miterlimit="10">
						<path d="M578.71,542.69v6.32c0,4.49-3.64,8.13-8.13,8.13h-7.9c-4.49,0-8.13-3.64-8.13-8.13v-6.32"/>
						<rect x="552.41" y="537.05" width="28.45" height="6.1"/>
						<path d="M569.76,524.78c5.66,1.4,9.86,6.51,9.86,12.6"/>
						<path d="M553.65,537.38c0-6.09,4.2-11.2,9.85-12.6"/>
						<path d="M563.43,527.28v-3.78c0-.75.61-1.35,1.35-1.35h3.69c.74,0,1.35.61,1.35,1.35v3.78"/>
						<line x1="557.36" y1="528.13" x2="560.4" y2="532.19"/>
						<line x1="575.64" y1="528.13" x2="572.6" y2="532.19"/>
						<path d="M543.64,575.88v-12.19l10.39-7.68s.45,2.48,4.29,7.45c3.84,4.97,8.13,7.9,8.13,7.9"/>
						<polyline points="548.16 560.08 553.57 567.3 557.64 563.69"/>
						<path d="M589.25,575.88v-12.19l-10.39-7.68s-.45,2.48-4.29,7.45c-3.84,4.97-8.13,7.9-8.13,7.9"/>
						<polyline points="584.73 560.08 579.31 567.3 575.25 563.69"/>
						<line x1="566.44" y1="571.36" x2="566.44" y2="577.68"/>
					</g>

					<g id="<?= esc_attr( $block_id ); ?>-worker-c" fill="none" stroke="#fff" stroke-width="1.5" stroke-miterlimit="10">
						<path d="M395.24,631.14c-4.49,0-8.13-2.14-6.77-8.13l2.71-6.32"/>
						<path d="M412.62,616.69l2.71,6.32c1.13,5.42-2.28,8.13-6.77,8.13"/>
						<rect x="387.68" y="611.05" width="28.45" height="6.1"/>
						<path d="M405.02,598.78c5.66,1.4,9.86,6.51,9.86,12.6"/>
						<path d="M388.92,611.38c0-6.09,4.2-11.2,9.85-12.6"/>
						<path d="M398.7,601.28v-3.78c0-.75.61-1.35,1.35-1.35h3.69c.75,0,1.36.61,1.36,1.35v3.78"/>
						<line x1="392.62" y1="602.13" x2="395.67" y2="606.19"/>
						<line x1="410.91" y1="602.13" x2="407.86" y2="606.19"/>
						<path d="M379.32,649.88v-12.19l10.39-7.68s.45,2.48,4.29,7.45c3.84,4.97,8.13,4.74,8.13,4.74"/>
						<polyline points="383.84 634.08 389.26 639.27 393.32 637.69"/>
						<path d="M424.93,649.88v-12.19l-10.39-7.68s-.45,2.48-4.29,7.45c-3.84,4.97-8.13,4.74-8.13,4.74"/>
						<polyline points="420.41 634.08 414.99 639.27 410.93 637.69"/>
						<line x1="402.13" y1="642.2" x2="402.13" y2="651.68"/>
						<path d="M411.83,616.92c0,9.01-4.45,16.32-9.93,16.32s-9.93-7.31-9.93-16.32"/>
					</g>
				</defs>

				<g id="<?= esc_attr( $block_id ); ?>-section-top">
					<rect x="0" y="0" width="1394" height="362" rx="15" fill="#fff" />
					<rect x="0.5" y="0.5" width="1393" height="361" rx="15" stroke="#0b644a" fill="none" />

					<text x="40" y="54" fill="#0b644a" font-family="Montserrat-Bold,montserrat" font-weight="700" font-size="24">
						<tspan>ASSET CREATION</tspan>
					</text>

					<text x="40" y="98" fill="#114e25" font-family="Montserrat,montserrat" font-size="24">
						<tspan>Impact of reinvesting £50m over 10 years</tspan>
					</text>

					<rect x="238" y="154" width="778" height="67" fill="url(#<?= esc_attr( $block_id ); ?>-bar-top)" data-cb-rpd-bar="top" />
					<circle cx="1016" cy="187.5" r="33.5" fill="url(#<?= esc_attr( $block_id ); ?>-bar-top)" data-cb-rpd-cap="top" />

					<line x1="503.5" y1="159" x2="503.5" y2="215" stroke="#fff" stroke-width="2" />
					<line x1="554.5" y1="159" x2="554.5" y2="215" stroke="#fff" stroke-width="2" />
					<line x1="605.5" y1="159" x2="605.5" y2="215" stroke="#fff" stroke-width="2" />
					<line x1="656.5" y1="159" x2="656.5" y2="215" stroke="#fff" stroke-width="2" />
					<line x1="707.5" y1="159" x2="707.5" y2="215" stroke="#fff" stroke-width="2" />
					<line x1="758.5" y1="159" x2="758.5" y2="215" stroke="#fff" stroke-width="2" />
					<line x1="809.5" y1="159" x2="809.5" y2="215" stroke="#fff" stroke-width="2" />
					<line x1="860.5" y1="159" x2="860.5" y2="215" stroke="#fff" stroke-width="2" />
					<line x1="911.5" y1="159" x2="911.5" y2="215" stroke="#fff" stroke-width="2" />
					<line x1="962.5" y1="159" x2="962.5" y2="215" stroke="#fff" stroke-width="2" />
					<line x1="1014.5" y1="159" x2="1014.5" y2="215" stroke="#fff" stroke-width="2" />

					<text x="249" y="198" fill="#114e25" font-family="Montserrat-SemiBold,montserrat" font-weight="600" font-size="23">
						<tspan>PRIVATE DEBT</tspan>
					</text>

					<rect x="238" y="230" width="197" height="67" fill="url(#<?= esc_attr( $block_id ); ?>-bar-top-sm)" />
					<circle cx="435" cy="263.5" r="33.5" fill="url(#<?= esc_attr( $block_id ); ?>-bar-top-sm)" />

					<text x="249" y="274" fill="#646464" font-family="Montserrat-SemiBold,montserrat" font-weight="600" font-size="23">
						<tspan>BUY &amp; HOLD</tspan>
					</text>

					<text x="507" y="261" fill="#646464" font-family="Montserrat-Bold,montserrat" font-weight="700" font-size="30">
						<tspan>10</tspan>
					</text>
					<text x="485" y="288" fill="#646464" font-family="Montserrat-Regular,montserrat" font-size="21">
						<tspan>Homes</tspan>
					</text>

					<g>
						<circle cx="587" cy="282" r="3" fill="none" stroke="#8d8d8d" stroke-width="2" />
						<circle cx="598.5" cy="282" r="3" fill="none" stroke="#8d8d8d" stroke-width="2" />
						<circle cx="610" cy="282" r="3" fill="none" stroke="#8d8d8d" stroke-width="2" />
						<path d="M587,289c-4,0-7-3-7-7s3-7,7-7h23c4,0,7,3,7,7s-3,7-7,7h-23Z" fill="none" stroke="#8d8d8d" stroke-width="2" />
						<rect x="585" y="270" width="25" height="5" fill="none" stroke="#8d8d8d" stroke-width="2" stroke-linejoin="round" />
						<polygon points="604 258 595 258 595 270 610 270 604 258" fill="none" stroke="#8d8d8d" stroke-width="2" stroke-linejoin="round" />
						<line x1="598" y1="258" x2="616" y2="238" stroke="#8d8d8d" stroke-width="2" />
						<line x1="607" y1="264" x2="620" y2="240" stroke="#8d8d8d" stroke-width="2" />
						<line x1="621" y1="240" x2="632" y2="253" stroke="#8d8d8d" stroke-width="2" stroke-linecap="round" />
						<line x1="625" y1="237" x2="635" y2="250" stroke="#8d8d8d" stroke-width="2" stroke-linecap="round" />
						<path d="M634,258l-14,15h13c4,0,7-2,7-6v-10" fill="none" stroke="#8d8d8d" stroke-width="2" stroke-linejoin="round" />
						<circle cx="636" cy="255" r="5" fill="none" stroke="#8d8d8d" stroke-width="2" />
						<circle cx="620" cy="236" r="5" fill="none" stroke="#8d8d8d" stroke-width="2" />
						<path d="M589,270v-15c0-2-1-3-3-3h-3" fill="none" stroke="#8d8d8d" stroke-width="2" />
						<polygon points="598 267 598 261 602 261 605 267 598 267" fill="none" stroke="#8d8d8d" stroke-width="2" stroke-linejoin="round" />
					</g>

					<text x="91" y="216" fill="#0b644a" font-family="Montserrat-Regular,montserrat" font-size="28">
						<tspan>Year</tspan>
					</text>

					<text x="110" y="259" fill="#0b644a" font-family="Montserrat-SemiBold,montserrat" font-weight="600" font-size="41" data-cb-rpd-year="top">0</text>

					<g data-cb-rpd-arrow="top">
						<path d="M125.81,312.85c-29.68,0-58.58-15.39-74.46-42.89-23.66-40.98-9.57-93.57,31.41-117.22,32.58-18.81,73.11-14.4,100.84,10.98l-4.73,5.16c-25.47-23.31-62.69-27.36-92.61-10.08-37.63,21.73-50.57,70.02-28.85,107.66,21.73,37.63,70.03,50.57,107.66,28.84,21.96-12.68,36.56-35.33,39.04-60.6l6.97.69c-2.71,27.51-18.6,52.17-42.51,65.98-13.48,7.78-28.22,11.48-42.76,11.48Z" fill="#0b644a"/>
						<polygon points="164.22 173.94 197.03 185.84 190.93 151.47 164.22 173.94" fill="#0b644a"/>
					</g>

					<text data-cb-rpd-result="top" class="cb-rpd-result" x="1071" y="181" fill="#0b644a" font-family="Montserrat-Bold,montserrat" font-weight="700" font-size="30">0</text>
					<text x="1066" y="208" fill="#0b644a" font-family="Montserrat-Regular,montserrat" font-size="21">
						<tspan>Homes</tspan>
					</text>

					<!-- Connector -->
					<circle cx="1169" cy="202" r="3" fill="none" stroke="#114e25" stroke-width="2" />
					<circle cx="1181" cy="202" r="3" fill="none" stroke="#114e25" stroke-width="2" />
					<circle cx="1192" cy="202" r="3" fill="none" stroke="#114e25" stroke-width="2" />
					<path d="M1169,209c-4,0-7-3-7-7s3-7,7-7h23c4,0,7,3,7,7s-3,7-7,7h-23Z" fill="none" stroke="#114e25" stroke-width="2"/>
					<rect x="1167" y="190" width="25" height="5" fill="none" stroke="#114e25" stroke-width="2" stroke-linejoin="round"/>
					<polygon points="1186 178 1177 178 1177 190 1192 190 1186 178" fill="none" stroke="#114e25" stroke-width="2" stroke-linejoin="round"/>
					<line x1="1180" y1="178" x2="1198" y2="158" stroke="#114e25" stroke-width="2" />
					<line x1="1190" y1="184" x2="1202" y2="160" stroke="#114e25" stroke-width="2" />
					<line x1="1204" y1="160" x2="1214" y2="173" stroke="#114e25" stroke-width="2" stroke-linecap="round" />
					<line x1="1207" y1="157" x2="1217" y2="170" stroke="#114e25" stroke-width="2" stroke-linecap="round" />
					<path d="M1216,178l-14,15h13c4,0,7-2,7-6v-10" fill="none" stroke="#114e25" stroke-width="2" stroke-linejoin="round"/>
					<circle cx="1219" cy="174" r="5" fill="none" stroke="#114e25" stroke-width="2" />
					<circle cx="1203" cy="155" r="5" fill="none" stroke="#114e25" stroke-width="2" />
					<path d="M1171,190v-15c0-2-1-3-3-3h-3" fill="none" stroke="#114e25" stroke-width="2" />
					<polygon points="1180 187 1180 181 1184 181 1187 187 1180 187" fill="none" stroke="#114e25" stroke-width="2" stroke-linejoin="round"/>
				</g>

				<!-- ===================== BOTTOM SECTION ===================== -->
				<g id="<?= esc_attr( $block_id ); ?>-section-bottom" transform="translate(0, 20)">
					<rect x="0" y="363" width="1394" height="362" rx="15" fill="#0b644a" />
					<rect x="0.5" y="363.5" width="1393" height="361" rx="15" stroke="#0b644a" fill="none" />

					<text x="40" y="416" fill="#fff" font-family="Montserrat-Bold,montserrat" font-weight="700" font-size="24">
						<tspan>JOB CREATION</tspan>
					</text>

					<text x="40" y="460" fill="#fff" font-size="24">
						<tspan>Construction jobs created (£50m invested over 10 year period)</tspan>
					</text>

					<rect x="239" y="516" width="808" height="67" fill="url(#<?= esc_attr( $block_id ); ?>-bar-bottom)" data-cb-rpd-bar="bottom" />
					<circle cx="1046" cy="549.5" r="33.5" fill="url(#<?= esc_attr( $block_id ); ?>-bar-bottom)" data-cb-rpd-cap="bottom" />

					<text x="249" y="560" fill="#fff" font-family="Montserrat-SemiBold,montserrat" font-weight="600" font-size="23">
						<tspan>PRIVATE DEBT</tspan>
					</text>

					<!-- Worker icons along the Private Debt bar -->
					<use href="#<?= esc_attr( $block_id ); ?>-worker-a" />
					<use href="#<?= esc_attr( $block_id ); ?>-worker-b" />
					<use href="#<?= esc_attr( $block_id ); ?>-worker-a" transform="translate(127, 0)" />
					<use href="#<?= esc_attr( $block_id ); ?>-worker-b" transform="translate(127, 0)" />
					<use href="#<?= esc_attr( $block_id ); ?>-worker-a" transform="translate(254, 0)" />
					<use href="#<?= esc_attr( $block_id ); ?>-worker-b" transform="translate(254, 0)" />
					<use href="#<?= esc_attr( $block_id ); ?>-worker-a" transform="translate(382, 0)" />
					<use href="#<?= esc_attr( $block_id ); ?>-worker-b" transform="translate(382, 0)" />
					<use href="#<?= esc_attr( $block_id ); ?>-worker-a" transform="translate(509, 0)" />

					<rect x="238" y="591" width="194" height="67" fill="url(#<?= esc_attr( $block_id ); ?>-bar-bottom-sm)" />
					<circle cx="432" cy="624.5" r="33.5" fill="url(#<?= esc_attr( $block_id ); ?>-bar-bottom-sm)" />

					<text x="249" y="635" fill="#fff" font-family="Montserrat-SemiBold,montserrat" font-weight="600" font-size="23">
						<tspan>BUY &amp; HOLD</tspan>
					</text>

					<!-- Faded worker icon on Equity bar -->
					<g opacity="0.42">
						<use href="#<?= esc_attr( $block_id ); ?>-worker-c" />
					</g>

					<text data-cb-rpd-result="equity" x="488" y="624" fill="#fff" font-family="Montserrat-Bold,montserrat" font-weight="700" font-size="30">0</text>
					<text x="488" y="650" fill="#fff" font-family="Montserrat-Regular,montserrat" font-size="21">
						<tspan>Jobs</tspan>
					</text>
					<g>
						<g>
							<path d="M616.78,617.16c-1.17.03-2.24-1.8-2.38-4.09-.14-2.28.82-3.95,2.1-4.74l17.33-8.17c3.16-.96,5.93,1.36,6.17,5.17.23,3.81-.83,6.83-4.32,7.75l-18.9,4.07Z" fill="#0b644a" stroke="#fff" stroke-width="2" stroke-miterlimit="10" />
							<path d="M618.65,612.97c.14,2.29-.7,4.16-1.87,4.19-1.17.03-2.24-1.8-2.38-4.09-.14-2.28.7-4.16,1.87-4.19,1.17-.03,2.24,1.8,2.38,4.09Z" fill="#0b644a" stroke="#fff" stroke-width="2" stroke-miterlimit="10" />
						</g>
						<polyline points="601.2 627.71 595.49 618.63 559.76 644.95 612.02 644.95 604.33 633" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
						<path d="M596.08,630.92s5.23-2.61,7.38-3.75c4.25-2.25,2.12-9,7.87-11.75,5.75-2.75,4.5-2.12,4.5-2.12" fill="none" stroke="#fff" stroke-width="2" stroke-miterlimit="10" />
					</g>

					<text x="91" y="575" fill="#dbecea" font-family="Montserrat-Regular,montserrat" font-size="28">
						<tspan>Year</tspan>
					</text>

					<text x="110" y="618" fill="#fff" font-family="Montserrat-SemiBold,montserrat" font-weight="600" font-size="41" data-cb-rpd-year="bottom">0</text>

					<g data-cb-rpd-arrow="bottom">
						<path d="M125.81,671.33c-29.68,0-58.58-15.39-74.46-42.89-23.66-40.98-9.57-93.57,31.41-117.22,32.58-18.81,73.11-14.4,100.84,10.98l-4.73,5.16c-25.47-23.31-62.69-27.36-92.61-10.08-37.63,21.73-50.57,70.02-28.85,107.66,21.73,37.63,70.03,50.57,107.66,28.84,21.96-12.68,36.56-35.33,39.04-60.6l6.97.69c-2.71,27.51-18.6,52.17-42.51,65.98-13.48,7.78-28.22,11.48-42.76,11.48Z" fill="#fff"/>
						<polygon points="164.22 532.42 197.03 544.32 190.93 509.96 164.22 532.42" fill="#fff"/>
					</g>

					<text data-cb-rpd-result="bottom" class="cb-rpd-result" x="1098" y="543" fill="#fff" font-family="Montserrat-Bold,montserrat" font-weight="700" font-size="30">0</text>
					<text x="1113" y="570" fill="#fff" font-family="Montserrat-Regular,montserrat" font-size="21">
						<tspan>Jobs</tspan>
					</text>
					<g>
						<g>
							<path d="M1237.45,536.49c-1.17.03-2.24-1.8-2.38-4.09-.14-2.28.82-3.95,2.1-4.74l17.33-8.17c3.16-.96,5.93,1.36,6.17,5.17.23,3.81-.83,6.83-4.32,7.75l-18.9,4.07Z" fill="#0b644a" stroke="#fff" stroke-width="2" stroke-miterlimit="10" />
							<path d="M1239.32,532.3c.14,2.29-.7,4.16-1.87,4.19-1.17.03-2.24-1.8-2.38-4.09-.14-2.28.7-4.16,1.87-4.19,1.17-.03,2.24,1.8,2.38,4.09Z" fill="#0b644a" stroke="#fff" stroke-width="2" stroke-miterlimit="10" />
						</g>
						<polyline points="1221.87 547.05 1216.16 537.96 1180.43 564.28 1232.69 564.28 1225 552.33" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
						<path d="M1216.75,550.25s5.23-2.61,7.38-3.75c4.25-2.25,2.12-9,7.88-11.75s4.5-2.12,4.5-2.12" fill="none" stroke="#fff" stroke-width="2" stroke-miterlimit="10" />
					</g>
				</g>
			</svg>
		</div>
	</div>
</section>
<script>
(() => {
	const root = document.getElementById(<?= wp_json_encode( $block_id ); ?>);
	if (!root) return;

	const init = () => {
		if (!window.gsap) return false;

		const r = 33.5;
		const barTop = root.querySelector('[data-cb-rpd-bar="top"]');
		const capTop = root.querySelector('[data-cb-rpd-cap="top"]');
		const barBottom = root.querySelector('[data-cb-rpd-bar="bottom"]');
		const capBottom = root.querySelector('[data-cb-rpd-cap="bottom"]');
		const yearTop = root.querySelector('[data-cb-rpd-year="top"]');
		const yearBottom = root.querySelector('[data-cb-rpd-year="bottom"]');
		const arrowTop = root.querySelector('[data-cb-rpd-arrow="top"]');
		const arrowBottom = root.querySelector('[data-cb-rpd-arrow="bottom"]');
		const resultTop = root.querySelector('[data-cb-rpd-result="top"]');
		const resultBottom = root.querySelector('[data-cb-rpd-result="bottom"]');
		const resultEquity = root.querySelector('[data-cb-rpd-result="equity"]');

		if (!barTop || !capTop || !barBottom || !capBottom || !yearTop || !yearBottom || !arrowTop || !arrowBottom || !resultTop || !resultBottom || !resultEquity) return false;

		const resultTopTarget = 3000;
		const resultBottomTarget = 8456;
		const resultEquityTarget = 256;
		const animDuration = 5;
		const animEase = 'power1.out';

		const updateBar = (rect, circle, w, x) => {
			const cw = Math.min(w, r);
			rect.setAttribute('width', w);
			circle.setAttribute('cx', x + w);
			circle.setAttribute('r', cw);
		};

		const countUp = (el, target) => {
			if (!target) return;
			const state = { value: 0 };
			window.gsap.to(state, {
				value: target,
				duration: animDuration,
				ease: animEase,
				onUpdate: () => {
					el.textContent = Math.round(state.value).toLocaleString();
				},
				onComplete: () => {
					el.textContent = target.toLocaleString();
				},
			});
		};

		const countUpYear = (el) => {
			const state = { value: 0 };
			window.gsap.to(state, {
				value: 10,
				duration: animDuration,
				ease: animEase,
				onUpdate: () => {
					el.textContent = String(Math.round(state.value));
				},
				onComplete: () => {
					el.textContent = '10';
				},
			});
		};

		const getArrowCenter = (el) => {
			const bb = el.getBBox();
			return { cx: bb.x + bb.width / 2, cy: bb.y + bb.height / 2 };
		};

		const setInitialArrow = (el) => {
			const c = getArrowCenter(el);
			el.setAttribute('transform', 'rotate(0, ' + c.cx + ', ' + c.cy + ')');
		};

		setInitialArrow(arrowTop);
		setInitialArrow(arrowBottom);

		const rotateArrow = (el) => {
			const c = getArrowCenter(el);
			const proxy = { angle: 0 };
			window.gsap.to(proxy, {
				angle: 360,
				duration: animDuration,
				ease: animEase,
				onUpdate: () => {
					el.setAttribute('transform', 'rotate(' + proxy.angle + ', ' + c.cx + ', ' + c.cy + ')');
				},
			});
		};

		const tl = window.gsap.timeline({ paused: true });

		const topProxy = { w: 0 };
		tl.to(topProxy, {
			w: 778,
			duration: animDuration,
			ease: animEase,
			onUpdate: () => updateBar(barTop, capTop, topProxy.w, 238),
		}, 0);
		const bottomProxy = { w: 0 };
		tl.to(bottomProxy, {
			w: 808,
			duration: animDuration,
			ease: animEase,
			onUpdate: () => updateBar(barBottom, capBottom, bottomProxy.w, 239),
		}, 0);

		tl.add(() => countUpYear(yearTop), 0);
		tl.add(() => countUpYear(yearBottom), 0);

		tl.add(() => rotateArrow(arrowTop), 0);
		tl.add(() => rotateArrow(arrowBottom), 0);

		tl.add(() => countUp(resultTop, resultTopTarget), 0);
		tl.add(() => countUp(resultBottom, resultBottomTarget), 0);
		tl.add(() => countUp(resultEquity, resultEquityTarget), 0);

		if (window.IntersectionObserver) {
			const observer = new IntersectionObserver((entries) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						tl.play();
						observer.disconnect();
					}
				});
			}, { threshold: 0.2 });
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
