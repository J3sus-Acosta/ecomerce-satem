<?php
/**
 * SATEM B2B Cart & Quantity Validation Module
 *
 * @package SatemCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Satem_B2B_Cart
 * Enforces B2B master box purchase multiples (_satem_units_per_box) on frontend and server.
 */
class Satem_B2B_Cart {

	/**
	 * Instance of this class.
	 *
	 * @var Satem_B2B_Cart|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Satem_B2B_Cart
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
		// Frontend quantity input step & min args filter.
		add_filter( 'woocommerce_quantity_input_args', array( $this, 'filter_quantity_input_args' ), 10, 2 );

		// Server-Side Add to Cart Validation.
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_to_cart_quantity' ), 10, 5 );

		// Server-Side Cart Update Validation.
		add_filter( 'woocommerce_update_cart_validation', array( $this, 'validate_update_cart_quantity' ), 10, 4 );

		// Server-Side Cart Integrity Check prior to Checkout.
		add_action( 'woocommerce_check_cart_items', array( $this, 'validate_all_cart_items_on_checkout' ) );
	}

	/**
	 * Check if current user is an approved B2B wholesale buyer.
	 *
	 * @return bool True if approved B2B user, false otherwise.
	 */
	public function is_approved_b2b_user() {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$user_id = get_current_user_id();
		$status  = get_user_meta( $user_id, '_satem_b2b_approval_status', true );

		if ( 'approved_b2b' !== $status ) {
			return false;
		}

		$user = wp_get_current_user();
		if ( ! $user || empty( $user->roles ) ) {
			return false;
		}

		$allowed_roles = array( 'b2b_toko', 'b2b_restaurant', 'b2b_supermarket' );
		foreach ( $user->roles as $role ) {
			if ( in_array( $role, $allowed_roles, true ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get units per box for a given product ID.
	 *
	 * @param int $product_id Product ID.
	 * @return int Units per box integer >= 1, or 0 if unconfigured.
	 */
	public function get_units_per_box( $product_id ) {
		if ( ! $product_id ) {
			return 0;
		}

		$units = get_post_meta( $product_id, '_satem_units_per_box', true );
		if ( is_numeric( $units ) && (int) $units > 1 ) {
			return (int) $units;
		}

		return 0;
	}

	/**
	 * Filter HTML quantity input min_value and step attributes for approved B2B users.
	 *
	 * @param array      $args    Quantity input arguments.
	 * @param WC_Product $product WooCommerce Product object.
	 * @return array Modified arguments.
	 */
	public function filter_quantity_input_args( $args, $product ) {
		if ( ! $this->is_approved_b2b_user() || ! $product ) {
			return $args;
		}

		$units_per_box = $this->get_units_per_box( $product->get_id() );
		if ( $units_per_box > 1 ) {
			$args['min_value'] = $units_per_box;
			$args['step']      = $units_per_box;
		}

		return $args;
	}

	/**
	 * Validate Add to Cart quantity on server side.
	 *
	 * @param bool $passed       Validation status.
	 * @param int  $product_id   Product ID.
	 * @param int  $quantity     Requested quantity.
	 * @param int  $variation_id Variation ID.
	 * @param array $variations  Variations data.
	 * @return bool True if valid, false if rejected.
	 */
	public function validate_add_to_cart_quantity( $passed, $product_id, $quantity, $variation_id = 0, $variations = array() ) {
		if ( ! $this->is_approved_b2b_user() ) {
			return $passed; // B2C users buy single suelta units without restriction.
		}

		$target_id     = $variation_id ? $variation_id : $product_id;
		$units_per_box = $this->get_units_per_box( $target_id ? $target_id : $product_id );

		if ( $units_per_box > 1 && ( (int) $quantity % $units_per_box !== 0 ) ) {
			$error_msg = sprintf(
				__( 'This product must be purchased in multiples of %d units.', 'satem-core' ),
				$units_per_box
			);
			wc_add_notice( $error_msg, 'error' );
			return false;
		}

		return $passed;
	}

	/**
	 * Validate Cart Update quantity on server side.
	 *
	 * @param bool   $passed        Validation status.
	 * @param string $cart_item_key Cart item key.
	 * @param array  $values        Cart item values.
	 * @param int    $quantity      Updated quantity.
	 * @return bool True if valid, false if rejected.
	 */
	public function validate_update_cart_quantity( $passed, $cart_item_key, $values, $quantity ) {
		if ( ! $this->is_approved_b2b_user() ) {
			return $passed;
		}

		$product_id    = isset( $values['product_id'] ) ? $values['product_id'] : 0;
		$units_per_box = $this->get_units_per_box( $product_id );

		if ( $units_per_box > 1 && ( (int) $quantity % $units_per_box !== 0 ) ) {
			$error_msg = sprintf(
				__( 'This product must be purchased in multiples of %d units.', 'satem-core' ),
				$units_per_box
			);
			wc_add_notice( $error_msg, 'error' );
			return false;
		}

		return $passed;
	}

	/**
	 * Validate all items in cart before proceeding to Checkout page.
	 */
	public function validate_all_cart_items_on_checkout() {
		if ( ! $this->is_approved_b2b_user() || ! WC()->cart ) {
			return;
		}

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$product_id    = $cart_item['product_id'];
			$quantity      = $cart_item['quantity'];
			$units_per_box = $this->get_units_per_box( $product_id );

			if ( $units_per_box > 1 && ( (int) $quantity % $units_per_box !== 0 ) ) {
				$error_msg = sprintf(
					__( 'Cart item "%s" must be purchased in multiples of %d units.', 'satem-core' ),
					$cart_item['data']->get_name(),
					$units_per_box
				);
				wc_add_notice( $error_msg, 'error' );
			}
		}
	}
}
