<?php
/**
 * SATEM HPOS Compatibility Module
 *
 * @package SatemCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Satem_HPOS
 * Handles WooCommerce High-Performance Order Storage compatibility.
 */
class Satem_HPOS {

	/**
	 * Instance of this class.
	 *
	 * @var Satem_HPOS|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Satem_HPOS
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
		add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
	}

	/**
	 * Declare High-Performance Order Storage (HPOS) compatibility.
	 */
	public function declare_hpos_compatibility() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
				'custom_order_tables',
				SATEM_CORE_FILE,
				true
			);
		}
	}
}
