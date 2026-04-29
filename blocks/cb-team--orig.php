<?php
/**
 * Block template for CB Team.
 *
 * Displays a grid of `person` posts filtered by selected `team` taxonomy
 * term(s). Each card shows the featured image (greyscale), name, role and
 * contact icons. Clicking the image / name / role opens a modal with the
 * full bio when present.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

// ACF fields.
$team_ids = get_field( 'teams' );

if ( ! is_array( $team_ids ) ) {
	$team_ids = $team_ids ? array( $team_ids ) : array();
}
$team_ids = array_filter( array_map( 'intval', $team_ids ) );

// Custom classes.
$custom_classes = '';
if ( isset( $block['className'] ) ) {
	$class_array    = explode( ' ', $block['className'] );
	$filtered       = array_filter(
		$class_array,
		function ( $item ) {
			return ! preg_match( '/^wp-/', $item );
		}
	);
	$custom_classes = implode( ' ', $filtered );
}

$bg = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';

$block_uid = 'cb-team-' . uniqid();

// Editor preview message when nothing chosen.
$is_preview = ! empty( $block['data']['_is_preview'] ) || ( function_exists( 'acf_is_block_editor' ) && false );
if ( empty( $team_ids ) ) {
	?>
	<section class="cb-team cb-team--empty <?= esc_attr( trim( $bg . ' ' . $fg . ' ' . $custom_classes ) ); ?>">
		<div class="container py-5">
			<p class="text-muted mb-0"><em><?php esc_html_e( 'Select one or more Teams to display.', 'cb-pluto2026' ); ?></em></p>
		</div>
	</section>
	<?php
	return;
}

// Build the query: people in any of the chosen teams. We sort by surname
// (last word of the post title) in PHP since WP can't ORDER BY surname.
$query = new WP_Query(
	array(
		'post_type'      => 'person',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'team',
				'field'    => 'term_id',
				'terms'    => $team_ids,
			),
		),
		'no_found_rows'  => true,
	)
);

$people = $query->posts ? $query->posts : array();
wp_reset_postdata();

/**
 * Surname extraction: strip suffixes (Jr., III, etc.), take last whitespace-
 * separated token, lowercase + collation-friendly.
 */
$cb_team_surname = function ( $title ) {
	$t = trim( wp_strip_all_tags( (string) $title ) );
	// Strip common suffixes.
	$t     = preg_replace( '/\b(Jr\.?|Sr\.?|I{1,3}V?|IV|V|PhD|MBE|OBE|CBE)\b\.?$/i', '', $t );
	$t     = trim( $t );
	$parts = preg_split( '/\s+/', $t );
	$last  = $parts ? end( $parts ) : $t;
	return function_exists( 'remove_accents' ) ? remove_accents( $last ) : $last;
};

usort(
	$people,
	function ( $a, $b ) use ( $cb_team_surname ) {
		$cmp = strcasecmp( $cb_team_surname( $a->post_title ), $cb_team_surname( $b->post_title ) );
		if ( 0 !== $cmp ) {
			return $cmp;
		}
		return strcasecmp( $a->post_title, $b->post_title );
	}
);

// Look up the chosen team terms (preserve picker order) for filter buttons.
$team_terms = array();
foreach ( $team_ids as $tid ) {
	$team_term = get_term( $tid, 'team' );
	if ( $team_term && ! is_wp_error( $team_term ) ) {
		$team_terms[ $tid ] = $team_term;
	}
}

$missing_img = get_stylesheet_directory_uri() . '/img/missing-person.jpg';

// Gravity Forms "Contact a person" form ID, configured via Site-Wide Settings.
$contact_form_id = function_exists( 'cb_team_get_contact_form_id' ) ? cb_team_get_contact_form_id() : 0;

// Resolve the recipient field ID once, so the JS can target it. We render
// ONE contact form per page (not per card) — multiple copies would all share
// the same wrapper / iframe target IDs, breaking AJAX submission.
$contact_field_ids = ( $contact_form_id && function_exists( 'cb_team_resolve_form_fields' ) )
	? cb_team_resolve_form_fields( $contact_form_id )
	: null;

	$recipient_field_id = $contact_field_ids && ! empty( $contact_field_ids['recipient'] )
	? (int) $contact_field_ids['recipient']
	: 0;

