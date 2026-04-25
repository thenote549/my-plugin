(function ($) {
	'use strict';

	function showMessage(type, message) {
		const box = $('#mam-action-message');
		if (!box.length) {
			return;
		}
		box.removeClass('mam-alert-success mam-alert-error').addClass(type === 'success' ? 'mam-alert-success' : 'mam-alert-error').text(message).show();
	}

	$(document).on('click', '.mam-reschedule-trigger', function () {
		$(this).siblings('.mam-reschedule-time').toggle();
	});

	$(document).on('click', '.mam-action', function (e) {
		e.preventDefault();
		const btn = $(this);
		const row = btn.closest('tr');
		const status = btn.data('status');
		const payload = {
			action: 'mam_update_appointment_status',
			nonce: mamData.nonce,
			appointment_id: btn.data('id'),
			status: status,
			note: row.find('.mam-note').val() || ''
		};

		if (status === 'rescheduled') {
			const time = row.find('.mam-reschedule-time').val();
			if (!time) {
				showMessage('error', 'Please choose a new date and time before rescheduling.');
				return;
			}
			payload.reschedule_time = time;
		}

		$.post(mamData.ajaxUrl, payload)
			.done(function (response) {
				if (response.success) {
					showMessage('success', response.data.message);
					window.location.reload();
				} else {
					showMessage('error', response.data && response.data.message ? response.data.message : 'Action failed.');
				}
			})
			.fail(function () {
				showMessage('error', 'Request failed. Please try again.');
			});
	});
})(jQuery);
