<?php
/**
 * SATEM B2B Registration & My Account Module
 *
 * @package SatemCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Satem_B2B_Registration
 * Handles commercial registration forms, security validation, and status display.
 */
class Satem_B2B_Registration {

	/**
	 * Instance of this class.
	 *
	 * @var Satem_B2B_Registration|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Satem_B2B_Registration
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
		add_shortcode( 'satem_b2b_registration_form', array( $this, 'render_registration_form' ) );
		add_action( 'template_redirect', array( $this, 'handle_registration_submission' ) );
		add_action( 'woocommerce_account_dashboard', array( $this, 'display_b2b_status_in_my_account' ) );
	}

	/**
	 * Render B2B Registration Form in English.
	 *
	 * @return string HTML Form markup.
	 */
	public function render_registration_form() {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			$status  = get_user_meta( $user_id, '_satem_b2b_approval_status', true );
			if ( $status ) {
				return '<div class="woocommerce-info">' . esc_html__( 'You have already submitted a B2B application. Check your My Account dashboard for status.', 'satem-core' ) . '</div>';
			}
		}

		ob_start();
		?>
		<div class="satem-b2b-registration-wrapper">
			<h3><?php esc_html_e( 'B2B Wholesale Account Application', 'satem-core' ); ?></h3>
			<p><?php esc_html_e( 'Please complete the form below to apply for a wholesale B2B account.', 'satem-core' ); ?></p>

			<?php
			if ( isset( $_GET['b2b_reg_error'] ) ) {
				echo '<div class="woocommerce-error">' . esc_html( urldecode( $_GET['b2b_reg_error'] ) ) . '</div>';
			}
			if ( isset( $_GET['b2b_reg_success'] ) ) {
				echo '<div class="woocommerce-message">' . esc_html__( 'Your B2B application has been received and is currently pending review. We will notify you by email upon approval.', 'satem-core' ) . '</div>';
				return ob_get_clean();
			}
			?>

			<form method="post" class="satem-b2b-form">
				<?php wp_nonce_field( 'satem_b2b_register_action', 'satem_b2b_nonce' ); ?>

