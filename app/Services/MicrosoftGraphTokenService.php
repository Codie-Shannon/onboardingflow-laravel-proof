<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MicrosoftGraphTokenService
{
    public function getAccessToken(): string
    {
        return Cache::remember('microsoft_graph_access_token', now()->addMinutes(50), function () {
            $tenantId = config('onboarding.microsoft.tenant_id');
            $clientId = config('onboarding.microsoft.client_id');
            $clientSecret = config('onboarding.microsoft.client_secret');

            if (! $tenantId || ! $clientId || ! $clientSecret) {
                throw new RuntimeException('Microsoft Graph credentials are missing from environment configuration.');
            }

            $response = Http::asForm()->post(
                "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token",
                [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'scope' => 'https://graph.microsoft.com/.default',
                    'grant_type' => 'client_credentials',
                ]
            );

            if (! $response->successful()) {
                throw new RuntimeException(
                    'Microsoft Graph token request failed: ' . $response->body()
                );
            }

            $token = $response->json('access_token');

            if (! $token) {
                throw new RuntimeException('Microsoft Graph token response did not include an access token.');
            }

            return $token;
        });
    }

    public function forgetAccessToken(): void
    {
        Cache::forget('microsoft_graph_access_token');
    }
}