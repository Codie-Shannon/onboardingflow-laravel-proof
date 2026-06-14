# Production Readiness Notes

OnboardingFlow is currently a proof-of-concept using sample data.

It demonstrates a possible workflow direction for tracked onboarding invites, applicant submissions, document requirements, reviewer actions, missing-information follow-up, reporting, and audit history.

## Working in the Proof

- Template-based onboarding invites
- Public applicant onboarding form
- Submission tracking
- Required document tracking
- SharePoint-backed document uploads
- Reviewer checklist
- Missing-information detection
- Missing-information follow-up messages
- Applicant resubmission workflow
- Admin / Reviewer / Read-only roles
- Activity history
- Reports page
- CSV export
- Microsoft Graph invite email sending
- Microsoft Graph needs-info email sending

## Proof vs Production

| Area | Proof Status | Production Requirement |
|---|---|---|
| Email | Microsoft Graph email works internally in the test tenant | Production tenant/domain deliverability review |
| Documents | SharePoint upload works through Graph | Retention, permissions, malware scanning, and storage policy |
| Authentication | Local demo users and roles | Organisation login, SSO, MFA, and access policy |
| Roles | Admin / Reviewer / Read-only implemented | Confirm real roles and permission boundaries |
| Data | Sample onboarding fields and documents | Approved real data model and privacy review |
| Audit trail | Activity history records major workflow events | Audit retention, reporting, and compliance review |
| Reports | Basic dashboard/reports/CSV export | Confirm reporting needs and access controls |
| Deployment | Local Laravel proof | Hosted environment, backups, monitoring, and support process |
| Secrets | `.env`-based local configuration | Secure secret storage and rotation process |
| File upload | SharePoint upload proof | File type restrictions, size limits, malware scanning |
| Error handling | Basic failure handling | Monitoring, alerting, retry strategy, support process |

## Not Production-Ready Yet

Before a real pilot or production deployment, the following would need review:

- Security review
- Privacy review
- Real organisation data requirements
- User acceptance testing
- Error handling and monitoring
- Backup and recovery process
- Data retention policy
- Access control review
- Microsoft 365 tenant/domain configuration
- Deployment process
- Logging and audit retention
- Email deliverability review
- File upload restrictions and malware scanning
- Secrets management

## Safe Demo Framing

This proof is separate from OSHE systems and uses sample data only.

It should be treated as a visual and technical proof-of-concept, not a production system.

## Suggested Pilot Path

1. Review the workflow with stakeholders.
2. Confirm actual onboarding fields and required documents.
3. Confirm whether Microsoft 365 is the preferred integration path.
4. Replace sample data with approved test data.
5. Harden authentication and access controls.
6. Confirm SharePoint storage structure and retention policy.
7. Run user acceptance testing.
8. Deploy to a controlled pilot environment.
9. Gather feedback.
10. Decide whether to continue, revise, or retire the proof.

## Production Hardening Questions

- Who owns onboarding data?
- What personal information is collected?
- How long should submissions and documents be retained?
- Which roles should access applicant documents?
- Should applicants authenticate before resubmitting?
- What file types and sizes are allowed?
- Where should uploaded documents be stored?
- Should files be scanned or reviewed before opening?
- What email address should system emails come from?
- What happens if Microsoft Graph or SharePoint upload fails?
- Who supports the workflow if users get stuck?
