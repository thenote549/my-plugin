<?php
/**
 * Plugin uninstall cleanup.
 *
 * @package Med_Appointment_Manager
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( 'yes' !== get_option( 'mam_cleanup_on_uninstall', 'no' ) ) {
	return;
}

global $wpdb;
$tables = array(
	$wpdb->prefix . 'mam_patients',
	$wpdb->prefix . 'mam_providers',
	$wpdb->prefix . 'mam_appointments',
	$wpdb->prefix . 'mam_appointment_logs',
);

foreach ( $tables as $table ) {
	$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

delete_option( 'mam_cleanup_on_uninstall' );
