<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase -- Ignoring to match post type name.
/**
 * Template for displaying single investor portfoliio posts.
 *
 * @package cb-identity2025
 */

defined( 'ABSPATH' ) || exit;
get_header();

$gallery_images = get_field( 'images' );
$gallery_id     = wp_unique_id( 'investor-portfolio-gallery-' );

?>
<main id="main" class="case-study">
	<div class="container">
		<div id="breadcrumbs" class="mb-3">
			<a href="/investors/">Home</a> &raquo; <a href="/investors/portfolio/">Portfolio</a> &raquo; <?= esc_html( get_the_title() ); ?>
		</div>
		<div class="row g-5 mb-5">
			<div class="col-lg-9 pb-5 order-lg-2">
				<h1 class="has-green-dark-1000-color"><?= esc_html( get_the_title() ); ?></h1>
				<h2 class="has-green-dark-600-color mb-4"><?= esc_html( get_field( 'subtitle' ) ); ?></h2>
				<?php
				if ( ! empty( $gallery_images ) && is_array( $gallery_images ) ) {
					?>
					<div class="row g-3" id="<?= esc_attr( $gallery_id ); ?>" data-cb-portfolio-gallery>
						<div class="col-md-9">
							<div class="cb-portfolio-gallery mb-4">
								<div class="swiper cb-portfolio-gallery__main-swiper">
								<div class="swiper-wrapper">
									<?php
									foreach ( $gallery_images as $image_id ) {
										?>
										<div class="swiper-slide">
											<?= wp_get_attachment_image( $image_id, 'full', false, array( 'class' => 'cb-portfolio-gallery__main-image' ) ); ?>
										</div>
										<?php
									}
									?>
								</div>
							</div>
						</div>
					</div>
					<div class="d-none d-md-block col-md-3 ps-md-0">
						<div class="swiper cb-portfolio-gallery__thumbs-swiper">
							<div class="swiper-wrapper">
								<?php
								foreach ( $gallery_images as $image_id ) {
									?>
									<div class="swiper-slide">
										<?= wp_get_attachment_image( $image_id, 'full', false, array( 'class' => 'cb-portfolio-gallery__thumb-image' ) ); ?>
									</div>
									<?php
								}
								?>
							</div>
						</div>
					</div>
				</div>
					<?php
				}
				?>
				<article>
					<?= wp_kses_post( get_field( 'project_description' ) ); ?>
				</article>
			</div>
			<div class="col-lg-3 order-lg-1">
				<?php
				while ( have_rows( 'sidebar_highlights' ) ) {
					the_row();
					?>
					<div class="mb-2 sidebar-highlight">
						<div class="fw-semibold"><?= esc_html( get_sub_field( 'title' ) ); ?> &ndash;</div>
						<div class=""><?= esc_html( get_sub_field( 'description' ) ); ?></div>
					</div>
					<?php
				}
				if ( get_field( 'map' ) ) {
					echo wp_get_attachment_image( get_field( 'map' ), 'full', false, array( 'class' => 'sidebar-map mt-4 d-block mx-auto' ) );
				}
				?>
			</div>
		</div>
	</div>
</main>
<?php
add_action(
	'wp_footer',
	function () use ( $gallery_id ) {
		?>
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const galleryElement = document.getElementById('<?= esc_js( $gallery_id ); ?>');
				if (galleryElement) {
					const thumbsSwiper = new Swiper(galleryElement.querySelector('.cb-portfolio-gallery__thumbs-swiper'), {
						slidesPerView: 'auto',
						spaceBetween: 10,
						watchSlidesProgress: true,
						slideToClickedSlide: true,
						direction: window.innerWidth >= 768 ? 'vertical' : 'horizontal',
						breakpoints: {
							768: {
								direction: 'vertical',
							},
							0: {
								direction: 'horizontal',
							}
						}
					});
					new Swiper(galleryElement.querySelector('.cb-portfolio-gallery__main-swiper'), {
						slidesPerView: 1,
						spaceBetween: 10,
						thumbs: {
							swiper: thumbsSwiper
						}
					});
				}
			});
		</script>
		<?php
	},
);
get_footer();
?>
