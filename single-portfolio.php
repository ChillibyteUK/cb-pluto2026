<?php
/**
 * Template for displaying single portfolio posts.
 *
 * @package cb-identity2025
 */

defined( 'ABSPATH' ) || exit;
get_header();

$context       = cb_get_site_context();
$home_url      = 'inv' === $context ? '/investors/' : '/property-finance/';
$portfolio_url = 'inv' === $context ? '/investors/portfolio/' : '/property-finance/portfolio/';

$gallery_images    = get_field( 'images' );
$gallery_images    = is_array( $gallery_images ) ? array_values( array_filter( array_map( 'intval', $gallery_images ) ) ) : array();
$featured_image_id = (int) get_post_thumbnail_id( get_the_ID() );

if ( $featured_image_id > 0 ) {
	$gallery_images = array_values( array_unique( array_merge( array( $featured_image_id ), $gallery_images ) ) );
}

$vimeo_id = get_field( 'vimeo_id' );

$vimeo_thumbnail_url = null;

if ( ! empty( $vimeo_id ) ) {
	$vimeo_thumbnail_url = get_vimeo_data_from_id( $vimeo_id, 'thumbnail_url' );
}

$media_count = ( empty( $vimeo_id ) ? 0 : 1 ) + count( $gallery_images );

$gallery_id = wp_unique_id( 'investor-portfolio-gallery-' );

?>
<main id="main" class="case-study">
	<div class="container">
		<div id="breadcrumbs" class="mb-3">
			<a href="<?= esc_url( $home_url ); ?>">Home</a> &raquo; <a href="<?= esc_url( $portfolio_url ); ?>">Portfolio</a> &raquo; <?= esc_html( get_the_title() ); ?>
		</div>
		<div class="row g-5 mb-5">
			<div class="col-lg-9 pb-5">
				<h1 class="has-green-dark-1000-color"><?= esc_html( get_the_title() ); ?></h1>
				<?php
				if ( get_field( 'subtitle' ) ) {
					?>
				<h2 class="has-green-dark-600-color mb-4"><?= esc_html( get_field( 'subtitle' ) ); ?></h2>
					<?php
				}
				if ( $media_count > 0 ) {
					if ( 1 === $media_count ) {
						if ( ! empty( $vimeo_id ) ) {
							?>
							<div class="mb-4">
								<iframe src="https://player.vimeo.com/video/<?= esc_attr( $vimeo_id ); ?>" style="aspect-ratio:16/9;width:100%" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
							</div>
							<?php
						} else {
							echo wp_get_attachment_image( $gallery_images[0], 'full', false, array( 'class' => 'w-100 mb-4' ) );
						}
					} else {
						?>
					<div class="row g-3" id="<?= esc_attr( $gallery_id ); ?>" data-cb-portfolio-gallery>
						<div class="col-md-9">
							<div class="cb-portfolio-gallery mb-4">
								<div class="swiper cb-portfolio-gallery__main-swiper">
								<div class="swiper-wrapper">
									<?php
									if ( ! empty( $vimeo_id ) ) {
										?>
										<div class="swiper-slide swiper-no-swiping">
											<iframe src="https://player.vimeo.com/video/<?= esc_attr( $vimeo_id ); ?>" style="aspect-ratio:16/9;width:100%" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
										</div>
										<?php
									}
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
								if ( ! empty( $vimeo_id ) && $vimeo_thumbnail_url ) {
									?>
									<div class="swiper-slide">
										<img src="<?= esc_url( $vimeo_thumbnail_url ); ?>" class="cb-portfolio-gallery__thumb-image" alt="">
									</div>
									<?php
								}
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
				}
				?>
				<article>
					<?= wp_kses_post( get_field( 'project_description' ) ); ?>
				</article>
			</div>
			<div class="col-lg-3">
				<?php
				if ( get_field( 'map' ) ) {
					echo wp_get_attachment_image( get_field( 'map' ), 'full', false, array( 'class' => 'sidebar-map mt-4 d-block mx-auto' ) );
				}

				$related_terms = wp_get_post_terms( get_the_ID(), 'portfolio_solution', array( 'fields' => 'ids' ) );

				if ( ! empty( $related_terms ) && ! is_wp_error( $related_terms ) ) {
					$related_query = new WP_Query(
						array(
							'post_type'      => 'portfolio',
							'posts_per_page' => 3,
							'post__not_in'   => array( get_the_ID() ),
							'no_found_rows'  => true,
							'tax_query'      => array(
								array(
									'taxonomy' => 'portfolio_solution',
									'field'    => 'term_id',
									'terms'    => $related_terms,
								),
							),
						)
					);

					if ( $related_query->have_posts() ) {
						?>
						<div class="mt-4">
							<h3 class="h5 has-green-dark-1000-color">Related</h3>
							<?php
							while ( $related_query->have_posts() ) {
								$related_query->the_post();
								$related_id            = get_the_ID();
								$related_image_id      = (int) get_post_thumbnail_id( $related_id );
								$related_vimeo_id      = get_field( 'vimeo_id', $related_id );
								$related_gal_images    = get_field( 'images', $related_id );
								$related_gal_images    = is_array( $related_gal_images ) ? array_filter( array_map( 'intval', $related_gal_images ) ) : array();
								$related_image_src     = '';
								$related_thumb_classes = '';

								if ( $related_image_id > 0 ) {
									$related_image_src     = wp_get_attachment_image_url( $related_image_id, 'medium_large' );
									$related_thumb_classes = 'attachment-medium_large size-medium_large';
								} elseif ( ! empty( $related_gal_images ) ) {
									$related_image_src     = wp_get_attachment_image_url( (int) $related_gal_images[0], 'medium_large' );
									$related_thumb_classes = 'attachment-medium_large size-medium_large';
								} elseif ( ! empty( $related_vimeo_id ) ) {
									$related_image_src = get_vimeo_data_from_id( $related_vimeo_id, 'thumbnail_url' );
								}
								?>
								<a href="<?= esc_url( get_permalink( $related_id ) ); ?>" class="d-flex gap-3 text-decoration-none mb-3">
									<?php
									if ( $related_image_src ) {
										?>
									<img src="<?= esc_url( $related_image_src ); ?>" class="rounded flex-shrink-0 <?= esc_attr( $related_thumb_classes ); ?>" alt="" style="width:80px;height:60px;object-fit:cover;">
										<?php
									}
									?>
									<span class="small fw-semibold has-green-dark-1000-color"><?= esc_html( get_the_title() ); ?></span>
								</a>
								<?php
							}
							wp_reset_postdata();
							?>
						</div>
						<?php
					}
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
