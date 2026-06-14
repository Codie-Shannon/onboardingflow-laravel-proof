# OnboardingFlow Project Summary

OnboardingFlow is a standalone Laravel/MySQL proof-of-concept using sample data. It is not connected to OSHE systems.

The project demonstrates a trackable onboarding workflow where an admin can create invites, an applicant can submit onboarding details and required documents, and reviewers can track checklist items, document status, missing information, resubmissions, reports, and activity history.

## Why This Exists

The proof was built to make an onboarding workflow easier to review visually.

Instead of discussing the process only as emails, documents, and manual follow-up, OnboardingFlow shows how the workflow could be tracked in one place:

- who was invited
- what they submitted
- what documents are required
- what still needs review
- what information is missing
- who followed up
- when the applicant resubmitted
- what the audit trail shows

## Key Features

- Template-based onboarding invites
- Public applicant onboarding form
- Required document tracking
- SharePoint-backed document uploads
- Reviewer checklist
- Missing-information follow-up
- Applicant resubmission workflow
- Admin / Reviewer / Read-only roles
- Microsoft Graph email sending
- Reports and CSV export
- Activity history / audit trail

## Current Status

This is a working proof-of-concept. It is suitable for visual review and workflow discussion, but not production use yet.

The proof uses sample data and a development Microsoft 365 tenant/integration path.

## What It Proves

The proof demonstrates that the onboarding process can be structured into a trackable workflow:

1. create an invite
2. collect applicant information
3. collect required documents
4. review submitted information
5. request missing information
6. allow applicant resubmission
7. review again
8. track the activity history

## Production Path

A real pilot would require stakeholder review of the actual onboarding workflow, confirmed onboarding fields and document requirements, security and privacy review, Microsoft 365 tenant/domain confirmation, approved test data, deployment planning, user acceptance testing, retention and backup rules, and error monitoring/support.