				<p class="form-row form-row-first">
					<label for="b2b_first_name"><?php esc_html_e( 'First Name', 'satem-core' ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text" name="b2b_first_name" id="b2b_first_name" required value="<?php echo isset( $_POST['b2b_first_name'] ) ? esc_attr( wp_unslash( $_POST['b2b_first_name'] ) ) : ''; ?>" />
				</p>

				<p class="form-row form-row-last">
					<label for="b2b_last_name"><?php esc_html_e( 'Last Name', 'satem-core' ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text" name="b2b_last_name" id="b2b_last_name" required value="<?php echo isset( $_POST['b2b_last_name'] ) ? esc_attr( wp_unslash( $_POST['b2b_last_name'] ) ) : ''; ?>" />
				</p>

				<p class="form-row form-row-wide">
					<label for="b2b_email"><?php esc_html_e( 'Email Address', 'satem-core' ); ?> <span class="required">*</span></label>
					<input type="email" class="input-text" name="b2b_email" id="b2b_email" required value="<?php echo isset( $_POST['b2b_email'] ) ? esc_attr( wp_unslash( $_POST['b2b_email'] ) ) : ''; ?>" />
				</p>

				<p class="form-row form-row-wide">
					<label for="b2b_company"><?php esc_html_e( 'Company Name', 'satem-core' ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text" name="b2b_company" id="b2b_company" required value="<?php echo isset( $_POST['b2b_company'] ) ? esc_attr( wp_unslash( $_POST['b2b_company'] ) ) : ''; ?>" />
				</p>

				<p class="form-row form-row-wide">
					<label for="b2b_phone"><?php esc_html_e( 'Phone Number', 'satem-core' ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text" name="b2b_phone" id="b2b_phone" required value="<?php echo isset( $_POST['b2b_phone'] ) ? esc_attr( wp_unslash( $_POST['b2b_phone'] ) ) : ''; ?>" />
				</p>

				<p class="form-row form-row-wide">
					<label for="b2b_business_type"><?php esc_html_e( 'Business Type', 'satem-core' ); ?> <span class="required">*</span></label>
					<select name="b2b_business_type" id="b2b_business_type" class="select" required>
						<option value=""><?php esc_html_e( '-- Select Business Type --', 'satem-core' ); ?></option>
						<option value="b2b_toko"><?php esc_html_e( 'Toko', 'satem-core' ); ?></option>
						<option value="b2b_restaurant"><?php esc_html_e( 'Restaurant', 'satem-core' ); ?></option>
						<option value="b2b_supermarket"><?php esc_html_e( 'Supermarket', 'satem-core' ); ?></option>
					</select>
				</p>

				<p class="form-row form-row-first">
					<label for="b2b_crib"><?php esc_html_e( 'CRIB Number', 'satem-core' ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text" name="b2b_crib" id="b2b_crib" required value="<?php echo isset( $_POST['b2b_crib'] ) ? esc_attr( wp_unslash( $_POST['b2b_crib'] ) ) : ''; ?>" />
				</p>

				<p class="form-row form-row-last">
					<label for="b2b_kvk"><?php esc_html_e( 'KVK Number', 'satem-core' ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text" name="b2b_kvk" id="b2b_kvk" required value="<?php echo isset( $_POST['b2b_kvk'] ) ? esc_attr( wp_unslash( $_POST['b2b_kvk'] ) ) : ''; ?>" />
				</p>

				<p class="form-row">
					<button type="submit" class="button alt" name="satem_b2b_submit" value="1"><?php esc_html_e( 'Submit Application', 'satem-core' ); ?></button>
				</p>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Handle Registration Form Submission with Nonce & Sanitization.
	 */
	public function handle_registration_submission() {
		if ( ! isset( $_POST['satem_b2b_submit'] ) ) {
			return;
		}

		// 1. Nonce Verification.
		if ( ! isset( $_POST['satem_b2b_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['satem_b2b_nonce'] ), 'satem_b2b_register_action' ) ) {
			wp_die( esc_html__( 'Security check failed. Invalid nonce.', 'satem-core' ) );
		}

		// 2. Sanitization.
		$first_name    = isset( $_POST['b2b_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['b2b_first_name'] ) ) : '';
		$last_name     = isset( $_POST['b2b_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['b2b_last_name'] ) ) : '';
		$email         = isset( $_POST['b2b_email'] ) ? sanitize_email( wp_unslash( $_POST['b2b_email'] ) ) : '';
		$company       = isset( $_POST['b2b_company'] ) ? sanitize_text_field( wp_unslash( $_POST['b2b_company'] ) ) : '';
		$phone         = isset( $_POST['b2b_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['b2b_phone'] ) ) : '';
		$business_type = isset( $_POST['b2b_business_type'] ) ? sanitize_text_field( wp_unslash( $_POST['b2b_business_type'] ) ) : '';
		$crib          = isset( $_POST['b2b_crib'] ) ? sanitize_text_field( wp_unslash( $_POST['b2b_crib'] ) ) : '';
		$kvk           = isset( $_POST['b2b_kvk'] ) ? sanitize_text_field( wp_unslash( $_POST['b2b_kvk'] ) ) : '';

		// Validate Business Type against allowed values.
		$allowed_types = array( 'b2b_toko', 'b2b_restaurant', 'b2b_supermarket' );
		if ( ! in_array( $business_type, $allowed_types, true ) ) {
			$this->redirect_with_error( __( 'Invalid Business Type selected.', 'satem-core' ) );
		}

		// 3. Validation.
		if ( empty( $first_name ) || empty( $last_name ) || empty( $email ) || empty( $company ) || empty( $crib ) || empty( $kvk ) ) {
			$this->redirect_with_error( __( 'Please fill in all mandatory fields.', 'satem-core' ) );
		}

		if ( ! is_email( $email ) ) {
			$this->redirect_with_error( __( 'Please enter a valid email address.', 'satem-core' ) );
		}

		if ( email_exists( $email ) ) {
			$this->redirect_with_error( __( 'An account with this email address already exists.', 'satem-core' ) );
		}

		// 4. Create User (Strict Self-Approval Protection: Always pending_b2b & role customer).
		$random_password = wp_generate_password( 12, false );
		$username        = sanitize_user( current( explode( '@', $email ) ) );

		// Ensure unique username.
		if ( username_exists( $username ) ) {
			$username .= '_' . wp_rand( 100, 999 );
		}

		$user_id = wp_insert_user(
			array(
				'user_login' => $username,
				'user_pass'  => $random_password,
				'user_email' => $email,
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'role'       => 'customer', // Customer role initially until approved by Admin.
			)
		);

		if ( is_wp_error( $user_id ) ) {
			$this->redirect_with_error( $user_id->get_error_message() );
		}

		// 5. Save B2B Meta (Strict pending status).
		update_user_meta( $user_id, '_satem_b2b_approval_status', 'pending_b2b' );
		update_user_meta( $user_id, '_satem_company_name', $company );
		update_user_meta( $user_id, '_satem_phone_number', $phone );
		update_user_meta( $user_id, '_satem_business_type', $business_type );
		update_user_meta( $user_id, '_satem_crib_number', $crib );
		update_user_meta( $user_id, '_satem_kvk_number', $kvk );

		// 6. Send Initial Email in English.
		$subject = __( 'Your B2B Wholesale Application Received - SATEM Soluciones', 'satem-core' );
		$message = sprintf(
			__( "Hello %1\$s,\n\nThank you for submitting your B2B wholesale account application for %2\$s.\n\nYour application is currently pending review by our administrative team. We will inspect your commercial information (CRIB/KVK) and notify you by email once your account is approved.\n\nBest regards,\nSATEM Soluciones", 'satem-core' ),
			$first_name,
			$company
		);
		wp_mail( $email, $subject, $message );

		// Redirect with success message.
		$redirect_url = add_query_arg( 'b2b_reg_success', '1', wp_get_referer() ? wp_get_referer() : home_url() );
		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Redirect with error message.
	 *
	 * @param string $error_msg Error message text.
	 */
	private function redirect_with_error( $error_msg ) {
		$redirect_url = add_query_arg( 'b2b_reg_error', rawurlencode( $error_msg ), wp_get_referer() ? wp_get_referer() : home_url() );
		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Display B2B status badge in My Account Dashboard.
	 */
	public function display_b2b_status_in_my_account() {
		$user_id = get_current_user_id();
		$status  = get_user_meta( $user_id, '_satem_b2b_approval_status', true );

		if ( ! $status ) {
			return;
		}

		$business_type = get_user_meta( $user_id, '_satem_business_type', true );
		$company       = get_user_meta( $user_id, '_satem_company_name', true );

		echo '<div class="satem-b2b-account-status-box" style="background:#f8fafc; border:1px solid #e2e8f0; padding:16px; border-radius:8px; margin-bottom:20px;">';
		echo '<h4 style="margin-top:0;">' . esc_html__( 'B2B Wholesale Account Information', 'satem-core' ) . '</h4>';
		echo '<p><strong>' . esc_html__( 'Company:', 'satem-core' ) . '</strong> ' . esc_html( $company ) . '</p>';

		if ( 'pending_b2b' === $status ) {
			echo '<p><strong>' . esc_html__( 'Application Status:', 'satem-core' ) . '</strong> <span style="color:#d97706; font-weight:bold;">' . esc_html__( 'Pending Review', 'satem-core' ) . '</span></p>';
			echo '<p style="font-size:0.9rem; color:#64748b;">' . esc_html__( 'Your application is currently being inspected by our team. Standard retail prices apply until approval.', 'satem-core' ) . '</p>';
		} elseif ( 'approved_b2b' === $status ) {
			$type_labels = array(
				'b2b_toko'        => 'Toko',
				'b2b_restaurant'  => 'Restaurant',
				'b2b_supermarket' => 'Supermarket',
			);
			$label       = isset( $type_labels[ $business_type ] ) ? $type_labels[ $business_type ] : $business_type;
			echo '<p><strong>' . esc_html__( 'Account Status:', 'satem-core' ) . '</strong> <span style="color:#16a34a; font-weight:bold;">' . esc_html__( 'Approved', 'satem-core' ) . '</span></p>';
			echo '<p><strong>' . esc_html__( 'Business Channel:', 'satem-core' ) . '</strong> ' . esc_html( $label ) . '</p>';
		} elseif ( 'rejected_b2b' === $status ) {
			echo '<p><strong>' . esc_html__( 'Application Status:', 'satem-core' ) . '</strong> <span style="color:#dc2626; font-weight:bold;">' . esc_html__( 'Not Approved', 'satem-core' ) . '</span></p>';
		}

		echo '</div>';
	}
}
