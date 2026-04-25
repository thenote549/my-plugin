<?php
/**
 * Admin settings.
 *
 * @package Med_Appointment_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MAM_Admin {

	public static function init(): void {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	public static function register_menu(): void {
		add_options_page(
			__( 'Med Appointment Manager', 'med-appointment-manager' ),
			__( 'Med Appointment Manager', 'med-appointment-manager' ),
			'manage_options',
			'med-appointment-manager',
			array( __CLASS__, 'settings_page' )
		);
	}

	public static function register_settings(): void {
		register_setting(
			'mam_settings',
			'mam_cleanup_on_uninstall',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( __CLASS__, 'sanitize_toggle' ),
				'default'           => 'no',
			)
		);
	}

	public static function sanitize_toggle( string $value ): string {
		return 'yes' === $value ? 'yes' : 'no';
	}

	public static function settings_page(): void {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Med Appointment Manager Settings', 'med-appointment-manager' ); ?></h1>
			<form action="options.php" method="post">
				<?php settings_fields( 'mam_settings' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row"><?php esc_html_e( 'Cleanup on uninstall', 'med-appointment-manager' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="mam_cleanup_on_uninstall" value="yes" <?php checked( get_option( 'mam_cleanup_on_uninstall', 'no' ), 'yes' ); ?> />
								<?php esc_html_e( 'Remove plugin database tables and options during uninstall.', 'med-appointment-manager' ); ?>
							</label>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
