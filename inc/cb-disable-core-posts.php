<?php
/**
 * Disable the core `post` post type.
 *
 * This site does not use core posts. The two sides (Investors and Property
 * Finance) each have their own insight CPT. Core `post` cannot be safely
 * unregistered — WP admin, REST, and various core queries assume it exists —
 * so instead we hide it from the editorial UI, strip it from front-end
 * queries, disable feeds, and 404 orphaned archive URLs.
 *
 * If core posts are ever needed again, remove this file from the require list
 * in inc/cb-theme.php and flush permalinks.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove the Posts menu entry from wp-admin.
 *
 * @return void
 */
function cb_remove_posts_admin_menu() {
	remove_menu_page( 'edit.php' );
}
add_action( 'admin_menu', 'cb_remove_posts_admin_menu' );

/**
 * Redirect any direct hit on the core-post admin screens to the dashboard.
 *
 * Covers: post list, new post, and the Categories / Tags taxonomy screens.
 *
 * @param WP_Screen $screen Current admin screen.
 * @return void
 */
function cb_redirect_core_post_screens( $screen ) {
	if ( ! is_admin() || ! $screen instanceof WP_Screen ) {
		return;
	}

	$is_post_list = ( 'edit-post' === $screen->id );
	$is_post_new  = ( 'post' === $screen->id && 'post' === $screen->post_type );
	$is_core_tax  = ( 'edit-tags' === $screen->base && in_array( $screen->taxonomy, array( 'category', 'post_tag' ), true ) )
		|| ( 'term' === $screen->base && in_array( $screen->taxonomy, array( 'category', 'post_tag' ), true ) );

	if ( $is_post_list || $is_post_new || $is_core_tax ) {
		wp_safe_redirect( admin_url( 'index.php' ) );
		exit;
	}
}
add_action( 'current_screen', 'cb_redirect_core_post_screens' );

/**
 * Remove the "New Post" node from the admin bar.
 *
 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance.
 * @return void
 */
function cb_remove_admin_bar_new_post( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'new-post' );
}
add_action( 'admin_bar_menu', 'cb_remove_admin_bar_new_post', 999 );

/**
 * Remove dashboard widgets that surface core post content.
 *
 * @return void
 */
function cb_remove_core_post_dashboard_widgets() {
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_recent_posts', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
}
add_action( 'wp_dashboard_setup', 'cb_remove_core_post_dashboard_widgets' );

/**
 * Scope front-end search to the two insight CPTs. Core posts are excluded
 * entirely; future CPTs (e.g. investor_portfolio, pf_portfolio) should be
 * added here when they land.
 *
 * @param WP_Query $query Current query.
 * @return void
 */
function cb_scope_search_to_insights( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}

	$query->set( 'post_type', array( 'investor_insight', 'pf_insight' ) );
}
add_action( 'pre_get_posts', 'cb_scope_search_to_insights' );

/**
 * Disable the default RSS/Atom feeds. Each CPT has its own feeds disabled via
 * its rewrite args; this additionally unhooks the generic /feed/ endpoints.
 *
 * @return void
 */
function cb_disable_default_feeds() {
	remove_action( 'do_feed_rdf', 'do_feed_rdf' );
	remove_action( 'do_feed_rss', 'do_feed_rss' );
	remove_action( 'do_feed_rss2', 'do_feed_rss2', 10 );
	remove_action( 'do_feed_atom', 'do_feed_atom', 10 );
	remove_action( 'do_feed_rss2_comments', 'do_feed_rss2_comments', 10 );
	remove_action( 'do_feed_atom_comments', 'do_feed_atom_comments', 10 );

	add_action( 'do_feed', 'cb_kill_feed', 1 );
	add_action( 'do_feed_rdf', 'cb_kill_feed', 1 );
	add_action( 'do_feed_rss', 'cb_kill_feed', 1 );
	add_action( 'do_feed_rss2', 'cb_kill_feed', 1 );
	add_action( 'do_feed_atom', 'cb_kill_feed', 1 );
	add_action( 'do_feed_rss2_comments', 'cb_kill_feed', 1 );
	add_action( 'do_feed_atom_comments', 'cb_kill_feed', 1 );
}
add_action( 'init', 'cb_disable_default_feeds' );

/**
 * 404 any feed request.
 *
 * @return void
 */
function cb_kill_feed() {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
	exit;
}

/**
 * Remove feed link tags from wp_head so browsers and crawlers don't discover
 * the now-404ing feeds.
 *
 * @return void
 */
function cb_remove_feed_head_links() {
	remove_action( 'wp_head', 'feed_links', 2 );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
}
add_action( 'init', 'cb_remove_feed_head_links' );

/**
 * 404 orphaned archive URLs: category, tag, and author archives are no longer
 * meaningful with core posts disabled and siloed topic taxonomies in use.
 *
 * @return void
 */
function cb_four_oh_four_orphaned_archives() {
	if ( is_admin() ) {
		return;
	}

	if ( is_category() || is_tag() || is_author() ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}
}
add_action( 'template_redirect', 'cb_four_oh_four_orphaned_archives' );
