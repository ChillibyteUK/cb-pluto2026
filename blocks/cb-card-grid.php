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

$block_id        = $block['anchor'] ?? ( 'cb-card-grid-' . uniqid() );
$context         = cb_get_site_context();
$modifier        = '' !== $context ? 'cb-card-grid--' . $context : '';
$columns         = '3' === (string) get_field( 'columns' ) ? 3 : 2;
$column_class    = 3 === $columns ? 'col-md-6 col-lg-4' : 'col-md-6';
$section_classes = array( 'cb-card-grid', 'cb-card-grid--' . $columns . '-col' );
$section_style   = '';

if ( '' !== $modifier ) {
	$section_classes[] = $modifier;
}

$has_non_white_background = false;

if ( ! empty( $block['backgroundColor'] ) ) {
	$section_classes[]        = 'has-' . $block['backgroundColor'] . '-background-color';
	$section_classes[]        = 'has-background';
	$has_non_white_background = 'white' !== $block['backgroundColor'];
}

if ( ! empty( $block['textColor'] ) ) {
	$section_classes[] = 'has-' . $block['textColor'] . '-color';
	$section_classes[] = 'has-text-color';
}

$custom_bg = $block['style']['color']['background'] ?? '';
$custom_fg = $block['style']['color']['text'] ?? '';
if ( $custom_bg ) {
	$section_style           .= 'background-color:' . $custom_bg . ';';
	$section_classes[]        = 'has-background';
	$normalised_custom_bg     = strtolower( trim( (string) $custom_bg ) );
	$has_non_white_background = ! in_array( $normalised_custom_bg, array( '#fff', '#ffffff', 'white', 'rgb(255,255,255)', 'rgb(255 255 255)' ), true );
}
if ( $custom_fg ) {
	$section_style    .= 'color:' . $custom_fg . ';';
	$section_classes[] = 'has-text-color';
}

if ( $has_non_white_background ) {
	$section_classes[] = 'cb-card-grid--white-cards';
}

// Extract custom classes (filter out wp-generated ones).
$custom_classes = 'py-5';
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

if ( '' !== trim( $custom_classes ) ) {
	$section_classes[] = trim( $custom_classes );
}

$classes = trim( implode( ' ', array_filter( $section_classes ) ) );

?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( $classes ); ?>"<?= $section_style ? ' style="' . esc_attr( $section_style ) . '"' : ''; ?>>
	<div class="container">
		<div class="row g-5 align-items-stretch">
			<?php
			$item_index = 0;
			foreach ( $cards as $card ) {
				$layout = $card['acf_fc_layout'] ?? '';
				$side   = ( 0 === $item_index % 2 ) ? 'left' : 'right';
				$aos    = ( 'left' === $side ) ? 'fade-right' : 'fade-left';
				?>
				<div class="<?= esc_attr( $column_class ); ?> cb-card-grid__col cb-card-grid__col--<?= esc_attr( $side ); ?>">
					<?php
					if ( 'card' === $layout ) {
						$card_title = $card['title'] ?? '';
						$content    = $card['content'] ?? '';
						?>
						<div class="cb-card-grid__card" data-aos="<?= esc_attr( $aos ); ?>">
							<?php if ( '' !== trim( (string) $card_title ) ) : ?>
								<h3 class="cb-card-grid__title"><?= esc_html( $card_title ); ?></h3>
							<?php endif; ?>
							<?php if ( '' !== trim( (string) $content ) ) : ?>
								<div class="cb-card-grid__content"><?= wp_kses_post( $content ); ?></div>
							<?php endif; ?>
						</div>
						<?php
					} elseif ( 'image' === $layout ) {
						$image_id      = (int) ( $card['image'] ?? 0 );
						$full_bleed    = 2 === $columns && ! empty( $card['full_bleed'] );
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
