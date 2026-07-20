<?php
/**
 * Block template for CB Report Pushthrough.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

// Support Gutenberg color picker.
$bg = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';

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
$classes = $custom_classes ? $custom_classes : '';

// Unique ID.
$block_uid = 'text-image-' . uniqid();

$button_text     = get_field( 'button_text' );
$gravity_form_id = get_field( 'gravity_form_id' );

$has_button = is_string( $button_text ) && '' !== trim( $button_text );
?>
<section id="<?= esc_attr( $block_uid ); ?>" class="<?= esc_attr( trim( 'cb-report-pushthrough ' . $bg . ' ' . $fg . ' ' . $classes ) ); ?>">
	<div class="container">
		<div class="cb-report-pushthrough__inner" data-aos="fade">
			<div class="row g-4">
				<div class="col-md-3">
					<?php
					if ( get_field( 'cover' ) ) {
						$attachment_id = get_field( 'cover' );
						echo wp_get_attachment_image( $attachment_id, 'full', '', array( 'class' => 'cb-report-pushthrough__cover' ) );
					}
					?>
				</div>
				<div class="col-md-9">
					<?php
					if ( get_field( 'title' ) ) {
						?>
					<h2 class="pb-4"><?= esc_html( get_field( 'title' ) ); ?></h2>
						<?php
					}
					?>
					<div class="cb-report-pushthrough__content">
						<?= wp_kses_post( get_field( 'intro' ) ); ?>
					</div>
					<div class="cb-report-pushthrough__cta">
						<?= wp_kses_post( get_field( 'cta_content' ) ); ?>
						<?php if ( $has_button ) : ?>
						<div class="cb-report-pushthrough__button">
							<button class="cb-link-dot" type="button" data-cb-report-pushthrough-open="cb-report-pushthrough-modal-<?= esc_attr( $block_uid ); ?>"><?= esc_html( $button_text ); ?></button>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php if ( $has_button && $gravity_form_id ) : ?>
<div class="cb-report-pushthrough__modal" id="cb-report-pushthrough-modal-<?= esc_attr( $block_uid ); ?>" role="dialog" aria-modal="true" hidden>
	<div class="cb-report-pushthrough__modal-overlay" data-cb-report-pushthrough-close></div>
	<div class="cb-report-pushthrough__modal-dialog">
		<button class="cb-report-pushthrough__modal-close" type="button" data-cb-report-pushthrough-close aria-label="<?php esc_attr_e( 'Close', 'cb-pluto2026' ); ?>">&times;</button>
		<?php
		if ( function_exists( 'gravity_form' ) ) {
			gravity_form( $gravity_form_id, false, false, false, null, true );
		} else {
			echo '<p class="text-muted"><em>' . esc_html__( 'Form unavailable.', 'cb-pluto2026' ) . '</em></p>';
		}
		?>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var modalId = 'cb-report-pushthrough-modal-<?= esc_js( $block_uid ); ?>';
	var modal = document.getElementById(modalId);
	if (!modal) return;

	function closeModal() {
		modal.hidden = true;
		document.body.classList.remove('cb-report-pushthrough-modal-open');
	}

	document.addEventListener('click', function (e) {
		var trigger = e.target.closest('[data-cb-report-pushthrough-open]');
		if (trigger && trigger.getAttribute('data-cb-report-pushthrough-open') === modalId) {
			e.preventDefault();
			modal.hidden = false;
			document.body.classList.add('cb-report-pushthrough-modal-open');
			return;
		}

		var closer = e.target.closest('[data-cb-report-pushthrough-close]');
		if (closer && modal.contains(closer)) {
			e.preventDefault();
			closeModal();
		}
	});

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && !modal.hidden) {
			closeModal();
		}
	});
});
</script>
<?php endif; ?>
