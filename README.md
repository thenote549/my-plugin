# Med Appointment Manager

A production-ready WordPress plugin for managing medical appointment requests and workflows between **patients**, **providers**, and **administrators**.

## Features

- Patient/provider registration and login via shortcode UI.
- Patient intake/profile form.
- Appointment request form.
- Patient dashboard with appointment status badges.
- Provider dashboard for pending and managed requests.
- Provider/admin workflow actions: **approve**, **reschedule**, **cancel**.
- Appointment statuses: `pending`, `approved`, `rescheduled`, `cancelled`.
- Email notifications for creation and status changes.
- AJAX-powered dashboard actions.
- Role-based access control.
- Secure nonces, sanitization, escaping.
- Database table creation with `dbDelta` on activation.
- Optional uninstall cleanup from Settings.

## Plugin Structure

```
/med-appointment-manager.php
/includes/
/admin/
/public/
/assets/css/
/assets/js/
/templates/
/uninstall.php
```

## Installation

1. Copy plugin folder into `wp-content/plugins/med-appointment-manager`.
2. Activate **Med Appointment Manager** from WordPress admin.
3. Ensure permalinks are initialized (visit **Settings > Permalinks** once if needed).
4. Create pages and place these shortcodes:
   - `[med_login]`
   - `[med_patient_dashboard]`
   - `[med_provider_dashboard]`
   - `[med_appointment_form]`
   - `[med_admin_panel]`
5. Go to **Settings > Med Appointment Manager** to configure uninstall cleanup behavior.

## Role Workflow

- **Patient**: register/login, complete intake profile, submit appointment requests, view status.
- **Provider**: review assigned appointments, approve/reschedule/cancel using AJAX actions.
- **Administrator**: full oversight with management panel and global updates.

## Database Tables

Created on activation:

- `{prefix}mam_patients`
- `{prefix}mam_providers`
- `{prefix}mam_appointments`
- `{prefix}mam_appointment_logs`

## Security Notes

- All forms use WordPress nonces.
- Inputs are sanitized (`sanitize_text_field`, `sanitize_textarea_field`, `sanitize_email`, etc.).
- Outputs are escaped (`esc_html`, `esc_attr`, `esc_url`, etc.).
- AJAX updates verify nonce + permissions.

## Testing Checklist

- Activate plugin without PHP warnings/errors.
- Register patient and provider via `[med_login]`.
- Submit appointment request via `[med_appointment_form]`.
- Approve/reschedule/cancel from provider dashboard.
- Confirm status reflects on patient dashboard.
- Verify notification emails are sent by your WP mail transport.

## Requirements

- WordPress 6.x+
- PHP 8.0+
- MySQL/MariaDB with table creation privileges
