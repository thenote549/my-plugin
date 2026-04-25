<?php
/**
 * Form handlers and shared data access.
 *
 * @package Med_Appointment_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MAM_Forms {

	public static function init(): void {
		add_action( 'admin_post_nopriv_mam_register', array( __CLASS__, 'handle_register' ) );
		add_action( 'admin_post_nopriv_mam_login', array( __CLASS__, 'handle_login' ) );
		add_action( 'admin_post_mam_profile_update', array( __CLASS__, 'handle_profile_update' ) );
		add_action( 'admin_post_mam_appointment_request', array( __CLASS__, 'handle_appointment_request' ) );
	}

	public static function status_labels(): array {
		return array(
			'pending'     => __( 'Pending', 'med-appointment-manager' ),
			'approved'    => __( 'Approved', 'med-appointment-manager' ),
			'rescheduled' => __( 'Rescheduled', 'med-appointment-manager' ),
			'cancelled'   => __( 'Cancelled', 'med-appointment-manager' ),
		);
	}

	public static function handle_register(): void {
		check_admin_referer( 'mam_register_nonce' );

		$role     = isset( $_POST['role'] ) ? sanitize_key( wp_unslash( $_POST['role'] ) ) : 'patient';
		$name     = isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '';
		$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$password = isset( $_POST['password'] ) ? (string) $_POST['password'] : '';

		if ( ! in_array( $role, array( 'patient', 'provider' ), true ) || empty( $name ) || ! is_email( $email ) || strlen( $password ) < 8 ) {
			self::redirect_with_message( 'error', 'invalid_registration' );
		}

		$user_id = wp_insert_user(
			array(
				'user_login'   => $email,
				'user_email'   => $email,
				'display_name' => $name,
				'user_pass'    => $password,
				'role'         => $role,
			)
		);

		if ( is_wp_error( $user_id ) ) {
			self::redirect_with_message( 'error', 'registration_failed' );
		}

		global $wpdb;
		if ( 'patient' === $role ) {
			$wpdb->insert( MAM_DB::patients_table(), array( 'user_id' => $user_id ), array( '%d' ) );
		} else {
			$wpdb->insert( MAM_DB::providers_table(), array( 'user_id' => $user_id ), array( '%d' ) );
		}

		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id );
		self::redirect_with_message( 'success', 'registered' );
	}

	public static function handle_login(): void {
		check_admin_referer( 'mam_login_nonce' );
		$creds = array(
			'user_login'    => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
			'user_password' => isset( $_POST['password'] ) ? (string) $_POST['password'] : '',
			'remember'      => true,
		);
		$user  = wp_signon( $creds, false );

		if ( is_wp_error( $user ) ) {
			self::redirect_with_message( 'error', 'login_failed' );
		}

		self::redirect_with_message( 'success', 'logged_in' );
	}

	public static function handle_profile_update(): void {
		if ( ! is_user_logged_in() ) {
			wp_die( esc_html__( 'Unauthorized request.', 'med-appointment-manager' ) );
		}

		check_admin_referer( 'mam_profile_nonce' );
		global $wpdb;

		$data = array(
			'phone'             => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
			'date_of_birth'     => isset( $_POST['date_of_birth'] ) ? sanitize_text_field( wp_unslash( $_POST['date_of_birth'] ) ) : null,
			'gender'            => isset( $_POST['gender'] ) ? sanitize_text_field( wp_unslash( $_POST['gender'] ) ) : '',
			'address'           => isset( $_POST['address'] ) ? sanitize_textarea_field( wp_unslash( $_POST['address'] ) ) : '',
			'emergency_contact' => isset( $_POST['emergency_contact'] ) ? sanitize_text_field( wp_unslash( $_POST['emergency_contact'] ) ) : '',
			'medical_history'   => isset( $_POST['medical_history'] ) ? sanitize_textarea_field( wp_unslash( $_POST['medical_history'] ) ) : '',
		);

		$exists = $wpdb->get_var( $wpdb->prepare( 'SELECT id FROM ' . MAM_DB::patients_table() . ' WHERE user_id = %d', get_current_user_id() ) );
		if ( $exists ) {
			$wpdb->update( MAM_DB::patients_table(), $data, array( 'user_id' => get_current_user_id() ) );
		} else {
			$data['user_id'] = get_current_user_id();
			$wpdb->insert( MAM_DB::patients_table(), $data );
		}

		self::redirect_with_message( 'success', 'profile_saved' );
	}

	public static function handle_appointment_request(): void {
		$user = wp_get_current_user();
		if ( ! is_user_logged_in() || ( ! in_array( 'patient', (array) $user->roles, true ) && ! current_user_can( 'manage_options' ) ) ) {
			wp_die( esc_html__( 'Unauthorized request.', 'med-appointment-manager' ) );
		}
		check_admin_referer( 'mam_appointment_nonce' );

		$provider_user_id = isset( $_POST['provider_user_id'] ) ? absint( $_POST['provider_user_id'] ) : 0;
		$datetime_raw     = isset( $_POST['appointment_time'] ) ? sanitize_text_field( wp_unslash( $_POST['appointment_time'] ) ) : '';
		$reason           = isset( $_POST['reason'] ) ? sanitize_textarea_field( wp_unslash( $_POST['reason'] ) ) : '';
		$timestamp        = strtotime( $datetime_raw );

		if ( $provider_user_id <= 0 || false === $timestamp || empty( $reason ) ) {
			self::redirect_with_message( 'error', 'invalid_appointment' );
		}

		global $wpdb;
		$wpdb->insert(
			MAM_DB::appointments_table(),
			array(
				'patient_user_id'   => get_current_user_id(),
				'provider_user_id'  => $provider_user_id,
				'appointment_time'  => gmdate( 'Y-m-d H:i:s', $timestamp ),
				'reason'            => $reason,
				'status'            => 'pending',
				'created_by'        => get_current_user_id(),
			),
			array( '%d', '%d', '%s', '%s', '%s', '%d' )
		);

		$appointment_id = (int) $wpdb->insert_id;
		self::log_action( $appointment_id, 'created', __( 'Appointment request submitted.', 'med-appointment-manager' ) );
		$appointment = self::get_appointment( $appointment_id );
		if ( $appointment ) {
			MAM_Notifications::notify_new_request( $appointment );
		}

		self::redirect_with_message( 'success', 'appointment_created' );
	}

	public static function get_appointment( int $appointment_id ): ?object {
		global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . MAM_DB::appointments_table() . ' WHERE id = %d', $appointment_id ) );
		return $row ?: null;
	}

	public static function get_user_appointments( int $user_id, string $role ): array {
		global $wpdb;

		if ( 'patient' === $role ) {
			$sql = $wpdb->prepare( 'SELECT * FROM ' . MAM_DB::appointments_table() . ' WHERE patient_user_id = %d ORDER BY appointment_time ASC', $user_id );
		} elseif ( 'provider' === $role ) {
			$sql = $wpdb->prepare( 'SELECT * FROM ' . MAM_DB::appointments_table() . ' WHERE provider_user_id = %d ORDER BY appointment_time ASC', $user_id );
		} else {
			$sql = 'SELECT * FROM ' . MAM_DB::appointments_table() . ' ORDER BY appointment_time ASC';
		}

		return $wpdb->get_results( $sql ) ?: array();
	}

	public static function update_appointment_status( int $appointment_id, string $status, string $note = '', ?string $reschedule = null ): bool {
		$allowed = array( 'approved', 'rescheduled', 'cancelled' );
		if ( ! in_array( $status, $allowed, true ) ) {
			return false;
		}

		global $wpdb;
		$update = array(
			'status'     => $status,
			'updated_by' => get_current_user_id(),
		);

		if ( 'rescheduled' === $status && $reschedule ) {
			$timestamp = strtotime( $reschedule );
			if ( false === $timestamp ) {
				return false;
			}
			$update['reschedule_time'] = gmdate( 'Y-m-d H:i:s', $timestamp );
		}

		$updated = $wpdb->update( MAM_DB::appointments_table(), $update, array( 'id' => $appointment_id ) );
		if ( false === $updated ) {
			return false;
		}

		self::log_action( $appointment_id, $status, $note );
		$appointment = self::get_appointment( $appointment_id );
		if ( $appointment ) {
			MAM_Notifications::notify_status_change( $appointment, $note );
		}

		return true;
	}

	public static function log_action( int $appointment_id, string $action, string $note = '' ): void {
		global $wpdb;
		$wpdb->insert(
			MAM_DB::logs_table(),
			array(
				'appointment_id' => $appointment_id,
				'action'         => sanitize_key( $action ),
				'note'           => $note,
				'performed_by'   => get_current_user_id() ?: 0,
			),
			array( '%d', '%s', '%s', '%d' )
		);
	}

	public static function get_messages(): array {
		$messages = array(
			'registered'          => __( 'Registration successful.', 'med-appointment-manager' ),
			'logged_in'           => __( 'Login successful.', 'med-appointment-manager' ),
			'profile_saved'       => __( 'Profile saved successfully.', 'med-appointment-manager' ),
			'appointment_created' => __( 'Appointment request submitted.', 'med-appointment-manager' ),
			'invalid_registration'=> __( 'Please complete all required registration fields.', 'med-appointment-manager' ),
			'registration_failed' => __( 'Registration failed. Try another email.', 'med-appointment-manager' ),
			'login_failed'        => __( 'Invalid email or password.', 'med-appointment-manager' ),
			'invalid_appointment' => __( 'Please enter valid appointment details.', 'med-appointment-manager' ),
		);

		$key  = isset( $_GET['mam_msg'] ) ? sanitize_key( wp_unslash( $_GET['mam_msg'] ) ) : '';
		$type = isset( $_GET['mam_type'] ) ? sanitize_key( wp_unslash( $_GET['mam_type'] ) ) : '';
		if ( empty( $key ) || empty( $type ) || ! isset( $messages[ $key ] ) ) {
			return array();
		}

		return array(
			'type'    => in_array( $type, array( 'success', 'error' ), true ) ? $type : 'success',
			'message' => $messages[ $key ],
		);
	}

	private static function redirect_with_message( string $type, string $key ): void {
		$url = wp_get_referer() ? wp_get_referer() : home_url( '/' );
		wp_safe_redirect(
			add_query_arg(
				array(
					'mam_type' => $type,
					'mam_msg'  => $key,
				),
				$url
			)
		);
		exit;
	}
}
