<?php
/**
 * Template Name: Text Page
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;
get_header();

?>
<main id="main" class="text-page">
	<div class="container py-5">
		<h1><?= esc_html( get_the_title() ); ?></h1>
		<?php
			echo apply_filters( 'the_content', get_the_content() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
		</div>
	</div>
</main>
<?php
get_footer();
?>