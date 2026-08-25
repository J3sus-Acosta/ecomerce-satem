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
 * Enqueue parent and child styles.
 */
function satem_child_enqueue_styles() {
	wp_enqueue_style( 'twentytwentyfour-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style(
		'satem-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'twentytwentyfour-style' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'satem_child_enqueue_styles' );

/**
 * Add custom body class for SATEM storefront layout.
 *
 * @param array $classes Existing body classes.
 * @return array Modified body classes.
 */
function satem_child_body_classes( $classes ) {
	$classes[] = 'satem-storefront-active';
	$classes[] = 'satem-no-page-builder';
	return $classes;
}
add_filter( 'body_class', 'satem_child_body_classes' );
