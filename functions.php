<?php
/**
 * Understrap Child Theme functions and definitions
 *
 * @package UnderstrapChild
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

define( 'CB_THEME_DIR', get_stylesheet_directory() );

require_once CB_THEME_DIR . '/inc/cb-theme.php';
require_once CB_THEME_DIR . '/inc/cb-block-usage.php';

/**
 * Removes the parent themes stylesheet and scripts from inc/enqueue.php
 */
function understrap_remove_scripts() {
	wp_dequeue_style( 'understrap-styles' );
	wp_deregister_style( 'understrap-styles' );

	wp_dequeue_script( 'understrap-scripts' );
	wp_deregister_script( 'understrap-scripts' );

	// Remove WordPress block library styles (mostly cruft).
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'wc-blocks-style' );
}
add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );


/**
 * Enqueue our stylesheet and javascript file
 */

/**
 * Enqueue child-theme.min.css with filemtime versioning.
 * No dependencies to ensure immediate loading and prevent FOUC.
 */
function cb_enqueue_theme_css() {
	$rel = '/css/child-theme.min.css';
	$abs = get_stylesheet_directory() . $rel;
	wp_enqueue_style(
		'cb-theme',
		get_stylesheet_directory_uri() . $rel,
		array(), // No dependencies - load immediately.
		file_exists( $abs ) ? filemtime( $abs ) : null
	);
}
// Load at default priority (10) for early rendering, before parent removal at priority 20.
add_action( 'wp_enqueue_scripts', 'cb_enqueue_theme_css' );

/**
 * Enqueue child-theme.min.js with filemtime versioning.
 */
