<?php
/**
 * Custom Post Types Registration
 *
 * This file contains the code to register custom post types for the theme.
 *
 * The site is effectively two sites in one: Investors and Property Finance.
 * Insights use the core post type and portfolio items use a shared CPT.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register custom post types for the theme.
 *
 * @return void
 */
function cb_register_post_types() {

	register_post_type(
		'person',
		array(
			'labels'              => array(
				'name'               => 'People',
				'singular_name'      => 'Person',
				'add_new_item'       => 'Add New Person',
				'edit_item'          => 'Edit Person',
				'new_item'           => 'New Person',
				'view_item'          => 'View Person',
				'search_items'       => 'Search People',
				'not_found'          => 'No people found',
				'not_found_in_trash' => 'No people in trash',
			),
			'has_archive'         => false,
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_in_nav_menus'   => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'menu_position'       => 26,
			'menu_icon'           => 'dashicons-nametag',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'rewrite'             => false,
		)
	);

	cb_register_portfolio();
}

add_action( 'init', 'cb_register_post_types' );

/**
 * Register the Portfolio post type.
 *
 * Lives under /investors/portfolio/%postname%/. The archive is disabled because
 * /investors/portfolio/ is an editable WP Page; explicit rewrite rules below
 * keep page-vs-CPT resolution unambiguous.
 *
 * @return void
 */
