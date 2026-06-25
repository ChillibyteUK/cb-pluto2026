<?php
/**
 * Block template for CB Text Pullout.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

/*
ACF field manifest format:
field_name|field_type|values|default_value|wrapper_width|instructions

ACF_FIELDS_START
order|radio|Text Pullout,Pullout Text|Text Pullout|33|Choose whether text or pullout renders first.
split|radio|60 40,50 50,40 60|50 50|33|Select text and pullout column split.
flourish|true_false||0|33|Toggle top flourish striping.
pullout_bullets|true_false||0|33|Toggle pullout bullets.
pullout_title|text||0|33|Enter the pullout title.
ACF_FIELDS_END
*/

// Get ACF fields.
$col_order       = get_field( 'order' ) ? get_field( 'order' ) : 'Text Pullout';
$split           = get_field( 'split' ) ? get_field( 'split' ) : '50 50';
$flourish        = (bool) get_field( 'flourish' );
$pullout_bullets = (bool) get_field( 'pullout_bullets' );
$pullout_title   = get_field( 'pullout_title' ) ? get_field( 'pullout_title' ) : '';

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

	// Mirror the flourish when the pullout sits on the left so the SVG curve
	// anchors to the right edge of the section.
	if ( 'Pullout Text' === $col_order ) {
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

// Side modifier (used by the full-bleed pullout treatment below).
$pullout_side = ( 'Pullout Text' === $col_order ) ? 'left' : 'right';

$modifier_classes = implode(
	' ',
	array(
		'cb-text-pullout--full-bleed',
		'cb-text-pullout--pullout-' . $pullout_side,
		'cb-text-pullout--split-' . str_replace( ' ', '-', $split ),
	)
);

// AOS intro animations: the pullout slides in from its own side, the text fades.
$pullout_aos  = ( 'left' === $pullout_side ) ? 'fade-right' : 'fade-left';
$text_aos     = 'fade';

// Unique ID.
$block_uid = 'text-pullout-' . uniqid();

// Column widths (BS col-md-N). text-col-N / 12 = the boundary fraction.
if ( '60 40' === $split ) {
	$text_col_n    = 7;
	$pullout_col_n = 5;
} elseif ( '40 60' === $split ) {
	$text_col_n    = 5;
	$pullout_col_n = 7;
} else {
	$text_col_n    = 6;
	$pullout_col_n = 6;
}

/**
 * Render the text content (title + wysiwyg + link). Shared by both layouts.
 */
$render_text = function () {
	if ( get_field( 'title' ) ) {
		echo '<h2 class="cb-text-pullout__title has-700-font-size mb-4">' . wp_kses_post( get_field( 'title' ) ) . '</h2>';
	}
	?>
	<div class="cb-text-pullout__content">
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

/**
 * Render the pullout content.
 */
$render_pullout = function () use ( $pullout_title, $pullout_bullets ) {
	if ( $pullout_title ) {
		echo '<h3 class="cb-text-pullout__pullout-title has-600-font-size mb-4">' . wp_kses_post( $pullout_title ) . '</h3>';
	}

	if ( $pullout_bullets ) {
		?>
	<ul class="cb-text-pullout__pullout-list"><?= wp_kses_post( cb_list( get_field( 'pullout' ) ) ); ?></ul>
		<?php
	} else {
		echo wp_kses_post( get_field( 'pullout' ) );
	}
};
?>
<section id="<?= esc_attr( $block_uid ); ?>" class="<?= esc_attr( trim( $flourish_classes . ' cb-text-pullout ' . $modifier_classes . ' ' . $bg . ' ' . $fg . ' ' . $classes ) ); ?>">
	<div class="container py-5">
		<div class="row gy-5 gx-4 gx-lg-5 align-items-start">
			<div class="col-md-<?= esc_attr( $text_col_n ); ?> <?= esc_attr( 'Pullout Text' === $col_order ? 'order-2 order-md-2 ps-md-5' : 'order-md-1 pe-md-5' ); ?>" data-aos="<?= esc_attr( $text_aos ); ?>">
				<?php $render_text(); ?>
			</div>
			<div class="col-md-<?= esc_attr( $pullout_col_n ); ?> <?= esc_attr( 'Pullout Text' === $col_order ? 'order-1 order-md-1' : 'order-md-2' ); ?> cb-text-pullout__pullout-col" data-aos="<?= esc_attr( $pullout_aos ); ?>">
				<div class="cb-text-pullout__pullout">
					<?php $render_pullout(); ?>
				</div>
			</div>
		</div>
	</div>

</section>
