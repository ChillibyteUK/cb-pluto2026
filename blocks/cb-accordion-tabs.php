<?php
/**
 * Block template for CB Accordion Tabs.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

/*
ACF field manifest format:
field_name|field_type|values|default_value|wrapper_width|instructions

ACF_FIELDS_START
items|repeater||0|100|Add accordion/tab items.
ACF_FIELDS_END
*/

$items = get_field( 'items' );
$items = is_array( $items ) ? array_values(
	array_filter(
		$items,
		static function ( $item ) {
			$title   = isset( $item['title'] ) ? trim( (string) $item['title'] ) : '';
			$content = isset( $item['content'] ) ? trim( (string) $item['content'] ) : '';

			return '' !== $title || '' !== $content;
		}
	)
) : array();

if ( ! $items ) {
	return;
}

$block_uid = 'cb-accordion-tabs-' . uniqid();

$custom_classes = 'py-5';
if ( isset( $block['className'] ) ) {
	$class_array = explode( ' ', $block['className'] );
	$filtered    = array_filter(
		$class_array,
		static function ( $item ) {
			return ! preg_match( '/^wp-/', $item );
		}
	);
	$custom_classes = implode( ' ', $filtered );
}

$classes = trim( 'cb-accordion-tabs ' . $custom_classes );

$context = cb_get_site_context();
if ( 'pf' === $context ) {
	$classes .= ' cb-accordion-tabs--pf';
} elseif ( 'inv' === $context ) {
	$classes .= ' cb-accordion-tabs--inv';
}

$render_panel = static function ( $item, $panel_id, $title_id ) {
	$title   = isset( $item['title'] ) ? $item['title'] : '';
	$content = isset( $item['content'] ) ? $item['content'] : '';
	?>
	<div class="cb-accordion-tabs__panel" id="<?= esc_attr( $panel_id ); ?>" role="tabpanel" aria-labelledby="<?= esc_attr( $title_id ); ?>" hidden>
		<div class="cb-accordion-tabs__panel-inner">
			<?php if ( '' !== trim( (string) $title ) ) : ?>
				<h3 class="cb-accordion-tabs__panel-title has-700-font-size mb-3"><?= esc_html( $title ); ?></h3>
			<?php endif; ?>
			<div class="cb-accordion-tabs__content">
				<?= wp_kses_post( wpautop( (string) $content ) ); ?>
			</div>
		</div>
	</div>
	<?php
};

?>
<section id="<?= esc_attr( $block_uid ); ?>" class="<?= esc_attr( $classes ); ?>">
	<div class="container">
		<div class="cb-accordion-tabs__desktop d-none d-xl-block" data-cb-accordion-tabs="tabs">
			<div class="row g-4 align-items-start">
				<div class="col-xl-4">
					<div class="cb-accordion-tabs__tablist" role="tablist" aria-label="Accordion tabs">
						<?php foreach ( $items as $index => $item ) :
							$tab_id   = $block_uid . '-desktop-tab-' . $index;
							$panel_id = $block_uid . '-desktop-panel-' . $index;
							$active   = 0 === $index;
							?>
							<button type="button" class="cb-accordion-tabs__trigger<?= $active ? ' is-active' : ''; ?>" id="<?= esc_attr( $tab_id ); ?>" role="tab" aria-controls="<?= esc_attr( $panel_id ); ?>" aria-selected="<?= $active ? 'true' : 'false'; ?>" aria-expanded="<?= $active ? 'true' : 'false'; ?>">
								<span class="cb-accordion-tabs__trigger-dot" aria-hidden="true"></span>
								<span class="cb-accordion-tabs__trigger-label"><?= esc_html( $item['title'] ); ?></span>
							</button>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="col-xl-8">
					<div class="cb-accordion-tabs__stage">
						<?php foreach ( $items as $index => $item ) :
							$tab_id   = $block_uid . '-desktop-tab-' . $index;
							$panel_id = $block_uid . '-desktop-panel-' . $index;
							$active   = 0 === $index;
							?>
							<?php $render_panel( $item, $panel_id, $tab_id ); ?>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="cb-accordion-tabs__mobile d-xl-none" data-cb-accordion-tabs="accordion">
			<?php foreach ( $items as $index => $item ) :
				$tab_id   = $block_uid . '-mobile-tab-' . $index;
				$panel_id = $block_uid . '-mobile-panel-' . $index;
				$active   = 0 === $index;
				?>
				<div class="cb-accordion-tabs__mobile-item">
					<button type="button" class="cb-accordion-tabs__trigger<?= $active ? ' is-active' : ''; ?>" id="<?= esc_attr( $tab_id ); ?>" role="button" aria-controls="<?= esc_attr( $panel_id ); ?>" aria-expanded="<?= $active ? 'true' : 'false'; ?>">
						<span class="cb-accordion-tabs__trigger-dot" aria-hidden="true"></span>
						<span class="cb-accordion-tabs__trigger-label"><?= esc_html( $item['title'] ); ?></span>
					</button>
					<div class="cb-accordion-tabs__mobile-panel" id="<?= esc_attr( $panel_id ); ?>" aria-labelledby="<?= esc_attr( $tab_id ); ?>" <?= $active ? '' : 'hidden'; ?>>
						<div class="cb-accordion-tabs__panel-inner">
							<div class="cb-accordion-tabs__content">
								<?= wp_kses_post( wpautop( (string) $item['content'] ) ); ?>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
