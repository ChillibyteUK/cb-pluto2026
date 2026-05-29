<?php
/**
 * Block template for CB Portfolio Index.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$context = cb_get_site_context();

if ( 'pf' === $context ) {
	$cpt_type = 'pf_portfolio';
	$cpt_tax  = 'pf_solution';
} elseif ( 'inv' === $context ) {
	$cpt_type = 'investor_portfolio';
	$cpt_tax  = 'investor_solution';
} else {
	return;
}

$filter_terms = get_terms(
	array(
		'taxonomy'   => $cpt_tax,
		'hide_empty' => true,
	)
);

$portfolio_query = new WP_Query(
	array(
		'post_type'      => $cpt_type,
		'posts_per_page' => -1,
		'no_found_rows'  => true,
	)
);

if ( ! $portfolio_query->have_posts() ) {
	return;
}

$block_id = $block['anchor'] ?? 'cb-portfolio-index-' . uniqid();
?>
<div id="<?= esc_attr( $block_id ); ?>" class="cb-portfolio-index">
	<div class="container">
		<?php
		if ( ! empty( $filter_terms ) && ! is_wp_error( $filter_terms ) ) {
			?>
		<div class="cb-portfolio-index__filters">
			<button class="cb-portfolio-index__filter cb-portfolio-index__filter--active" data-filter="all">All</button>
			<?php
			foreach ( $filter_terms as $filter_term ) {
				?>
			<button class="cb-portfolio-index__filter" data-filter="<?= esc_attr( $filter_term->slug ); ?>"><?= esc_html( $filter_term->name ); ?></button>
				<?php
			}
			?>
		</div>
			<?php
		}
		?>
		<div class="row">
			<?php
			while ( $portfolio_query->have_posts() ) {
				$portfolio_query->the_post();
				$the_id            = get_the_ID();
				$gallery_images    = get_field( 'images', $the_id );
				$featured_image_id = (int) get_post_thumbnail_id( $the_id );
				if ( $featured_image_id > 0 ) {
					$card_image_id = $featured_image_id;
				} elseif ( is_array( $gallery_images ) && ! empty( $gallery_images ) ) {
					$card_image_id = (int) $gallery_images[0];
				} else {
					$card_image_id = 0;
				}
				$card_highlights = (string) get_field( 'card_highlights', $the_id );
				$has_highlights  = '' !== trim( wp_strip_all_tags( $card_highlights ) );

				$subtitle             = (string) get_field( 'subtitle', $the_id );
				$project_description  = (string) get_field( 'project_description', $the_id );
				$map_image_id         = (int) get_field( 'map', $the_id );
				$sidebar_highlights   = get_field( 'sidebar_highlights', $the_id );
				$has_sidebar_details  = is_array( $sidebar_highlights ) && ! empty( $sidebar_highlights );
				$has_portfolio_fields =
					'' !== trim( wp_strip_all_tags( $subtitle ) ) ||
					'' !== trim( wp_strip_all_tags( $project_description ) ) ||
					$map_image_id > 0 ||
					$has_sidebar_details ||
					! empty( $gallery_images );

				$should_link = $has_highlights && $has_portfolio_fields;

				$post_terms  = wp_get_post_terms( $the_id, $cpt_tax, array( 'fields' => 'slugs' ) );
				$filter_attr = ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ? implode( ' ', $post_terms ) : '';

				$primary_term_slug = ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ? $post_terms[0] : '';
				$primary_term_name = '';
				if ( $primary_term_slug ) {
					$primary_term_obj  = get_term_by( 'slug', $primary_term_slug, $cpt_tax );
					$primary_term_name = $primary_term_obj ? $primary_term_obj->name : '';
				}
				?>
			<div class="col-md-6 col-lg-4 mb-4 cb-portfolio-index__col"<?= $filter_attr ? ' data-filter="' . esc_attr( $filter_attr ) . '"' : ''; ?>>
				<?php
				if ( $should_link ) {
					?>
				<a class="portfolio__card" href="<?= esc_url( get_permalink( $the_id ) ); ?>">
					<?php
				} else {
					?>
				<div class="portfolio__card">
					<?php
				}
				?>
					<div class="portfolio__card-image-wrapper">
						<?php
						if ( $primary_term_name ) {
							?>
						<span class="portfolio__card-pill"><?= esc_html( $primary_term_name ); ?></span>
							<?php
						}
						if ( $card_image_id > 0 ) {
							echo wp_get_attachment_image( $card_image_id, 'medium_large', false, array( 'class' => 'portfolio__card-image' ) );
						} else {
							?>
						<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/missing-image.webp' ); ?>" class="portfolio__card-image" alt="">
							<?php
						}
						?>
					</div>
					<div class="portfolio__card-inner">
						<h2 class="portfolio__card-title"><?= esc_html( get_the_title() ); ?></h2>
						<?php
						if ( 'pf' === $context ) {
							echo wp_kses_post( $card_highlights );
						} else {
							?>
						<ul class="portfolio__highlights">
							<?= wp_kses_post( cb_list( $card_highlights ) ); ?>
						</ul>
							<?php
						}
						?>
					</div>
				<?php
				if ( $should_link ) {
					?>
				</a>
					<?php
				} else {
					?>
				</div>
					<?php
				}
				?>
			</div>
				<?php
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</div>
<?php
if ( ! empty( $filter_terms ) && ! is_wp_error( $filter_terms ) ) {
	?>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var container = document.getElementById(<?= wp_json_encode( $block_id ); ?>);
	if (!container) return;
	var btns = container.querySelectorAll('.cb-portfolio-index__filter');
	var cols = container.querySelectorAll('.cb-portfolio-index__col');
	btns.forEach(function (btn) {
		btn.addEventListener('click', function () {
			var filter = this.getAttribute('data-filter');
			btns.forEach(function (b) { b.classList.remove('cb-portfolio-index__filter--active'); });
			this.classList.add('cb-portfolio-index__filter--active');
			cols.forEach(function (col) {
				if (filter === 'all') {
					col.style.display = '';
				} else {
					var colFilters = col.getAttribute('data-filter') || '';
					col.style.display = colFilters.split(' ').indexOf(filter) !== -1 ? '' : 'none';
				}
			});
		});
	});
});
</script>
	<?php
}