// SVG icons (inline so we don't depend on an icon font).
$icon_email = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" width="18" height="18"><path fill="currentColor" d="M2 5.5A2.5 2.5 0 0 1 4.5 3h15A2.5 2.5 0 0 1 22 5.5v13a2.5 2.5 0 0 1-2.5 2.5h-15A2.5 2.5 0 0 1 2 18.5v-13Zm2.5-.5a.5.5 0 0 0-.5.5v.41l8 5.2 8-5.2V5.5a.5.5 0 0 0-.5-.5h-15Zm15.5 2.79-7.45 4.84a1 1 0 0 1-1.1 0L4 7.79V18.5a.5.5 0 0 0 .5.5h15a.5.5 0 0 0 .5-.5V7.79Z"/></svg>';
$icon_phone = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" width="18" height="18"><path fill="currentColor" d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.5 11.5 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1.04l-2.22 2.19Z"/></svg>';
$icon_li    = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" width="18" height="18"><path fill="currentColor" d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5ZM3 9.75h4V21H3V9.75ZM9.5 9.75h3.83v1.54h.05c.53-1 1.84-2.06 3.79-2.06 4.05 0 4.8 2.66 4.8 6.13V21h-4v-4.86c0-1.16-.02-2.65-1.62-2.65-1.62 0-1.86 1.27-1.86 2.57V21h-4V9.75Z"/></svg>';
?>
<section id="<?= esc_attr( $block_uid ); ?>" class="cb-team full-flourish <?= esc_attr( trim( $bg . ' ' . $fg . ' ' . $custom_classes ) ); ?>">
	<div class="container py-5">
		<?php if ( empty( $people ) ) : ?>
			<p class="text-muted"><em><?php esc_html_e( 'No people found in the selected team(s).', 'cb-pluto2026' ); ?></em></p>
		<?php else : ?>
			<?php if ( count( $team_terms ) > 1 ) : ?>
				<div class="cb-team__filters mb-4" role="group" aria-label="<?php esc_attr_e( 'Filter by team', 'cb-pluto2026' ); ?>">
					<button type="button" class="cb-team__filter is-active" data-cb-team-filter="all" aria-pressed="true"><?php esc_html_e( 'All', 'cb-pluto2026' ); ?></button>
					<?php foreach ( $team_terms as $tid => $team_term ) : ?>
						<button type="button" class="cb-team__filter" data-cb-team-filter="<?= esc_attr( $tid ); ?>" aria-pressed="false"><?= esc_html( $team_term->name ); ?></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<div class="row gy-4 cb-team__grid">
				<?php
				foreach ( $people as $p ) :
					$pid       = (int) $p->ID;
					$name      = get_the_title( $p );
					$urole     = (string) get_field( 'role', $pid );
					$email     = (string) get_field( 'email', $pid );
					$phone     = (string) get_field( 'phone', $pid );
					$linkedin  = (string) get_field( 'linkedin_url', $pid );
					$bio_html  = apply_filters( 'the_content', $p->post_content );
					$has_bio   = (bool) trim( wp_strip_all_tags( $p->post_content ) );
					$thumb_id  = get_post_thumbnail_id( $pid );
					$img_url   = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium_large' ) : $missing_img;
					$img_url   = $img_url ? $img_url : $missing_img;
					$modal_id  = 'cb-team-modal-' . $pid;
					$big_url   = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : $missing_img;
					$big_url   = $big_url ? $big_url : $missing_img;
					$btn_attrs = $has_bio ? sprintf( ' type="button" data-cb-team-open="%s"', esc_attr( $modal_id ) ) : ' type="button" disabled';

					// First name for "Contact <FirstName>" labelling.
					$first_name = '';
					$name_parts = preg_split( '/\s+/', trim( wp_strip_all_tags( $name ) ) );
					if ( $name_parts ) {
						$first_name = (string) reset( $name_parts );
					}
					$contact_modal_id = 'cb-team-contact-' . $pid;
					$has_contact      = ( $email && $contact_form_id );

					// Person's team term IDs intersected with the picker selection.
					$person_term_ids = wp_get_post_terms( $pid, 'team', array( 'fields' => 'ids' ) );
					if ( is_wp_error( $person_term_ids ) ) {
						$person_term_ids = array();
					}
					$person_team_attr = implode( ' ', array_map( 'intval', array_intersect( $person_term_ids, array_keys( $team_terms ) ) ) );
					?>
					<div class="col-12 col-sm-6 col-lg-4 col-xl-3 cb-team__col" data-cb-team-ids="<?= esc_attr( $person_team_attr ); ?>">
						<article class="cb-team__card" data-person-id="<?= esc_attr( $pid ); ?>">
							<?php if ( $has_bio ) : ?>
								<button class="cb-team__media" type="button" data-cb-team-open="<?= esc_attr( $modal_id ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: person name */ __( 'View profile for %s', 'cb-pluto2026' ), $name ) ); ?>">
									<img src="<?= esc_url( $img_url ); ?>" alt="<?= esc_attr( $name ); ?>" loading="lazy" />
								</button>
							<?php else : ?>
								<div class="cb-team__media cb-team__media--static">
									<img src="<?= esc_url( $img_url ); ?>" alt="<?= esc_attr( $name ); ?>" loading="lazy" />
								</div>
							<?php endif; ?>

							<div class="cb-team__body">
								<?php if ( $has_bio ) : ?>
									<h3 class="cb-team__name">
										<button class="cb-team__name-btn" type="button" data-cb-team-open="<?= esc_attr( $modal_id ); ?>"><?= esc_html( $name ); ?></button>
									</h3>
									<?php if ( $urole ) : ?>
										<p class="cb-team__role">
											<button class="cb-team__role-btn" type="button" data-cb-team-open="<?= esc_attr( $modal_id ); ?>"><?= esc_html( $urole ); ?></button>
										</p>
									<?php endif; ?>
								<?php else : ?>
									<h3 class="cb-team__name"><?= esc_html( $name ); ?></h3>
									<?php if ( $urole ) : ?>
										<p class="cb-team__role"><?= esc_html( $urole ); ?></p>
									<?php endif; ?>
								<?php endif; ?>

								<?php if ( $has_contact || $phone || $linkedin ) : ?>
									<ul class="cb-team__contacts" aria-label="<?php esc_attr_e( 'Contact', 'cb-pluto2026' ); ?>">
										<?php if ( $has_contact ) : ?>
											<li><button type="button" class="cb-team__contact-btn" data-cb-team-contact data-cb-team-pid="<?= esc_attr( $pid ); ?>" data-cb-team-name="<?= esc_attr( $name ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: person first name */ __( 'Contact %s', 'cb-pluto2026' ), $first_name ? $first_name : $name ) ); ?>"><?= $icon_email; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button></li>
										<?php endif; ?>
										<?php if ( $phone ) : ?>
											<li><a href="tel:<?= esc_attr( parse_phone( $phone ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: person name */ __( 'Call %s', 'cb-pluto2026' ), $name ) ); ?>"><?= $icon_phone; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
										<?php endif; ?>
										<?php if ( $linkedin ) : ?>
											<li><a href="<?= esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: person name */ __( '%s on LinkedIn', 'cb-pluto2026' ), $name ) ); ?>"><?= $icon_li; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
										<?php endif; ?>
									</ul>
								<?php endif; ?>
							</div>
						</article>

						<?php if ( $has_bio ) : ?>
							<div class="cb-team__modal" id="<?= esc_attr( $modal_id ); ?>" role="dialog" aria-modal="true" aria-labelledby="<?= esc_attr( $modal_id ); ?>-title" hidden>
								<div class="cb-team__modal-overlay" data-cb-team-close></div>
								<div class="cb-team__modal-dialog" role="document">
									<button class="cb-team__modal-close" type="button" data-cb-team-close aria-label="<?php esc_attr_e( 'Close', 'cb-pluto2026' ); ?>">&times;</button>
									<div class="cb-team__modal-body">
										<div class="cb-team__modal-image">
											<img src="<?= esc_url( $big_url ); ?>" alt="<?= esc_attr( $name ); ?>" />
										</div>
										<div class="cb-team__modal-content">
											<h3 class="cb-team__modal-name" id="<?= esc_attr( $modal_id ); ?>-title"><?= esc_html( $name ); ?></h3>
											<?php if ( $urole ) : ?>
												<p class="cb-team__modal-role"><?= esc_html( $urole ); ?></p>
											<?php endif; ?>
											<div class="cb-team__modal-bio"><?= wp_kses_post( $bio_html ); ?></div>
											<?php if ( $has_contact || $phone || $linkedin ) : ?>
												<ul class="cb-team__contacts cb-team__contacts--modal" aria-label="<?php esc_attr_e( 'Contact', 'cb-pluto2026' ); ?>">
													<?php if ( $has_contact ) : ?>
														<li><button type="button" class="cb-team__contact-btn" data-cb-team-contact data-cb-team-pid="<?= esc_attr( $pid ); ?>" data-cb-team-name="<?= esc_attr( $name ); ?>"><?= $icon_email; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( sprintf( /* translators: %s: person first name */ __( 'Contact %s', 'cb-pluto2026' ), $first_name ? $first_name : $name ) ); ?></span></button></li>
													<?php endif; ?>
													<?php if ( $phone ) : ?>
														<li><a href="tel:<?= esc_attr( parse_phone( $phone ) ); ?>"><?= $icon_phone; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?= esc_html( $phone ); ?></span></a></li>
													<?php endif; ?>
													<?php if ( $linkedin ) : ?>
														<li><a href="<?= esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer"><?= $icon_li; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span>LinkedIn</span></a></li>
													<?php endif; ?>
												</ul>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
