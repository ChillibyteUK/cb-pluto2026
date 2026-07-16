<?php
/**
 * Block template for CB Show Hide Cards.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$cards   = get_field( 'cards' );
$columns = (int) get_field( 'columns' );

if ( empty( $cards ) || ! is_array( $cards ) ) {
	return;
}

$block_id        = $block['anchor'] ?? wp_unique_id( 'cb-show-hide-cards-' );
$columns         = in_array( $columns, array( 2, 3 ), true ) ? $columns : 2;
$column_class    = 3 === $columns ? 'col-12' : 'col-12';
$section_classes = array( 'cb-show-hide-cards' );

$custom_classes = 'py-5';
if ( isset( $block['className'] ) ) {
	$class_array    = explode( ' ', $block['className'] );
	$filtered       = array_filter(
		$class_array,
		static function ( $item ) {
			return ! preg_match( '/^wp-/', $item );
		}
	);
	$custom_classes = implode( ' ', $filtered );
}

if ( '' !== trim( $custom_classes ) ) {
	$section_classes[] = trim( $custom_classes );
}

if ( ! empty( $block['backgroundColor'] ) ) {
	$section_classes[] = 'has-' . $block['backgroundColor'] . '-background-color';
	$section_classes[] = 'has-background';
}

?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="container">
		<div class="row g-4 align-items-stretch">
			<?php
			foreach ( $cards as $index => $card ) {
				$card_title   = $card['card_title'] ?? '';
				$card_image   = $card['card_image'] ?? '';
				$content      = $card['card_content'] ?? '';
				$content_type = $card['card_content_type'] ?? 'plain';
				$small_print  = $card['card_small_print'] ?? '';

				if ( '' === trim( $card_title ) && '' === trim( $content ) ) {
					continue;
				}

				$card_uid    = $block_id . '-card-' . $index;
				$collapse_id = $card_uid . '-collapse';
				$aos_delay   = ( $index % $columns ) * 100;
				?>
				<div class="<?= esc_attr( $column_class ); ?> cb-show-hide-cards__col" data-aos="fade-up" data-aos-delay="<?= esc_attr( $aos_delay ); ?>">
					<div class="cb-show-hide-cards__card">
						<?php if ( '' !== trim( $card_title ) ) { ?>
							<button type="button"
								class="cb-show-hide-cards__toggle"
								data-bs-toggle="collapse"
								data-bs-target="#<?= esc_attr( $collapse_id ); ?>"
								aria-expanded="false"
								aria-controls="<?= esc_attr( $collapse_id ); ?>">
								<?php if ( $card_image ) { ?>
									<img class="cb-show-hide-cards__toggle-image" src="<?= esc_url( $card_image ); ?>" alt="" loading="lazy">
								<?php } else { ?>
									<span class="cb-show-hide-cards__toggle-icon" aria-hidden="true"></span>
								<?php } ?>
								<?= esc_html( $card_title ); ?>
							</button>
						<?php } ?>
						<div class="collapse cb-show-hide-cards__collapse" id="<?= esc_attr( $collapse_id ); ?>">
							<div class="cb-show-hide-cards__collapse-inner">
								<div class="cb-show-hide-cards__body">
									<?php
									if ( 'bullets' === $content_type ) {
										echo '<ul class="cb-show-hide-cards__list">' . wp_kses_post( cb_list( $content ) ) . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									} else {
										echo wp_kses_post( wpautop( $content ) );
									}
									?>
								</div>
								<?php if ( '' !== trim( (string) $small_print ) ) { ?>
									<div class="cb-show-hide-cards__card-small-print"><?= wp_kses_post( wpautop( $small_print ) ); ?></div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</section>
