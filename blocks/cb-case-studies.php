<?php
/**
 * Block template for CB Case Studies.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$_cb_post_type = 'portfolio';
$_cb_taxonomy  = 'portfolio_solution';
$_cb_term_id   = (int) get_field( 'portfolio_solution_term' );

if ( ! $_cb_term_id ) {
    return;
}

$_cb_term = get_term( $_cb_term_id, $_cb_taxonomy );
if ( ! $_cb_term || is_wp_error( $_cb_term ) ) {
    return;
}

$_cb_query = new WP_Query(
    array(
        'post_type'      => $_cb_post_type,
        'posts_per_page' => 12,
        'no_found_rows'  => true,
        'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
            array(
                'taxonomy' => $_cb_taxonomy,
                'field'    => 'term_id',
                'terms'    => $_cb_term_id,
            ),
        ),
    )
);

if ( ! $_cb_query->have_posts() ) {
    return;
}

$block_id = $block['anchor'] ?? 'cb-case-studies-' . uniqid();
?>
<section id="<?= esc_attr( $block_id ); ?>" class="cb-case-studies">
    <div class="container py-5">
        <div class="swiper cb-case-studies__slider">
            <div class="swiper-wrapper">
        <?php
        while ( $_cb_query->have_posts() ) {
            $_cb_query->the_post();
            $_cb_post_id              = get_the_ID();
            $_cb_card_image_id        = (int) get_post_thumbnail_id( $_cb_post_id );
            $_cb_card_highlights      = (string) get_field( 'card_highlights', $_cb_post_id );
            $_cb_has_highlights       = '' !== trim( wp_strip_all_tags( $_cb_card_highlights ) );
            $_cb_subtitle             = (string) get_field( 'subtitle', $_cb_post_id );
            $_cb_project_desc         = (string) get_field( 'project_description', $_cb_post_id );
            $_cb_map_image_id         = (int) get_field( 'map', $_cb_post_id );
            $_cb_gallery_images       = get_field( 'images', $_cb_post_id );
            $_cb_has_gallery          = is_array( $_cb_gallery_images ) && ! empty( $_cb_gallery_images );
            $_cb_has_portfolio_fields =
                '' !== trim( wp_strip_all_tags( $_cb_subtitle ) ) ||
                '' !== trim( wp_strip_all_tags( $_cb_project_desc ) ) ||
                $_cb_map_image_id > 0 ||
                $_cb_has_gallery;
            $_cb_should_link          = $_cb_has_highlights && $_cb_has_portfolio_fields;
            ?>
                <div class="swiper-slide cb-case-studies__slide">
                <?php
				if ( $_cb_should_link ) {
					?>
                    <a href="<?= esc_url( get_permalink( $_cb_post_id ) ); ?>" class="cb-case-studies__card">
                	<?php
				} else {
					?>
                    <div class="cb-case-studies__card">
                	<?php
				}
				?>
                    <div class="cb-case-studies__image-wrapper">
                        <span class="cb-case-studies__pill"><?= esc_html( $_cb_term->name ); ?></span>
            <?php
            if ( $_cb_card_image_id > 0 ) {
                echo wp_get_attachment_image( $_cb_card_image_id, 'medium_large', false, array( 'class' => 'cb-case-studies__image' ) );
            } else {
                ?>
                        <img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/missing-image.webp' ); ?>" class="cb-case-studies__image" alt="">
                <?php
            }
            ?>
                    </div>
                    <div class="cb-case-studies__inner">
                        <h3 class="cb-case-studies__title"><?= esc_html( get_the_title() ); ?></h3>
			<?php
			if ( $_cb_has_highlights ) {
				echo wp_kses_post( $_cb_card_highlights );
			}
			?>
                    </div>
                <?php if ( $_cb_should_link ) { ?>
                    </a>
                <?php } else { ?>
                    </div>
                <?php } ?>
                </div>
            <?php
        }
        wp_reset_postdata();
        ?>
            </div>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById(<?= wp_json_encode( $block_id ); ?>);
    if (!el) return;
    var slider = el.querySelector('.swiper');
    if (!slider) return;
    new Swiper(slider, {
        slidesPerView: 1,
        spaceBetween: 16,
        rewind: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true
        },
        breakpoints: {
            768: { slidesPerView: 2, spaceBetween: 20 },
            992: { slidesPerView: 3, spaceBetween: 24 }
        }
    });
});
</script>