/*
 * Single shared contact modal — rendered once per page (not per card) to
 * avoid colliding GF form/wrapper/iframe IDs that would otherwise break
 * AJAX submission, validation rendering, and entry creation. The hidden
 * `recipient_pid` field is populated on the fly when a contact button is
 * clicked.
 */
static $cb_team_contact_modal_emitted = false;
if ( ! $cb_team_contact_modal_emitted && $contact_form_id && $recipient_field_id ) :
	$cb_team_contact_modal_emitted = true;
	?>
	<div class="cb-team__modal cb-team__modal--contact" id="cb-team-contact-modal" role="dialog" aria-modal="true" aria-labelledby="cb-team-contact-modal-title" data-cb-team-recipient-field="<?= esc_attr( $recipient_field_id ); ?>" data-cb-team-form-id="<?= esc_attr( $contact_form_id ); ?>" hidden>
		<div class="cb-team__modal-overlay" data-cb-team-close></div>
		<div class="cb-team__modal-dialog" role="document">
			<button class="cb-team__modal-close" type="button" data-cb-team-close aria-label="<?php esc_attr_e( 'Close', 'cb-pluto2026' ); ?>">&times;</button>
			<div class="cb-team__modal-content">
				<h3 class="cb-team__modal-name" id="cb-team-contact-modal-title"><?php esc_html_e( 'Contact', 'cb-pluto2026' ); ?></h3>
				<?php
				if ( function_exists( 'gravity_form' ) ) {
					gravity_form(
						$contact_form_id,
						false,
						false,
						false,
						array( 'recipient_pid' => 0 ),
						true
					);
				} else {
					echo '<p class="text-muted"><em>' . esc_html__( 'Contact form unavailable.', 'cb-pluto2026' ) . '</em></p>';
				}
				?>
			</div>
		</div>
	</div>
