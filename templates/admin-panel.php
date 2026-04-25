<div class="mam-wrap">
	<div class="mam-card">
		<h3><?php esc_html_e( 'Administrator Appointment Management', 'med-appointment-manager' ); ?></h3>
		<p><?php esc_html_e( 'Use this panel to monitor and process all appointment requests.', 'med-appointment-manager' ); ?></p>
		<?php echo do_shortcode( '[med_provider_dashboard]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</div>
