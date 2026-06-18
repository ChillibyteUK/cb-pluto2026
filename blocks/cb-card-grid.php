<?php
/**
 * Block template for CB Card Grid.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$cards = get_field( 'cards' );

if ( empty( $cards ) || ! is_array( $cards ) ) {
	return;
}

$block_id = $block['anchor'] ?? ( 'cb-card-grid-' . uniqid() );
$context  = cb_get_site_context();
$modifier = '' !== $context ? 'cb-card-grid--' . $context : '';

// Extract custom classes (filter out wp-generated ones).
$custom_classes = 'pb-5';
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

$classes = trim( 'cb-card-grid ' . $modifier . ' ' . $custom_classes );

?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( $classes ); ?>">
	<div class="container">
		<div class="row g-5 align-items-stretch">
			<?php
			$item_index = 0;
			foreach ( $cards as $card ) {
				$layout = $card['acf_fc_layout'] ?? '';
				$side   = ( 0 === $item_index % 2 ) ? 'left' : 'right';
				$aos    = ( 'left' === $side ) ? 'fade-right' : 'fade-left';
				?>
				<div class="col-md-6 cb-card-grid__col cb-card-grid__col--<?= esc_attr( $side ); ?>">
					<?php
					if ( 'card' === $layout ) {
						$title   = $card['title'] ?? '';
						$content = $card['content'] ?? '';
						?>
						<div class="cb-card-grid__card" data-aos="<?= esc_attr( $aos ); ?>">
							<?php if ( '' !== trim( (string) $title ) ) : ?>
								<h3 class="cb-card-grid__title"><?= esc_html( $title ); ?></h3>
							<?php endif; ?>
							<?php if ( '' !== trim( (string) $content ) ) : ?>
								<div class="cb-card-grid__content"><?= wp_kses_post( $content ); ?></div>
							<?php endif; ?>
						</div>
						<?php
					} elseif ( 'image' === $layout ) {
						$image_id   = (int) ( $card['image'] ?? 0 );
						$full_bleed = ! empty( $card['full_bleed'] );
						$image_classes = array( 'cb-card-grid__image' );
						if ( $full_bleed ) {
							$image_classes[] = 'cb-card-grid__image--full-bleed';
							$image_classes[] = 'cb-card-grid__image--bleed-' . $side;
						}
						?>
						<div class="<?= esc_attr( implode( ' ', $image_classes ) ); ?>" data-aos="<?= esc_attr( $aos ); ?>">
							<?php
							if ( $image_id > 0 ) {
								echo wp_get_attachment_image( $image_id, 'large' );
							}
							?>
						</div>
						<?php
					}
					?>
				</div>
				<?php
				++$item_index;
			}
			?>
		</div>
	</div>
</section>
