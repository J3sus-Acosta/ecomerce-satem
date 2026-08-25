<?php
/**
 * SATEM B2B Roles Module
 *
 * @package SatemCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Satem_B2B_Roles
 * Handles registration and capabilities for B2B user roles.
 */
class Satem_B2B_Roles {

	/**
	 * Instance of this class.
	 *
	 * @var Satem_B2B_Roles|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Satem_B2B_Roles
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
		add_action( 'init', array( $this, 'register_b2b_roles' ) );
	}

	/**
	 * Register B2B roles with customer-level capabilities (Zero admin privileges).
	 */
	public function register_b2b_roles() {
		$b2b_roles = array(
			'b2b_toko'        => __( 'Toko', 'satem-core' ),
			'b2b_restaurant'  => __( 'Restaurant', 'satem-core' ),
			'b2b_supermarket' => __( 'Supermarket', 'satem-core' ),
		);

		// Base capabilities identical to standard customer.
		$customer_caps = array(
			'read'                   => true,
			'edit_posts'             => false,
			'delete_posts'           => false,
			'manage_options'         => false,
			'manage_woocommerce'     => false,
			'edit_products'          => false,
			'publish_products'       => false,
			'delete_products'        => false,
			'edit_users'             => false,
			'promote_users'          => false,
		);

		foreach ( $b2b_roles as $role_key => $role_name ) {
			if ( ! get_role( $role_key ) ) {
				add_role( $role_key, $role_name, $customer_caps );
			}
		}
	}
}
