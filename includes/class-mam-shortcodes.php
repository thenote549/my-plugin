<?php
/**
 * Shortcodes.
 *
 * @package Med_Appointment_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MAM_Shortcodes {

	public static function init(): void {
		add_shortcode( 'med_login', array( __CLASS__, 'render_login' ) );
		add_shortcode( 'med_patient_dashboard', array( __CLASS__, 'render_patient_dashboard' ) );
		add_shortcode( 'med_provider_dashboard', array( __CLASS__, 'render_provider_dashboard' ) );
		add_shortcode( 'med_appointment_form', array( __CLASS__, 'render_appointment_form' ) );
		add_shortcode( 'med_admin_panel', array( __CLASS__, 'render_admin_panel' ) );
	}

	public static function render_login(): string {
		if ( is_user_logged_in() ) {
			return '<div class="mam-alert mam-alert-success">' . esc_html__( 'You are already logged in.', 'med-appointment-manager' ) . '</div>';
		}

		return self::render_template(
			'login.php',
			array(
				'message' => MAM_Forms::get_messages(),
			)
		);
	}

	public static function render_patient_dashboard(): string {
		if ( ! is_user_logged_in() ) {
			return '<div class="mam-alert mam-alert-error">' . esc_html__( 'Please login to access patient dashboard.', 'med-appointment-manager' ) . '</div>';
		}

		$user = wp_get_current_user();
		if ( ! in_array( 'patient', (array) $user->roles, true ) && ! current_user_can( 'manage_options' ) ) {
			return '<div class="mam-alert mam-alert-error">' . esc_html__( 'Patient access only.', 'med-appointment-manager' ) . '</div>';
		}

		global $wpdb;
		$profile = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . MAM_DB::patients_table() . ' WHERE user_id = %d', get_current_user_id() ) );
		$apps    = MAM_Forms::get_user_appointments( get_current_user_id(), 'patient' );

		return self::render_template(
			'patient-dashboard.php',
			array(
				'message'      => MAM_Forms::get_messages(),
				'profile'      => $profile,
				'appointments' => $apps,
				'labels'       => MAM_Forms::status_labels(),
			)
		);
	}

	public static function render_provider_dashboard(): string {
		if ( ! is_user_logged_in() ) {
			return '<div class="mam-alert mam-alert-error">' . esc_html__( 'Please login to access provider dashboard.', 'med-appointment-manager' ) . '</div>';
		}
		$user = wp_get_current_user();
		if ( ! in_array( 'provider', (array) $user->roles, true ) && ! current_user_can( 'manage_options' ) ) {
			return '<div class="mam-alert mam-alert-error">' . esc_html__( 'Provider access only.', 'med-appointment-manager' ) . '</div>';
		}

		$apps = MAM_Forms::get_user_appointments( get_current_user_id(), current_user_can( 'manage_options' ) ? 'administrator' : 'provider' );
		return self::render_template(
			'provider-dashboard.php',
			array(
				'appointments' => $apps,
				'labels'       => MAM_Forms::status_labels(),
			)
		);
	}

	public static function render_appointment_form(): string {
		if ( ! is_user_logged_in() ) {
			return '<div class="mam-alert mam-alert-error">' . esc_html__( 'Please login to request an appointment.', 'med-appointment-manager' ) . '</div>';
		}

		$user = wp_get_current_user();
		if ( ! in_array( 'patient', (array) $user->roles, true ) && ! current_user_can( 'manage_options' ) ) {
			return '<div class="mam-alert mam-alert-error">' . esc_html__( 'Patient access only.', 'med-appointment-manager' ) . '</div>';
		}

		$providers = get_users(
			array(
				'role'   => 'provider',
				'fields' => array( 'ID', 'display_name' ),
			)
		);

		return self::render_template(
			'appointment-form.php',
			array(
				'message'   => MAM_Forms::get_messages(),
				'providers' => $providers,
			)
		);
	}

	public static function render_admin_panel(): string {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '<div class="mam-alert mam-alert-error">' . esc_html__( 'Administrator access only.', 'med-appointment-manager' ) . '</div>';
		}

		$apps = MAM_Forms::get_user_appointments( get_current_user_id(), 'administrator' );
		return self::render_template(
			'admin-panel.php',
			array(
				'appointments' => $apps,
				'labels'       => MAM_Forms::status_labels(),
			)
		);
	}

	private static function render_template( string $template, array $vars = array() ): string {
		$path = MAM_PLUGIN_DIR . 'templates/' . $template;
		if ( ! file_exists( $path ) ) {
			return '';
		}

		ob_start();
		extract( $vars, EXTR_SKIP );
		include $path;
		return (string) ob_get_clean();
	}
}
