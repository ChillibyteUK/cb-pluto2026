<?php
/**
 * Block template for CB Text Video.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$btitle  = get_field( 'title' );
$content = get_field( 'content' );

$vimeo_id = get_field( 'vimeo_id' );

$vimeo_thumbnail_url = null;

if ( ! empty( $vimeo_id ) ) {
	$vimeo_thumbnail_url = get_vimeo_data_from_id( $vimeo_id, 'thumbnail_url' );
}

$thumbnail_id = (int) get_field( 'thumbnail' );


$col_order = get_field( 'order' ) ? get_field( 'order' ) : 'Text Video';

$block_id        = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-text-video-' );
$section_classes = array( 'cb-text-video' );

if ( ! empty( $block['className'] ) ) {
	$section_classes[] = $block['className'];
}

$bg = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
if ( $bg ) {
	$section_classes[] = $bg;
}

// AOS intro animations: the video slides in from its own side, the text fades.
$video_side = ( 'Video Text' === $col_order ) ? 'left' : 'right';
$video_aos  = ( 'left' === $video_side ) ? 'fade-right' : 'fade-left';
$text_aos   = 'fade';

$text_col_order  = ( 'Video Text' === $col_order ) ? 'order-2 order-md-2' : 'order-md-1';
$video_col_order = ( 'Video Text' === $col_order ) ? 'order-1 order-md-1' : 'order-md-2';
?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="container">
		<div class="row gy-5 gx-4 gx-lg-5 align-items-start">
			<div class="col-md-6 cb-text-video pe-lg-5 pt-3 <?= esc_attr( $text_col_order ); ?>" data-aos="<?= esc_attr( $text_aos ); ?>">
				<?php
				if ( $btitle ) {
					?>
				<h2 class="cb-text-video has-700-font-size mb-4"><?= wp_kses_post( $btitle ); ?></h2>
					<?php
				}
				if ( $content ) {
					?>
				<div class="cb-text-video"><?= wp_kses_post( $content ); ?></div>
					<?php
				}
				?>
			</div>
			<div class="col-md-6 cb-text-video-col <?= esc_attr( $video_col_order ); ?>" data-aos="<?= esc_attr( $video_aos ); ?>">
				<?php if ( ! empty( $vimeo_id ) ) : ?>
				<button type="button" class="cb-text-video__video-btn" data-cb-text-video-open="<?= esc_attr( $block_id ); ?>-modal" aria-label="<?php esc_attr_e( 'Play video', 'cb-pluto2026' ); ?>">
					<?php
					if ( $thumbnail_id > 0 ) {
						echo wp_get_attachment_image( $thumbnail_id, 'large', false, array( 'class' => 'cb-text-video__video-image' ) );
					} else {
						?>
					<img src="<?= esc_url( $vimeo_thumbnail_url ); ?>" class="cb-text-video__video-image" alt="">
						<?php
					}
					?>
					<span class="cb-text-video__video-overlay">
						<span class="cb-text-video__video-circle">
							<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/chevron-right.svg' ); ?>" class="cb-text-video__video-chevron" alt="">
						</span>
					</span>
				</button>
				<?php else : ?>
				<img src="<?= esc_url( $vimeo_thumbnail_url ); ?>" class="cb-text-video__video-image" alt="">
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

<?php if ( ! empty( $vimeo_id ) ) : ?>
<div class="cb-text-video__modal" id="<?= esc_attr( $block_id ); ?>-modal" role="dialog" aria-modal="true" hidden>
	<div class="cb-text-video__modal-overlay" data-cb-text-video-close></div>
	<div class="cb-text-video__modal-dialog" role="document">
		<button class="cb-text-video__modal-close" type="button" data-cb-text-video-close aria-label="<?php esc_attr_e( 'Close', 'cb-pluto2026' ); ?>">&times;</button>
		<div class="cb-text-video__modal-body" data-cb-text-video-embed data-vimeo-id="<?= esc_attr( $vimeo_id ); ?>"></div>
	</div>
</div>
<?php endif; ?>

<?php
/*
 * Output the modal JS once per page (front-end only). Mirrors the
 * cb-team modal pattern: portal to <body> so it escapes any ancestor
 * stacking context, inject/teardown the iframe on open/close so playback
 * actually stops when the modal is dismissed rather than just hiding it.
 */
static $cb_text_video_assets_emitted = false;
if ( ! $cb_text_video_assets_emitted ) {
	$cb_text_video_assets_emitted = true;
	?>
<script>
(function () {
	if (window.cbTextVideoModalsBound) return;
	window.cbTextVideoModalsBound = true;

	var lastFocus = null;

	function portalModals(root) {
		(root || document).querySelectorAll('.cb-text-video__modal').forEach(function (m) {
			if (m.parentNode !== document.body) {
				document.body.appendChild(m);
			}
		});
	}
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () { portalModals(); });
	} else {
		portalModals();
	}

	function openModal(id) {
		var modal = document.getElementById(id);
		if (!modal) return;
		lastFocus = document.activeElement;

		var embed = modal.querySelector('[data-cb-text-video-embed]');
		if (embed && !embed.querySelector('iframe')) {
			var vimeoId = embed.getAttribute('data-vimeo-id');
			var iframe = document.createElement('iframe');
			iframe.src = 'https://player.vimeo.com/video/' + encodeURIComponent(vimeoId) + '?autoplay=1';
			iframe.setAttribute('allow', 'autoplay; fullscreen; picture-in-picture');
			iframe.setAttribute('allowfullscreen', '');
			embed.appendChild(iframe);
		}

		modal.hidden = false;
		document.body.classList.add('cb-text-video-modal-open');
		var closeBtn = modal.querySelector('[data-cb-text-video-close]');
		if (closeBtn && typeof closeBtn.focus === 'function') closeBtn.focus();
	}

	function closeModal(modal) {
		if (!modal) return;
		modal.hidden = true;
		// Tear down the iframe (rather than just hiding it) so playback stops.
		var embed = modal.querySelector('[data-cb-text-video-embed]');
		if (embed) embed.innerHTML = '';

		if (!document.querySelector('.cb-text-video__modal:not([hidden])')) {
			document.body.classList.remove('cb-text-video-modal-open');
		}
		if (lastFocus && typeof lastFocus.focus === 'function') {
			try { lastFocus.focus(); } catch (e) {}
		}
	}

	document.addEventListener('click', function (e) {
		var opener = e.target.closest('[data-cb-text-video-open]');
		if (opener) {
			e.preventDefault();
			openModal(opener.getAttribute('data-cb-text-video-open'));
			return;
		}
		var closer = e.target.closest('[data-cb-text-video-close]');
		if (closer) {
			e.preventDefault();
			closeModal(closer.closest('.cb-text-video__modal'));
		}
	});

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') {
			var open = document.querySelector('.cb-text-video__modal:not([hidden])');
			if (open) closeModal(open);
		}
	});
})();
</script>
<?php } ?>