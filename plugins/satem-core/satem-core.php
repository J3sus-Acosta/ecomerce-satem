<?php
/**
 * Plugin Name: SATEM Core Engine
 * Plugin URI: https://tienda.satemsoluciones.com
 * Description: Plugin de lógica de negocio propia para el e-commerce SATEM (Curaçao).
 * Version: 1.0.0
 * Author: SATEM Soluciones / Antigravity
 * Text Domain: satem-core
 * Requires PHP: 8.0
 * Requires at least: 6.4
 * WC requires at least: 8.0
 *
 * @package SatemCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'SATEM_CORE_VERSION', '1.0.0' );
define( 'SATEM_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'SATEM_CORE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main Satem_Core Class.
 */
final class Satem_Core {

	/**
	 * Instance of this class.
	 *
	 * @var Satem_Core|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Satem_Core
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
	}

	/**
	 * Actions on plugins loaded.
	 */
	public function on_plugins_loaded() {
		// Declare HPOS compatibility for WooCommerce.
		add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
	}

	/**
	 * Declare High-Performance Order Storage (HPOS) compatibility.
	 */
	public function declare_hpos_compatibility() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
}

/**
 * Initialize SATEM Core.
 */
function satem_core_init() {
	return Satem_Core::get_instance();
}
satem_core_init();
