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
		'hierarchical'       => true,
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

	// Hierarchical solution taxonomy for the shared portfolio CPT.
	$portfolio_solution_args = array(
		'labels'             => array(
			'name'              => _x( 'Portfolio Solutions', 'taxonomy general name', 'cb-pluto2026' ),
			'singular_name'     => _x( 'Portfolio Solution', 'taxonomy singular name', 'cb-pluto2026' ),
			'search_items'      => __( 'Search Portfolio Solutions', 'cb-pluto2026' ),
			'all_items'         => __( 'All Portfolio Solutions', 'cb-pluto2026' ),
			'parent_item'       => __( 'Parent Portfolio Solution', 'cb-pluto2026' ),
			'parent_item_colon' => __( 'Parent Portfolio Solution:', 'cb-pluto2026' ),
			'edit_item'         => __( 'Edit Portfolio Solution', 'cb-pluto2026' ),
			'update_item'       => __( 'Update Portfolio Solution', 'cb-pluto2026' ),
			'add_new_item'      => __( 'Add New Portfolio Solution', 'cb-pluto2026' ),
			'new_item_name'     => __( 'New Portfolio Solution', 'cb-pluto2026' ),
			'menu_name'         => __( 'Solutions', 'cb-pluto2026' ),
		),
		'public'             => false,
		'publicly_queryable' => false,
		'hierarchical'       => true,
		'show_ui'            => true,
		'show_in_nav_menus'  => false,
		'show_tagcloud'      => false,
		'show_in_quick_edit' => true,
		'show_admin_column'  => true,
		'show_in_rest'       => true,
		'rewrite'            => false,
		'query_var'          => false,
	);
	register_taxonomy( 'portfolio_solution', array( 'portfolio' ), $portfolio_solution_args );
}
add_action( 'init', 'cb_register_taxes' );
