<?php
/**
 * Plugin Name: SATEM Core Engine
 * Plugin URI: https://tienda.satemsoluciones.com
 * Description: Plugin de lógica de negocio propia y metadatos de empaque/picking para E-Commerce SATEM (Curaçao).
 * Version: 1.1.0
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

define( 'SATEM_CORE_VERSION', '1.1.0' );
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
		add_action( 'init', array( $this, 'register_product_meta_fields' ) );

		// WooCommerce Product Metabox hooks (Admin).
		add_action( 'woocommerce_product_options_inventory_product_data', array( $this, 'add_packaging_product_fields' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_packaging_product_fields' ) );

		// WooCommerce REST API output hook.
		add_filter( 'woocommerce_rest_prepare_product_object', array( $this, 'expose_packaging_meta_in_rest' ), 10, 3 );
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

	/**
	 * Register product custom meta fields in WordPress meta API.
	 */
	public function register_product_meta_fields() {
		$meta_keys = array(
			'_satem_barcode_unit' => 'string',
			'_satem_barcode_box'  => 'string',
			'_satem_units_per_box' => 'integer',
			'_satem_sku_box'      => 'string',
		);

		foreach ( $meta_keys as $meta_key => $type ) {
			register_post_meta(
				'product',
				$meta_key,
				array(
					'show_in_rest' => true,
					'single'       => true,
					'type'         => $type,
					'auth_callback' => function() {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}

	/**
	 * Add custom packaging fields to WooCommerce product inventory panel in admin.
	 */
	public function add_packaging_product_fields() {
		echo '<div class="options_group satem_packaging_options">';
		echo '<h4 style="margin-left:12px; margin-top:10px;">' . esc_html__( 'Empaque y Código de Barras (SATEM)', 'satem-core' ) . '</h4>';

		woocommerce_wp_text_input(
			array(
				'id'          => '_satem_barcode_unit',
				'label'       => __( 'Código de Barras Unitario', 'satem-core' ),
				'placeholder' => 'ej. 7591234567890',
				'desc_tip'    => 'true',
				'description' => __( 'Código EAN-13 / UPC impreso en la unidad individual.', 'satem-core' ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => '_satem_barcode_box',
				'label'       => __( 'Código de Barras de Caja', 'satem-core' ),
				'placeholder' => 'ej. 17591234567897',
				'desc_tip'    => 'true',
				'description' => __( 'Código EAN-14 / ITF-14 impreso en la caja máster.', 'satem-core' ),
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
	 * Save packaging fields when product is saved in admin.
	 *
	 * @param int $post_id Product Post ID.
	 */
	public function save_packaging_product_fields( $post_id ) {
		$fields = array(
			'_satem_barcode_unit' => 'sanitize_text_field',
			'_satem_barcode_box'  => 'sanitize_text_field',
			'_satem_units_per_box' => 'absint',
			'_satem_sku_box'      => 'sanitize_text_field',
		);

		foreach ( $fields as $field_key => $sanitize_func ) {
			if ( isset( $_POST[ $field_key ] ) ) {
				$val = call_user_func( $sanitize_func, wp_unslash( $_POST[ $field_key ] ) );
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
			'barcode_unit'  => get_post_meta( $object->get_id(), '_satem_barcode_unit', true ),
			'barcode_box'   => get_post_meta( $object->get_id(), '_satem_barcode_box', true ),
			'units_per_box' => (int) get_post_meta( $object->get_id(), '_satem_units_per_box', true ),
			'sku_box'       => get_post_meta( $object->get_id(), '_satem_sku_box', true ),
		);

		$response->set_data( $data );
		return $response;
	}
}

/**
 * Initialize SATEM Core.
 */
function satem_core_init() {
	return Satem_Core::get_instance();
}
satem_core_init();
