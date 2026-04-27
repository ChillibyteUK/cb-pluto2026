<?php
/**
 * Footer template for the Identity Coda 2026 theme.
 *
 * This file contains the footer section of the theme, including navigation menus,
 * office addresses, and colophon information.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="footer-top"></div>

<footer class="footer">
	<div class="footer__overlay" aria-hidden="true"></div>

	<div class="footer__main pt-5 pb-4">
		<div class="container">
			<div class="row pb-4 g-4">
				<div class="col-12">
					<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/pluto-logo-wo.svg' ); ?>" alt="Pluto Finance logo" class="footer__logo">
				</div>
				<div class="col-12 col-md-6">
					<div class="footer__title">Contact</div>
					<p><strong>T:</strong> <a href="tel:<?= esc_attr( parse_phone( get_field( 'contact_phone', 'option' ) ) ); ?>" class="footer__contact"><?= esc_html( get_field( 'contact_phone', 'option' ) ); ?></a></p>
					<p><strong>E:</strong> <a href="mailto:<?= esc_attr( antispambot( get_field( 'contact_email', 'option' ) ) ); ?>" class="footer__contact"><?= esc_html( antispambot( get_field( 'contact_email', 'option' ) ) ); ?></a></p>
					<p><strong>London:</strong> 15-16 Buckingham Street, London, WC2N 6DU</p>
					<p><strong>Edinburgh:</strong> 26 Alva Street, Edinburgh, EH2 4PY</p>
					<p><a href="<?= esc_url( get_field( 'linkedin_url', 'option' ) ); ?>" target="_blank" rel="nofollow noopener">Find us on <strong>LinkedIn</strong></a></p>
				</div>
				<div class="col-12 col-sm-6 col-md-3">
					<div class="footer__title has-orange-1000-color">Pluto Lending</div>
					<?=
					wp_nav_menu(
						array(
							'theme_location' => 'footer_menu_lending',
							'menu_class'     => 'footer__menu',
						)
					);
					?>
				</div>
				<div class="col-12 col-sm-6 col-md-3">
					<div class="footer__title has-green-teal-400-color">Pluto Investing</div>
					<?=
					wp_nav_menu(
						array(
							'theme_location' => 'footer_menu_investing',
							'menu_class'     => 'footer__menu mb-4',
						)
					);
					?>
				</div>
			</div>
		</div>
	</div>

	<div class="footer__colophon py-4">
		<div class="container">
			<div class="row g-5">
				<div class="col-12 col-md-3">
					&copy; <?= esc_html( gmdate( 'Y' ) ); ?> Pluto Finance (UK) LLP.<br>
				</div>
				<div class="col-12 col-md-9">
					<?= wp_kses_post( get_field( 'colophon', 'option' ) ); ?>
				</div>
			</div>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>

</html>