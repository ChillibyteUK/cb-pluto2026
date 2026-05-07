<?php
/**
 * Block template for CB Nav Cards.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

// URL-based preset detection (mirrors cb-text-image.php / cb-ticker-x3.php).
$context = cb_get_site_context();

if ( 'pf' === $context ) {
	$preset           = 'cb-nav-cards--property-finance';
	$flourish_variant = 'full-flourish--lending';
} elseif ( 'inv' === $context ) {
	$preset           = 'cb-nav-cards--investors';
	$flourish_variant = 'full-flourish--investors';
} else {
	// Block is only meaningful inside a silo; render nothing elsewhere.
	return;
}

?>
<div class="cb-nav-cards <?= esc_attr( $preset ); ?>">
	<div class="container py-5">
		<div class="row g-4">
			<?php
			while ( have_rows( 'cards' ) ) {
				the_row();
				$card_link = get_sub_field( 'link' );
				?>
			<div class="col-12 col-md-6 col-lg-4">
				<a href="<?php echo esc_url( $card_link['url'] ); ?>" class="cb-nav-cards__card full-flourish full-flourish--flip <?= esc_attr( $flourish_variant ); ?>" target="<?php echo esc_attr( $card_link['target'] ); ?>">
					<div class="cb-nav-cards__content">
						<h3 class="cb-nav-cards__title"><?php the_sub_field( 'title' ); ?></h3>
						<p class="cb-link-dot">Find out more</p>
					</div>
				</a>
			</div>
				<?php
			}
			?>
		</div>
	</div>
</div>