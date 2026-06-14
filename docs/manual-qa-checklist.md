# Manual QA Checklist

This checklist was used to test the OnboardingFlow proof-of-concept manually.

## Admin

| Test | Expected Result | Result | Notes |
|---|---|---|---|
| Can log in as admin | Admin dashboard loads |  |  |
| Can create onboarding template | Template saves successfully |  |  |
| Can create onboarding invite | Invite detail page loads |  |  |
| Can send invite email through Microsoft Graph | Email sends and send count updates |  |  |
| Can update invite status | Status updates and activity log records it |  |  |
| Can update review checklist | Checklist item toggles and logs actor |  |  |
| Can update document requirement status | Document status updates and logs actor |  |  |
| Can export CSV | CSV downloads |  |  |

## Applicant

| Test | Expected Result | Result | Notes |
|---|---|---|---|
| Can open public onboarding link | Public form loads |  |  |
| Can submit onboarding form | Submission saves |  |  |
| Can upload required documents | File uploads successfully |  |  |
| Uploaded documents appear in SharePoint | File appears in configured folder/library |  |  |
| Can reopen same link when invite is Needs Info | Resubmission mode loads |  |  |
| Can update missing information | Existing values are prefilled and update saves |  |  |
| Resubmission returns invite to In Review | Invite status changes back to In Review |  |  |

## Reviewer

| Test | Expected Result | Result | Notes |
|---|---|---|---|
| Can log in as reviewer | Reviewer dashboard loads |  |  |
| Can update review checklist | Checklist update allowed |  |  |
| Can mark documents Provided / Reviewed / Missing / Not Required | Document status update allowed |  |  |
| Can add missing-information follow-up | Follow-up saves |  |  |
| Can send needs-info email | Email sends and follow-up count updates |  |  |
| Can export CSV | CSV downloads |  |  |
| Cannot create invite | Create invite control hidden or blocked |  |  |
| Cannot create template | Create template control hidden or blocked |  |  |
| Cannot send original invite email | Send invite email control hidden or blocked |  |  |

## Read-only

| Test | Expected Result | Result | Notes |
|---|---|---|---|
| Can log in as read-only | Read-only dashboard loads |  |  |
| Can view dashboard/invites/templates/reports/activity | Pages visible |  |  |
| Cannot create invite | Create invite control hidden or blocked |  |  |
| Cannot create template | Create template control hidden or blocked |  |  |
| Cannot send emails | Email send controls hidden or blocked |  |  |
| Cannot update review checklist/document status | Update controls hidden or blocked |  |  |
| Cannot add notes | Note form hidden or blocked |  |  |
| Cannot export CSV | Export hidden or blocked |  |  |

## Microsoft 365

| Test | Expected Result | Result | Notes |
|---|---|---|---|
| Invite email sends internally through Microsoft Graph | Internal tenant recipient receives email |  |  |
| Needs-info email sends internally through Microsoft Graph | Internal tenant recipient receives email |  |  |
| Uploaded files appear in SharePoint | File appears in target SharePoint library/folder |  |  |
| SharePoint file links open from admin invite detail | Link opens uploaded file |  |  |
| External personal Outlook delivery limitation documented | Limitation appears in docs |  |  |

## Activity Log

| Test | Expected Result | Result | Notes |
|---|---|---|---|
| Invite creation is logged | Activity appears with actor |  |  |
| Applicant submission is logged | Activity appears as Applicant |  |  |
| Document upload is logged | Activity appears as Applicant |  |  |
| Reviewer actions show reviewer name | Demo Reviewer appears as actor |  |  |
| Needs-info email send is logged | Activity appears with actor |  |  |
| Applicant resubmission is logged | Activity appears as Applicant |  |  |
