<?php
/**
 * cb-team person-contact form integration with Gravity Forms.
 *
 * The cb-team block renders a Gravity Forms form (ID stored in the
 * `contact_person_form_id` ACF option) inside a per-person modal. The form
 * carries a hidden "Recipient Person ID" field populated dynamically with
 * the person's WP post ID. On submit, this module:
 *
 *  1. Validates the post ID points to a published `person` with an email.
 *  2. Routes the notification "To" to that person's email and CCs the
 *     site-wide contact_email option. Email addresses are NEVER rendered
 *     into the page markup.
 *
 * Field IDs are resolved at runtime by GF Admin Field Label (case-insensitive)
 * with type-based fallbacks, cached per `$form['version']`. This keeps things
 * working when Gravity Forms renumbers field IDs after edits.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get the configured contact form ID from site-wide settings.
 *
 * @return int 0 if not configured.
 */
function cb_team_get_contact_form_id() {
	if ( ! function_exists( 'get_field' ) ) {
		return 0;
	}
	return (int) get_field( 'contact_person_form_id', 'option' );
}

/**
 * Resolve known field IDs on a given form by admin label, with type-based
 * fallbacks. Cached per form version.
 *
 * @param int $form_id Gravity Forms form ID.
 * @return array{version:string,name:?int,email:?int,message:?int,recipient:?int}|null
 */
function cb_team_resolve_form_fields( $form_id ) {
	$form_id = (int) $form_id;
	if ( ! $form_id || ! class_exists( 'GFAPI' ) ) {
		return null;
	}

	$cache_key = 'cb_team_form_fields_' . $form_id;
	$form      = GFAPI::get_form( $form_id );
	if ( ! $form ) {
		return null;
	}
	$version = isset( $form['version'] ) ? (string) $form['version'] : '';

	$cached = get_transient( $cache_key );
	if ( is_array( $cached ) && isset( $cached['version'] ) && $cached['version'] === $version ) {
		return $cached;
	}

	$by_label = array();
	$by_type  = array();
	if ( ! empty( $form['fields'] ) && is_array( $form['fields'] ) ) {
		foreach ( $form['fields'] as $f ) {
			$admin_label = '';
			if ( is_object( $f ) && isset( $f->adminLabel ) ) {
				$admin_label = (string) $f->adminLabel;
			} elseif ( is_array( $f ) && isset( $f['adminLabel'] ) ) {
				$admin_label = (string) $f['adminLabel'];
			}
			$type = is_object( $f ) ? (string) $f->type : (string) ( $f['type'] ?? '' );
			$id   = is_object( $f ) ? (int) $f->id     : (int) ( $f['id'] ?? 0 );
			if ( ! $id ) {
				continue;
			}
			if ( $admin_label ) {
				$by_label[ strtolower( trim( $admin_label ) ) ] = $id;
			}
			$by_type[ $type ][] = $id;
		}
	}

	$first_of = function ( $type ) use ( $by_type ) {
		return isset( $by_type[ $type ][0] ) ? (int) $by_type[ $type ][0] : null;
	};

	$resolved = array(
		'version'   => $version,
		'name'      => $by_label['name']                ?? ( $first_of( 'name' )     ?: $first_of( 'text' ) ),
		'email'     => $by_label['email']               ?? $first_of( 'email' ),
		'message'   => $by_label['message']             ?? $first_of( 'textarea' ),
		'recipient' => $by_label['recipient person id'] ?? $first_of( 'hidden' ),
	);

	set_transient( $cache_key, $resolved, DAY_IN_SECONDS );
	return $resolved;
}

/**
 * Module-scoped store for the recipient resolution made during pre-submission,
 * read back during notification routing.
 *
 * @param array|null $set Pass an array to set; null to read.
 * @return array{pid:int,email:string,name:string}|null
 */
function cb_team_recipient_store( $set = null ) {
	static $store = null;
	if ( is_array( $set ) ) {
		$store = $set;
	}
	return $store;
}

/**
 * Hook driver: registers GF filters for the configured form ID.
 */
function cb_team_register_gf_hooks() {
	$form_id = cb_team_get_contact_form_id();
	if ( ! $form_id ) {
		return;
	}
	add_filter( "gform_pre_submission_filter_{$form_id}", 'cb_team_validate_recipient' );
	add_filter( "gform_notification_{$form_id}", 'cb_team_route_notification', 10, 3 );
}
add_action( 'init', 'cb_team_register_gf_hooks' );

/**
 * Validate the submitted recipient_pid points to a real `person` with email.
 * Stash the resolution for the notification stage. Always returns the form
 * unchanged.
 *
 * @param array $form GF form definition.
 * @return array
 */
function cb_team_validate_recipient( $form ) {
	$ids = cb_team_resolve_form_fields( (int) $form['id'] );
	if ( ! $ids || empty( $ids['recipient'] ) ) {
		return $form;
	}
	$pid = isset( $_POST[ 'input_' . $ids['recipient'] ] )
		? (int) $_POST[ 'input_' . $ids['recipient'] ] // phpcs:ignore WordPress.Security.NonceVerification.Missing
		: 0;
	if ( $pid <= 0 ) {
		return $form;
	}
	$post = get_post( $pid );
	if ( ! $post || 'person' !== $post->post_type || 'publish' !== $post->post_status ) {
		return $form;
	}
	$email = (string) get_field( 'email', $pid );
	if ( ! $email || ! is_email( $email ) ) {
		return $form;
	}
	cb_team_recipient_store(
		array(
			'pid'   => $pid,
			'email' => $email,
			'name'  => get_the_title( $pid ),
		)
	);
	return $form;
}

/**
 * Route the GF notification To/CC at send time. The person's email is never
 * rendered into the page; we resolve it here from the post ID.
 *
 * @param array $notification GF notification.
 * @param array $form         GF form.
 * @param array $entry        GF entry.
 * @return array
 */
function cb_team_route_notification( $notification, $form, $entry ) {
	$store = cb_team_recipient_store();
	$cc    = function_exists( 'get_field' ) ? (string) get_field( 'contact_email', 'option' ) : '';

	if ( ! $store ) {
		// Fallback: route to the site contact only, flag in subject.
		if ( $cc ) {
			$notification['toType'] = 'email';
			$notification['to']     = $cc;
			$notification['cc']     = '';
		}
		if ( ! empty( $notification['subject'] ) ) {
			$notification['subject'] = '[unrouted] ' . $notification['subject'];
		}
		return $notification;
	}

	$ids = cb_team_resolve_form_fields( (int) $form['id'] );

	$notification['toType'] = 'email';
	$notification['to']     = $store['email'];
	if ( $cc ) {
		$notification['cc'] = $cc;
	}
	if ( $ids && ! empty( $ids['email'] ) ) {
		$notification['replyTo'] = '{:' . $ids['email'] . '}';
	}
	return $notification;
}
