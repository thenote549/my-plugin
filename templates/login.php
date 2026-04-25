<div class="mam-wrap">
	<?php if ( ! empty( $message ) ) : ?>
		<div class="mam-alert mam-alert-<?php echo esc_attr( $message['type'] ); ?>"><?php echo esc_html( $message['message'] ); ?></div>
	<?php endif; ?>
	<div class="mam-grid mam-grid-2">
		<div class="mam-card">
			<h3><?php esc_html_e( 'Login', 'med-appointment-manager' ); ?></h3>
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" class="mam-form">
				<input type="hidden" name="action" value="mam_login" />
				<?php wp_nonce_field( 'mam_login_nonce' ); ?>
				<label for="mam_login_email"><?php esc_html_e( 'Email', 'med-appointment-manager' ); ?></label>
				<input id="mam_login_email" type="email" name="email" required />
				<label for="mam_login_password"><?php esc_html_e( 'Password', 'med-appointment-manager' ); ?></label>
				<input id="mam_login_password" type="password" name="password" required />
				<button type="submit" class="mam-btn"><?php esc_html_e( 'Login', 'med-appointment-manager' ); ?></button>
			</form>
		</div>
		<div class="mam-card">
			<h3><?php esc_html_e( 'Register', 'med-appointment-manager' ); ?></h3>
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" class="mam-form">
				<input type="hidden" name="action" value="mam_register" />
				<?php wp_nonce_field( 'mam_register_nonce' ); ?>
				<label for="mam_full_name"><?php esc_html_e( 'Full Name', 'med-appointment-manager' ); ?></label>
				<input id="mam_full_name" type="text" name="full_name" required />
				<label for="mam_email"><?php esc_html_e( 'Email', 'med-appointment-manager' ); ?></label>
				<input id="mam_email" type="email" name="email" required />
				<label for="mam_password"><?php esc_html_e( 'Password (8+ chars)', 'med-appointment-manager' ); ?></label>
				<input id="mam_password" type="password" name="password" minlength="8" required />
				<label for="mam_role"><?php esc_html_e( 'Role', 'med-appointment-manager' ); ?></label>
				<select id="mam_role" name="role" required>
					<option value="patient"><?php esc_html_e( 'Patient', 'med-appointment-manager' ); ?></option>
					<option value="provider"><?php esc_html_e( 'Provider', 'med-appointment-manager' ); ?></option>
				</select>
				<button type="submit" class="mam-btn"><?php esc_html_e( 'Create Account', 'med-appointment-manager' ); ?></button>
			</form>
		</div>
	</div>
</div>
