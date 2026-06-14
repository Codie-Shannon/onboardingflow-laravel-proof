<?php

namespace App\Services;

use App\Models\DocumentRequirement;
use App\Models\OnboardingInvite;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MicrosoftSharePointDocumentService
{
    public function __construct(
        private MicrosoftGraphTokenService $tokenService
    ) {
    }

    public function uploadRequirementDocument(
        OnboardingInvite $invite,
        DocumentRequirement $requirement,
        UploadedFile $file
    ): array {
        $driveId = config('onboarding.sharepoint.drive_id');
        $rootFolder = trim((string) config('onboarding.sharepoint.folder', 'OnboardingFlow'), '/');

        if (! $driveId) {
            throw new RuntimeException('MICROSOFT_SHAREPOINT_DRIVE_ID is not configured.');
        }

        if ($rootFolder === '') {
            throw new RuntimeException('MICROSOFT_SHAREPOINT_FOLDER is not configured.');
        }

        $this->ensureFolderPath($driveId, [$rootFolder]);

        $inviteFolder = $this->safePathSegment(
            'Invite-' . $invite->id . '-' . ($invite->recipient_name ?: 'Applicant')
        );

        $this->ensureFolderPath($driveId, [$rootFolder, $inviteFolder]);

        $extension = $file->getClientOriginalExtension();
        $baseName = $this->safePathSegment($requirement->label ?: 'Document');
        $fileName = $baseName . '-' . now()->format('Ymd-His');

        if ($extension) {
            $fileName .= '.' . Str::lower($extension);
        }

        $pathSegments = [$rootFolder, $inviteFolder, $fileName];
        $encodedPath = $this->encodePath($pathSegments);

        $response = Http::withToken($this->tokenService->getAccessToken())
            ->withBody(
                file_get_contents($file->getRealPath()),
                $file->getMimeType() ?: 'application/octet-stream'
            )
            ->put("https://graph.microsoft.com/v1.0/drives/{$driveId}/root:/{$encodedPath}:/content");

        if (! $response->successful()) {
            throw new RuntimeException('SharePoint upload failed: ' . $response->body());
        }

        return [
            'sharepoint_drive_id' => $driveId,
            'sharepoint_item_id' => $response->json('id'),
            'sharepoint_web_url' => $response->json('webUrl'),
            'uploaded_original_name' => $file->getClientOriginalName(),
            'uploaded_mime_type' => $file->getMimeType(),
            'uploaded_size' => $file->getSize(),
            'uploaded_at' => now(),
            'upload_error' => null,
        ];
    }

    private function ensureFolderPath(string $driveId, array $segments): void
    {
        $currentSegments = [];

        foreach ($segments as $segment) {
            $currentSegments[] = $segment;
            $encodedCurrentPath = $this->encodePath($currentSegments);

            $existingResponse = Http::withToken($this->tokenService->getAccessToken())
                ->acceptJson()
                ->get("https://graph.microsoft.com/v1.0/drives/{$driveId}/root:/{$encodedCurrentPath}");

            if ($existingResponse->successful()) {
                continue;
            }

            if ($existingResponse->status() !== 404) {
                throw new RuntimeException('SharePoint folder lookup failed: ' . $existingResponse->body());
            }

            $parentSegments = array_slice($currentSegments, 0, -1);
            $createEndpoint = empty($parentSegments)
                ? "https://graph.microsoft.com/v1.0/drives/{$driveId}/root/children"
                : "https://graph.microsoft.com/v1.0/drives/{$driveId}/root:/{$this->encodePath($parentSegments)}:/children";

            $createResponse = Http::withToken($this->tokenService->getAccessToken())
                ->acceptJson()
                ->post($createEndpoint, [
                    'name' => $segment,
                    'folder' => new \stdClass(),
                    '@microsoft.graph.conflictBehavior' => 'fail',
                ]);

            if (! $createResponse->successful() && $createResponse->status() !== 409) {
                throw new RuntimeException('SharePoint folder creation failed: ' . $createResponse->body());
            }
        }
    }

    private function encodePath(array $segments): string
    {
        return collect($segments)
            ->map(fn (string $segment) => rawurlencode($segment))
            ->implode('/');
    }

    private function safePathSegment(string $value): string
    {
        $cleaned = Str::slug($value, '-');

        return $cleaned !== '' ? Str::limit($cleaned, 80, '') : 'Document';
    }
}