function cb_enqueue_theme_js() {
	$rel = '/js/child-theme.min.js';
	$abs = get_stylesheet_directory() . $rel;
	if ( file_exists( $abs ) ) {
		wp_enqueue_script(
			'cb-theme-js',
			get_stylesheet_directory_uri() . $rel,
			array(),
			filemtime( $abs ),
			true
		);
	}

	$accordion_rel = '/js/cb-accordion-tabs.js';
	$accordion_abs = get_stylesheet_directory() . $accordion_rel;
	if ( file_exists( $accordion_abs ) ) {
		wp_enqueue_script(
			'cb-accordion-tabs',
			get_stylesheet_directory_uri() . $accordion_rel,
			array(),
			filemtime( $accordion_abs ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'cb_enqueue_theme_js', 20 );


/**
 * Load the child theme's text domain
 */
function add_child_theme_textdomain() {
	load_child_theme_textdomain( 'cb-pluto2026', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'add_child_theme_textdomain' );


/**
 * Overrides the theme_mod to default to Bootstrap 5
 *
 * This function uses the `theme_mod_{$name}` hook and
 * can be duplicated to override other theme settings.
 *
 * @return string
 */
function understrap_default_bootstrap_version() {
	return 'bootstrap5';
}
add_filter( 'theme_mod_understrap_bootstrap_version', 'understrap_default_bootstrap_version', 20 );



/**
 * Loads javascript for showing customizer warning dialog.
 */
function understrap_child_customize_controls_js() {
	wp_enqueue_script(
		'understrap_child_customizer',
		get_stylesheet_directory_uri() . '/js/customizer-controls.js',
		array( 'customize-preview' ),
		'20130508',
		true
	);
}
add_action( 'customize_controls_enqueue_scripts', 'understrap_child_customize_controls_js' );

/**
 * AJAX handler for post search functionality
 */
function cb_ajax_search_posts() {
	if ( ! wp_verify_nonce( $_POST['nonce'], 'post_search_nonce' ) ) {
		wp_die( 'Security check failed' );
	}

	$search_term = isset( $_POST['search_term'] ) ? sanitize_text_field( wp_unslash( $_POST['search_term'] ) ) : '';
	$category    = isset( $_POST['category'] ) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : '';

	$args = array(
		'post_type'      => 'post',
		'post_status'    => array( 'publish', 'future' ),
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $search_term ) ) {
		$args['s'] = $search_term;
	}

	if ( ! empty( $category ) && 'all' !== $category ) {
		$args['category_name'] = $category;
	}

	$query       = new WP_Query( $args );
	$fallback    = get_stylesheet_directory_uri() . '/img/pluto-logo.png';
	$excerpt_len = 30;

	$posts_by_year = array();
	foreach ( $query->posts as $p ) {
		$y = get_the_date( 'Y', $p );
		$posts_by_year[ $y ][] = $p;
	}
	krsort( $posts_by_year );

	ob_start();

	if ( $query->have_posts() ) {
		foreach ( $posts_by_year as $year => $year_posts ) {
			?>
		<div class="cb-insights-index__year-group" data-year="<?= esc_attr( $year ); ?>">
			<h2 class="cb-insights-index__year-heading"><?= esc_html( $year ); ?></h2>
			<div class="row g-4">
				<?php foreach ( $year_posts as $post_item ) : ?>
				<?php
				setup_postdata( $post_item );
				$cats      = get_the_category( $post_item->ID );
				$cat_slugs = ! empty( $cats ) && ! is_wp_error( $cats ) ? implode( ' ', wp_list_pluck( $cats, 'slug' ) ) : '';
				$cat_name  = ! empty( $cats ) && ! is_wp_error( $cats ) ? $cats[0]->name : '';
				?>
				<div class="col-md-4 insights-item" data-category="<?= esc_attr( $cat_slugs ); ?>" data-year="<?= esc_attr( get_the_date( 'Y', $post_item ) ); ?>">
					<a href="<?= esc_url( get_permalink( $post_item ) ); ?>" class="cb-insights-index__card cb-news-card">
						<div class="cb-news-card__image cb-news-card__image--4-3">
							<?php if ( has_post_thumbnail( $post_item ) ) : ?>
								<?= get_the_post_thumbnail( $post_item, 'medium_large' ); ?>
							<?php else : ?>
								<img src="<?= esc_url( $fallback ); ?>" alt="<?= esc_attr( get_bloginfo( 'name' ) ); ?>">
							<?php endif; ?>
							<?php if ( $cat_name ) : ?>
							<span class="cb-insights-index__pill"><?= esc_html( $cat_name ); ?></span>
							<?php endif; ?>
						</div>
						<h3 class="cb-news-card__title"><?= esc_html( get_the_title( $post_item ) ); ?></h3>
						<div class="cb-news-card__date"><?= esc_html( get_the_date( 'jS F, Y', $post_item ) ); ?></div>
						<div class="cb-news-card__excerpt"><?= esc_html( wp_trim_words( wp_strip_all_tags( get_the_content( $post_item ) ), $excerpt_len ) ); ?></div>
						<div class="cb-news-card__link">Learn more</div>
					</a>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
			<?php
		}
		wp_reset_postdata();
	} else {
		echo '<div class="col-12"><p class="text-center">No posts found matching your criteria.</p></div>';
	}

	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}

add_action( 'wp_ajax_search_posts', 'cb_ajax_search_posts' );
add_action( 'wp_ajax_nopriv_search_posts', 'cb_ajax_search_posts' );

/**
 * AJAX handler for portfolio search.
 */
function cb_ajax_search_portfolio() {
	if ( ! wp_verify_nonce( $_POST['nonce'], 'portfolio_search_nonce' ) ) {
		wp_die( 'Security check failed' );
	}

	$search_term = isset( $_POST['search_term'] ) ? sanitize_text_field( wp_unslash( $_POST['search_term'] ) ) : '';
	$solution    = isset( $_POST['solution'] ) ? sanitize_text_field( wp_unslash( $_POST['solution'] ) ) : '';
	$market      = isset( $_POST['market'] ) ? sanitize_text_field( wp_unslash( $_POST['market'] ) ) : '';

	$args = array(
		'post_type'      => 'portfolio',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'no_found_rows'  => true,
	);

	if ( ! empty( $search_term ) ) {
		$args['s'] = $search_term;
	}

	if ( ! empty( $solution ) && 'all' !== $solution ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'portfolio_solution',
			'field'    => 'slug',
			'terms'    => $solution,
		);
	}

	if ( ! empty( $market ) && 'all' !== $market ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'portfolio_market',
			'field'    => 'slug',
			'terms'    => $market,
		);
	}

	if ( ! empty( $args['tax_query'] ) ) {
		$args['tax_query']['relation'] = 'AND';
	}

	$query       = new WP_Query( $args );
	$missing_img = get_stylesheet_directory_uri() . '/img/missing-image.webp';

	ob_start();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
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
			$has_highlights  = '' !== trim( wp_strip_all_tags( $card_highlights ) );

			$subtitle             = (string) get_field( 'subtitle', $the_id );
			$project_description  = (string) get_field( 'project_description', $the_id );
			$map_image_id         = (int) get_field( 'map', $the_id );
			$has_portfolio_fields =
				'' !== trim( wp_strip_all_tags( $subtitle ) ) ||
				'' !== trim( wp_strip_all_tags( $project_description ) ) ||
				$map_image_id > 0 ||
				! empty( $vimeo_id ) ||
				! empty( $gallery_images );

			$should_link = $has_highlights && $has_portfolio_fields;

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
			data-solution="<?= esc_attr( $solution_attr ); ?>"
			data-market="<?= esc_attr( $market_attr ); ?>">
			<?php if ( $should_link ) : ?>
			<a class="portfolio__card" href="<?= esc_url( get_permalink( $the_id ) ); ?>">
			<?php else : ?>
			<div class="portfolio__card">
			<?php endif; ?>
				<div class="portfolio__card-image-wrapper">
					<?php if ( $primary_term_name ) : ?>
					<span class="portfolio__card-pill"><?= esc_html( $primary_term_name ); ?></span>
					<?php endif; ?>
					<?php if ( $card_image_id > 0 ) : ?>
						<?= wp_get_attachment_image( $card_image_id, 'medium_large', false, array( 'class' => 'portfolio__card-image' ) ); ?>
					<?php elseif ( ! empty( $card_image_url ) ) : ?>
					<img src="<?= esc_url( $card_image_url ); ?>" class="portfolio__card-image" alt="">
					<?php else : ?>
					<img src="<?= esc_url( $missing_img ); ?>" class="portfolio__card-image" alt="">
					<?php endif; ?>
				</div>
				<div class="portfolio__card-inner">
					<h2 class="portfolio__card-title"><?= esc_html( get_the_title() ); ?></h2>
					<?= wp_kses_post( $card_highlights ); ?>
				</div>
			<?php if ( $should_link ) : ?>
			</a>
			<?php else : ?>
			</div>
			<?php endif; ?>
		</div>
			<?php
		}
		wp_reset_postdata();
	} else {
		echo '<div class="col-12"><p class="text-center">' . esc_html__( 'No portfolio items found.', 'cb-pluto2026' ) . '</p></div>';
	}

	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}

