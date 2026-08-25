<?php
/**
 * SATEM B2B Pricing Engine Module
 *
 * @package SatemCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Satem_B2B_Pricing
 * Handles dynamic in-memory B2B group pricing filters and admin price meta panels.
 */
class Satem_B2B_Pricing {

	/**
	 * Instance of this class.
	 *
	 * @var Satem_B2B_Pricing|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Satem_B2B_Pricing
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
		add_action( 'init', array( $this, 'register_b2b_price_meta' ) );

		// Admin Product Data Pricing Panel hooks.
		add_action( 'woocommerce_product_options_pricing', array( $this, 'add_b2b_pricing_fields' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_b2b_pricing_fields' ), 10, 1 );

		// Dynamic Price In-Memory Filtering Hooks (Product Page, Shop, Archive).
		add_filter( 'woocommerce_product_get_price', array( $this, 'filter_product_price' ), 10, 2 );
		add_filter( 'woocommerce_product_get_regular_price', array( $this, 'filter_product_price' ), 10, 2 );
		add_filter( 'woocommerce_product_variation_get_price', array( $this, 'filter_product_price' ), 10, 2 );
		add_filter( 'woocommerce_product_variation_get_regular_price', array( $this, 'filter_product_price' ), 10, 2 );

		// Cart Calculation & Display Hooks.
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'apply_b2b_price_to_cart' ), 10, 1 );
		add_filter( 'woocommerce_cart_item_price', array( $this, 'filter_cart_item_price_display' ), 10, 3 );
	}

	/**
	 * Register B2B price meta keys (Hidden from public REST API to prevent price leakage).
	 */
	public function register_b2b_price_meta() {
		$meta_keys = array(
			'_satem_price_b2b_toko',
			'_satem_price_b2b_restaurant',
			'_satem_price_b2b_supermarket',
		);

		foreach ( $meta_keys as $meta_key ) {
			register_post_meta(
				'product',
				$meta_key,
				array(
					'show_in_rest'  => false, // Prevent public REST API price leakage.
					'single'        => true,
					'type'          => 'string',
					'auth_callback' => function() {
						return current_user_can( 'edit_products' );
					},
				)
			);
		}
	}

