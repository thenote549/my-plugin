<?php
/**
 * AJAX handlers.
 *
 * @package Med_Appointment_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MAM_Ajax {

	public static function init(): void {
		add_action( 'wp_ajax_mam_update_appointment_status', array( __CLASS__, 'update_appointment_status' ) );
	}

	public static function update_appointment_status(): void {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'med-appointment-manager' ) ), 403 );
		}

		check_ajax_referer( 'mam_appointment_action', 'nonce' );

		$appointment_id = isset( $_POST['appointment_id'] ) ? absint( $_POST['appointment_id'] ) : 0;
		$status         = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : '';
		$note           = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';
		$reschedule     = isset( $_POST['reschedule_time'] ) ? sanitize_text_field( wp_unslash( $_POST['reschedule_time'] ) ) : null;

		$appointment = MAM_Forms::get_appointment( $appointment_id );
		if ( ! $appointment ) {
			wp_send_json_error( array( 'message' => __( 'Appointment not found.', 'med-appointment-manager' ) ), 404 );
		}

		$user            = wp_get_current_user();
		$can_manage      = current_user_can( 'manage_options' ) || in_array( 'provider', (array) $user->roles, true );
		$provider_owns   = (int) $appointment->provider_user_id === get_current_user_id();
		if ( ! current_user_can( 'manage_options' ) && ( ! $can_manage || ! $provider_owns ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission.', 'med-appointment-manager' ) ), 403 );
		}

		$updated = MAM_Forms::update_appointment_status( $appointment_id, $status, $note, $reschedule );
		if ( ! $updated ) {
			wp_send_json_error( array( 'message' => __( 'Unable to update appointment status.', 'med-appointment-manager' ) ), 400 );
		}

		wp_send_json_success(
			array(
				'message' => __( 'Appointment updated successfully.', 'med-appointment-manager' ),
			)
		);
	}
}