function cb_register_portfolio() {
	$labels = array(
		'name'                  => _x( 'Portfolio', 'post type general name', 'cb-pluto2026' ),
		'singular_name'         => _x( 'Portfolio Item', 'post type singular name', 'cb-pluto2026' ),
		'menu_name'             => _x( 'Portfolio', 'admin menu', 'cb-pluto2026' ),
		'name_admin_bar'        => _x( 'Portfolio Item', 'add new on admin bar', 'cb-pluto2026' ),
		'add_new'               => _x( 'Add New', 'portfolio item', 'cb-pluto2026' ),
		'add_new_item'          => __( 'Add New Portfolio Item', 'cb-pluto2026' ),
		'new_item'              => __( 'New Portfolio Item', 'cb-pluto2026' ),
		'edit_item'             => __( 'Edit Portfolio Item', 'cb-pluto2026' ),
		'view_item'             => __( 'View Portfolio Item', 'cb-pluto2026' ),
		'all_items'             => __( 'All Portfolio Items', 'cb-pluto2026' ),
		'search_items'          => __( 'Search Portfolio', 'cb-pluto2026' ),
		'not_found'             => __( 'No portfolio items found.', 'cb-pluto2026' ),
		'not_found_in_trash'    => __( 'No portfolio items found in Trash.', 'cb-pluto2026' ),
		'featured_image'        => __( 'Featured image', 'cb-pluto2026' ),
		'set_featured_image'    => __( 'Set featured image', 'cb-pluto2026' ),
		'remove_featured_image' => __( 'Remove featured image', 'cb-pluto2026' ),
		'use_featured_image'    => __( 'Use as featured image', 'cb-pluto2026' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_rest'       => false,
		'query_var'          => true,
		'has_archive'        => false,
		'rewrite'            => array(
			'slug'       => 'property-finance/portfolio',
			'with_front' => false,
			'feeds'      => false,
		),
		'capability_type'    => 'post',
		'menu_position'      => 6,
		'menu_icon'          => 'dashicons-chart-line',
		'supports'           => array( 'title', 'thumbnail', 'revisions', 'author' ),
	);

	register_post_type( 'portfolio', $args );
}

/**
 * Add explicit rewrite rules so the editable WP Pages at the section index
 * URLs resolve to the Page, while child URLs resolve to the matching CPT
 * single.
 *
 * Without these, WP's rewrite resolution can be ambiguous when a Page slug
 * matches a CPT rewrite slug.
 *
 * @return void
 */
function cb_contextual_rewrite_rules() {
	// Insights index pages and single posts. Property Finance is canonical.
	add_rewrite_rule(
		'^investors/insights/?$',
		'index.php?pagename=investors/insights',
		'top'
	);
	add_rewrite_rule(
		'^investors/insights/page/([0-9]{1,})/?$',
		'index.php?pagename=investors/insights&paged=$matches[1]',
		'top'
	);
	add_rewrite_rule(
		'^investors/insights/([^/]+)/?$',
		'index.php?name=$matches[1]',
		'top'
	);
	add_rewrite_rule(
		'^property-finance/insights/?$',
		'index.php?pagename=property-finance/insights',
		'top'
	);
	add_rewrite_rule(
		'^property-finance/insights/page/([0-9]{1,})/?$',
		'index.php?pagename=property-finance/insights&paged=$matches[1]',
		'top'
	);
	add_rewrite_rule(
		'^property-finance/insights/([^/]+)/?$',
		'index.php?name=$matches[1]',
		'top'
	);

	// Portfolio index pages and single items. Property Finance is canonical.
	add_rewrite_rule(
		'^investors/portfolio/?$',
		'index.php?pagename=investors/portfolio',
		'top'
	);
	add_rewrite_rule(
		'^investors/portfolio/page/([0-9]{1,})/?$',
		'index.php?pagename=investors/portfolio&paged=$matches[1]',
		'top'
	);
	add_rewrite_rule(
		'^investors/portfolio/([^/]+)/?$',
		'index.php?post_type=portfolio&name=$matches[1]',
		'top'
	);
	add_rewrite_rule(
		'^property-finance/portfolio/?$',
		'index.php?pagename=property-finance/portfolio',
		'top'
	);
	add_rewrite_rule(
		'^property-finance/portfolio/page/([0-9]{1,})/?$',
		'index.php?pagename=property-finance/portfolio&paged=$matches[1]',
		'top'
	);
	add_rewrite_rule(
		'^property-finance/portfolio/([^/]+)/?$',
		'index.php?post_type=portfolio&name=$matches[1]',
		'top'
	);
}
add_action( 'init', 'cb_contextual_rewrite_rules', 20 );

/**
 * Return the contextual URL for a shared core post insight.
 *
 * @param int         $post_id Post ID.
 * @param string|null $context Optional context override.
 * @return string
 */
function cb_get_contextual_post_url( $post_id, $context = null ) {
	$post = get_post( $post_id );
	if ( ! $post || 'post' !== $post->post_type ) {
		return get_permalink( $post_id );
	}

	$context = null === $context ? cb_get_site_context() : $context;
	$base    = 'inv' === $context ? '/investors/insights/' : '/property-finance/insights/';

	return home_url( $base . $post->post_name . '/' );
}

/**
 * Return the contextual URL for a shared portfolio item.
 *
 * @param int         $post_id Post ID.
 * @param string|null $context Optional context override.
 * @return string
 */
function cb_get_contextual_portfolio_url( $post_id, $context = null ) {
	$post = get_post( $post_id );
	if ( ! $post || 'portfolio' !== $post->post_type ) {
		return get_permalink( $post_id );
	}

	$context = null === $context ? cb_get_site_context() : $context;
	$base    = 'inv' === $context ? '/investors/portfolio/' : '/property-finance/portfolio/';

	return home_url( $base . $post->post_name . '/' );
}

function cb_contextual_post_permalink( $permalink, $post ) {
	if ( ! $post || 'post' !== $post->post_type ) {
		return $permalink;
	}

	return cb_get_contextual_post_url( $post->ID );
}
add_filter( 'post_link', 'cb_contextual_post_permalink', 10, 2 );

function cb_contextual_portfolio_permalink( $permalink, $post ) {
	if ( ! $post || 'portfolio' !== $post->post_type ) {
		return $permalink;
	}

	return cb_get_contextual_portfolio_url( $post->ID );
}
add_filter( 'post_type_link', 'cb_contextual_portfolio_permalink', 10, 2 );

function cb_property_finance_canonical_url( $url ) {
	if ( is_singular( 'post' ) ) {
		return cb_get_contextual_post_url( get_queried_object_id(), 'pf' );
	}

	if ( is_singular( 'portfolio' ) ) {
		return cb_get_contextual_portfolio_url( get_queried_object_id(), 'pf' );
	}

	return $url;
}
add_filter( 'wpseo_canonical', 'cb_property_finance_canonical_url' );
add_filter( 'get_canonical_url', 'cb_property_finance_canonical_url' );

/**
 * Disable the block editor for portfolio post types.
 *
 * These items are edited via ACF field groups rather than Gutenberg content.
 *
 * @param bool   $use_block_editor Whether the post type can use the block editor.
 * @param string $post_type        Post type slug.
 * @return bool
 */
function cb_disable_block_editor_for_portfolio( $use_block_editor, $post_type ) {
	if ( 'portfolio' === $post_type ) {
		return false;
	}

	return $use_block_editor;
}
add_filter( 'use_block_editor_for_post_type', 'cb_disable_block_editor_for_portfolio', 10, 2 );
