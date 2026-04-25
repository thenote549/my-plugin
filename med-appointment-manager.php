<?php
/**
 * Plugin Name: Med Appointment Manager
 * Description: Medical appointment management plugin with patient and provider dashboards.
 * Version: 1.0.0
 * Author: Med Appointment Manager
 * Requires PHP: 8.0
 * Text Domain: med-appointment-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MAM_VERSION', '1.0.0' );
define( 'MAM_PLUGIN_FILE', __FILE__ );
define( 'MAM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MAM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once MAM_PLUGIN_DIR . 'includes/class-mam-db.php';
require_once MAM_PLUGIN_DIR . 'includes/class-mam-notifications.php';
require_once MAM_PLUGIN_DIR . 'includes/class-mam-forms.php';
require_once MAM_PLUGIN_DIR . 'includes/class-mam-shortcodes.php';
require_once MAM_PLUGIN_DIR . 'includes/class-mam-ajax.php';
require_once MAM_PLUGIN_DIR . 'includes/class-mam-admin.php';

register_activation_hook( __FILE__, array( 'MAM_DB', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'MAM_DB', 'deactivate' ) );

final class Med_Appointment_Manager {

	private static ?Med_Appointment_Manager $instance = null;

	public static function instance(): Med_Appointment_Manager {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		MAM_Forms::init();
		MAM_Shortcodes::init();
		MAM_Ajax::init();
		MAM_Admin::init();
	}

	public function load_textdomain(): void {
		load_plugin_textdomain( 'med-appointment-manager', false, dirname( plugin_basename( MAM_PLUGIN_FILE ) ) . '/languages' );
	}

	public function enqueue_public_assets(): void {
		wp_enqueue_style( 'mam-public', MAM_PLUGIN_URL . 'assets/css/mam-public.css', array(), MAM_VERSION );
		wp_enqueue_script( 'mam-public', MAM_PLUGIN_URL . 'assets/js/mam-public.js', array( 'jquery' ), MAM_VERSION, true );
		wp_localize_script(
			'mam-public',
			'mamData',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'mam_appointment_action' ),
			)
		);
	}

	public function enqueue_admin_assets(): void {
		wp_enqueue_style( 'mam-admin', MAM_PLUGIN_URL . 'assets/css/mam-admin.css', array(), MAM_VERSION );
	}
}

Med_Appointment_Manager::instance();
