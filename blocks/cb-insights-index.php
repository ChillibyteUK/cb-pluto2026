<?php
/**
 * Block template for CB Insights Index.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$block_id       = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-insights-index-' );
$context        = cb_get_site_context();
$fallback_image = get_stylesheet_directory_uri() . '/img/pluto-logo.png';

if ( ! in_array( $context, array( 'pf', 'inv' ), true ) ) {
	return;
}

$query = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => -1,
		'ignore_sticky_posts' => true,
	)
);

if ( ! $query->have_posts() ) {
	return;
}

$all_years   = array();
$posts_by_year = array();
foreach ( $query->posts as $p ) {
	$y = get_the_date( 'Y', $p );
	$posts_by_year[ $y ][] = $p;
	$all_years[ $y ] = $y;
}
krsort( $posts_by_year );
krsort( $all_years );

$all_categories = array();
foreach ( $query->posts as $p ) {
	$cats = get_the_category( $p->ID );
	foreach ( $cats as $c ) {
		if ( in_array( $c->slug, array( 'uncategorized', 'uncategorised' ), true ) ) {
			continue;
		}
		$all_categories[ $c->slug ] = $c->name;
	}
}
ksort( $all_categories );

$get_content_excerpt = static function ( $post ) {
	$content = get_post_field( 'post_content', $post );
	$content = strip_shortcodes( $content );
	$content = wp_strip_all_tags( $content );
	return wp_trim_words( $content, 30 );
};

$search_nonce = wp_create_nonce( 'post_search_nonce' );
$ajax_url     = admin_url( 'admin-ajax.php' );
?>
<section id="<?= esc_attr( $block_id ); ?>" class="cb-insights-index">
	<div class="container pb-5">
		<div class="row">
			<div class="col-md-6">
				<div class="cb-insights-index__filters cb-insights-index__filters--cats">
					<button class="cb-insights-index__filter insights-filter cb-insights-index__filter--active" data-filter="all">All</button>
					<?php foreach ( $all_categories as $slug => $name ) : ?>
						<button class="cb-insights-index__filter insights-filter" data-filter="<?= esc_attr( $slug ); ?>"><?= esc_html( $name ); ?></button>
					<?php endforeach; ?>
				</div>
				<div class="cb-insights-index__filters cb-insights-index__filters--years">
					<button class="cb-insights-index__filter insights-year-filter cb-insights-index__filter--active" data-year="all">All years</button>
					<?php foreach ( $all_years as $year ) : ?>
						<button class="cb-insights-index__filter insights-year-filter" data-year="<?= esc_attr( $year ); ?>"><?= esc_html( $year ); ?></button>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="col-md-6">
				<div class="cb-insights-index__toolbar">
					<input type="text" class="cb-insights-index__search" placeholder="Search posts..." aria-label="Search posts">
					<button type="button" class="cb-insights-index__reset">Clear</button>
				</div>
			</div>
		</div>

		<div class="cb-insights-index__results">
			<?php foreach ( $posts_by_year as $year => $year_posts ) : ?>
			<div class="cb-insights-index__year-group" data-year="<?= esc_attr( $year ); ?>">
				<h2 class="cb-insights-index__year-heading"><?= esc_html( $year ); ?></h2>
				<div class="row g-4">
					<?php foreach ( $year_posts as $post_item ) : ?>
					<?php
					$item_cats  = get_the_category( $post_item->ID );
					$item_cat   = ! empty( $item_cats ) ? $item_cats[0]->slug : '';
					$item_cname = ! empty( $item_cats ) ? $item_cats[0]->name : '';
					?>
					<div class="col-md-4 insights-item" data-category="<?= esc_attr( $item_cat ); ?>" data-year="<?= esc_attr( get_the_date( 'Y', $post_item ) ); ?>">
						<a href="<?= esc_url( get_permalink( $post_item ) ); ?>" class="cb-insights-index__card cb-news-card">
							<div class="cb-news-card__image cb-news-card__image--16-9">
								<?php if ( has_post_thumbnail( $post_item ) ) : ?>
									<?= get_the_post_thumbnail( $post_item, 'medium_large' ); ?>
								<?php else : ?>
									<img src="<?= esc_url( $fallback_image ); ?>" alt="<?= esc_attr( get_bloginfo( 'name' ) ); ?>">
								<?php endif; ?>
								<?php if ( $item_cname ) : ?>
								<span class="cb-insights-index__pill"><?= esc_html( $item_cname ); ?></span>
								<?php endif; ?>
							</div>
							<h3 class="cb-news-card__title"><?= esc_html( get_the_title( $post_item ) ); ?></h3>
							<div class="cb-news-card__date"><?= esc_html( get_the_date( 'jS F, Y', $post_item ) ); ?></div>
							<div class="cb-news-card__excerpt"><?= esc_html( $get_content_excerpt( $post_item ) ); ?></div>
							<div class="cb-news-card__link">Learn more</div>
						</a>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<script>
(function() {
	var container = document.getElementById(<?= wp_json_encode( $block_id ); ?>);
	if (!container) return;

	var searchInput  = container.querySelector('.cb-insights-index__search');
	var resetBtn    = container.querySelector('.cb-insights-index__reset');
	var catFilters  = container.querySelectorAll('.insights-filter');
	var yearFilters = container.querySelectorAll('.insights-year-filter');
	var results     = container.querySelector('.cb-insights-index__results');

	function getActiveCat() {
		var active = container.querySelector('.insights-filter.cb-insights-index__filter--active');
		return active ? active.getAttribute('data-filter') : 'all';
	}

	function getActiveYear() {
		var active = container.querySelector('.insights-year-filter.cb-insights-index__filter--active');
		return active ? active.getAttribute('data-year') : 'all';
	}

	function resetFilters() {
		catFilters.forEach(function(f) { f.classList.remove('cb-insights-index__filter--active'); });
		container.querySelector('.insights-filter[data-filter="all"]').classList.add('cb-insights-index__filter--active');
		yearFilters.forEach(function(f) { f.classList.remove('cb-insights-index__filter--active'); });
		container.querySelector('.insights-year-filter[data-year="all"]').classList.add('cb-insights-index__filter--active');
	}

	function filterItems() {
		var cat  = getActiveCat();
		var year = getActiveYear();
		var groups = container.querySelectorAll('.cb-insights-index__year-group');
		groups.forEach(function(group) {
			var items = group.querySelectorAll('.insights-item');
			var anyVisible = false;
			items.forEach(function(item) {
				var matchCat  = cat === 'all' || item.getAttribute('data-category') === cat;
				var matchYear = year === 'all' || item.getAttribute('data-year') === year;
				var match = matchCat && matchYear;
				item.style.display = match ? '' : 'none';
				if (match) anyVisible = true;
			});
			group.style.display = anyVisible ? '' : 'none';
		});
	}

	catFilters.forEach(function(btn) {
		btn.addEventListener('click', function() {
			catFilters.forEach(function(f) { f.classList.remove('cb-insights-index__filter--active'); });
			this.classList.add('cb-insights-index__filter--active');
			filterItems();
		});
	});

	yearFilters.forEach(function(btn) {
		btn.addEventListener('click', function() {
			yearFilters.forEach(function(f) { f.classList.remove('cb-insights-index__filter--active'); });
			this.classList.add('cb-insights-index__filter--active');
			filterItems();
		});
	});

	var debounceTimer;
	function doSearch(term) {
		var data = new FormData();
		data.append('action', 'search_posts');
		data.append('nonce', <?= wp_json_encode( $search_nonce ); ?>);
		data.append('search_term', term);
		data.append('category', getActiveCat());

		fetch(<?= wp_json_encode( $ajax_url ); ?>, {
			method: 'POST',
			body: data
		})
		.then(function(r) { return r.json(); })
		.then(function(res) {
			if (res.success) {
				results.innerHTML = res.data.html;
				resetFilters();
				filterItems();
			}
		});
	}

	searchInput.addEventListener('input', function() {
		clearTimeout(debounceTimer);
		debounceTimer = setTimeout(function() {
			doSearch(searchInput.value.trim());
		}, 300);
	});

	resetBtn.addEventListener('click', function() {
		searchInput.value = '';
		resetFilters();
		doSearch('');
	});
})();
</script>
<?php
wp_reset_postdata();
