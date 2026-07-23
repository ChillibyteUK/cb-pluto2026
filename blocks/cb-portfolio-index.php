<?php
/**
 * Block template for CB Portfolio Index.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$context = cb_get_site_context();

if ( ! in_array( $context, array( 'pf', 'inv' ), true ) ) {
	return;
}

$cpt_type = 'portfolio';

// Solution filter terms.
$solution_terms = get_terms(
	array(
		'taxonomy'   => 'portfolio_solution',
		'hide_empty' => true,
	)
);

// Market filter terms.
$market_terms = get_terms(
	array(
		'taxonomy'   => 'portfolio_market',
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

$has_solution_filters = ! empty( $solution_terms ) && ! is_wp_error( $solution_terms );
$has_market_filters   = ! empty( $market_terms ) && ! is_wp_error( $market_terms );

// URL parameter overrides.
$active_solution = 'all';
$active_market   = 'all';

if ( isset( $_GET['solution'] ) && is_string( $_GET['solution'] ) && '' !== trim( $_GET['solution'] ) ) {
	$url_solution = sanitize_title( $_GET['solution'] );
	if ( 'all' === $url_solution ) {
		$active_solution = 'all';
	} elseif ( $has_solution_filters ) {
		$solution_slugs = wp_list_pluck( $solution_terms, 'slug' );
		if ( in_array( $url_solution, $solution_slugs, true ) ) {
			$active_solution = $url_solution;
		}
	}
}

if ( isset( $_GET['market'] ) && is_string( $_GET['market'] ) && '' !== trim( $_GET['market'] ) ) {
	$url_market = sanitize_title( $_GET['market'] );
	if ( 'all' === $url_market ) {
		$active_market = 'all';
	} elseif ( $has_market_filters ) {
		$market_slugs = wp_list_pluck( $market_terms, 'slug' );
		if ( in_array( $url_market, $market_slugs, true ) ) {
			$active_market = $url_market;
		}
	}
}

$search_nonce = wp_create_nonce( 'portfolio_search_nonce' );
$ajax_url     = admin_url( 'admin-ajax.php' );
?>
<div id="<?= esc_attr( $block_id ); ?>" class="cb-portfolio-index cb-portfolio-index--<?= esc_attr( $context ); ?>">
	<div class="container">
		<div class="row">
			<div class="col-lg-6">
		<?php if ( $has_solution_filters ) : ?>
		<div class="cb-portfolio-index__filters cb-portfolio-index__filters--solutions">
			<button class="cb-portfolio-index__filter<?= 'all' === $active_solution ? ' cb-portfolio-index__filter--active' : ''; ?>" data-filter="all"><?php esc_html_e( 'All', 'cb-pluto2026' ); ?></button>
			<?php foreach ( $solution_terms as $sterm ) : ?>
			<button class="cb-portfolio-index__filter<?= $sterm->slug === $active_solution ? ' cb-portfolio-index__filter--active' : ''; ?>" data-filter="<?= esc_attr( $sterm->slug ); ?>"><?= esc_html( $sterm->name ); ?></button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<?php if ( $has_market_filters ) : ?>
		<div class="cb-portfolio-index__filters cb-portfolio-index__filters--markets">
			<button class="cb-portfolio-index__filter<?= 'all' === $active_market ? ' cb-portfolio-index__filter--active' : ''; ?>" data-filter="all"><?php esc_html_e( 'All Markets', 'cb-pluto2026' ); ?></button>
			<?php foreach ( $market_terms as $mterm ) : ?>
			<button class="cb-portfolio-index__filter<?= $mterm->slug === $active_market ? ' cb-portfolio-index__filter--active' : ''; ?>" data-filter="<?= esc_attr( $mterm->slug ); ?>"><?= esc_html( $mterm->name ); ?></button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
			</div>
			<div class="col-lg-6">
				<div class="cb-portfolio-index__toolbar">
					<input type="text" class="cb-portfolio-index__search" placeholder="<?php esc_attr_e( 'Search portfolio...', 'cb-pluto2026' ); ?>" aria-label="<?php esc_attr_e( 'Search portfolio', 'cb-pluto2026' ); ?>">
					<button type="button" class="cb-portfolio-index__reset"><?php esc_html_e( 'Clear', 'cb-pluto2026' ); ?></button>
				</div>
			</div>
		</div>

		<div class="row">
			<?php
			while ( $portfolio_query->have_posts() ) {
				$portfolio_query->the_post();
				$the_id            = get_the_ID();
				$gallery_images    = get_field( 'images', $the_id );
				$featured_image_id = (int) get_post_thumbnail_id( $the_id );
				$vimeo_id          = get_field( 'vimeo_id', $the_id );

				$card_image_id  = 0;
				$card_image_url = '';

				if ( $featured_image_id > 0 ) {
					$card_image_id = $featured_image_id;
				} elseif ( ! empty( $vimeo_id ) ) {
					$card_image_url = (string) get_vimeo_data_from_id( $vimeo_id, 'thumbnail_url' );
				} elseif ( is_array( $gallery_images ) && ! empty( $gallery_images ) ) {
					$card_image_id = (int) $gallery_images[0];
				}
				$card_highlights = (string) get_field( 'card_highlights', $the_id );

				$project_description = (string) get_field( 'project_description', $the_id );
				$should_link         = '' !== trim( wp_strip_all_tags( $project_description ) );

				$post_solution_terms = wp_get_post_terms( $the_id, 'portfolio_solution', array( 'fields' => 'slugs' ) );
				$solution_attr       = ! empty( $post_solution_terms ) && ! is_wp_error( $post_solution_terms ) ? implode( ' ', $post_solution_terms ) : '';

				$post_market_terms = wp_get_post_terms( $the_id, 'portfolio_market', array( 'fields' => 'slugs' ) );
				$market_attr       = ! empty( $post_market_terms ) && ! is_wp_error( $post_market_terms ) ? implode( ' ', $post_market_terms ) : '';

				$primary_term_slug = ! empty( $post_solution_terms ) && ! is_wp_error( $post_solution_terms ) ? $post_solution_terms[0] : '';
				$primary_term_name = '';
				if ( $primary_term_slug ) {
					$primary_term_obj  = get_term_by( 'slug', $primary_term_slug, 'portfolio_solution' );
					$primary_term_name = $primary_term_obj ? $primary_term_obj->name : '';
				}
				?>
			<div class="col-md-6 col-lg-4 mb-4 cb-portfolio-index__col"
				<?= $solution_attr ? ' data-solution="' . esc_attr( $solution_attr ) . '"' : ' data-solution=""'; ?>
				<?= $market_attr ? ' data-market="' . esc_attr( $market_attr ) . '"' : ' data-market=""'; ?>>
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
						} elseif ( ! empty( $card_image_url ) ) {
							?>
						<img src="<?= esc_url( $card_image_url ); ?>" class="portfolio__card-image" alt="">
							<?php
						} else {
							?>
						<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/missing-image.webp' ); ?>" class="portfolio__card-image" alt="">
							<?php
						}
						?>
					</div>
					<div class="portfolio__card-inner">
						<h2 class="portfolio__card-title"><?= esc_html( get_the_title() ); ?></h2>
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

<?php if ( $has_solution_filters || $has_market_filters ) : ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var container = document.getElementById(<?= wp_json_encode( $block_id ); ?>);
	if (!container) return;

	var solutionBtns = container.querySelectorAll('.cb-portfolio-index__filters--solutions .cb-portfolio-index__filter');
	var marketBtns = container.querySelectorAll('.cb-portfolio-index__filters--markets .cb-portfolio-index__filter');
	var searchInput = container.querySelector('.cb-portfolio-index__search');
	var resetBtn = container.querySelector('.cb-portfolio-index__reset');
	var resultsRow = container.querySelector('.row:last-child');

	var activeSolution = <?= wp_json_encode( $active_solution ); ?>;
	var activeMarket = <?= wp_json_encode( $active_market ); ?>;

	function applyFilters() {
		var cols = container.querySelectorAll('.cb-portfolio-index__col');
		cols.forEach(function (col) {
			var show = true;

			if (activeSolution !== 'all') {
				var colSolutions = col.getAttribute('data-solution') || '';
				if (colSolutions.split(' ').indexOf(activeSolution) === -1) {
					show = false;
				}
			}

			if (activeMarket !== 'all') {
				var colMarkets = col.getAttribute('data-market') || '';
				if (colMarkets.split(' ').indexOf(activeMarket) === -1) {
					show = false;
				}
			}

			col.style.display = show ? '' : 'none';
		});
	}

	if (solutionBtns.length) {
		solutionBtns.forEach(function (btn) {
			btn.addEventListener('click', function () {
				var filter = this.getAttribute('data-filter');
				solutionBtns.forEach(function (b) { b.classList.remove('cb-portfolio-index__filter--active'); });
				this.classList.add('cb-portfolio-index__filter--active');
				activeSolution = filter;
				applyFilters();
			});
		});
	}

	if (marketBtns.length) {
		marketBtns.forEach(function (btn) {
			btn.addEventListener('click', function () {
				var filter = this.getAttribute('data-filter');
				marketBtns.forEach(function (b) { b.classList.remove('cb-portfolio-index__filter--active'); });
				this.classList.add('cb-portfolio-index__filter--active');
				activeMarket = filter;
				applyFilters();
			});
		});
	}

	function resetFilters() {
		solutionBtns.forEach(function (b) { b.classList.remove('cb-portfolio-index__filter--active'); });
		var allSolution = container.querySelector('.cb-portfolio-index__filters--solutions .cb-portfolio-index__filter[data-filter="all"]');
		if (allSolution) allSolution.classList.add('cb-portfolio-index__filter--active');
		activeSolution = 'all';

		marketBtns.forEach(function (b) { b.classList.remove('cb-portfolio-index__filter--active'); });
		var allMarket = container.querySelector('.cb-portfolio-index__filters--markets .cb-portfolio-index__filter[data-filter="all"]');
		if (allMarket) allMarket.classList.add('cb-portfolio-index__filter--active');
		activeMarket = 'all';
	}

	function doSearch(term) {
		var data = new FormData();
		data.append('action', 'search_portfolio');
		data.append('nonce', <?= wp_json_encode( $search_nonce ); ?>);
		data.append('search_term', term);
		data.append('solution', activeSolution);
		data.append('market', activeMarket);

		fetch(<?= wp_json_encode( $ajax_url ); ?>, {
			method: 'POST',
			body: data
		})
		.then(function (r) { return r.json(); })
		.then(function (res) {
			if (res.success) {
				resultsRow.innerHTML = res.data.html;
				resetFilters();
				applyFilters();
			}
		});
	}

	if (searchInput) {
		var debounceTimer;
		searchInput.addEventListener('input', function () {
			clearTimeout(debounceTimer);
			debounceTimer = setTimeout(function () {
				doSearch(searchInput.value.trim());
			}, 300);
		});
	}

	if (resetBtn) {
		resetBtn.addEventListener('click', function () {
			searchInput.value = '';
			resetFilters();
			doSearch('');
		});
	}

	applyFilters();
});
</script>
<?php endif; ?>