	/**
	 * Add B2B Group Pricing fields to WooCommerce Product Data -> General panel in English.
	 */
	public function add_b2b_pricing_fields() {
		echo '<div class="options_group satem_b2b_pricing_options" style="border-top:1px solid #eee; padding-top:10px;">';
		echo '<h4 style="margin-left:12px;">' . esc_html__( 'SATEM B2B Wholesale Group Pricing ($)', 'satem-core' ) . '</h4>';

		woocommerce_wp_text_input(
			array(
				'id'          => '_satem_price_b2b_toko',
				'label'       => __( 'Toko B2B Price ($)', 'satem-core' ),
				'placeholder' => __( 'Leave empty to use B2C price', 'satem-core' ),
				'data_type'   => 'price',
				'desc_tip'    => 'true',
				'description' => __( 'Wholesale price for approved Toko buyers in USD ($).', 'satem-core' ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => '_satem_price_b2b_restaurant',
				'label'       => __( 'Restaurant B2B Price ($)', 'satem-core' ),
				'placeholder' => __( 'Leave empty to use B2C price', 'satem-core' ),
				'data_type'   => 'price',
				'desc_tip'    => 'true',
				'description' => __( 'Wholesale price for approved Restaurant buyers in USD ($).', 'satem-core' ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => '_satem_price_b2b_supermarket',
				'label'       => __( 'Supermarket B2B Price ($)', 'satem-core' ),
				'placeholder' => __( 'Leave empty to use B2C price', 'satem-core' ),
				'data_type'   => 'price',
				'desc_tip'    => 'true',
				'description' => __( 'Wholesale price for approved Supermarket buyers in USD ($).', 'satem-core' ),
			)
		);

		echo '</div>';
	}

	/**
	 * Save B2B Group Pricing fields with capability & nonce verification.
	 *
	 * @param int $post_id Product Post ID.
	 */
	public function save_b2b_pricing_fields( $post_id ) {
		// Defense-in-depth security checks.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( get_post_type( $post_id ) !== 'product' ) {
			return;
		}
		if ( ! current_user_can( 'edit_product', $post_id ) ) {
			return;
		}
		if ( isset( $_POST['woocommerce_meta_nonce'] ) ) {
			if ( ! wp_verify_nonce( wp_unslash( $_POST['woocommerce_meta_nonce'] ), 'woocommerce_save_data' ) ) {
				return;
			}
		}

		$meta_keys = array(
			'_satem_price_b2b_toko',
			'_satem_price_b2b_restaurant',
			'_satem_price_b2b_supermarket',
		);

		foreach ( $meta_keys as $meta_key ) {
			if ( isset( $_POST[ $meta_key ] ) ) {
				$raw_val = wp_unslash( $_POST[ $meta_key ] );

				if ( '' === trim( $raw_val ) ) {
					delete_post_meta( $post_id, $meta_key );
				} else {
					$clean_price = wc_format_decimal( $raw_val );
					if ( is_numeric( $clean_price ) && $clean_price >= 0 ) {
						update_post_meta( $post_id, $meta_key, $clean_price );
					}
				}
			}
		}
	}

	/**
	 * Get current approved B2B group for logged-in user.
	 *
	 * @return string|false Role key ('b2b_toko', 'b2b_restaurant', 'b2b_supermarket') or false if unapproved/guest.
	 */
	public function get_current_b2b_group() {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$user_id = get_current_user_id();
		$status  = get_user_meta( $user_id, '_satem_b2b_approval_status', true );

		// Strict security check: MUST have approved_b2b status.
		if ( 'approved_b2b' !== $status ) {
			return false;
		}

		$user = wp_get_current_user();
		if ( ! $user || empty( $user->roles ) ) {
			return false;
		}

		$allowed_groups = array( 'b2b_toko', 'b2b_restaurant', 'b2b_supermarket' );
		foreach ( $user->roles as $role ) {
			if ( in_array( $role, $allowed_groups, true ) ) {
				return $role;
			}
		}

		return false;
	}

	/**
	 * Get B2B Price for a specific product and group.
	 *
	 * @param WC_Product $product Product instance.
	 * @param string     $group   B2B Group role key.
	 * @return float|string|false B2B price value or false if fallback to B2C.
	 */
	public function get_b2b_price( $product, $group ) {
		if ( ! $product || ! $group ) {
			return false;
		}

		$meta_key  = '_satem_price_' . $group;
		$b2b_price = get_post_meta( $product->get_id(), $meta_key, true );

		if ( '' !== $b2b_price && null !== $b2b_price && is_numeric( $b2b_price ) && (float) $b2b_price >= 0 ) {
			return (float) $b2b_price;
		}

		return false; // Fallback to B2C regular price.
	}

	/**
	 * Filter product price in memory for approved B2B users.
	 *
	 * @param string|float $price   Original WooCommerce product price.
	 * @param WC_Product   $product Product object.
	 * @return string|float Filtered price.
	 */
	public function filter_product_price( $price, $product ) {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return $price;
		}

		$group = $this->get_current_b2b_group();
		if ( ! $group ) {
			return $price; // Guest, B2C, Pending, or Rejected -> Return standard retail B2C price.
		}

		$b2b_price = $this->get_b2b_price( $product, $group );
		if ( false !== $b2b_price ) {
			return $b2b_price;
		}

		return $price; // Empty B2B price -> Fallback to B2C price.
	}

	/**
	 * Apply B2B price to Cart items during cart totals calculation.
	 *
	 * @param WC_Cart $cart WooCommerce Cart object.
	 */
	public function apply_b2b_price_to_cart( $cart ) {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return;
		}

		$group = $this->get_current_b2b_group();
		if ( ! $group ) {
			return;
		}

		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
			$product   = $cart_item['data'];
			$b2b_price = $this->get_b2b_price( $product, $group );

			if ( false !== $b2b_price ) {
				$cart_item['data']->set_price( $b2b_price );
			}
		}
	}

	/**
	 * Filter Cart Item price display in cart & mini-cart HTML.
	 *
	 * @param string     $price_html Cart item price HTML.
	 * @param array      $cart_item  Cart item data array.
	 * @param string     $cart_item_key Cart item key.
	 * @return string Filtered price HTML.
	 */
	public function filter_cart_item_price_display( $price_html, $cart_item, $cart_item_key ) {
		$group = $this->get_current_b2b_group();
		if ( ! $group ) {
			return $price_html;
		}

		$product   = $cart_item['data'];
		$b2b_price = $this->get_b2b_price( $product, $group );

		if ( false !== $b2b_price ) {
			return wc_price( $b2b_price );
		}

		return $price_html;
	}
}
