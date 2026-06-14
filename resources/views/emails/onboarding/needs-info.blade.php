@php
    $unresolvedMissingInfoItems = $invite->missingInfoItems
        ->where('resolved', false)
        ->values();

    $missingDocumentRequirements = $invite->documentRequirements
        ->filter(fn ($requirement) => in_array($requirement->status, ['missing', 'provided'], true))
        ->values();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Onboarding information needed</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family:Arial, sans-serif; color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding:24px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:680px; background:#ffffff; border-radius:18px; overflow:hidden; border:1px solid #e2e8f0;">
                    <tr>
                        <td style="background:#0f766e; padding:24px;">
                            <div style="color:#ffffff; font-size:22px; font-weight:bold;">OnboardingFlow</div>
                            <div style="color:#ccfbf1; font-size:14px; margin-top:6px;">More information needed</div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:28px;">
                            <p style="font-size:16px; line-height:1.6; margin:0 0 16px;">
                                Hi {{ $invite->recipient_name }},
                            </p>

                            <p style="font-size:15px; line-height:1.6; margin:0 0 18px; color:#334155;">
                                We need a few updates before your onboarding can continue. Please use the button below to reopen your onboarding form and provide the missing information or documents.
                            </p>

                            @if ($unresolvedMissingInfoItems->isNotEmpty())
                                <div style="margin-top:22px;">
                                    <h2 style="font-size:16px; margin:0 0 10px; color:#0f172a;">
                                        Missing information
                                    </h2>

                                    <ul style="margin:0; padding-left:20px; color:#334155; line-height:1.6;">
                                        @foreach ($unresolvedMissingInfoItems as $item)
                                            <li>{{ $item->label }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if ($missingDocumentRequirements->isNotEmpty())
                                <div style="margin-top:22px;">
                                    <h2 style="font-size:16px; margin:0 0 10px; color:#0f172a;">
                                        Documents to check or provide
                                    </h2>

                                    <ul style="margin:0; padding-left:20px; color:#334155; line-height:1.6;">
                                        @foreach ($missingDocumentRequirements as $requirement)
                                            <li>
                                                {{ $requirement->label }}
                                                <span style="color:#64748b;">
                                                    — {{ ucfirst(str_replace('_', ' ', $requirement->status)) }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div style="margin-top:28px;">
                                <a href="{{ $publicUrl }}"
                                   style="display:inline-block; background:#0f766e; color:#ffffff; text-decoration:none; padding:12px 18px; border-radius:12px; font-weight:bold; font-size:14px;">
                                    Update Onboarding
                                </a>
                            </div>

                            <p style="font-size:13px; line-height:1.6; margin:24px 0 0; color:#64748b;">
                                If the button does not work, copy and paste this link into your browser:
                            </p>

                            <p style="font-size:13px; line-height:1.6; word-break:break-all; margin:6px 0 0; color:#0f766e;">
                                {{ $publicUrl }}
                            </p>

                            @if ($invite->expires_at)
                                <p style="font-size:13px; line-height:1.6; margin:20px 0 0; color:#64748b;">
                                    This onboarding link expires on {{ $invite->expires_at->format('d M Y') }}.
                                </p>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#f8fafc; padding:18px 28px; color:#64748b; font-size:12px;">
                            This is a sample OnboardingFlow proof-of-concept notification.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>