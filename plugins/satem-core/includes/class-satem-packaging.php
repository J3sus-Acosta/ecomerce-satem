<?php
/**
 * SATEM Packaging & Barcode Module
 *
 * @package SatemCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Satem_Packaging
 * Handles product packaging metadata, admin metaboxes, and REST API integration.
 */
class Satem_Packaging {

	/**
	 * Instance of this class.
	 *
	 * @var Satem_Packaging|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Satem_Packaging
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
		add_action( 'init', array( $this, 'register_product_meta_fields' ) );

		// WooCommerce Product Data Metabox hooks (Admin).
		add_action( 'woocommerce_product_options_inventory_product_data', array( $this, 'add_packaging_product_fields' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_packaging_product_fields' ), 10, 1 );

		// WooCommerce REST API output filter.
		add_filter( 'woocommerce_rest_prepare_product_object', array( $this, 'expose_packaging_meta_in_rest' ), 10, 3 );
	}

	/**
	 * Register packaging meta fields with strict capabilities (`edit_products`).
	 */
	public function register_product_meta_fields() {
		$meta_keys = array(
			'_satem_barcode_unit'  => 'string',
			'_satem_barcode_box'   => 'string',
			'_satem_units_per_box' => 'integer',
			'_satem_sku_box'       => 'string',
		);

		foreach ( $meta_keys as $meta_key => $type ) {
			register_post_meta(
				'product',
				$meta_key,
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'type'          => $type,
					'auth_callback' => function() {
						return current_user_can( 'edit_products' );
					},
				)
			);
		}
	}

	/**
	 * Add packaging fields to WooCommerce product inventory panel in admin.
	 */
	public function add_packaging_product_fields() {
		echo '<div class="options_group satem_packaging_options">';
		echo '<h4 style="margin-left:12px; margin-top:10px;">' . esc_html__( 'Empaque y Código de Barras (SATEM)', 'satem-core' ) . '</h4>';

		woocommerce_wp_text_input(
			array(
				'id'          => '_satem_barcode_unit',
				'label'       => __( 'Código de Barras Unitario', 'satem-core' ),
				'placeholder' => 'ej. 0759123456789',
				'desc_tip'    => 'true',
				'description' => __( 'Código EAN-13 / UPC impreso en la unidad individual (texto).', 'satem-core' ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => '_satem_barcode_box',
				'label'       => __( 'Código de Barras de Caja', 'satem-core' ),
				'placeholder' => 'ej. 17591234567896',
				'desc_tip'    => 'true',
				'description' => __( 'Código EAN-14 / ITF-14 impreso en la caja máster (texto).', 'satem-core' ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => '_satem_units_per_box',
				'label'       => __( 'Unidades por Caja', 'satem-core' ),
				'placeholder' => '12',
				'type'        => 'number',
				'custom_attributes' => array(
					'step' => '1',
					'min'  => '1',
				),
				'desc_tip'    => 'true',
				'description' => __( 'Cantidad de unidades contenidas en una caja máster.', 'satem-core' ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => '_satem_sku_box',
				'label'       => __( 'SKU de Caja (Opcional)', 'satem-core' ),
				'placeholder' => 'ej. BOX-BEB-001',
				'desc_tip'    => 'true',
				'description' => __( 'SKU o código de empaque asignado por el fabricante.', 'satem-core' ),
			)
		);

		echo '</div>';
	}

	/**
	 * Save packaging fields with strict capability and nonce security hardening.
	 *
	 * @param int $post_id Product Post ID.
	 */
	public function save_packaging_product_fields( $post_id ) {
		// 1. Defense-in-depth: Check defined autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// 2. Defense-in-depth: Check post type.
		if ( get_post_type( $post_id ) !== 'product' ) {
			return;
		}

		// 3. Defense-in-depth: Capability check.
		if ( ! current_user_can( 'edit_product', $post_id ) ) {
			return;
		}

		// 4. Defense-in-depth: Nonce verification for WooCommerce product save.
		if ( isset( $_POST['woocommerce_meta_nonce'] ) ) {
			if ( ! wp_verify_nonce( wp_unslash( $_POST['woocommerce_meta_nonce'] ), 'woocommerce_save_data' ) ) {
				return;
			}
		}

		// Sanitization and update logic.
		$fields = array(
			'_satem_barcode_unit'  => 'sanitize_text_field',
			'_satem_barcode_box'   => 'sanitize_text_field',
			'_satem_units_per_box' => 'absint',
			'_satem_sku_box'       => 'sanitize_text_field',
		);

		foreach ( $fields as $field_key => $sanitize_func ) {
			if ( isset( $_POST[ $field_key ] ) ) {
				$raw_val = wp_unslash( $_POST[ $field_key ] );
				$val     = call_user_func( $sanitize_func, $raw_val );
				update_post_meta( $post_id, $field_key, $val );
			}
		}
	}

	/**
	 * Expose packaging meta in WooCommerce REST API product responses.
	 *
	 * @param WP_REST_Response $response Response object.
	 * @param WC_Data          $object   WC Product object.
	 * @param WP_REST_Request  $request  Request object.
	 * @return WP_REST_Response
	 */
	public function expose_packaging_meta_in_rest( $response, $object, $request ) {
		$data = $response->get_data();

		$data['satem_packaging'] = array(
			'barcode_unit'  => (string) get_post_meta( $object->get_id(), '_satem_barcode_unit', true ),
			'barcode_box'   => (string) get_post_meta( $object->get_id(), '_satem_barcode_box', true ),
			'units_per_box' => (int) get_post_meta( $object->get_id(), '_satem_units_per_box', true ),
			'sku_box'       => (string) get_post_meta( $object->get_id(), '_satem_sku_box', true ),
		);

		$response->set_data( $data );
		return $response;
	}
}
