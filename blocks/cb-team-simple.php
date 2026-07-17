<?php
/**
 * Block template for CB Team Simple.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$block_id = $block['anchor'] ?? ( 'cb-team-simple-' . uniqid() );

// Support Gutenberg color picker.
$bg = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';

$context = cb_get_site_context();
if ( 'pf' === $context ) {
	$bg = 'has-grey-400-background-color';
	$fg = 'has-grey-900-color';
}

// Extract custom classes (filter out wp-generated ones).
$custom_classes = '';
if ( isset( $block['className'] ) ) {
	$class_array    = explode( ' ', $block['className'] );
	$filtered       = array_filter(
		$class_array,
		function ( $item ) {
			return '' !== $item && ! preg_match( '/^wp-/', $item );
		}
	);
	$custom_classes = implode( ' ', $filtered );
}

$initial_team_filter = get_field( 'initial_team_filter' );

$people = get_posts(
	array(
		'post_type'      => 'person',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'no_found_rows'  => true,
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);

if ( empty( $people ) ) {
	return;
}

$teams = get_terms(
	array(
		'taxonomy'   => 'team',
		'orderby'    => 'name',
		'order'      => 'ASC',
		'hide_empty' => true,
	)
);

if ( is_wp_error( $teams ) ) {
	$teams = array();
}

$initial_team_slugs = array();
if ( ! empty( $initial_team_filter ) && is_array( $initial_team_filter ) ) {
	$initial_team_slugs = get_terms(
		array(
			'taxonomy'   => 'team',
			'include'    => $initial_team_filter,
			'fields'     => 'slugs',
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $initial_team_slugs ) ) {
		$initial_team_slugs = array();
	}
}

// Override with URL parameter ?t= (comma-separated team slugs).
if ( isset( $_GET['t'] ) && is_string( $_GET['t'] ) && '' !== trim( $_GET['t'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$url_terms = array_map( 'sanitize_title', explode( ',', $_GET['t'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$url_terms = array_filter( $url_terms );
	if ( in_array( 'all', $url_terms, true ) ) {
		$initial_team_slugs = array();
	} elseif ( ! empty( $url_terms ) ) {
		$initial_team_slugs = $url_terms;
	}
}

$missing_img = get_stylesheet_directory_uri() . '/img/missing-person.jpg';

// SVG icons (inline so we don't depend on an icon font).
$icon_email = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" width="18" height="18"><path fill="currentColor" d="M2 5.5A2.5 2.5 0 0 1 4.5 3h15A2.5 2.5 0 0 1 22 5.5v13a2.5 2.5 0 0 1-2.5 2.5h-15A2.5 2.5 0 0 1 2 18.5v-13Zm2.5-.5a.5.5 0 0 0-.5.5v.41l8 5.2 8-5.2V5.5a.5.5 0 0 0-.5-.5h-15Zm15.5 2.79-7.45 4.84a1 1 0 0 1-1.1 0L4 7.79V18.5a.5.5 0 0 0 .5.5h15a.5.5 0 0 0 .5-.5V7.79Z"/></svg>';
$icon_phone = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" width="18" height="18"><path fill="currentColor" d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.5 11.5 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1.04l-2.22 2.19Z"/></svg>';
$icon_li    = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" width="18" height="18"><path fill="currentColor" d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5ZM3 9.75h4V21H3V9.75ZM9.5 9.75h3.83v1.54h.05c.53-1 1.84-2.06 3.79-2.06 4.05 0 4.8 2.66 4.8 6.13V21h-4v-4.86c0-1.16-.02-2.65-1.62-2.65-1.62 0-1.86 1.27-1.86 2.57V21h-4V9.75Z"/></svg>';

$contact_form_id    = function_exists( 'cb_team_get_contact_form_id' ) ? cb_team_get_contact_form_id() : 0;
$contact_field_ids  = ( $contact_form_id && function_exists( 'cb_team_resolve_form_fields' ) )
	? cb_team_resolve_form_fields( $contact_form_id )
	: null;
$recipient_field_id = $contact_field_ids && ! empty( $contact_field_ids['recipient'] )
	? (int) $contact_field_ids['recipient']
	: 0;
?>
<section id="<?= esc_attr( $block_id ); ?>" class="cb-team-simple <?= esc_attr( trim( $bg . ' ' . $fg . ' ' . $custom_classes ) ); ?>">
	<div class="container py-5">
		<?php if ( ! empty( $teams ) ) : ?>
			<div class="cb-team-simple__filters" aria-label="<?php esc_attr_e( 'Filter team', 'cb-pluto2026' ); ?>">
				<button class="cb-team-simple__filter<?= empty( $initial_team_slugs ) ? ' cb-team-simple__filter--active' : ''; ?>" type="button" data-team-filter="all"><?php esc_html_e( 'All', 'cb-pluto2026' ); ?></button>
				<?php foreach ( $teams as $team ) : ?>
					<button class="cb-team-simple__filter<?= in_array( $team->slug, $initial_team_slugs, true ) ? ' cb-team-simple__filter--active' : ''; ?>" type="button" data-team-filter="<?= esc_attr( $team->slug ); ?>"><?= esc_html( $team->name ); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="row gy-4 cb-team-simple__grid">
			<?php foreach ( $people as $person ) : ?>
				<?php
				$person_id = (int) $person->ID;
				$name      = get_the_title( $person );
				$urole     = (string) get_field( 'role', $person_id );
				$email     = (string) get_field( 'email', $person_id );
				$phone     = (string) get_field( 'phone', $person_id );
				$linkedin  = (string) get_field( 'linkedin_url', $person_id );
				$bio_html  = apply_filters( 'the_content', $person->post_content );
				$has_bio   = (bool) trim( wp_strip_all_tags( $person->post_content ) );
				$terms     = wp_get_post_terms( $person_id, 'team' );

				if ( is_wp_error( $terms ) ) {
					$terms = array();
				}

				$team_slugs = array_map(
					function ( $term ) {
						return sanitize_html_class( $term->slug );
					},
					$terms
				);
				$team_names = wp_list_pluck( $terms, 'name' );

				$thumb_id    = get_post_thumbnail_id( $person_id );
				$img_url     = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium_large' ) : $missing_img;
				$img_url     = $img_url ? $img_url : $missing_img;
				$big_url     = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : $missing_img;
				$big_url     = $big_url ? $big_url : $missing_img;
				$modal_id    = 'cb-team-simple-modal-' . $person_id;
				$has_contact = (bool) ( $email && $contact_form_id && $recipient_field_id );
				?>
				<div class="col-12 col-sm-6 col-lg-4 col-xl-3 cb-team-simple__col" data-team="<?= esc_attr( implode( ' ', $team_slugs ) ); ?>">
					<article class="cb-team-simple__card">
						<?php if ( $has_bio ) : ?>
							<button class="cb-team-simple__media" type="button" data-cb-team-simple-open="<?= esc_attr( $modal_id ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: person name */ __( 'View profile for %s', 'cb-pluto2026' ), $name ) ); ?>">
								<img src="<?= esc_url( $img_url ); ?>" alt="<?= esc_attr( $name ); ?>" loading="lazy">
							</button>
						<?php else : ?>
							<div class="cb-team-simple__media cb-team-simple__media--static">
								<img src="<?= esc_url( $img_url ); ?>" alt="<?= esc_attr( $name ); ?>" loading="lazy">
							</div>
						<?php endif; ?>
						<div class="cb-team-simple__body">
							<?php if ( $has_bio ) : ?>
								<h3 class="cb-team-simple__name"><button class="cb-team-simple__name-btn" type="button" data-cb-team-simple-open="<?= esc_attr( $modal_id ); ?>"><?= esc_html( $name ); ?></button></h3>
							<?php else : ?>
								<h3 class="cb-team-simple__name"><?= esc_html( $name ); ?></h3>
							<?php endif; ?>
							<?php if ( '' !== trim( $urole ) ) : ?>
								<p class="cb-team-simple__role"><?= esc_html( $urole ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $team_names ) ) : ?>
								<p class="cb-team-simple__team"><?= esc_html( implode( ', ', $team_names ) ); ?></p>
							<?php endif; ?>
							<?php if ( $email || $phone || $linkedin ) : ?>
								<ul class="cb-team-simple__contacts" aria-label="<?php esc_attr_e( 'Contact', 'cb-pluto2026' ); ?>">
									<?php if ( $has_contact ) : ?>
										<li><button type="button" data-cb-team-simple-contact data-cb-team-simple-pid="<?= esc_attr( $person_id ); ?>" data-cb-team-simple-name="<?= esc_attr( $name ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: person name */ __( 'Contact %s', 'cb-pluto2026' ), $name ) ); ?>"><?= $icon_email; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button></li>
									<?php elseif ( $email ) : ?>
										<li><a href="mailto:<?= esc_attr( antispambot( $email ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: person name */ __( 'Email %s', 'cb-pluto2026' ), $name ) ); ?>"><?= $icon_email; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
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
						<div class="cb-team-simple__modal" id="<?= esc_attr( $modal_id ); ?>" role="dialog" aria-modal="true" aria-labelledby="<?= esc_attr( $modal_id ); ?>-title" hidden>
							<div class="cb-team-simple__modal-overlay" data-cb-team-simple-close></div>
							<div class="cb-team-simple__modal-dialog" role="document">
								<button class="cb-team-simple__modal-close" type="button" data-cb-team-simple-close aria-label="<?php esc_attr_e( 'Close', 'cb-pluto2026' ); ?>">&times;</button>
								<div class="cb-team-simple__modal-body">
									<div class="cb-team-simple__modal-image">
										<img src="<?= esc_url( $big_url ); ?>" alt="<?= esc_attr( $name ); ?>">
									</div>
									<div class="cb-team-simple__modal-content">
										<h3 class="cb-team-simple__modal-name" id="<?= esc_attr( $modal_id ); ?>-title"><?= esc_html( $name ); ?></h3>
										<?php if ( '' !== trim( $urole ) ) : ?>
											<p class="cb-team-simple__modal-role"><?= esc_html( $urole ); ?></p>
										<?php endif; ?>
										<?php if ( ! empty( $team_names ) ) : ?>
											<p class="cb-team-simple__modal-team"><?= esc_html( implode( ', ', $team_names ) ); ?></p>
										<?php endif; ?>
										<div class="cb-team-simple__modal-bio"><?= wp_kses_post( $bio_html ); ?></div>
										<?php if ( $email || $phone || $linkedin ) : ?>
											<ul class="cb-team-simple__contacts cb-team-simple__contacts--modal" aria-label="<?php esc_attr_e( 'Contact', 'cb-pluto2026' ); ?>">
												<?php if ( $has_contact ) : ?>
													<li><button type="button" data-cb-team-simple-contact data-cb-team-simple-pid="<?= esc_attr( $person_id ); ?>" data-cb-team-simple-name="<?= esc_attr( $name ); ?>"><?= $icon_email; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( sprintf( /* translators: %s: person name */ __( 'Contact %s', 'cb-pluto2026' ), $name ) ); ?></span></button></li>
												<?php elseif ( $email ) : ?>
													<li><a href="mailto:<?= esc_attr( antispambot( $email ) ); ?>"><?= $icon_email; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?= esc_html( antispambot( $email ) ); ?></span></a></li>
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
	</div>
