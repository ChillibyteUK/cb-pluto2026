<?php
/**
 * Block template for CB Definition Cards.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$context = cb_get_site_context();

if ( 'pf' === $context ) {
	$class = 'pf';
} elseif ( 'inv' === $context ) {
	$class = 'investor';
} else {
	return;
}

$block_id        = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-definition-cards-' );
$section_classes = array( 'cb-definition-cards--' . $class );

// Support Gutenberg color picker.
$section_classes[] = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$section_classes[] = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';


if ( ! empty( $block['className'] ) ) {
	$section_classes[] = $block['className'];
} else {
	$section_classes[] = 'py-5';
}

?>
<section
	id="<?php echo esc_attr( $block_id ); ?>"
	class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>"
>
	<div class="container cb-definition-cards__inner">
		<?php
		if ( get_field( 'title' ) ) {
			?>
		<h2 class="cb-definition-cards__title text-center pb-4"><?= wp_kses_post( get_field( 'title' ) ); ?></h2>
			<?php
		}
		?>
		<div class="row">
		<?php
		if ( have_rows( 'cards' ) ) {
			while ( have_rows( 'cards' ) ) {
				the_row();
				$card_link = get_sub_field( 'link' );
				$has_link  = ! empty( $card_link['url'] );
				?>
				<div class="cb-definition-cards__item col-12 col-md-6 col-lg-4">
					<?php if ( $has_link ) : ?>
					<a class="cb-definition-cards__card cb-definition-cards__card--link" href="<?= esc_url( $card_link['url'] ); ?>" target="<?= esc_attr( ! empty( $card_link['target'] ) ? $card_link['target'] : '_self' ); ?>">
					<?php else : ?>
					<div class="cb-definition-cards__card">
					<?php endif; ?>
					<div class="cb-definition-cards__header">
						<h3><?= wp_kses_post( get_sub_field( 'title' ) ); ?></h3>
					</div>
					<div class="cb-definition-cards__body">
						<?php
						if ( have_rows( 'fields' ) ) {
							?>
						<dl class="cb-definition-cards__list mb-4">
							<?php
							while ( have_rows( 'fields' ) ) {
								the_row();
								$field_term = get_sub_field( 'term' );
								$definition = get_sub_field( 'definition' );
								?>
							<div class="cb-definition-cards__field">
								<?php
								if ( $field_term ) {
									?>
								<dt class="cb-definition-cards__term"><?= esc_html( $field_term ); ?></dt>
									<?php
								}
								if ( $definition ) {
									?>
								<dd class="cb-definition-cards__definition"><?= nl2br( esc_html( $definition ) ); ?></dd>
									<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</dl>
							<?php
						}
						if ( $has_link ) {
							$link_label = ! empty( $card_link['title'] ) ? $card_link['title'] : __( 'Find out more' );
							?>
						<p class="cb-link-dot cb-definition-cards__link"><?= esc_html( $link_label ); ?></p>
							<?php
						}
						?>
					</div>
					<?php if ( $has_link ) : ?>
					</a>
					<?php else : ?>
					</div>
					<?php endif; ?>
				</div>
				<?php
			}
		}
		?>
		</div>
	</div>
</section>