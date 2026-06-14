# OnboardingFlow

OnboardingFlow is a Laravel/MySQL proof-of-concept for a trackable onboarding workflow.

It was built as a standalone sample-data project to demonstrate how an onboarding process could move from manual emailed documents into a structured review workflow.

This proof is not connected to OSHE systems and is not production-ready.

## Project Summary

OnboardingFlow demonstrates a complete onboarding review loop:

1. Admin creates an onboarding template.
2. Admin creates an invite for an applicant.
3. Applicant opens the public onboarding link.
4. Applicant submits onboarding details and required documents.
5. Documents are uploaded to SharePoint through Microsoft Graph.
6. Reviewer checks the submission, checklist, and documents.
7. Reviewer can request missing information.
8. Applicant can reopen the same link and resubmit updates.
9. Activity history records the workflow trail.
10. Reports summarise onboarding progress.

## What It Demonstrates

- Template-based onboarding invites
- Public applicant onboarding form
- Submission tracking
- Required document tracking
- SharePoint-backed document uploads
- Reviewer checklist
- Missing-information follow-up
- Applicant resubmission workflow
- Admin / Reviewer / Read-only roles
- Activity history
- Reports page
- CSV export
- Microsoft Graph invite email sending
- Microsoft Graph needs-info email sending
- Microsoft 365 integration path

## Recommended Demo Path

1. Log in as admin.
2. Open the dashboard.
3. Open the invite list.
4. Open an onboarding invite.
5. View the public applicant form.
6. Upload a required document.
7. Confirm the document appears in SharePoint.
8. Log in as reviewer.
9. Mark a document as reviewed.
10. Request missing information.
11. Preview or send the needs-info follow-up email.
12. Open the public link again in resubmission mode.
13. Resubmit updated information.
14. View the activity log and reports.

## Tech Stack

- Laravel
- PHP
- MySQL
- Blade
- Tailwind CSS
- Microsoft Graph
- Outlook email sending
- SharePoint document storage

## Demo Users

After seeding:

```text
admin@example.com / password
reviewer@example.com / password
readonly@example.com / password
```

## Environment Configuration

Copy `.env.example` to `.env` and configure the local database and Microsoft 365 values as required.

Microsoft 365 values should never be committed to source control.

```env
ONBOARDING_EMAIL_PROVIDER=microsoft_graph
ONBOARDING_DOCUMENT_STORAGE_PROVIDER=sharepoint

MICROSOFT_TENANT_ID=
MICROSOFT_CLIENT_ID=
MICROSOFT_CLIENT_SECRET=
MICROSOFT_MAIL_FROM=

MICROSOFT_SHAREPOINT_SITE_ID=
MICROSOFT_SHAREPOINT_DRIVE_ID=
MICROSOFT_SHAREPOINT_FOLDER=OnboardingFlow
```

## Local Setup

Install dependencies:

```bash
composer install
npm install
```

Create the app key:

```bash
php artisan key:generate
```

Run migrations and seed demo users:

```bash
php artisan migrate
php artisan db:seed --class=DemoUserSeeder
```

Run the app:

```bash
php artisan serve
npm run dev
```

## Documentation

See the `docs` folder for project summary, demo script, demo video plan, manual QA checklist, Microsoft 365 setup notes, production readiness notes, known limitations, pilot next steps, and screenshots.

## Known Limitations

This is a proof-of-concept, not a production system.

Before production or pilot use, it would need security review, privacy review, deployment hardening, monitoring/error handling, data retention policy, Microsoft 365 tenant/domain review, user acceptance testing, secrets management, file upload security review, and backup/recovery planning.

## Microsoft 365 Note

The proof uses Microsoft Graph for Outlook email and SharePoint document storage.

External delivery from a new development Microsoft 365 tenant may be blocked by Microsoft outbound protection. A real pilot should use the organisation's production tenant/domain or complete Microsoft support/domain reputation setup.

## Safe Demo Framing

This project uses sample data and is separate from any real organisation systems. It is intended to make the workflow easier to review visually.
