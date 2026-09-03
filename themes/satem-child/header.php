<?php
/**
 * SATEM Child Theme Header
 *
 * @package SatemChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main-content"><?php esc_html_e( 'Skip to content', 'satem-child' ); ?></a>

<header class="satem-site-header">
	<div class="satem-container">
		<div class="satem-header-wrapper">
			<!-- Logo & Brand Identity -->
			<div class="satem-logo-area">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<span class="satem-logo-text">SATEM</span>
					<span class="satem-logo-badge">SOLUCIONES</span>
				</a>
			</div>

			<!-- Main Navigation -->
			<nav class="satem-nav-container" aria-label="<?php esc_attr_e( 'Main Navigation', 'satem-child' ); ?>">
				<ul class="satem-nav-main">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'satem-child' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'Shop', 'satem-child' ); ?></a></li>
					<li><?php echo satem_get_b2b_header_nav_link(); ?></li>
					<li><a href="<?php echo esc_url( home_url( '/my-account/' ) ); ?>"><?php esc_html_e( 'My Account', 'satem-child' ); ?></a></li>
				</ul>
			</nav>

			<!-- Header Actions & Cart -->
			<div class="satem-header-actions">
				<?php
				$cart_count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
				$cart_url   = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
				?>
				<a href="<?php echo esc_url( $cart_url ); ?>" class="satem-cart-link" aria-label="<?php esc_attr_e( 'View Shopping Cart', 'satem-child' ); ?>">
					<span><?php esc_html_e( 'Cart', 'satem-child' ); ?></span>
					<span class="satem-cart-count"><?php echo esc_html( $cart_count ); ?></span>
				</a>

				<button class="satem-nav-toggle" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle Navigation Menu', 'satem-child' ); ?>">
					&#9776;
				</button>
			</div>
		</div>
	</div>
</header>

<main id="main-content" class="satem-main-site-content">
