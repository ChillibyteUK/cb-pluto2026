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

$people = get_posts(
	array(
		'post_type'      => 'person',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'no_found_rows'  => true,
	)
);

if ( empty( $people ) ) {
	return;
}

$get_sort_name = function ( WP_Post $person ) {
	$name  = trim( wp_strip_all_tags( get_the_title( $person ) ) );
	$parts = preg_split( '/\s+/', $name );

	if ( ! $parts ) {
		return '';
	}

	$surname = (string) end( $parts );
	return strtolower( remove_accents( $surname . ' ' . $name ) );
};

usort(
	$people,
	function ( WP_Post $a, WP_Post $b ) use ( $get_sort_name ) {
		return strnatcasecmp( $get_sort_name( $a ), $get_sort_name( $b ) );
	}
);

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

$missing_img = get_stylesheet_directory_uri() . '/img/missing-person.jpg';
?>
<section id="<?= esc_attr( $block_id ); ?>" class="cb-team-simple <?= esc_attr( trim( $bg . ' ' . $fg . ' ' . $custom_classes ) ); ?>">
	<div class="container py-5">
		<?php if ( ! empty( $teams ) ) : ?>
			<div class="cb-team-simple__filters" aria-label="<?php esc_attr_e( 'Filter team', 'cb-pluto2026' ); ?>">
				<button class="cb-team-simple__filter cb-team-simple__filter--active" type="button" data-team-filter="all"><?php esc_html_e( 'All', 'cb-pluto2026' ); ?></button>
				<?php foreach ( $teams as $team ) : ?>
					<button class="cb-team-simple__filter" type="button" data-team-filter="<?= esc_attr( $team->slug ); ?>"><?= esc_html( $team->name ); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="row gy-4 cb-team-simple__grid">
			<?php foreach ( $people as $person ) : ?>
				<?php
				$person_id = (int) $person->ID;
				$name      = get_the_title( $person );
				$role      = (string) get_field( 'role', $person_id );
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

				$thumb_id = get_post_thumbnail_id( $person_id );
				$img_url  = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium_large' ) : $missing_img;
				$img_url  = $img_url ? $img_url : $missing_img;
				?>
				<div class="col-12 col-sm-6 col-lg-4 col-xl-3 cb-team-simple__col" data-team="<?= esc_attr( implode( ' ', $team_slugs ) ); ?>">
					<article class="cb-team-simple__card">
						<div class="cb-team-simple__media">
							<img src="<?= esc_url( $img_url ); ?>" alt="<?= esc_attr( $name ); ?>" loading="lazy">
						</div>
						<div class="cb-team-simple__body">
							<h3 class="cb-team-simple__name"><?= esc_html( $name ); ?></h3>
							<?php if ( '' !== trim( $role ) ) : ?>
								<p class="cb-team-simple__role"><?= esc_html( $role ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $team_names ) ) : ?>
								<p class="cb-team-simple__team"><?= esc_html( implode( ', ', $team_names ) ); ?></p>
							<?php endif; ?>
						</div>
					</article>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php if ( ! empty( $teams ) ) : ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var block = document.getElementById(<?= wp_json_encode( $block_id ); ?>);
	if (!block) return;

	var buttons = block.querySelectorAll('[data-team-filter]');
	var items = block.querySelectorAll('.cb-team-simple__col');

	buttons.forEach(function (button) {
		button.addEventListener('click', function () {
			var filter = button.getAttribute('data-team-filter');

			buttons.forEach(function (btn) {
				btn.classList.toggle('cb-team-simple__filter--active', btn === button);
			});

			items.forEach(function (item) {
				var teams = item.getAttribute('data-team') || '';
				item.hidden = filter !== 'all' && teams.split(' ').indexOf(filter) === -1;
			});
		});
	});
});
</script>
<?php endif; ?>
