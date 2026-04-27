<?php
/**
 * Custom taxonomies.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register custom taxonomies for the theme.
 *
 * @return void
 */
function cb_register_taxes() {

	// Siloed, tag-like topic taxonomies — one per side of the site.
	// No public archives (topics are editorial metadata only); exposed to the
	// REST API and admin column for editor convenience.
	$investor_topic_args = array(
		'labels'             => array(
			'name'                       => _x( 'Investor Topics', 'taxonomy general name', 'cb-pluto2026' ),
			'singular_name'              => _x( 'Investor Topic', 'taxonomy singular name', 'cb-pluto2026' ),
			'search_items'               => __( 'Search Investor Topics', 'cb-pluto2026' ),
			'popular_items'              => __( 'Popular Investor Topics', 'cb-pluto2026' ),
			'all_items'                  => __( 'All Investor Topics', 'cb-pluto2026' ),
			'edit_item'                  => __( 'Edit Investor Topic', 'cb-pluto2026' ),
			'update_item'                => __( 'Update Investor Topic', 'cb-pluto2026' ),
			'add_new_item'               => __( 'Add New Investor Topic', 'cb-pluto2026' ),
			'new_item_name'              => __( 'New Investor Topic', 'cb-pluto2026' ),
			'separate_items_with_commas' => __( 'Separate topics with commas', 'cb-pluto2026' ),
			'add_or_remove_items'        => __( 'Add or remove topics', 'cb-pluto2026' ),
			'choose_from_most_used'      => __( 'Choose from the most used topics', 'cb-pluto2026' ),
			'menu_name'                  => __( 'Topics', 'cb-pluto2026' ),
		),
		'public'             => false,
		'publicly_queryable' => false,
		'hierarchical'       => false,
		'show_ui'            => true,
		'show_in_nav_menus'  => false,
		'show_tagcloud'      => false,
		'show_in_quick_edit' => true,
		'show_admin_column'  => true,
		'show_in_rest'       => true,
		'rewrite'            => false,
		'query_var'          => false,
	);
	register_taxonomy( 'investor_topic', array( 'investor_insight' ), $investor_topic_args );

	$pf_topic_args = array(
		'labels'             => array(
			'name'                       => _x( 'PF Topics', 'taxonomy general name', 'cb-pluto2026' ),
			'singular_name'              => _x( 'PF Topic', 'taxonomy singular name', 'cb-pluto2026' ),
			'search_items'               => __( 'Search PF Topics', 'cb-pluto2026' ),
			'popular_items'              => __( 'Popular PF Topics', 'cb-pluto2026' ),
			'all_items'                  => __( 'All PF Topics', 'cb-pluto2026' ),
			'edit_item'                  => __( 'Edit PF Topic', 'cb-pluto2026' ),
			'update_item'                => __( 'Update PF Topic', 'cb-pluto2026' ),
			'add_new_item'               => __( 'Add New PF Topic', 'cb-pluto2026' ),
			'new_item_name'              => __( 'New PF Topic', 'cb-pluto2026' ),
			'separate_items_with_commas' => __( 'Separate topics with commas', 'cb-pluto2026' ),
			'add_or_remove_items'        => __( 'Add or remove topics', 'cb-pluto2026' ),
			'choose_from_most_used'      => __( 'Choose from the most used topics', 'cb-pluto2026' ),
			'menu_name'                  => __( 'Topics', 'cb-pluto2026' ),
		),
		'public'             => false,
		'publicly_queryable' => false,
		'hierarchical'       => false,
		'show_ui'            => true,
		'show_in_nav_menus'  => false,
		'show_tagcloud'      => false,
		'show_in_quick_edit' => true,
		'show_admin_column'  => true,
		'show_in_rest'       => true,
		'rewrite'            => false,
		'query_var'          => false,
	);
	register_taxonomy( 'pf_topic', array( 'pf_insight' ), $pf_topic_args );

	// Teams for the shared `person` CPT. Flat, editorial-only (no archives).
	// Terms like `leadership`, `lending-and-credit`, `investor-relations`.
	// The front-end team block will read the selected team slug(s) to decide
	// which side(s) of the site a given rendering applies to.
	$team_args = array(
		'labels'             => array(
			'name'                       => _x( 'Teams', 'taxonomy general name', 'cb-pluto2026' ),
			'singular_name'              => _x( 'Team', 'taxonomy singular name', 'cb-pluto2026' ),
			'search_items'               => __( 'Search Teams', 'cb-pluto2026' ),
			'popular_items'              => __( 'Popular Teams', 'cb-pluto2026' ),
			'all_items'                  => __( 'All Teams', 'cb-pluto2026' ),
			'edit_item'                  => __( 'Edit Team', 'cb-pluto2026' ),
			'update_item'                => __( 'Update Team', 'cb-pluto2026' ),
			'add_new_item'               => __( 'Add New Team', 'cb-pluto2026' ),
			'new_item_name'              => __( 'New Team', 'cb-pluto2026' ),
			'separate_items_with_commas' => __( 'Separate teams with commas', 'cb-pluto2026' ),
			'add_or_remove_items'        => __( 'Add or remove teams', 'cb-pluto2026' ),
			'choose_from_most_used'      => __( 'Choose from the most used teams', 'cb-pluto2026' ),
			'menu_name'                  => __( 'Teams', 'cb-pluto2026' ),
		),
		'public'             => false,
		'publicly_queryable' => false,
		'hierarchical'       => false,
		'show_ui'            => true,
		'show_in_nav_menus'  => false,
		'show_tagcloud'      => false,
		'show_in_quick_edit' => true,
		'show_admin_column'  => true,
		'show_in_rest'       => true,
		'rewrite'            => false,
		'query_var'          => false,
	);
	register_taxonomy( 'team', array( 'person' ), $team_args );
}
add_action( 'init', 'cb_register_taxes' );
