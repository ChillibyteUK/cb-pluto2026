<?php
/**
 * Block template for CB Text Definition.
 *
 * Two-column layout: text content (left) mirroring the cb-text-image text
 * column, and a single definition card (right) reusing the cb-definition-cards
 * card markup/styles. Supports the brand flourish along the top edge.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$context  = cb_get_site_context();
$flourish = (bool) get_field( 'flourish' );

// Flourish utility classes, mirroring cb-text-image.
$flourish_classes = '';
if ( $flourish ) {
	$flourish_classes = 'full-flourish';
	if ( 'pf' === $context ) {
		$flourish_classes .= ' full-flourish--lending';
	} elseif ( 'inv' === $context ) {
		$flourish_classes .= ' full-flourish--investors';
	}
}

// Card context modifier (drives header colour + flourish sweep colour),
// matching cb-definition-cards.
if ( 'pf' === $context ) {
	$card_modifier = 'cb-definition-cards--pf';
} elseif ( 'inv' === $context ) {
	$card_modifier = 'cb-definition-cards--investor';
} else {
	$card_modifier = '';
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

// Support Gutenberg color picker.
$bg = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';

$block_uid = $block['anchor'] ?? ( 'text-definition-' . uniqid() );
?>
<section
	id="<?= esc_attr( $block_uid ); ?>"
	class="<?= esc_attr( trim( $flourish_classes . ' cb-text-definition ' . $bg . ' ' . $fg . ' ' . $custom_classes ) ); ?>"
>
	<div class="container">
		<div class="row gy-5 gx-4 gx-lg-5 align-items-center">

			<div class="col-md-6 cb-text-definition__text pe-lg-5">
				<?php if ( get_field( 'title' ) ) : ?>
					<h2 class="cb-text-definition__title has-700-font-size mb-4"><?= wp_kses_post( get_field( 'title' ) ); ?></h2>
				<?php endif; ?>
				<div class="cb-text-definition__content">
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
			</div>

			<div class="col-md-6 cb-text-definition__card-col">
				<div class="cb-definition-cards <?= esc_attr( $card_modifier ); ?>">
					<div class="cb-definition-cards__card">
						<div class="cb-definition-cards__header">
							<h3><?= wp_kses_post( get_field( 'card_title' ) ); ?></h3>
						</div>
						<div class="cb-definition-cards__body">
							<?php if ( have_rows( 'fields' ) ) : ?>
								<dl class="cb-definition-cards__list">
									<?php
									while ( have_rows( 'fields' ) ) {
										the_row();
										$field_term = get_sub_field( 'term' );
										$definition = get_sub_field( 'definition' );
										?>
										<div class="cb-definition-cards__field">
											<?php if ( $field_term ) : ?>
												<dt class="cb-definition-cards__term"><?= esc_html( $field_term ); ?></dt>
											<?php endif; ?>
											<?php if ( $definition ) : ?>
												<dd class="cb-definition-cards__definition"><?= nl2br( esc_html( $definition ) ); ?></dd>
											<?php endif; ?>
										</div>
										<?php
									}
									?>
								</dl>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</section>
