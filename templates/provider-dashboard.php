<div class="mam-wrap">
	<div id="mam-action-message" class="mam-alert" style="display:none;"></div>
	<div class="mam-card">
		<h3><?php esc_html_e( 'Provider Appointment Queue', 'med-appointment-manager' ); ?></h3>
		<div class="mam-table-wrap">
			<table class="mam-table">
				<thead><tr><th><?php esc_html_e( 'Patient', 'med-appointment-manager' ); ?></th><th><?php esc_html_e( 'Date & Time', 'med-appointment-manager' ); ?></th><th><?php esc_html_e( 'Status', 'med-appointment-manager' ); ?></th><th><?php esc_html_e( 'Reason', 'med-appointment-manager' ); ?></th><th><?php esc_html_e( 'Actions', 'med-appointment-manager' ); ?></th></tr></thead>
				<tbody>
					<?php if ( empty( $appointments ) ) : ?>
						<tr><td colspan="5"><?php esc_html_e( 'No appointments found.', 'med-appointment-manager' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $appointments as $appointment ) : ?>
							<?php $patient = get_userdata( (int) $appointment->patient_user_id ); ?>
							<tr>
								<td><?php echo esc_html( $patient ? $patient->display_name : __( 'Unknown', 'med-appointment-manager' ) ); ?></td>
								<td><?php echo esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $appointment->reschedule_time ?: $appointment->appointment_time ) ) ); ?></td>
								<td><span class="mam-badge mam-<?php echo esc_attr( $appointment->status ); ?>"><?php echo esc_html( $labels[ $appointment->status ] ?? $appointment->status ); ?></span></td>
								<td><?php echo esc_html( $appointment->reason ); ?></td>
								<td>
									<div class="mam-action-group">
										<button class="mam-btn mam-btn-small mam-action" data-status="approved" data-id="<?php echo esc_attr( $appointment->id ); ?>"><?php esc_html_e( 'Approve', 'med-appointment-manager' ); ?></button>
										<button class="mam-btn mam-btn-small mam-btn-secondary mam-action" data-status="cancelled" data-id="<?php echo esc_attr( $appointment->id ); ?>"><?php esc_html_e( 'Cancel', 'med-appointment-manager' ); ?></button>
										<button class="mam-btn mam-btn-small mam-btn-outline mam-action mam-reschedule-trigger" data-status="rescheduled" data-id="<?php echo esc_attr( $appointment->id ); ?>"><?php esc_html_e( 'Reschedule', 'med-appointment-manager' ); ?></button>
										<input type="datetime-local" class="mam-reschedule-time" style="display:none;" />
										<input type="text" class="mam-note" placeholder="<?php esc_attr_e( 'Optional note', 'med-appointment-manager' ); ?>" />
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
