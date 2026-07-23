<?php

namespace App\Services;

use Aws\S3\S3Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CdnUploadService
{
    /**
     * The CDN disk name configured in filesystems.php.
     */
    private const DISK = 'cdn';

    /**
     * Allowed directory names for uploads.
     */
    private const ALLOWED_DIRECTORIES = ['hero', 'about', 'portfolio', 'testimonials'];

    /**
     * Generate a unique path and a presigned S3 PUT URL for direct browser upload.
     *
     * @param  string  $directory  Subdirectory within the bucket, e.g. 'hero'
     * @return array{url: string, path: string}
     *
     * @throws \InvalidArgumentException
     */
    public function generatePresignedUrl(string $directory): array
    {
        $directory = trim($directory, '/');

        if (! preg_match('/^[a-zA-Z0-9_-]+$/', $directory)) {
            throw new \InvalidArgumentException("Invalid directory: '$directory'. Only letters, numbers, hyphens, and underscores allowed.");
        }

        $path = sprintf(
            '/assets/%s/%s_%s.webp',
            $directory,
            now()->timestamp,
            Str::random(10),
        );

        /** @var S3Client $client */
        $client = Storage::disk(self::DISK)->getClient();

        $command = $client->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks.cdn.bucket'),
            'Key' => ltrim($path, '/'),
            'ContentType' => 'image/webp',
        ]);

        $presignedRequest = $client->createPresignedRequest($command, '+5 minutes');
        $presignedUrl = (string) $presignedRequest->getUri();

        return [
            'url' => $presignedUrl,
            'path' => $path,
        ];
    }

    /**
     * Delete a file from the CDN.
     *
     * Null-safe. Only deletes paths under "/assets/" to prevent path traversal.
     * Skips paths prefixed with "asset/" (legacy static assets without trailing 's').
     */
    public function delete(?string $path): void
    {
        $clean = ltrim((string) $path, '/');

        if ($clean === '' || str_starts_with($clean, 'asset/')) {
            return;
        }

        // Only allow deletion of paths under the assets/ directory
        if (! str_starts_with($clean, 'assets/')) {
            return;
        }

        // Guard against path traversal (e.g. "../../etc/passwd")
        if (str_contains($clean, '..')) {
            return;
        }

        Storage::disk(self::DISK)->delete($clean);
    }

    /**
     * Return the full CDN URL for a stored path.
     *
     * Falls back to asset() for legacy "asset/" prefixed paths.
     */
    public function url(?string $path): string
    {
        if ($path === null || $path === '') {
            return asset('asset/img/preview-images-kosong.png');
        }

        if (str_starts_with($path, 'asset/') || str_starts_with($path, '/asset/')) {
            return asset($path);
        }

        return config('filesystems.disks.cdn.url') . '/' . ltrim($path, '/');
    }

    /**
     * Delete an old file, then generate a presigned URL for a new one.
     *
     * Convenience wrapper for replace-image flows.
     *
     * @return array{url: string, path: string}
     */
    public function deleteOldAndGeneratePresignedUrl(string $directory, ?string $oldPath): array
    {
        $this->delete($oldPath);

        return $this->generatePresignedUrl($directory);
    }
}