</section>

<?php if ( $contact_form_id && $recipient_field_id ) : ?>
	<div class="cb-team-simple__modal cb-team-simple__modal--contact" id="cb-team-simple-contact-modal" role="dialog" aria-modal="true" aria-labelledby="cb-team-simple-contact-modal-title" data-cb-team-simple-recipient-field="<?= esc_attr( $recipient_field_id ); ?>" data-cb-team-simple-form-id="<?= esc_attr( $contact_form_id ); ?>" hidden>
		<div class="cb-team-simple__modal-overlay" data-cb-team-simple-close></div>
		<div class="cb-team-simple__modal-dialog" role="document">
			<button class="cb-team-simple__modal-close" type="button" data-cb-team-simple-close aria-label="<?php esc_attr_e( 'Close', 'cb-pluto2026' ); ?>">&times;</button>
			<div class="cb-team-simple__modal-content">
				<h3 class="cb-team-simple__modal-name" id="cb-team-simple-contact-modal-title"><?php esc_html_e( 'Contact', 'cb-pluto2026' ); ?></h3>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
	var block = document.getElementById(<?= wp_json_encode( $block_id ); ?>);
	if (!block) return;

	var buttons = block.querySelectorAll('[data-team-filter]');
	var items = block.querySelectorAll('.cb-team-simple__col');
	var lastFocus = null;

	function applyFilters() {
		var activeFilters = [];
		buttons.forEach(function (btn) {
			if (btn.classList.contains('cb-team-simple__filter--active')) {
				activeFilters.push(btn.getAttribute('data-team-filter'));
			}
		});

		var showingAll = activeFilters.length === 0 || activeFilters.indexOf('all') !== -1;

		items.forEach(function (item) {
			if (showingAll) {
				item.hidden = false;
			} else {
				var teams = (item.getAttribute('data-team') || '').split(' ');
				var matches = activeFilters.some(function (f) {
					return teams.indexOf(f) !== -1;
				});
				item.hidden = !matches;
			}
		});
	}

	buttons.forEach(function (button) {
		button.addEventListener('click', function () {
			var filter = button.getAttribute('data-team-filter');

			buttons.forEach(function (btn) {
				btn.classList.remove('cb-team-simple__filter--active');
			});
			button.classList.add('cb-team-simple__filter--active');

			applyFilters();
		});
	});

	applyFilters();

	block.querySelectorAll('.cb-team-simple__modal').forEach(function (modal) {
		if (modal.parentNode !== document.body) {
			document.body.appendChild(modal);
		}
	});

	function openModal(id) {
		var modal = document.getElementById(id);
		if (!modal) return;

		lastFocus = document.activeElement;
		modal.hidden = false;
		document.body.classList.add('cb-team-simple-modal-open');

		var closeBtn = modal.querySelector('[data-cb-team-simple-close]');
		if (closeBtn && typeof closeBtn.focus === 'function') closeBtn.focus();
	}

	function closeModal(modal) {
		if (!modal) return;

		modal.hidden = true;
		if (!document.querySelector('.cb-team-simple__modal:not([hidden])')) {
			document.body.classList.remove('cb-team-simple-modal-open');
		}

		if (lastFocus && typeof lastFocus.focus === 'function') {
			try { lastFocus.focus(); } catch (e) {}
		}
	}

	document.addEventListener('click', function (event) {
		var contactBtn = event.target.closest('[data-cb-team-simple-contact]');
		if (contactBtn) {
			event.preventDefault();

			var contactModal = document.getElementById('cb-team-simple-contact-modal');
			if (!contactModal) return;

			var pid = contactBtn.getAttribute('data-cb-team-simple-pid') || '';
			var name = contactBtn.getAttribute('data-cb-team-simple-name') || '';
			var fieldId = contactModal.getAttribute('data-cb-team-simple-recipient-field') || '';
			var formId = contactModal.getAttribute('data-cb-team-simple-form-id') || '';

			if (fieldId) {
				var selector = '#input_' + formId + '_' + fieldId + ', input[name="input_' + fieldId + '"]';
				contactModal.querySelectorAll(selector).forEach(function (input) { input.value = pid; });
			}

			contactModal.querySelectorAll('input[name="gform_field_values"]').forEach(function (input) {
				var value = input.value || '';
				if (/(^|&)recipient_pid=/.test(value)) {
					value = value.replace(/(^|&)recipient_pid=[^&]*/, '$1recipient_pid=' + encodeURIComponent(pid));
				} else {
					value = value ? value + '&recipient_pid=' + encodeURIComponent(pid) : 'recipient_pid=' + encodeURIComponent(pid);
				}
				input.value = value;
			});

			var title = contactModal.querySelector('#cb-team-simple-contact-modal-title');
			if (title && name) title.textContent = 'Contact ' + name;

			openModal('cb-team-simple-contact-modal');
			return;
		}

		var opener = event.target.closest('[data-cb-team-simple-open]');
		if (opener) {
			event.preventDefault();
			openModal(opener.getAttribute('data-cb-team-simple-open'));
			return;
		}

		var closer = event.target.closest('[data-cb-team-simple-close]');
		if (closer) {
			event.preventDefault();
			closeModal(closer.closest('.cb-team-simple__modal'));
		}
	});

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') {
			closeModal(document.querySelector('.cb-team-simple__modal:not([hidden])'));
		}
	});
});
</script>
