<?php
/**
 * Email notifications.
 *
 * @package Med_Appointment_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MAM_Notifications {

	public static function notify_new_request( object $appointment ): void {
		$provider = get_userdata( (int) $appointment->provider_user_id );
		$patient  = get_userdata( (int) $appointment->patient_user_id );

		if ( $provider ) {
			$subject = __( 'New Appointment Request', 'med-appointment-manager' );
			$message = sprintf(
				/* translators: 1: patient name, 2: appointment datetime */
				__( 'You received a new appointment request from %1$s for %2$s.', 'med-appointment-manager' ),
				$patient ? $patient->display_name : __( 'Unknown patient', 'med-appointment-manager' ),
				wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $appointment->appointment_time ) )
			);
			wp_mail( $provider->user_email, $subject, $message );
		}

		if ( $patient ) {
			wp_mail(
				$patient->user_email,
				__( 'Appointment Request Submitted', 'med-appointment-manager' ),
				__( 'Your appointment request has been submitted and is pending approval.', 'med-appointment-manager' )
			);
		}
	}

	public static function notify_status_change( object $appointment, string $note = '' ): void {
		$patient = get_userdata( (int) $appointment->patient_user_id );
		if ( ! $patient ) {
			return;
		}

		$labels = MAM_Forms::status_labels();
		$status = $labels[ $appointment->status ] ?? $appointment->status;
		$time   = 'rescheduled' === $appointment->status && ! empty( $appointment->reschedule_time ) ? $appointment->reschedule_time : $appointment->appointment_time;
		$msg    = sprintf(
			/* translators: 1: status, 2: datetime */
			__( 'Your appointment was updated to %1$s. Date and time: %2$s.', 'med-appointment-manager' ),
			$status,
			wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $time ) )
		);

		if ( $note ) {
			$msg .= "\n\n" . sprintf(
				/* translators: %s: note */
				__( 'Note: %s', 'med-appointment-manager' ),
				$note
			);
		}

		wp_mail( $patient->user_email, __( 'Appointment Status Update', 'med-appointment-manager' ), $msg );
	}
}
