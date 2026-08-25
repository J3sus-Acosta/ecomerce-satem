<?php
/**
 * SATEM B2B Admin Approval Module
 *
 * @package SatemCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Satem_B2B_Admin
 * Handles admin UI, approval workflow, security checks, and notification mailers.
 */
class Satem_B2B_Admin {

	/**
	 * Instance of this class.
	 *
	 * @var Satem_B2B_Admin|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Satem_B2B_Admin
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
		add_action( 'admin_menu', array( $this, 'add_b2b_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'process_admin_actions' ) );
	}

	/**
	 * Add B2B Approvals sub-menu under WooCommerce menu in WP Admin.
	 */
	public function add_b2b_admin_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'B2B Approvals', 'satem-core' ),
			__( 'B2B Approvals', 'satem-core' ),
			'manage_woocommerce',
			'wc-b2b-approvals',
			array( $this, 'render_admin_approval_page' )
		);
	}

	/**
	 * Process Admin Approve / Reject actions with Nonce & Capability checks.
	 */
	public function process_admin_actions() {
		if ( ! isset( $_POST['satem_b2b_admin_action'] ) ) {
			return;
		}

		// 1. Capability check.
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'Unauthorized. You do not have permission to manage B2B approvals.', 'satem-core' ) );
		}

		// 2. Nonce check.
		if ( ! isset( $_POST['satem_b2b_admin_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['satem_b2b_admin_nonce'] ), 'satem_b2b_admin_approval_action' ) ) {
			wp_die( esc_html__( 'Security check failed. Invalid nonce.', 'satem-core' ) );
		}

		$target_user_id = isset( $_POST['target_user_id'] ) ? absint( $_POST['target_user_id'] ) : 0;
		$action_type    = isset( $_POST['action_type'] ) ? sanitize_text_field( wp_unslash( $_POST['action_type'] ) ) : '';

		if ( ! $target_user_id ) {
			return;
		}

		// 3. Self-action check (Prevent admin from modifying own B2B status).
		if ( $target_user_id === get_current_user_id() ) {
			wp_die( esc_html__( 'Security violation: You cannot alter your own B2B approval status.', 'satem-core' ) );
		}

		if ( 'approve' === $action_type ) {
			$this->approve_b2b_user( $target_user_id );
		} elseif ( 'reject' === $action_type ) {
			$this->reject_b2b_user( $target_user_id );
		}

		wp_safe_redirect( admin_url( 'admin.php?page=wc-b2b-approvals&b2b_msg=updated' ) );
		exit;
	}

	/**
	 * Approve a B2B User.
	 *
	 * @param int $user_id Target User ID.
	 */
	public function approve_b2b_user( $user_id ) {
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return;
		}

		$business_type = get_user_meta( $user_id, '_satem_business_type', true );
		$allowed_roles = array( 'b2b_toko', 'b2b_restaurant', 'b2b_supermarket' );

		if ( ! in_array( $business_type, $allowed_roles, true ) ) {
			$business_type = 'b2b_toko'; // Fallback to Toko role if unassigned.
		}

		// Update User Meta & Role.
		update_user_meta( $user_id, '_satem_b2b_approval_status', 'approved_b2b' );
		$user->set_role( $business_type );

		// Send Email Notification in English.
		$company = get_user_meta( $user_id, '_satem_company_name', true );
		$subject = __( 'Your B2B Wholesale Account Has Been Approved - SATEM Soluciones', 'satem-core' );
		$message = sprintf(
			__( "Hello %1\$s,\n\nWe are pleased to inform you that your B2B wholesale account application for %2\$s has been APPROVED!\n\nYou can now log in to your account at https://tienda.satemsoluciones.com to access commercial wholesale purchasing.\n\nBest regards,\nSATEM Soluciones", 'satem-core' ),
			$user->first_name ? $user->first_name : $user->display_name,
			$company
		);
		wp_mail( $user->user_email, $subject, $message );
	}

	/**
	 * Reject a B2B User.
	 *
	 * @param int $user_id Target User ID.
	 */
	public function reject_b2b_user( $user_id ) {
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return;
		}

		// Update User Meta & Reset Role to Customer.
		update_user_meta( $user_id, '_satem_b2b_approval_status', 'rejected_b2b' );
		$user->set_role( 'customer' );

		// Send Email Notification in English.
		$company = get_user_meta( $user_id, '_satem_company_name', true );
		$subject = __( 'B2B Wholesale Application Status Update - SATEM Soluciones', 'satem-core' );
		$message = sprintf(
			__( "Hello %1\$s,\n\nThank you for your interest in SATEM Soluciones. After reviewing your commercial application for %2\$s, we regret to inform you that your account has not been approved for B2B wholesale pricing at this time.\n\nYou may continue purchasing products via our retail B2C storefront.\n\nBest regards,\nSATEM Soluciones", 'satem-core' ),
			$user->first_name ? $user->first_name : $user->display_name,
			$company
		);
		wp_mail( $user->user_email, $subject, $message );
	}

	/**
	 * Render Admin Approval Management Page in WP Admin.
	 */
	public function render_admin_approval_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'Unauthorized access.', 'satem-core' ) );
		}

		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'B2B Wholesale Account Approvals', 'satem-core' ); ?></h1>
			<hr class="wp-header-end">

			<?php if ( isset( $_GET['b2b_msg'] ) ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'B2B user status updated successfully.', 'satem-core' ); ?></p></div>
			<?php endif; ?>

			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Company', 'satem-core' ); ?></th>
						<th><?php esc_html_e( 'Applicant Name', 'satem-core' ); ?></th>
						<th><?php esc_html_e( 'Email', 'satem-core' ); ?></th>
						<th><?php esc_html_e( 'Phone', 'satem-core' ); ?></th>
						<th><?php esc_html_e( 'Business Type', 'satem-core' ); ?></th>
						<th><?php esc_html_e( 'CRIB Number', 'satem-core' ); ?></th>
						<th><?php esc_html_e( 'KVK Number', 'satem-core' ); ?></th>
						<th><?php esc_html_e( 'Status', 'satem-core' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'satem-core' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$args = array(
						'meta_key'     => '_satem_b2b_approval_status',
						'meta_compare' => 'EXISTS',
						'number'       => 50,
					);
					$user_query = new WP_User_Query( $args );
					$b2b_users  = $user_query->get_results();

					if ( ! empty( $b2b_users ) ) :
						foreach ( $b2b_users as $user ) :
							$user_id       = $user->ID;
							$company       = get_user_meta( $user_id, '_satem_company_name', true );
							$phone         = get_user_meta( $user_id, '_satem_phone_number', true );
							$business_type = get_user_meta( $user_id, '_satem_business_type', true );
							$crib          = get_user_meta( $user_id, '_satem_crib_number', true );
							$kvk           = get_user_meta( $user_id, '_satem_kvk_number', true );
							$status        = get_user_meta( $user_id, '_satem_b2b_approval_status', true );

							$type_labels = array(
								'b2b_toko'        => 'Toko',
								'b2b_restaurant'  => 'Restaurant',
								'b2b_supermarket' => 'Supermarket',
							);
							$type_display = isset( $type_labels[ $business_type ] ) ? $type_labels[ $business_type ] : $business_type;

							$status_colors = array(
								'pending_b2b'  => '#d97706',
								'approved_b2b' => '#16a34a',
								'rejected_b2b' => '#dc2626',
							);
							$color         = isset( $status_colors[ $status ] ) ? $status_colors[ $status ] : '#64748b';
							?>
							<tr>
								<td><strong><?php echo esc_html( $company ); ?></strong></td>
								<td><?php echo esc_html( $user->first_name . ' ' . $user->last_name ); ?> (<?php echo esc_html( $user->user_login ); ?>)</td>
								<td><a href="mailto:<?php echo esc_attr( $user->user_email ); ?>"><?php echo esc_html( $user->user_email ); ?></a></td>
								<td><?php echo esc_html( $phone ); ?></td>
								<td><span class="badge"><?php echo esc_html( $type_display ); ?></span></td>
								<td><code><?php echo esc_html( $crib ); ?></code></td>
								<td><code><?php echo esc_html( $kvk ); ?></code></td>
								<td><strong style="color:<?php echo esc_attr( $color ); ?>;"><?php echo esc_html( strtoupper( str_replace( '_b2b', '', $status ) ) ); ?></strong></td>
								<td>
									<form method="post" style="display:inline-block; margin-right:4px;">
										<?php wp_nonce_field( 'satem_b2b_admin_approval_action', 'satem_b2b_admin_nonce' ); ?>
										<input type="hidden" name="satem_b2b_admin_action" value="1" />
										<input type="hidden" name="target_user_id" value="<?php echo esc_attr( $user_id ); ?>" />
										<input type="hidden" name="action_type" value="approve" />
										<button type="submit" class="button button-primary button-small" <?php echo 'approved_b2b' === $status ? 'disabled' : ''; ?>><?php esc_html_e( 'Approve', 'satem-core' ); ?></button>
									</form>
									<form method="post" style="display:inline-block;">
										<?php wp_nonce_field( 'satem_b2b_admin_approval_action', 'satem_b2b_admin_nonce' ); ?>
										<input type="hidden" name="satem_b2b_admin_action" value="1" />
										<input type="hidden" name="target_user_id" value="<?php echo esc_attr( $user_id ); ?>" />
										<input type="hidden" name="action_type" value="reject" />
										<button type="submit" class="button button-secondary button-small" <?php echo 'rejected_b2b' === $status ? 'disabled' : ''; ?>><?php esc_html_e( 'Reject', 'satem-core' ); ?></button>
									</form>
								</td>
							</tr>
							<?php
						endforeach;
					else :
						?>
						<tr>
							<td colspan="9"><?php esc_html_e( 'No B2B applications found.', 'satem-core' ); ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}
}