<?php endif; ?>

<?php
/*
 * Output the modal JS once per page (front-end only). The block markup is
 * self-contained for the editor preview; behaviour binds delegated.
 */
static $cb_team_assets_emitted = false;
if ( ! $cb_team_assets_emitted ) :
	$cb_team_assets_emitted = true;
	?>
<script>
(function () {
	if (window.cbTeamModalsBound) return;
	window.cbTeamModalsBound = true;

	var lastFocus = null;

	// Portal all .cb-team__modal nodes to <body> so they escape any ancestor
	// stacking context (e.g. `.full-flourish { isolation: isolate }`). Without
	// this, the fixed-top header (z-index: 1030) renders above the modal even
	// though the modal sets a higher z-index, because z-index is only
	// meaningful within the same stacking context.
	function portalModals(root) {
		(root || document).querySelectorAll('.cb-team__modal').forEach(function (m) {
			if (m.parentNode !== document.body) {
				document.body.appendChild(m);
			}
		});
	}
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () { portalModals(); });
	} else {
		portalModals();
	}

	function openModal(id) {
		var modal = document.getElementById(id);
		if (!modal) return;
		lastFocus = document.activeElement;
		modal.hidden = false;
		document.body.classList.add('cb-team-modal-open');
		// focus the close button for a11y.
		var closeBtn = modal.querySelector('[data-cb-team-close]');
		if (closeBtn && typeof closeBtn.focus === 'function') closeBtn.focus();
	}

	function closeModal(modal) {
		if (!modal) return;
		modal.hidden = true;
		// only remove body lock if no other modals are open.
		if (!document.querySelector('.cb-team__modal:not([hidden])')) {
			document.body.classList.remove('cb-team-modal-open');
		}
		if (lastFocus && typeof lastFocus.focus === 'function') {
			try { lastFocus.focus(); } catch (e) {}
		}
	}

	document.addEventListener('click', function (e) {
		// Contact buttons: populate the shared contact modal with the chosen
		// person's PID + name, then open it. We render only one GF form per
		// page; rendering one per card would collide on form/wrapper/iframe
		// IDs and break AJAX submission entirely.
		var contactBtn = e.target.closest('[data-cb-team-contact]');
		if (contactBtn) {
			e.preventDefault();
			var modal = document.getElementById('cb-team-contact-modal');
			if (!modal) return;
			var pid = contactBtn.getAttribute('data-cb-team-pid') || '';
			var name = contactBtn.getAttribute('data-cb-team-name') || '';
			var fieldId = modal.getAttribute('data-cb-team-recipient-field') || '';
			var formId = modal.getAttribute('data-cb-team-form-id') || '';
			// Hidden recipient field — set both the visible-named input and any
			// `gform_field_values` query-string variant GF may use. The latter is
			// what GF actually reads server-side when the field is admin-only
			// (no input_X_Y element is rendered in that case).
			if (fieldId) {
				var sel = '#input_' + formId + '_' + fieldId
					+ ', input[name="input_' + fieldId + '"]';
				modal.querySelectorAll(sel).forEach(function (i) { i.value = pid; });
			}
			// Always rewrite gform_field_values: replace recipient_pid=<n> or
			// append it if missing.
			modal.querySelectorAll('input[name="gform_field_values"]').forEach(function (i) {
				var v = i.value || '';
				if (/(^|&)recipient_pid=/.test(v)) {
					v = v.replace(/(^|&)recipient_pid=[^&]*/, '$1recipient_pid=' + encodeURIComponent(pid));
				} else {
					v = v ? v + '&recipient_pid=' + encodeURIComponent(pid) : 'recipient_pid=' + encodeURIComponent(pid);
				}
				i.value = v;
			});
			// Title
			var title = modal.querySelector('#cb-team-contact-modal-title');
			if (title && name) {
				title.textContent = (window.cbTeamContactPrefix || 'Contact ') + name;
			}
			openModal('cb-team-contact-modal');
			return;
		}

		var opener = e.target.closest('[data-cb-team-open]');
		if (opener) {
			e.preventDefault();
			openModal(opener.getAttribute('data-cb-team-open'));
			return;
		}
		var closer = e.target.closest('[data-cb-team-close]');
		if (closer) {
			e.preventDefault();
			var modal2 = closer.closest('.cb-team__modal');
			closeModal(modal2);
		}
	});

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') {
			var open = document.querySelector('.cb-team__modal:not([hidden])');
			if (open) closeModal(open);
		}
	});

	// Filter buttons: hide cards whose data-cb-team-ids doesn't include the
	// chosen team. Scope to the section so multiple cb-team blocks coexist.
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('[data-cb-team-filter]');
		if (!btn) return;
		e.preventDefault();
		var section = btn.closest('.cb-team');
		if (!section) return;
		var filter = btn.getAttribute('data-cb-team-filter');
		section.querySelectorAll('[data-cb-team-filter]').forEach(function (b) {
			var active = b === btn;
			b.classList.toggle('is-active', active);
			b.setAttribute('aria-pressed', active ? 'true' : 'false');
		});
		section.querySelectorAll('.cb-team__col').forEach(function (col) {
			if (filter === 'all') {
				col.hidden = false;
				return;
			}
			var ids = (col.getAttribute('data-cb-team-ids') || '').split(/\s+/);
			col.hidden = ids.indexOf(filter) === -1;
		});
	});
})();
</script>
<?php endif; ?>
