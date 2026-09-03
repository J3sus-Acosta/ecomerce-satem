<?php
/**
 * SATEM Child Theme Footer
 *
 * @package SatemChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main><!-- #main-content -->

<footer class="satem-site-footer">
	<div class="satem-container">
		<div class="satem-footer-grid">
			<!-- Brand Column -->
			<div class="satem-footer-brand">
				<h3>SATEM SOLUCIONES</h3>
				<p style="color:#60A5FA; font-weight:700; font-style:italic; margin-bottom:12px;">"Si no Existe, Lo Creamos..."</p>
				<p><?php esc_html_e( 'Providing advanced commercial technology solutions, hardware, infrastructure, and specialized wholesale B2B/B2C services.', 'satem-child' ); ?></p>
			</div>

			<!-- Navigation Links Column -->
			<div class="satem-footer-col">
				<h4><?php esc_html_e( 'Navigation', 'satem-child' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'satem-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'Shop Products', 'satem-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/wholesale/' ) ); ?>"><?php esc_html_e( 'Wholesale / B2B', 'satem-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/my-account/' ) ); ?>"><?php esc_html_e( 'My Account', 'satem-child' ); ?></a></li>
				</ul>
			</div>

			<!-- Customer Support Column -->
			<div class="satem-footer-col">
				<h4><?php esc_html_e( 'B2B Wholesale', 'satem-child' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/wholesale/' ) ); ?>"><?php esc_html_e( 'Apply for B2B Account', 'satem-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/my-account/' ) ); ?>"><?php esc_html_e( 'Wholesale Portal', 'satem-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'Bulk Ordering Catalog', 'satem-child' ); ?></a></li>
				</ul>
			</div>

			<!-- Legal & Info Column -->
			<div class="satem-footer-col">
				<h4><?php esc_html_e( 'Information', 'satem-child' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'satem-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'satem-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact Support', 'satem-child' ); ?></a></li>
				</ul>
			</div>
		</div>

		<!-- Footer Bottom Bar -->
		<div class="satem-footer-bottom">
			<div>
				&copy; <?php echo esc_html( date( 'Y' ) ); ?> <strong>SATEM Soluciones</strong>. <?php esc_html_e( 'All rights reserved.', 'satem-child' ); ?> (Curaçao)
			</div>
			<div>
				<?php esc_html_e( 'Commercial E-Commerce Platform B2C / B2B', 'satem-child' ); ?>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
