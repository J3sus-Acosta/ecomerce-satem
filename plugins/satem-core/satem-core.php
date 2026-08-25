<?php
/**
 * Plugin Name: SATEM Core Engine
 * Plugin URI: https://tienda.satemsoluciones.com
 * Description: Plugin de lógica de negocio propia, metadatos de empaque y motor B2B para E-Commerce SATEM (Curaçao).
 * Version: 1.5.0
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

define( 'SATEM_CORE_VERSION', '1.5.0' );
define( 'SATEM_CORE_FILE', __FILE__ );
define( 'SATEM_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'SATEM_CORE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main Satem_Core Bootstrap Class.
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
		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
	}

	/**
	 * Initialize modules if WooCommerce is active.
	 */
	public function init_plugin() {
		// Verify WooCommerce dependency.
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			return;
		}

		$this->includes();
		$this->init_modules();
	}

	/**
	 * Include modular component files.
	 */
	private function includes() {
		require_once SATEM_CORE_PATH . 'includes/class-satem-hpos.php';
		require_once SATEM_CORE_PATH . 'includes/class-satem-packaging.php';
		require_once SATEM_CORE_PATH . 'includes/class-satem-b2b-roles.php';
		require_once SATEM_CORE_PATH . 'includes/class-satem-b2b-registration.php';
		require_once SATEM_CORE_PATH . 'includes/class-satem-b2b-admin.php';
		require_once SATEM_CORE_PATH . 'includes/class-satem-b2b-pricing.php';
		require_once SATEM_CORE_PATH . 'includes/class-satem-b2b-cart.php';
	}

	/**
	 * Instantiate active modules.
	 */
	private function init_modules() {
		Satem_HPOS::get_instance();
		Satem_Packaging::get_instance();
		Satem_B2B_Roles::get_instance();
		Satem_B2B_Registration::get_instance();
		Satem_B2B_Admin::get_instance();
		Satem_B2B_Pricing::get_instance();
		Satem_B2B_Cart::get_instance();
	}

	/**
	 * Admin notice if WooCommerce is missing.
	 */
	public function woocommerce_missing_notice() {
		echo '<div class="error"><p>';
		echo esc_html__( 'SATEM Core Engine requiere que WooCommerce esté instalado y activo.', 'satem-core' );
		echo '</p></div>';
	}
}

/**
 * Initialize SATEM Core Engine.
 */
function satem_core() {
	return Satem_Core::get_instance();
}
satem_core();
