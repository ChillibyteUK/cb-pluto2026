<?php
/**
 * People CSV Importer
 *
 * Adds an "Import" submenu under the People (person CPT) admin menu.
 * Accepts a CSV upload with columns:
 *   Section, Name, Role, Email, Phone, LinkedIn, Image, Bio
 *
 * Behaviour:
 *  - Creates the `team` taxonomy term from `Section` if missing.
 *  - Creates a `person` post using `Name` as the title (matches existing on title).
 *  - Sets ACF fields: role, email, phone, linkedin_url.
 *  - Sets the post content (main editor) to `Bio`.
 *  - Looks up the `Image` filename in the media library and links as featured image.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the import submenu under the People CPT.
 */
add_action(
	'admin_menu',
	function () {
		add_submenu_page(
			'edit.php?post_type=person',
			__( 'Import People', 'cb-pluto2026' ),
			__( 'Import', 'cb-pluto2026' ),
			'edit_posts',
			'person-import',
			'cb_people_import_page'
		);
	}
);

/**
 * Render the import admin page.
 */
function cb_people_import_page() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'cb-pluto2026' ) );
	}

	$results = null;

	if (
		isset( $_POST['cb_people_import_nonce'] ) &&
		wp_verify_nonce( sanitize_key( wp_unslash( $_POST['cb_people_import_nonce'] ) ), 'cb_people_import' ) &&
		! empty( $_FILES['cb_people_csv']['tmp_name'] )
	) {
		$dry_run = ! empty( $_POST['cb_people_dry_run'] );
		$update  = ! empty( $_POST['cb_people_update_existing'] );
		$results = cb_people_import_process_csv( $_FILES['cb_people_csv']['tmp_name'], $dry_run, $update ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}

	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Import People', 'cb-pluto2026' ); ?></h1>
		<p><?php esc_html_e( 'Upload a CSV with columns: Section, Name, Role, Email, Phone, LinkedIn, Image, Bio.', 'cb-pluto2026' ); ?></p>
		<p>
			<?php esc_html_e( 'The Section becomes a Team taxonomy term (created if missing). The Image column should be a filename already present in the Media Library (matched by filename, e.g. justin-faiz.jpg).', 'cb-pluto2026' ); ?>
		</p>

		<form method="post" enctype="multipart/form-data">
			<?php wp_nonce_field( 'cb_people_import', 'cb_people_import_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="cb_people_csv"><?php esc_html_e( 'CSV File', 'cb-pluto2026' ); ?></label></th>
					<td><input type="file" id="cb_people_csv" name="cb_people_csv" accept=".csv,text/csv" required /></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Options', 'cb-pluto2026' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="cb_people_update_existing" value="1" checked />
							<?php esc_html_e( 'Update existing people (matched by post title)', 'cb-pluto2026' ); ?>
						</label><br />
						<label>
							<input type="checkbox" name="cb_people_dry_run" value="1" />
							<?php esc_html_e( 'Dry run (preview without making changes)', 'cb-pluto2026' ); ?>
						</label>
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'Import', 'cb-pluto2026' ) ); ?>
		</form>

		<?php if ( is_array( $results ) ) : ?>
			<h2><?php esc_html_e( 'Import Results', 'cb-pluto2026' ); ?></h2>
			<?php if ( ! empty( $results['errors'] ) ) : ?>
				<div class="notice notice-error"><p><strong><?php esc_html_e( 'Errors:', 'cb-pluto2026' ); ?></strong></p>
					<ul style="list-style:disc;margin-left:20px;">
						<?php foreach ( $results['errors'] as $err ) : ?>
							<li><?php echo esc_html( $err ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
			<p>
				<?php
				printf(
					/* translators: 1: created, 2: updated, 3: skipped, 4: terms created, 5: images linked, 6: images missing */
					esc_html__( 'Created: %1$d &middot; Updated: %2$d &middot; Skipped: %3$d &middot; Terms created: %4$d &middot; Images linked: %5$d &middot; Images missing: %6$d', 'cb-pluto2026' ),
					(int) $results['created'],
					(int) $results['updated'],
					(int) $results['skipped'],
					(int) $results['terms_created'],
					(int) $results['images_linked'],
					(int) $results['images_missing']
				);
				?>
			</p>
			<?php if ( ! empty( $results['log'] ) ) : ?>
				<table class="widefat striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Row', 'cb-pluto2026' ); ?></th>
							<th><?php esc_html_e( 'Name', 'cb-pluto2026' ); ?></th>
							<th><?php esc_html_e( 'Action', 'cb-pluto2026' ); ?></th>
							<th><?php esc_html_e( 'Section/Term', 'cb-pluto2026' ); ?></th>
							<th><?php esc_html_e( 'Image', 'cb-pluto2026' ); ?></th>
							<th><?php esc_html_e( 'Notes', 'cb-pluto2026' ); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ( $results['log'] as $row ) : ?>
						<tr>
							<td><?php echo (int) $row['row']; ?></td>
							<td>
								<?php if ( ! empty( $row['post_id'] ) ) : ?>
									<a href="<?php echo esc_url( get_edit_post_link( $row['post_id'] ) ); ?>"><?php echo esc_html( $row['name'] ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $row['name'] ); ?>
								<?php endif; ?>
							</td>
							<td><?php echo esc_html( $row['action'] ); ?></td>
							<td><?php echo esc_html( $row['section'] ); ?></td>
							<td><?php echo esc_html( $row['image'] ); ?></td>
							<td><?php echo esc_html( $row['notes'] ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Process an uploaded CSV file.
 *
 * @param string $file_path Path to uploaded CSV.
 * @param bool   $dry_run   If true, no DB writes occur.
 * @param bool   $update    If true, update existing people matched by title.
 * @return array Results summary.
 */
function cb_people_import_process_csv( $file_path, $dry_run = false, $update = true ) {
	$results = array(
		'created'        => 0,
		'updated'        => 0,
		'skipped'        => 0,
		'terms_created'  => 0,
		'images_linked'  => 0,
		'images_missing' => 0,
		'errors'         => array(),
		'log'            => array(),
	);

	$handle = fopen( $file_path, 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
	if ( ! $handle ) {
		$results['errors'][] = __( 'Could not open uploaded CSV.', 'cb-pluto2026' );
		return $results;
	}

	$header = fgetcsv( $handle );
	if ( ! $header ) {
		$results['errors'][] = __( 'CSV is empty or unreadable.', 'cb-pluto2026' );
		fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		return $results;
	}

	// Normalise header keys (lowercase, trimmed) for lookup.
	$header_map = array();
	foreach ( $header as $i => $col ) {
		$header_map[ strtolower( trim( $col ) ) ] = $i;
	}

	$required = array( 'name' );
	foreach ( $required as $req ) {
		if ( ! isset( $header_map[ $req ] ) ) {
			/* translators: %s: column name */
			$results['errors'][] = sprintf( __( 'Required column missing: %s', 'cb-pluto2026' ), $req );
			fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
			return $results;
		}
	}

	$get = function ( $row, $col ) use ( $header_map ) {
		if ( ! isset( $header_map[ $col ] ) ) {
			return '';
		}
		$idx = $header_map[ $col ];
		return isset( $row[ $idx ] ) ? trim( (string) $row[ $idx ] ) : '';
	};

	$row_num = 1;
	while ( ( $row = fgetcsv( $handle ) ) !== false ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition
		++$row_num;

		$name = $get( $row, 'name' );
		if ( '' === $name ) {
			++$results['skipped'];
			$results['log'][] = array(
				'row'     => $row_num,
				'name'    => '',
				'action'  => __( 'skipped', 'cb-pluto2026' ),
				'section' => '',
				'image'   => '',
				'notes'   => __( 'Empty name', 'cb-pluto2026' ),
			);
			continue;
		}

		$section  = $get( $row, 'section' );
		$role     = $get( $row, 'role' );
		$email    = $get( $row, 'email' );
		$phone    = $get( $row, 'phone' );
		$linkedin = $get( $row, 'linkedin' );
		$image    = $get( $row, 'image' );
		$bio      = $get( $row, 'bio' );

		$notes = array();

		// Find existing person by exact title.
		$existing = get_page_by_title( $name, OBJECT, 'person' ); // phpcs:ignore WordPress.Deprecated.FunctionUse.get_page_by_title_NoExternalUse
		$post_id  = $existing ? (int) $existing->ID : 0;

		if ( $post_id && ! $update ) {
			++$results['skipped'];
			$results['log'][] = array(
				'row'     => $row_num,
				'name'    => $name,
				'action'  => __( 'skipped (exists)', 'cb-pluto2026' ),
				'section' => $section,
				'image'   => $image,
				'notes'   => __( 'Existing person; update disabled', 'cb-pluto2026' ),
				'post_id' => $post_id,
			);
			continue;
		}

		$post_args = array(
			'post_type'    => 'person',
			'post_title'   => $name,
			'post_content' => cb_people_bio_to_blocks( $bio ),
			'post_status'  => 'publish',
		);

		if ( $dry_run ) {
			if ( $post_id ) {
				++$results['updated'];
				$action = __( 'would update', 'cb-pluto2026' );
			} else {
				++$results['created'];
				$action = __( 'would create', 'cb-pluto2026' );
			}
		} else {
			if ( $post_id ) {
				$post_args['ID'] = $post_id;
				$res             = wp_update_post( $post_args, true );
				if ( is_wp_error( $res ) ) {
					$results['errors'][] = sprintf( 'Row %d (%s): %s', $row_num, $name, $res->get_error_message() );
					++$results['skipped'];
					continue;
				}
				++$results['updated'];
				$action = __( 'updated', 'cb-pluto2026' );
			} else {
				$res = wp_insert_post( $post_args, true );
				if ( is_wp_error( $res ) ) {
					$results['errors'][] = sprintf( 'Row %d (%s): %s', $row_num, $name, $res->get_error_message() );
					++$results['skipped'];
					continue;
				}
				$post_id = (int) $res;
				++$results['created'];
				$action = __( 'created', 'cb-pluto2026' );
			}

			// ACF fields. Use update_field with field keys to be safe even pre-save.
			if ( function_exists( 'update_field' ) ) {
				update_field( 'field_69f0a50425229', $role, $post_id );      // role.
				update_field( 'field_69f0a50f2522a', $email, $post_id );     // email.
				update_field( 'field_69f0a51b2522b', $phone, $post_id );     // phone.
				update_field( 'field_69f0a52b2522c', $linkedin, $post_id );  // linkedin_url.
			} else {
				update_post_meta( $post_id, 'role', $role );
				update_post_meta( $post_id, 'email', $email );
				update_post_meta( $post_id, 'phone', $phone );
				update_post_meta( $post_id, 'linkedin_url', $linkedin );
			}

			// Team taxonomy term.
			if ( '' !== $section ) {
				$term = term_exists( $section, 'team' );
				if ( ! $term ) {
					$term = wp_insert_term( $section, 'team' );
					if ( is_wp_error( $term ) ) {
						$results['errors'][] = sprintf( 'Row %d (%s): term error: %s', $row_num, $name, $term->get_error_message() );
						$term                = null;
					} else {
						++$results['terms_created'];
						$notes[] = sprintf( 'created term "%s"', $section );
					}
				}
				if ( $term && ! is_wp_error( $term ) ) {
					$term_id = (int) ( is_array( $term ) ? $term['term_id'] : $term );
					wp_set_object_terms( $post_id, array( $term_id ), 'team', false );
				}
			}

			// Featured image: match by filename in the media library.
			if ( '' !== $image ) {
				$attachment_id = cb_people_find_attachment_by_filename( $image );
				if ( $attachment_id ) {
					set_post_thumbnail( $post_id, $attachment_id );
					++$results['images_linked'];
				} else {
					++$results['images_missing'];
					$notes[] = sprintf( 'image not found: %s', $image );
				}
			}
		}

		$results['log'][] = array(
			'row'     => $row_num,
			'name'    => $name,
			'action'  => $action,
			'section' => $section,
			'image'   => $image,
			'notes'   => implode( '; ', $notes ),
			'post_id' => $post_id,
		);
	}

	fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose

	return $results;
}

/**
 * Convert a plain-text bio into Gutenberg paragraph block markup.
 *
 * Splits on blank lines into separate paragraphs; single newlines become <br>.
 *
 * @param string $bio Raw bio text.
 * @return string Block-serialised content (or empty string).
 */
function cb_people_bio_to_blocks( $bio ) {
	$bio = trim( (string) $bio );
	if ( '' === $bio ) {
		return '';
	}

	// Normalise line endings.
	$bio = str_replace( array( "\r\n", "\r" ), "\n", $bio );

	// Split on blank lines into paragraphs.
	$paragraphs = preg_split( '/\n\s*\n/', $bio );

	$out = array();
	foreach ( $paragraphs as $para ) {
		$para = trim( $para );
		if ( '' === $para ) {
			continue;
		}
		// Preserve single line breaks within a paragraph as <br>.
		$html  = nl2br( esc_html( $para ), false );
		$out[] = "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
	}

	return implode( "\n\n", $out );
}

/**
 * Locate an attachment ID by filename.
 *
 * Tries an exact match on the basename of the attached file (with or without
 * extension), so 'justin-faiz.jpg' or 'justin-faiz' will both work.
 *
 * @param string $filename The image filename.
 * @return int Attachment post ID or 0 if not found.
 */
function cb_people_find_attachment_by_filename( $filename ) {
	global $wpdb;

	$filename = ltrim( wp_basename( $filename ), '/' );
	if ( '' === $filename ) {
		return 0;
	}

	// Exact basename match.
	$like = '%/' . $wpdb->esc_like( $filename );
	$id   = (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta}
			 WHERE meta_key = '_wp_attached_file'
			   AND ( meta_value = %s OR meta_value LIKE %s )
			 LIMIT 1",
			$filename,
			$like
		)
	);
	if ( $id ) {
		return $id;
	}

	// Fallback: match by sanitised file_name in attachment metadata (covers
	// scaled/edited originals).
	$id = (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta}
			 WHERE meta_key = '_wp_attachment_metadata'
			   AND meta_value LIKE %s
			 LIMIT 1",
			'%' . $wpdb->esc_like( $filename ) . '%'
		)
	);

	return $id;
}