add_action( 'wp_ajax_search_portfolio', 'cb_ajax_search_portfolio' );
add_action( 'wp_ajax_nopriv_search_portfolio', 'cb_ajax_search_portfolio' );

/**
 * Add data-text attribute to primary nav links for width reservation.
 *
 * @param array  $atts Link attributes.
 * @param object $item Menu item.
 * @param object $args Menu args.
 * @return array
 */
function cb_add_primary_nav_data_text( $atts, $item, $args ) {
	if ( isset( $args->theme_location ) && 'primary_nav' === $args->theme_location ) {
		$label             = wp_strip_all_tags( $item->title );
		$atts['data-text'] = $label;
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'cb_add_primary_nav_data_text', 10, 3 );

/**
 * Render menu divider items as non-clickable labels.
 *
 * @param string   $item_output The menu item's starting HTML output.
 * @param WP_Post  $item        Menu item data object.
 * @param int      $depth       Depth of menu item.
 * @param stdClass $args        An object of wp_nav_menu() arguments.
 * @return string
 */
function cb_render_primary_nav_menu_divider( $item_output, $item, $depth, $args ) {
	if ( ! isset( $args->theme_location ) || 'primary_nav' !== $args->theme_location ) {
		return $item_output;
	}

	if ( empty( $item->classes ) || ! in_array( 'menu-divider-label', $item->classes, true ) ) {
		return $item_output;
	}

	return '<span class="dropdown-divider-label">' . esc_html( $item->title ) . '</span>';
}
add_filter( 'walker_nav_menu_start_el', 'cb_render_primary_nav_menu_divider', 10, 4 );
