<?php
/**
 * Template Name: Service Sidebar Page
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

get_header();
the_post();

$blocks = parse_blocks( get_the_content() );

// Separate hero, cta, and body blocks.
$hero_block  = null;
$cta_block   = null;
$body_blocks = array();

foreach ( $blocks as $block ) {
	if ( 'acf/cb-hero' === $block['blockName'] && null === $hero_block ) {
		$hero_block = $block;
	} elseif ( 'acf/cb-cta' === $block['blockName'] ) {
		$cta_block = $block;
	} else {
		$body_blocks[] = $block;
	}
}
?>
<main id="main">

	<?php
	if ( $hero_block ) {
		echo apply_filters( 'the_content', serialize_block( $hero_block ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>

	<div class="container my-5">
		<div class="row">
			<div class="col-lg-9">
				<?php
				foreach ( $body_blocks as $block ) {
					echo apply_filters( 'the_content', serialize_block( $block ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
			<div class="col-lg-3 service-sidebar">
				<?php
				$people_ids = get_field( 'associated_people' );
				if ( $people_ids ) {
					?>
				<h3 class="has-underline mb-4">Product contact</h3>
				<ul class="service-sidebar__people list-unstyled">
					<?php
					foreach ( $people_ids as $person_id ) {
						?>
					<div class="service-sidebar__person">
						<div class="d-flex align-items-center gap-2 mb-3">
							<?=
							get_the_post_thumbnail(
								$person_id,
								'thumbnail',
								array(
									'class' => 'service-sidebar__person-img rounded-circle mb-2',
									'width' => '80',
								)
							);
							?>
							<div class="service-sidebar__person-name"><?= esc_html( get_the_title( $person_id ) ); ?></div>
						</div>
						<div class="service-sidebar__person-contact d-flex flex-column mb-4">
							<?php
							$email = get_field( 'email_address', $person_id );
							if ( $email ) {
								?>
							<span>E: <a href="mailto:<?= esc_attr( $email ); ?>" class="service-sidebar__person-email"><?= esc_html( $email ); ?></a></span>
								<?php
							}
							$phone = get_field( 'phone_number', $person_id );
							if ( $phone ) {
								?>
							<span>T: <a href="tel:<?= esc_attr( preg_replace( '/\D+/', '', $phone ) ); ?>" class="service-sidebar__person-phone"><?= esc_html( $phone ); ?></a></span>
								<?php
							}
							?>
						</div>
					</div>
						<?php
					}
					?>
				</ul>
					<?php
				}
				?>
			</div>
		</div>
	</div>

	<?php
	if ( $cta_block ) {
		echo apply_filters( 'the_content', serialize_block( $cta_block ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>

</main>

<?php
add_action(
	'wp_footer',
	function () {
		?>
<script>
(function () {
	if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) return;

	const sidebar = document.querySelector( '.service-sidebar' );
	if ( ! sidebar ) return;

	gsap.from( sidebar, {
		y: 20,
		opacity: 0,
		duration: 0.6,
		ease: 'power2.out',
		delay: 0.3,
	} );
})();
</script>
		<?php
	},
	20
);

get_footer();
