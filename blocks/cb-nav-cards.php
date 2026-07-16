<?php
/**
 * Block template for CB Nav Cards.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

// URL-based preset detection (mirrors cb-text-image.php / cb-ticker-x3.php).
$context = cb_get_site_context();

if ( 'pf' === $context ) {
	$preset           = 'cb-nav-cards--property-finance';
} elseif ( 'inv' === $context ) {
	$preset           = 'cb-nav-cards--investors';
} else {
	// Block is only meaningful inside a silo; render nothing elsewhere.
	return;
}

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


?>
<div class="cb-nav-cards <?= esc_attr( $preset ); ?> <?= esc_attr( $custom_classes ); ?>">
	<div class="container py-5">
		<div class="row g-4">
			<?php
			$cards      = get_field( 'cards' );
			$card_count = is_array( $cards ) ? count( $cards ) : 0;
			$col_class  = 'col-lg-4';

			if ( $card_count > 0 && 0 === $card_count % 4 ) {
				$col_class = 'col-lg-3';
			}

			while ( have_rows( 'cards' ) ) {
				the_row();
				$card_link = get_sub_field( 'link' );

				// `image` is an ACF image field (return format: array).
				$card_image     = get_sub_field( 'image' );
				$card_image_url = '';
				if ( is_array( $card_image ) && ! empty( $card_image['url'] ) ) {
					$card_image_url = $card_image['url'];
				} elseif ( is_numeric( $card_image ) ) {
					$card_image_url = wp_get_attachment_image_url( $card_image, 'full' );
				} elseif ( is_string( $card_image ) ) {
					$card_image_url = $card_image;
				}

				$bg_style     = $card_image_url ? 'background-image: url(' . esc_url( $card_image_url ) . ');' : '';
				$card_classes = 'cb-nav-cards__card';
				if ( $bg_style ) {
					$card_classes .= ' cb-nav-cards__card--has-bg';
				}

				?>
			<div class="col-12 col-md-6 <?= esc_attr( $col_class ); ?>">
				<a href="<?= esc_url( $card_link['url'] ); ?>" class="<?= esc_attr( $card_classes ); ?>" style="<?= esc_attr( $bg_style ); ?>" target="<?= esc_attr( $card_link['target'] ); ?>">
					<div class="cb-nav-cards__overlay"></div>
					<h3 class="cb-nav-cards__title"><?php the_sub_field( 'title' ); ?></h3>
					<div class="cb-nav-cards__content">
						<div class="full-flourish"></div>
						<div class="cb-nav-cards__link">
							<p class="cb-link-dot">Find out more</p>
						</div>
					</div>
				</a>
			</div>
				<?php
			}
			?>
		</div>
	</div>
</div>