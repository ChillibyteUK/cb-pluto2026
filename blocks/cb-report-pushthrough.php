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
					<div class="cb-report-pushthrough__content w-constrained">
						<?= wp_kses_post( get_field( 'intro' ) ); ?>
					</div>
					<div class="cb-report-pushthrough__cta">
						<?= wp_kses_post( get_field( 'cta_content' ) ); ?>
						<div class="cb-report-pushthrough__button">
							<?php
							$button = get_field( 'button' );
							if ( $button ) {
								$button_url    = $button['url'];
								$button_title  = $button['title'];
								$button_target = $button['target'] ? $button['target'] : '_self';
								?>
							<a class="cb-link-dot" href="<?= esc_url( $button_url ); ?>" target="<?= esc_attr( $button_target ); ?>"><?= esc_html( $button_title ); ?></a>
								<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>