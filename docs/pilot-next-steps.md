# Pilot Next Steps

This document outlines a possible path from proof-of-concept to controlled pilot.

## 1. Review the Workflow

Review the proof with stakeholders and confirm whether the demonstrated workflow matches the real onboarding process.

Questions to answer:

- Who creates onboarding invites?
- Who reviews submissions?
- What statuses are required?
- What documents are actually required?
- What missing-information follow-up process is expected?
- What reports are useful?

## 2. Confirm Data Requirements

Confirm the actual fields that need to be collected from applicants.

Examples:

- name
- email
- phone
- organisation
- role / position
- emergency contact
- required documents
- notes / declarations

## 3. Confirm Document Requirements

Confirm required documents by onboarding type.

Examples:

- Photo ID
- Signed agreement
- Insurance document
- Qualification / certificate
- Site induction evidence

## 4. Confirm Technology Environment

Confirm whether Microsoft 365 is the preferred integration path.

Questions:

- Is Outlook used for official email?
- Is SharePoint used for document storage?
- Should Microsoft login/SSO be used?
- Are there existing retention/compliance rules?
- Are there existing SharePoint sites/libraries for onboarding?

## 5. Define Pilot Scope

Keep the first pilot controlled.

Suggested pilot scope:

- one onboarding type
- small number of internal reviewers
- sample or approved test applicants
- limited document set
- clear feedback window

## 6. Harden Security and Privacy

Before real data:

- complete security review
- complete privacy review
- confirm access roles
- confirm document permissions
- confirm data retention
- confirm backup/recovery process
- confirm file upload restrictions

## 7. Prepare Deployment

A real pilot would need:

- hosting environment
- database setup
- secure secret storage
- Microsoft Graph app registration
- SharePoint library/folder
- monitoring/logging
- backup process
- support contact/process

## 8. User Acceptance Testing

Run structured UAT:

- admin creates invite
- applicant submits onboarding
- reviewer reviews
- reviewer requests missing information
- applicant resubmits
- reviewer approves/rejects
- reports/activity log are checked

## 9. Pilot Decision

After review/testing, decide whether to continue toward production, revise workflow, change integration path, or pause/retire the proof.
