<?php
/**
 * The header for the theme
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

if ( session_status() === PHP_SESSION_NONE ) {
    session_start();
}



?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta
        charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, minimum-scale=1">

	<link rel="preload"
        href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/montserrat-v31-latin-regular.woff2' ); ?>"
        as="font" type="font/woff2" crossorigin="anonymous">
	<link rel="preload"
        href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/montserrat-v31-latin-500.woff2' ); ?>"
        as="font" type="font/woff2" crossorigin="anonymous">
	<link rel="preload"
        href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/montserrat-v31-latin-600.woff2' ); ?>"
        as="font" type="font/woff2" crossorigin="anonymous">

	
    <?php
    if ( ! is_user_logged_in() ) {
        if ( get_field( 'ga_property', 'options' ) ) {
            ?>
            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async
                src="<?= esc_url( 'https://www.googletagmanager.com/gtag/js?id=' . get_field( 'ga_property', 'options' ) ); ?>">
            </script>
            <script>
                window.dataLayer = window.dataLayer || [];

                function gtag() {
                    dataLayer.push(arguments);
                }
                gtag('js', new Date());
                gtag('config',
                    '<?= esc_js( get_field( 'ga_property', 'options' ) ); ?>'
                );
            </script>
        	<?php
        }
        if ( get_field( 'gtm_property', 'options' ) ) {
            ?>
            <!-- Google Tag Manager -->
            <script>
                (function(w, d, s, l, i) {
                    w[l] = w[l] || [];
                    w[l].push({
                        'gtm.start': new Date().getTime(),
                        event: 'gtm.js'
                    });
                    var f = d.getElementsByTagName(s)[0],
                        j = d.createElement(s),
                        dl = l != 'dataLayer' ? '&l=' + l : '';
                    j.async = true;
                    j.src =
                        'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                    f.parentNode.insertBefore(j, f);
                })(window, document, 'script', 'dataLayer',
                    '<?= esc_js( get_field( 'gtm_property', 'options' ) ); ?>'
                );
            </script>
            <!-- End Google Tag Manager -->
    		<?php
        }
    }
	if ( get_field( 'google_site_verification', 'options' ) ) {
		echo '<meta name="google-site-verification" content="' . esc_attr( get_field( 'google_site_verification', 'options' ) ) . '" />';
	}
	if ( get_field( 'bing_site_verification', 'options' ) ) {
		echo '<meta name="msvalidate.01" content="' . esc_attr( get_field( 'bing_site_verification', 'options' ) ) . '" />';
	}
	wp_head();
	?>
</head>

<body <?php body_class( is_front_page() ? 'homepage' : '' ); ?>
    <?php understrap_body_attributes(); ?>>
    <?php
	do_action( 'wp_body_open' );
	if ( ! is_user_logged_in() ) {
    	if ( get_field( 'gtm_property', 'options' ) ) {
        	?>
            <!-- Google Tag Manager (noscript) -->
            <noscript><iframe
                    src="<?= esc_url( 'https://www.googletagmanager.com/ns.html?id=' . get_field( 'gtm_property', 'options' ) ); ?>"
                    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
            <!-- End Google Tag Manager (noscript) -->
    		<?php
    	}
	}

    $request_uri     = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL );
    $request_uri     = is_string( $request_uri ) ? wp_unslash( $request_uri ) : '/';
    $current_path    = wp_parse_url( $request_uri, PHP_URL_PATH );
    $current_path    = is_string( $current_path ) ? $current_path : '/';
    $normalized_path = trailingslashit( $current_path );

    $home_url  = '/';
	$the_menu  = '';
	$the_class = '';
    if ( '/' === $current_path ) {
        $home_url  = '/';
		$the_menu  = '';
		$the_class = '';
    } elseif ( 0 === strpos( $normalized_path, '/property-finance/' ) ) {
        $home_url  = '/property-finance/';
		$the_menu  = 'pf_nav';
		$the_class = 'navbar-pf';
    } elseif ( 0 === strpos( $normalized_path, '/investors/' ) ) {
        $home_url  = '/investors/';
		$the_menu  = 'inv_nav';
		$the_class = 'navbar-inv';
	}
	?>
<header id="wrapper-navbar" class="fixed-top <?= esc_attr( $the_class ); ?>" itemscope itemtype="http://schema.org/WPHeader">
	<nav class="navbar navbar-expand-lg p-0 flex-column align-items-stretch">
		<div class="navbar-top w-100">
			<div class="container py-4 px-4 px-md-5">
				<div class="d-flex justify-content-between align-items-center">
					<a href="<?= esc_url( $home_url ? $home_url : '/' ); ?>" class="site-logo" aria-label="Pluto Finance Homepage">
						<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/pluto-logo-colour.svg' ); ?>" alt="Pluto Finance logo" height="45">
					</a>
					<button class="navbar-toggler" type="button" data-bs-toggle="collapse"
						data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false"
						aria-label="Toggle navigation">
						<i class="fas fa-bars"></i>
					</button>
				</div>
			</div>
		</div>
		<div id="navbar" class="navbar-bottom collapse navbar-collapse w-100">
			<div class="container px-4 px-md-5">
				<!-- Navigation -->
				<?php
				if ( has_nav_menu( $the_menu ) ) {
					wp_nav_menu(
						array(
							'theme_location' => $the_menu,
							'container'      => false,
							'menu_class'     => 'navbar-nav w-100 justify-content-start gap-5',
							'fallback_cb'    => '',
							'depth'          => 3,
							'walker'         => new Understrap_WP_Bootstrap_Navwalker(),
						)
					);
				}
				?>
			</div>
		</div>
	</nav>
</header>