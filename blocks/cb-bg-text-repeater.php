<?php
/**
 * Block template for CB BG Text Repeater.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$background_image_id  = get_field( 'background' );
$background_image_url = $background_image_id ? wp_get_attachment_image_url( $background_image_id, 'full' ) : '';

$block_id        = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-bg-text-repeater-' );
$section_classes = array( 'cb-bg-text-repeater' );
$section_style   = '';

if ( $background_image_url ) {
	$section_classes[] = 'cb-bg-text-repeater--has-background-image';
	$section_style     = sprintf( '--cb-bg-text-repeater-bg: url(%s);', esc_url_raw( $background_image_url ) );
}

// Support Gutenberg color picker.
$section_classes[] = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$section_classes[] = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';


if ( ! empty( $block['className'] ) ) {
	$section_classes[] = $block['className'];
}

?>

<section
	id="<?php echo esc_attr( $block_id ); ?>"
	class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>"
	<?php echo $section_style ? ' style="' . esc_attr( $section_style ) . '"' : ''; ?>
>
	<?php if ( $background_image_url ) : ?>
		<div class="cb-bg-text-repeater__overlay" aria-hidden="true"></div>
	<?php endif; ?>
	<div class="container cb-bg-text-repeater__inner">
		<div class="row">
		<?php
		if ( have_rows( 'sections' ) ) {
			$row_index = 0;
			while ( have_rows( 'sections' ) ) {
				the_row();
				++$row_index;
				$item_classes = 'cb-bg-text-repeater__item col-12 col-lg-6';
				if ( 0 === $row_index % 2 ) {
					$item_classes .= ' offset-lg-6';
				}
				?>
				<div class="<?php echo esc_attr( $item_classes ); ?>">
					<div class="cb-bg-text-repeater__item-inner">
						<?php
						$section_title = get_sub_field( 'title' );
						if ( $section_title ) {
							echo '<h2>' . esc_html( $section_title ) . '</h2>';
						}
						?>
						<div class="cb-bg-text-repeater__text">
							<?php the_sub_field( 'content' ); ?>
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

<?php if ( $background_image_url ) : ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var section = document.getElementById(<?php echo wp_json_encode( $block_id ); ?>);
	if (!section) return;

	var ticking = false;

	function update() {
		var rect = section.getBoundingClientRect();
		var windowHeight = window.innerHeight;

		if (rect.bottom > 0 && rect.top < windowHeight) {
			var percent = (windowHeight - rect.top) / (windowHeight + rect.height);
			percent = Math.max(0, Math.min(1, percent));
			var translateY = (percent - 0.5) * 240;
			section.style.setProperty('--cb-bg-text-repeater-parallax-y', translateY.toFixed(1) + 'px');
		}

		ticking = false;
	}

	function onScroll() {
		if (!ticking) {
			window.requestAnimationFrame(update);
			ticking = true;
		}
	}

	window.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', onScroll);
	onScroll();
});
</script>
<?php endif; ?>