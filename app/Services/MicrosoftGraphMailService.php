<?php

namespace App\Services;

use App\Models\OnboardingInvite;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MicrosoftGraphMailService
{
    public function __construct(
        private MicrosoftGraphTokenService $tokenService
    ) {
    }

    public function sendInviteEmail(OnboardingInvite $invite, string $htmlBody): void
    {
        $this->sendMail(
            invite: $invite,
            subject: 'Onboarding invitation',
            htmlBody: $htmlBody,
            failurePrefix: 'Microsoft Graph sendMail failed'
        );
    }

    public function sendNeedsInfoEmail(OnboardingInvite $invite, string $htmlBody): void
    {
        $this->sendMail(
            invite: $invite,
            subject: 'Onboarding information needed',
            htmlBody: $htmlBody,
            failurePrefix: 'Microsoft Graph needs-info email failed'
        );
    }

    private function sendMail(
        OnboardingInvite $invite,
        string $subject,
        string $htmlBody,
        string $failurePrefix
    ): void {
        $mailFrom = config('onboarding.microsoft.mail_from');

        if (! $mailFrom) {
            throw new RuntimeException('MICROSOFT_MAIL_FROM is not configured.');
        }

        $accessToken = $this->tokenService->getAccessToken();

        $endpoint = 'https://graph.microsoft.com/v1.0/users/'
            . rawurlencode($mailFrom)
            . '/sendMail';

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->post($endpoint, [
                'message' => [
                    'subject' => $subject,
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => $htmlBody,
                    ],
                    'toRecipients' => [
                        [
                            'emailAddress' => [
                                'address' => $invite->recipient_email,
                                'name' => $invite->recipient_name,
                            ],
                        ],
                    ],
                ],
                'saveToSentItems' => true,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException(
                $failurePrefix . ': ' . $response->body()
            );
        }
    }
}