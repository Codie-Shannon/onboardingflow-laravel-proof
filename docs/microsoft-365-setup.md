# Microsoft 365 Setup Notes

OnboardingFlow uses Microsoft Graph as one possible integration path for email sending and SharePoint document storage.

This proof-of-concept uses sample data and is not connected to OSHE systems.

## Important Security Notes

Never commit `.env`, tenant IDs, client secrets, SharePoint IDs, Graph IDs, mailbox addresses, or real organisation configuration values to the repository.

Use `.env.example` placeholders only.

Application credentials should be rotated if they are accidentally shared, committed, or displayed publicly.

## Graph Explorer vs App Registration

Graph Explorer permissions are only used for manual testing and finding IDs.

Laravel does not use Graph Explorer permissions.

Laravel uses the configured Microsoft Entra app registration, client credentials, and application permissions.

## Required Microsoft Graph Application Permissions

The app registration requires Microsoft Graph application permissions for the proof:

- Mail.Send
- Sites.ReadWrite.All
- Files.ReadWrite.All

Admin consent must be granted in Microsoft Entra.

## Required Environment Variables

Do not commit real secrets or tenant IDs.

Example placeholders:

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

## Email Sending

Invite emails and needs-info follow-up emails are sent through Microsoft Graph using the configured Microsoft 365 mailbox.

The proof successfully tested internal tenant delivery.

## External Delivery Limitation

External delivery from a new or development Microsoft 365 tenant may be blocked by Microsoft outbound protection, including NDR code:

```text
550 5.7.708 Access denied, traffic not accepted from this IP
```

This is a Microsoft 365 tenant/mail reputation limitation, not an application workflow failure.

For a real pilot, external sending should use the organisation's production tenant/domain, proper SPF/DKIM/DMARC configuration, and Microsoft support clearance if required.

## SharePoint Document Storage

Required document uploads are stored in SharePoint through Microsoft Graph.

Each document requirement stores SharePoint metadata, including Drive ID, Item ID, Web URL, original filename, MIME type, file size, and upload timestamp.

## SharePoint Site and Drive IDs

The SharePoint Site ID and Drive ID can be found through Microsoft Graph.

General process:

1. Find the SharePoint site.
2. Copy the site `id`.
3. List the drives/document libraries for that site.
4. Copy the drive `id` for the document library being used.

The Site ID often looks like a hostname plus two GUIDs separated by commas.

The Drive ID often begins with `b!`.

Do not commit real IDs to the repository.

## Production Notes

For production use, Graph permissions should be reviewed and reduced where possible. Application credentials should be stored securely. SharePoint permissions and retention should be confirmed. File upload type/size limits, malware scanning, and compliance requirements should be reviewed.
