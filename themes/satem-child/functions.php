<?php
/**
 * SATEM Child Theme Functions
 *
 * @package SatemChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Enqueue parent and child styles, fonts, and scripts.
 */
function satem_child_enqueue_assets() {
	// Google Fonts: Inter & Outfit.
	wp_enqueue_style(
		'satem-google-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap',
		array(),
		null
	);

	// Parent theme style fallback.
	if ( file_exists( get_template_directory() . '/style.css' ) ) {
		wp_enqueue_style( 'twentytwentyfour-style', get_template_directory_uri() . '/style.css' );
	}

	// SATEM Child Theme System Design Tokens & Layout CSS.
	wp_enqueue_style(
		'satem-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'satem-google-fonts' ),
		'1.5.0'
	);

	// Frontend Vanilla JS.
	wp_enqueue_script(
		'satem-frontend-script',
		get_stylesheet_directory_uri() . '/assets/js/satem-frontend.js',
		array(),
		'1.5.0',
		true
	);
}
add_action( 'wp_enqueue_scripts', 'satem_child_enqueue_assets' );

/**
 * Add custom body classes for SATEM storefront layout and B2B user states.
 *
 * @param array $classes Existing body classes.
 * @return array Modified body classes.
 */
function satem_child_body_classes( $classes ) {
	$classes[] = 'satem-storefront-active';
	$classes[] = 'satem-no-page-builder';

	if ( is_user_logged_in() ) {
		$user_id = get_current_user_id();
		$status  = get_user_meta( $user_id, '_satem_b2b_approval_status', true );
		if ( 'approved_b2b' === $status ) {
			$classes[] = 'satem-b2b-approved';
		} elseif ( 'pending_b2b' === $status ) {
			$classes[] = 'satem-b2b-pending';
		} else {
			$classes[] = 'satem-b2b-retail';
		}
	} else {
		$classes[] = 'satem-b2b-guest';
	}

	return $classes;
}
add_filter( 'body_class', 'satem_child_body_classes' );

/**
 * Helper: Render dynamic B2B link for Header Navigation based on user state.
 *
 * @return string HTML link markup.
 */
function satem_get_b2b_header_nav_link() {
	if ( ! is_user_logged_in() ) {
		return '<a href="' . esc_url( home_url( '/wholesale/' ) ) . '" class="satem-nav-item">' . esc_html__( 'Wholesale', 'satem-child' ) . '</a>';
	}

	$user_id = get_current_user_id();
	$status  = get_user_meta( $user_id, '_satem_b2b_approval_status', true );

	if ( 'approved_b2b' === $status ) {
		return '<a href="' . esc_url( wc_get_account_endpoint_url( 'dashboard' ) ) . '" class="satem-b2b-header-tag approved">' . esc_html__( 'Wholesale Account', 'satem-child' ) . '</a>';
	} elseif ( 'pending_b2b' === $status ) {
		return '<a href="' . esc_url( wc_get_account_endpoint_url( 'dashboard' ) ) . '" class="satem-b2b-header-tag pending">' . esc_html__( 'Application Pending', 'satem-child' ) . '</a>';
	}

	return '<a href="' . esc_url( home_url( '/wholesale/' ) ) . '" class="satem-nav-item">' . esc_html__( 'Apply Wholesale', 'satem-child' ) . '</a>';
}

/**
 * Display Packaging & Box Multiple information on Single Product page.
 */
function satem_display_single_product_packaging_info() {
	global $product;
	if ( ! $product ) {
		return;
	}

	$product_id    = $product->get_id();
	$units_per_box = get_post_meta( $product_id, '_satem_units_per_box', true );
	$is_b2b        = false;

	if ( is_user_logged_in() ) {
		$status = get_user_meta( get_current_user_id(), '_satem_b2b_approval_status', true );
		if ( 'approved_b2b' === $status ) {
			$is_b2b = true;
		}
	}

	if ( $units_per_box && (int) $units_per_box > 1 ) {
		echo '<div class="satem-packaging-info-box">';
		echo '<h5>' . esc_html__( 'Case Pack Specifications', 'satem-child' ) . '</h5>';
		echo '<p><strong>' . esc_html__( 'Case Pack:', 'satem-child' ) . '</strong> ' . sprintf( esc_html__( '%d units per master box', 'satem-child' ), (int) $units_per_box ) . '</p>';

		if ( $is_b2b ) {
			echo '<p style="margin-top:6px; color:#1D4ED8; font-weight:600;">' . sprintf( esc_html__( 'Wholesale Account Notice: Sold in multiples of %d units.', 'satem-child' ), (int) $units_per_box ) . '</p>';
		} else {
			echo '<p style="margin-top:4px; font-size:0.85rem; color:#64748B;">' . esc_html__( 'Retail customers may purchase single individual units.', 'satem-child' ) . '</p>';
		}
		echo '</div>';
	}
}
add_action( 'woocommerce_single_product_summary', 'satem_display_single_product_packaging_info', 25 );

/**
 * Display B2B Badge on Product Loop cards for approved buyers.
 */
function satem_display_loop_product_b2b_badge() {
	if ( ! is_user_logged_in() ) {
		return;
	}

	$status = get_user_meta( get_current_user_id(), '_satem_b2b_approval_status', true );
	if ( 'approved_b2b' === $status ) {
		echo '<span class="satem-b2b-price-badge">' . esc_html__( 'Wholesale Price', 'satem-child' ) . '</span>';
	}
}
add_action( 'woocommerce_after_shop_loop_item_title', 'satem_display_loop_product_b2b_badge', 5 );
