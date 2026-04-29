<?php
/**
 * Block template for CB Text Image.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

// Get ACF fields.
$col_order  = get_field( 'order' ) ? get_field( 'order' ) : 'Text Image';
$split      = get_field( 'split' ) ? get_field( 'split' ) : '50 50';
$level      = get_field( 'level' ) ? get_field( 'level' ) : 'h2';
$full_bleed = (bool) get_field( 'full_bleed' );
$flourish   = (bool) get_field( 'flourish' );
$aspect     = get_field( 'aspect' ) ? get_field( 'aspect' ) : 'native';
// `rounded` defaults to true for backwards-compat with existing blocks
// authored before the field existed (get_field returns null on missing).
$rounded_raw = get_field( 'rounded' );
$rounded     = ( null === $rounded_raw ) ? true : (bool) $rounded_raw;

// Determine the section (lending / investors) from the current URL so the
// flourish utility can pick up the correct colour variant.
$flourish_classes = '';
if ( $flourish ) {
	$flourish_classes = 'full-flourish';
	$context          = cb_get_site_context();
	if ( 'pf' === $context ) {
		$flourish_classes .= ' full-flourish--lending';
	} elseif ( 'inv' === $context ) {
		$flourish_classes .= ' full-flourish--investors';
	}

	// Mirror the flourish when the image sits on the left so the SVG curve
	// anchors to the right edge of the section.
	if ( 'Image Text' === $col_order ) {
		$flourish_classes .= ' full-flourish--flip';
	}
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
$classes = $custom_classes ? $custom_classes : '';

// Support Gutenberg color picker.
$bg = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';

// Side modifier (always set so SCSS can mirror radius / break-out).
$image_side = ( 'Image Text' === $col_order ) ? 'left' : 'right';
$modifiers  = array( 'cb-text-image--image-' . $image_side );
if ( $full_bleed ) {
	$modifiers[] = 'cb-text-image--full-bleed';
	$modifiers[] = 'cb-text-image--split-' . str_replace( ' ', '-', $split );
	if ( $rounded ) {
		$modifiers[] = 'cb-text-image--rounded';
	}
} elseif ( 'native' !== $aspect ) {
	$modifiers[] = 'cb-text-image--aspect-' . $aspect;
}
$modifier_classes = implode( ' ', $modifiers );

// Unique ID.
$block_uid = 'text-image-' . uniqid();

// Column widths (BS col-md-N). text-col-N / 12 = the boundary fraction.
if ( '60 40' === $split ) {
	$text_col_n  = 7;
	$image_col_n = 5;
} elseif ( '40 60' === $split ) {
	$text_col_n  = 5;
	$image_col_n = 7;
} else {
	$text_col_n  = 6;
	$image_col_n = 6;
}

/**
 * Render the text content (title + wysiwyg + link). Shared by both layouts.
 */
$render_text = function () {
	if ( get_field( 'title' ) ) {
		echo '<h2 class="cb-text-image__title has-700-font-size text-uppercase mb-4">' . wp_kses_post( get_field( 'title' ) ) . '</h2>';
	}
	?>
	<div class="cb-text-image__content">
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
	<?php
};
?>
<section id="<?= esc_attr( $block_uid ); ?>" class="<?= esc_attr( trim( $flourish_classes . ' cb-text-image ' . $modifier_classes . ' ' . $bg . ' ' . $fg . ' ' . $classes ) ); ?>">

	<?php if ( $full_bleed ) : ?>

		<?php
		// Text uses real BS .container/.row/.col so its left edge lines up
		// exactly with the rest of the page. The image is rendered OUTSIDE
		// the container and absolutely positioned at md+ to bleed.
		// Both .container and .cb-text-image__image sit inside an inner
		// wrapper (.cb-text-image__inner) which is the absolute image's
		// positioning context — that way any vertical padding utility
		// (py-5 etc.) applied to the section sits OUTSIDE this wrapper
		// and pads above/below the image, rather than shrinking the
		// padding-edge that the image references.
		$text_col_classes = 'col-md-' . $text_col_n;
		if ( 'left' === $image_side ) {
			// Push text into the right half so the absolute image fits the left.
			$text_col_classes .= ' offset-md-' . ( 12 - $text_col_n );
			$text_col_classes .= ' ps-md-5';
		} else {
			$text_col_classes .= ' pe-md-5';
		}
		?>
		<div class="cb-text-image__inner">
			<div class="container">
				<div class="row">
					<div class="<?= esc_attr( $text_col_classes ); ?>">
						<?php $render_text(); ?>
					</div>
				</div>
			</div>
			<div class="cb-text-image__image">
				<?= wp_get_attachment_image( get_field( 'image' ), 'full', false, array() ); ?>
			</div>
		</div>

	<?php else : ?>

		<?php
		$text_col_order  = ( 'Image Text' === $col_order ) ? 'order-2 order-md-2' : 'order-md-1';
		$image_col_order = ( 'Image Text' === $col_order ) ? 'order-1 order-md-1' : 'order-md-2';
		?>
		<div class="container">
			<div class="row gy-5 gx-4 gx-lg-5 align-items-center">
				<div class="col-md-<?= esc_attr( $text_col_n ); ?> <?= esc_attr( $text_col_order ); ?> <?= esc_attr( 'Image Text' === $col_order ? 'pe-md-5' : 'ps-md-5' ); ?>">
					<?php $render_text(); ?>
				</div>
				<div class="col-md-<?= esc_attr( $image_col_n ); ?> <?= esc_attr( $image_col_order ); ?> cb-text-image__image text-center">
					<?= wp_get_attachment_image( get_field( 'image' ), 'full', false, array() ); ?>
				</div>
			</div>
		</div>

	<?php endif; ?>

</section>
