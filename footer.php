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

<footer class="footer pt-5 pb-4">
    <div class="container">
        <div class="row pb-4 g-4">
			<div class="col-12 col-md-2">
				<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/sis-logo-wo.png' ); ?>" alt="Strategic Insurance Services logo" class="footer__logo">
			</div>
			<div class="col-12 col-md-4">
				<div class="mb-4">
					Strategic Insurance Services Limited<br>
					Shoreham-by-Sea<br>
					United Kingdom
				</div>
				<div>
					T: <a href="tel:<?= esc_attr( parse_phone( get_field( 'phone_uk', 'option' ) ) ); ?>" class="footer__contact"><?= esc_html( get_field( 'phone_uk', 'option' ) ); ?></a><br>
					E: <a href="mailto:<?= esc_attr( antispambot( get_field( 'email_uk', 'option' ) ) ); ?>" class="footer__contact"><?= esc_html( antispambot( get_field( 'email_uk', 'option' ) ) ); ?></a>
				</div>
			</div>
			<div class="col-12 col-sm-6 col-md-3">
				<?=
				wp_nav_menu(
					array(
						'theme_location' => 'footer_menu_services',
						'menu_class'     => 'footer__menu',
					)
				);
				?>
			</div>
            <div class="col-12 col-sm-6 col-md-3">
				<?=
				wp_nav_menu(
					array(
						'theme_location' => 'footer_menu_about',
						'menu_class'     => 'footer__menu mb-4',
					)
				);
				?>
				<?=
				wp_nav_menu(
					array(
						'theme_location' => 'footer_menu_legal',
						'menu_class'     => 'footer__menu',
					)
				);
				?>
			</div>
		</div>
	</div>

	<div class="container pt-4 footer__colophon">
		&copy; <?= esc_html( gmdate( 'Y' ) ); ?> Strategic Insurance Services Limited<br>
		<?= wp_kses_post( get_field( 'colophon', 'option' ) ); ?>
	</div>
</footer>
<?php wp_footer(); ?>
</body>

</html>