<div class="mam-wrap">
	<?php if ( ! empty( $message ) ) : ?>
		<div class="mam-alert mam-alert-<?php echo esc_attr( $message['type'] ); ?>"><?php echo esc_html( $message['message'] ); ?></div>
	<?php endif; ?>
	<div class="mam-card">
		<h3><?php esc_html_e( 'Patient Intake Profile', 'med-appointment-manager' ); ?></h3>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="mam-form mam-grid mam-grid-2">
			<input type="hidden" name="action" value="mam_profile_update" />
			<?php wp_nonce_field( 'mam_profile_nonce' ); ?>
			<label><?php esc_html_e( 'Phone', 'med-appointment-manager' ); ?><input type="text" name="phone" value="<?php echo esc_attr( $profile->phone ?? '' ); ?>" /></label>
			<label><?php esc_html_e( 'Date of Birth', 'med-appointment-manager' ); ?><input type="date" name="date_of_birth" value="<?php echo esc_attr( $profile->date_of_birth ?? '' ); ?>" /></label>
			<label><?php esc_html_e( 'Gender', 'med-appointment-manager' ); ?><input type="text" name="gender" value="<?php echo esc_attr( $profile->gender ?? '' ); ?>" /></label>
			<label><?php esc_html_e( 'Emergency Contact', 'med-appointment-manager' ); ?><input type="text" name="emergency_contact" value="<?php echo esc_attr( $profile->emergency_contact ?? '' ); ?>" /></label>
			<label class="mam-col-full"><?php esc_html_e( 'Address', 'med-appointment-manager' ); ?><textarea name="address"><?php echo esc_textarea( $profile->address ?? '' ); ?></textarea></label>
			<label class="mam-col-full"><?php esc_html_e( 'Medical History', 'med-appointment-manager' ); ?><textarea name="medical_history"><?php echo esc_textarea( $profile->medical_history ?? '' ); ?></textarea></label>
			<div class="mam-col-full"><button class="mam-btn" type="submit"><?php esc_html_e( 'Save Profile', 'med-appointment-manager' ); ?></button></div>
		</form>
	</div>

	<div class="mam-card">
		<h3><?php esc_html_e( 'Upcoming Appointments', 'med-appointment-manager' ); ?></h3>
		<div class="mam-table-wrap">
			<table class="mam-table">
				<thead><tr><th><?php esc_html_e( 'Provider', 'med-appointment-manager' ); ?></th><th><?php esc_html_e( 'Date & Time', 'med-appointment-manager' ); ?></th><th><?php esc_html_e( 'Status', 'med-appointment-manager' ); ?></th><th><?php esc_html_e( 'Reason', 'med-appointment-manager' ); ?></th></tr></thead>
				<tbody>
					<?php if ( empty( $appointments ) ) : ?>
						<tr><td colspan="4"><?php esc_html_e( 'No appointments found.', 'med-appointment-manager' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $appointments as $appointment ) : ?>
							<?php $provider = get_userdata( (int) $appointment->provider_user_id ); ?>
							<tr>
								<td><?php echo esc_html( $provider ? $provider->display_name : __( 'Unknown', 'med-appointment-manager' ) ); ?></td>
								<td><?php echo esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $appointment->reschedule_time ?: $appointment->appointment_time ) ) ); ?></td>
								<td><span class="mam-badge mam-<?php echo esc_attr( $appointment->status ); ?>"><?php echo esc_html( $labels[ $appointment->status ] ?? $appointment->status ); ?></span></td>
								<td><?php echo esc_html( $appointment->reason ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
