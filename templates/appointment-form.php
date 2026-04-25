<div class="mam-wrap">
	<?php if ( ! empty( $message ) ) : ?>
		<div class="mam-alert mam-alert-<?php echo esc_attr( $message['type'] ); ?>"><?php echo esc_html( $message['message'] ); ?></div>
	<?php endif; ?>
	<div class="mam-card">
		<h3><?php esc_html_e( 'Request Appointment', 'med-appointment-manager' ); ?></h3>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="mam-form">
			<input type="hidden" name="action" value="mam_appointment_request" />
			<?php wp_nonce_field( 'mam_appointment_nonce' ); ?>
			<label for="mam_provider"><?php esc_html_e( 'Select Provider', 'med-appointment-manager' ); ?></label>
			<select id="mam_provider" name="provider_user_id" required>
				<option value=""><?php esc_html_e( 'Choose provider', 'med-appointment-manager' ); ?></option>
				<?php foreach ( $providers as $provider ) : ?>
					<option value="<?php echo esc_attr( $provider->ID ); ?>"><?php echo esc_html( $provider->display_name ); ?></option>
				<?php endforeach; ?>
			</select>

			<label for="mam_appointment_time"><?php esc_html_e( 'Preferred Date and Time', 'med-appointment-manager' ); ?></label>
			<input id="mam_appointment_time" type="datetime-local" name="appointment_time" required />

			<label for="mam_reason"><?php esc_html_e( 'Reason for Visit', 'med-appointment-manager' ); ?></label>
			<textarea id="mam_reason" name="reason" required></textarea>

			<button class="mam-btn" type="submit"><?php esc_html_e( 'Submit Request', 'med-appointment-manager' ); ?></button>
		</form>
	</div>
</div>
