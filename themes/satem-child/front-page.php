<?php
/**
 * SATEM Child Theme Front Page Template (Home)
 *
 * @package SatemChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<!-- HERO SECTION -->
<section class="satem-hero">
	<div class="satem-container">
		<div class="satem-hero-content">
			<span class="satem-hero-tag"><?php esc_html_e( 'SATEM COMMERCIAL PLATFORM', 'satem-child' ); ?></span>
			<h1 class="satem-hero-title"><?php esc_html_e( 'Technology for Business. Products for Growth.', 'satem-child' ); ?></h1>
			<p class="satem-hero-subtitle"><?php esc_html_e( 'Discover professional products and solutions designed for businesses of every size.', 'satem-child' ); ?></p>
			<div class="satem-hero-actions">
				<a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="satem-btn satem-btn-primary"><?php esc_html_e( 'Shop Products', 'satem-child' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/wholesale/' ) ); ?>" class="satem-btn satem-btn-outline"><?php esc_html_e( 'Wholesale / B2B', 'satem-child' ); ?></a>
			</div>
		</div>
	</div>
</section>

<!-- SHOP BY CATEGORY SECTION -->
<section class="satem-section">
	<div class="satem-container">
		<div class="satem-section-header">
			<div>
				<h2 class="satem-section-title"><?php esc_html_e( 'Shop by Category', 'satem-child' ); ?></h2>
				<p class="satem-section-desc"><?php esc_html_e( 'Explore our curated commercial product lines', 'satem-child' ); ?></p>
			</div>
			<a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" style="font-weight:700; color:var(--satem-accent);"><?php esc_html_e( 'View All Categories &rarr;', 'satem-child' ); ?></a>
		</div>

		<div class="satem-categories-grid">
			<a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="satem-category-card">
				<div class="satem-category-icon">&#128187;</div>
				<div class="satem-category-name"><?php esc_html_e( 'Technology', 'satem-child' ); ?></div>
				<div style="font-size:0.85rem; color:var(--satem-text-muted);"><?php esc_html_e( 'Hardware, Systems & Devices', 'satem-child' ); ?></div>
			</a>

			<a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="satem-category-card">
				<div class="satem-category-icon">&#128246;</div>
				<div class="satem-category-name"><?php esc_html_e( 'Networking', 'satem-child' ); ?></div>
				<div style="font-size:0.85rem; color:var(--satem-text-muted);"><?php esc_html_e( 'Routers, Switches & Cabling', 'satem-child' ); ?></div>
			</a>

			<a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="satem-category-card">
				<div class="satem-category-icon">&#128295;</div>
				<div class="satem-category-name"><?php esc_html_e( 'Hardware', 'satem-child' ); ?></div>
				<div style="font-size:0.85rem; color:var(--satem-text-muted);"><?php esc_html_e( 'Infrastructure & Tools', 'satem-child' ); ?></div>
			</a>

			<a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="satem-category-card">
				<div class="satem-category-icon">&#128092;</div>
				<div class="satem-category-name"><?php esc_html_e( 'Accessories', 'satem-child' ); ?></div>
				<div style="font-size:0.85rem; color:var(--satem-text-muted);"><?php esc_html_e( 'Peripherals & Supplies', 'satem-child' ); ?></div>
			</a>
		</div>
	</div>
</section>

<!-- FEATURED PRODUCTS SECTION -->
<section class="satem-section" style="background:var(--satem-surface); border-top:1px solid var(--satem-border); border-bottom:1px solid var(--satem-border);">
	<div class="satem-container">
		<div class="satem-section-header">
			<div>
				<h2 class="satem-section-title"><?php esc_html_e( 'Featured Products', 'satem-child' ); ?></h2>
				<p class="satem-section-desc"><?php esc_html_e( 'High demand items available for individual retail or wholesale bulk orders', 'satem-child' ); ?></p>
			</div>
			<a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" style="font-weight:700; color:var(--satem-accent);"><?php esc_html_e( 'Browse Shop Catalog &rarr;', 'satem-child' ); ?></a>
		</div>

		<?php
		if ( class_exists( 'WooCommerce' ) ) {
			echo do_shortcode( '[products limit="4" columns="4" orderby="date" order="DESC"]' );
		} else {
			echo '<p style="color:var(--satem-text-muted);">' . esc_html__( 'WooCommerce is initializing...', 'satem-child' ) . '</p>';
		}
		?>
	</div>
</section>

<!-- WHOLESALE CALLOUT BANNER -->
<section class="satem-section">
	<div class="satem-container">
		<div class="satem-wholesale-banner">
			<div>
				<h3><?php esc_html_e( 'Expand Your Business with SATEM Wholesale B2B', 'satem-child' ); ?></h3>
				<p><?php esc_html_e( 'Register your Toko, Restaurant, or Supermarket to unlock special volume pricing, master case ordering, and simplified business logistics.', 'satem-child' ); ?></p>
			</div>
			<a href="<?php echo esc_url( home_url( '/wholesale/' ) ); ?>" class="satem-btn satem-btn-primary" style="white-space:nowrap;"><?php esc_html_e( 'Apply for Wholesale Account', 'satem-child' ); ?></a>
		</div>
	</div>
</section>

<?php
get_footer();
