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
		<h2 class="cb-definition-cards__title text-center text-uppercase pb-4"><?= wp_kses_post( get_field( 'title' ) ); ?></h2>
			<?php
		}
		?>
		<div class="row">
		<?php
		if ( have_rows( 'cards' ) ) {
			while ( have_rows( 'cards' ) ) {
				the_row();
				?>
				<div class="cb-definition-cards__item col-12 col-md-6 col-lg-4">
					<div class="cb-definition-cards__card">
					<div class="cb-definition-cards__header">
						<h3><?= wp_kses_post( get_sub_field( 'title' ) ); ?></h3>
					</div>
					<div class="cb-definition-cards__body">
						<?php
						if ( have_rows( 'fields' ) ) {
							?>
						<dl class="cb-definition-cards__list">
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
						?>
					</div>
				</div>
				</div>
				<?php
			}
		}
		?>
		</div>
	</div>
</section>