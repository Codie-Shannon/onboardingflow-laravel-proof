# Known Limitations

OnboardingFlow is a proof-of-concept using sample data. It is not production-ready.

## General Limitations

- The workflow is based on assumed onboarding requirements.
- It has not been validated against real OSHE data requirements.
- It has not been reviewed for production security.
- It has not been reviewed for privacy/compliance.
- It has not gone through formal user acceptance testing.
- It has not been deployed to a production hosting environment.

## Authentication and Access

- Local demo users are used for Admin / Reviewer / Read-only roles.
- Organisation login / SSO is not fully implemented.
- Multi-factor authentication is not configured in the application itself.
- Permission boundaries are suitable for proof review, but would need real stakeholder approval.

## Microsoft 365

- Microsoft Graph email sending works internally in the test tenant.
- External email delivery from a new/development tenant may be blocked by Microsoft outbound protection.
- SharePoint upload works through Graph in the configured test environment.
- A real pilot would need the organisation's tenant/domain, permissions, retention, and storage policies confirmed.

## Document Uploads

- SharePoint document upload is implemented.
- File type policy is not final.
- Malware scanning policy is not implemented in the application.
- File retention/deletion rules are not final.
- Replacement uploads do not currently include a full document versioning/cleanup workflow inside the application.

## Data and Privacy

- Sample data is used.
- Real applicant data should not be entered until privacy/security requirements are confirmed.
- Data retention and backup rules are not final.
- Audit retention rules are not final.

## Deployment

- The proof runs locally.
- Docker/Lando/deployment documentation may be added as a production path.
- Monitoring, logging, backups, and error alerting are not production-hardened.
