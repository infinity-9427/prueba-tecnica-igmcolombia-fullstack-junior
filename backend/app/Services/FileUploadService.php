<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class FileUploadService
{
    private Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key' => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
        ]);
    }

    /**
     * Upload a file to Cloudinary
     */
    public function uploadFile(UploadedFile $file, string $folder = 'invoices'): array
    {
        try {
            $result = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
                'folder' => $folder,
                'resource_type' => 'auto',
                'use_filename' => true,
                'unique_filename' => true,
                'overwrite' => false,
                'quality' => 'auto',
                'fetch_format' => 'auto',
            ]);

            Log::info('File uploaded to Cloudinary', [
                'public_id' => $result['public_id'],
                'secure_url' => $result['secure_url'],
                'original_filename' => $file->getClientOriginalName()
            ]);

            return [
                'success' => true,
                'public_id' => $result['public_id'],
                'secure_url' => $result['secure_url'],
                'original_filename' => $file->getClientOriginalName(),
                'file_size' => $result['bytes'],
                'file_type' => $result['resource_type'],
                'format' => $result['format']
            ];

        } catch (\Exception $e) {
            Log::error('File upload failed', [
                'error' => $e->getMessage(),
                'filename' => $file->getClientOriginalName()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete a file from Cloudinary
     */
    public function deleteFile(string $publicId): array
    {
        try {
            $result = $this->cloudinary->uploadApi()->destroy($publicId);

            Log::info('File deleted from Cloudinary', [
                'public_id' => $publicId,
                'result' => $result['result']
            ]);

            return [
                'success' => true,
                'result' => $result['result']
            ];

        } catch (\Exception $e) {
            Log::error('File deletion failed', [
                'error' => $e->getMessage(),
                'public_id' => $publicId
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get optimized URL for file display
     */
    public function getOptimizedUrl(string $publicId, array $transformations = []): string
    {
        $defaultTransformations = [
            'quality' => 'auto',
            'fetch_format' => 'auto'
        ];

        $transformations = array_merge($defaultTransformations, $transformations);

        return $this->cloudinary->image($publicId)->resize($transformations)->toUrl();
    }

    /**
     * Validate file type and size
     */
    public function validateFile(UploadedFile $file): array
    {
        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx'];
        $maxSize = 10 * 1024 * 1024; // 10MB

        $extension = strtolower($file->getClientOriginalExtension());
        $size = $file->getSize();

        if (!in_array($extension, $allowedTypes)) {
            return [
                'valid' => false,
                'error' => 'Invalid file type. Allowed types: ' . implode(', ', $allowedTypes)
            ];
        }

        if ($size > $maxSize) {
            return [
                'valid' => false,
                'error' => 'File size exceeds 10MB limit'
            ];
        }

        return ['valid' => true];
    }
}