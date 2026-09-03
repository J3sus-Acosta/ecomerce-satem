<?php
/**
 * Template Name: Wholesale Account Application
 *
 * @package SatemChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="satem-container" style="padding-top: 40px; padding-bottom: 64px;">
	<div style="max-width: 800px; margin: 0 auto; text-align: center; margin-bottom: 32px;">
		<span class="satem-hero-tag" style="background:var(--satem-badge-b2b-bg); color:var(--satem-badge-b2b-text); border:1px solid #BFDBFE;">
			<?php esc_html_e( 'COMMERCIAL B2B PORTAL', 'satem-child' ); ?>
		</span>
		<h1 style="font-size: 2.4rem; font-weight: 800; margin-top: 12px; margin-bottom: 12px; color: var(--satem-text-heading);">
			<?php esc_html_e( 'Apply for a Wholesale Account', 'satem-child' ); ?>
		</h1>
		<p style="font-size: 1.1rem; color: var(--satem-text-muted); max-width: 640px; margin: 0 auto;">
			<?php esc_html_e( 'Are you a retailer, restaurant or supermarket? Apply for a wholesale account and access business pricing.', 'satem-child' ); ?>
		</p>
	</div>

	<?php
	if ( shortcode_exists( 'satem_b2b_registration_form' ) ) {
		echo do_shortcode( '[satem_b2b_registration_form]' );
	} else {
		echo '<div class="satem-b2b-registration-wrapper"><p style="color:var(--satem-danger); text-align:center;">' . esc_html__( 'SATEM Core Engine is required to process commercial B2B registrations.', 'satem-child' ) . '</p></div>';
	}
	?>
</div>

<?php
get_footer();
