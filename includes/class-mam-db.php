<?php
/**
 * Database setup and helpers.
 *
 * @package Med_Appointment_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MAM_DB {

	public static function patients_table(): string {
		global $wpdb;
		return $wpdb->prefix . 'mam_patients';
	}

	public static function providers_table(): string {
		global $wpdb;
		return $wpdb->prefix . 'mam_providers';
	}

	public static function appointments_table(): string {
		global $wpdb;
		return $wpdb->prefix . 'mam_appointments';
	}

	public static function logs_table(): string {
		global $wpdb;
		return $wpdb->prefix . 'mam_appointment_logs';
	}

	public static function activate(): void {
		self::create_tables();
		self::add_roles();
		add_option( 'mam_cleanup_on_uninstall', 'no' );
	}

	public static function deactivate(): void {
		remove_role( 'patient' );
		remove_role( 'provider' );
	}

	private static function add_roles(): void {
		add_role(
			'patient',
			__( 'Patient', 'med-appointment-manager' ),
			array(
				'read' => true,
			)
		);

		add_role(
			'provider',
			__( 'Provider', 'med-appointment-manager' ),
			array(
				'read'                  => true,
				'mam_manage_appointments' => true,
			)
		);
	}

	private static function create_tables(): void {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate  = $wpdb->get_charset_collate();
		$patients_table   = self::patients_table();
		$providers_table  = self::providers_table();
		$appointments_tbl = self::appointments_table();
		$logs_table       = self::logs_table();

		$sql_patients = "CREATE TABLE {$patients_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT UNSIGNED NOT NULL,
			phone VARCHAR(30) DEFAULT '',
			date_of_birth DATE NULL,
			gender VARCHAR(20) DEFAULT '',
			address TEXT NULL,
			emergency_contact VARCHAR(255) DEFAULT '',
			medical_history LONGTEXT NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			UNIQUE KEY user_id (user_id)
		) {$charset_collate};";

		$sql_providers = "CREATE TABLE {$providers_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT UNSIGNED NOT NULL,
			specialty VARCHAR(255) DEFAULT '',
			bio TEXT NULL,
			availability TEXT NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			UNIQUE KEY user_id (user_id)
		) {$charset_collate};";

		$sql_appointments = "CREATE TABLE {$appointments_tbl} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			patient_user_id BIGINT UNSIGNED NOT NULL,
			provider_user_id BIGINT UNSIGNED NOT NULL,
			appointment_time DATETIME NOT NULL,
			reason TEXT NULL,
			status VARCHAR(30) NOT NULL DEFAULT 'pending',
			reschedule_time DATETIME NULL,
			created_by BIGINT UNSIGNED NOT NULL,
			updated_by BIGINT UNSIGNED NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY patient_user_id (patient_user_id),
			KEY provider_user_id (provider_user_id),
			KEY status (status)
		) {$charset_collate};";

		$sql_logs = "CREATE TABLE {$logs_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			appointment_id BIGINT UNSIGNED NOT NULL,
			action VARCHAR(50) NOT NULL,
			note TEXT NULL,
			performed_by BIGINT UNSIGNED NOT NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY appointment_id (appointment_id)
		) {$charset_collate};";

		dbDelta( $sql_patients );
		dbDelta( $sql_providers );
		dbDelta( $sql_appointments );
		dbDelta( $sql_logs );
	}
}
