<?php
/**
 * Custom Post Types Registration
 *
 * This file contains the code to register custom post types for the theme.
 *
 * The site is effectively two sites in one: Investors and Property Finance.
 * Each side has its own siloed Insights (and in future, Portfolio) CPT so that
 * URLs stay within their section context. Naming convention: {side}_{type},
 * e.g. investor_insight, pf_insight, investor_portfolio, pf_portfolio.
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

	cb_register_investor_insight();
	cb_register_pf_insight();
}

add_action( 'init', 'cb_register_post_types' );

/**
 * Register the Investor Insight post type.
 *
 * Lives under /investors/insights/%postname%/. The archive is disabled because
 * /investors/insights/ is an editable WP Page; an explicit rewrite rule below
 * keeps page-vs-CPT resolution unambiguous.
 *
 * @return void
 */
function cb_register_investor_insight() {
	$labels = array(
		'name'                  => _x( 'Investor Insights', 'post type general name', 'cb-pluto2026' ),
		'singular_name'         => _x( 'Investor Insight', 'post type singular name', 'cb-pluto2026' ),
		'menu_name'             => _x( 'Investor Insights', 'admin menu', 'cb-pluto2026' ),
		'name_admin_bar'        => _x( 'Investor Insight', 'add new on admin bar', 'cb-pluto2026' ),
		'add_new'               => _x( 'Add New', 'investor insight', 'cb-pluto2026' ),
		'add_new_item'          => __( 'Add New Investor Insight', 'cb-pluto2026' ),
		'new_item'              => __( 'New Investor Insight', 'cb-pluto2026' ),
		'edit_item'             => __( 'Edit Investor Insight', 'cb-pluto2026' ),
		'view_item'             => __( 'View Investor Insight', 'cb-pluto2026' ),
		'all_items'             => __( 'All Investor Insights', 'cb-pluto2026' ),
		'search_items'          => __( 'Search Investor Insights', 'cb-pluto2026' ),
		'not_found'             => __( 'No investor insights found.', 'cb-pluto2026' ),
		'not_found_in_trash'    => __( 'No investor insights found in Trash.', 'cb-pluto2026' ),
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
		'show_in_rest'       => true,
		'query_var'          => true,
		'has_archive'        => false,
		'rewrite'            => array(
			'slug'       => 'investors/insights',
			'with_front' => false,
			'feeds'      => false,
		),
		'capability_type'    => 'post',
		'menu_position'      => 5,
		'menu_icon'          => 'dashicons-chart-line',
		'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'custom-fields' ),
	);

	register_post_type( 'investor_insight', $args );
}

/**
 * Register the Property Finance Insight post type.
 *
 * Lives under /property-finance/insights/%postname%/. Same rationale as the
 * investor variant.
 *
 * @return void
 */
function cb_register_pf_insight() {
	$labels = array(
		'name'                  => _x( 'Property Finance Insights', 'post type general name', 'cb-pluto2026' ),
		'singular_name'         => _x( 'Property Finance Insight', 'post type singular name', 'cb-pluto2026' ),
		'menu_name'             => _x( 'PF Insights', 'admin menu', 'cb-pluto2026' ),
		'name_admin_bar'        => _x( 'PF Insight', 'add new on admin bar', 'cb-pluto2026' ),
		'add_new'               => _x( 'Add New', 'pf insight', 'cb-pluto2026' ),
		'add_new_item'          => __( 'Add New Property Finance Insight', 'cb-pluto2026' ),
		'new_item'              => __( 'New Property Finance Insight', 'cb-pluto2026' ),
		'edit_item'             => __( 'Edit Property Finance Insight', 'cb-pluto2026' ),
		'view_item'             => __( 'View Property Finance Insight', 'cb-pluto2026' ),
		'all_items'             => __( 'All Property Finance Insights', 'cb-pluto2026' ),
		'search_items'          => __( 'Search Property Finance Insights', 'cb-pluto2026' ),
		'not_found'             => __( 'No property finance insights found.', 'cb-pluto2026' ),
		'not_found_in_trash'    => __( 'No property finance insights found in Trash.', 'cb-pluto2026' ),
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
		'show_in_rest'       => true,
		'query_var'          => true,
		'has_archive'        => false,
		'rewrite'            => array(
			'slug'       => 'property-finance/insights',
			'with_front' => false,
			'feeds'      => false,
		),
		'capability_type'    => 'post',
		'menu_position'      => 6,
		'menu_icon'          => 'dashicons-building',
		'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'custom-fields' ),
	);

	register_post_type( 'pf_insight', $args );
}

/**
 * Add explicit rewrite rules so the editable WP Pages at /investors/insights/
 * and /property-finance/insights/ resolve to the Page, while child URLs
 * resolve to the matching CPT single.
 *
 * Without these, WP's rewrite resolution can be ambiguous when a Page slug
 * matches a CPT rewrite slug.
 *
 * @return void
 */
function cb_insights_rewrite_rules() {
	// Investor side.
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
		'index.php?post_type=investor_insight&name=$matches[1]',
		'top'
	);

	// Property Finance side.
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
		'index.php?post_type=pf_insight&name=$matches[1]',
		'top'
	);
}
add_action( 'init', 'cb_insights_rewrite_rules', 20 );
