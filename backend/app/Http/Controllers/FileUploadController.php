<?php

namespace App\Http\Controllers;

use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FileUploadController extends Controller
{
    public function __construct(
        private FileUploadService $fileUploadService
    ) {}

    public function uploadInvoiceAttachment(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,gif,doc,docx', 'max:10240'], // 10MB max
        ]);

        try {
            $file = $request->file('file');
            
            // Validate file
            $validation = $this->fileUploadService->validateFile($file);
            if (!$validation['valid']) {
                return response()->json([
                    'message' => 'File validation failed',
                    'error' => $validation['error'],
                ], 422);
            }

            // Upload to Cloudinary
            $result = $this->fileUploadService->uploadFile($file, 'invoices/attachments');

            if (!$result['success']) {
                return response()->json([
                    'message' => 'File upload failed',
                    'error' => $result['error'],
                ], 500);
            }

            Log::info('Invoice attachment uploaded to Cloudinary', [
                'public_id' => $result['public_id'],
                'original_name' => $result['original_filename'],
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'File uploaded successfully',
                'public_id' => $result['public_id'],
                'secure_url' => $result['secure_url'],
                'original_name' => $result['original_filename'],
                'file_size' => $result['file_size'],
                'file_type' => $result['file_type'],
                'format' => $result['format'],
            ]);

        } catch (\Exception $e) {
            Log::error('File upload failed', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'File upload failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getFileUrl(string $publicId): JsonResponse
    {
        try {
            $url = $this->fileUploadService->getOptimizedUrl($publicId);

            Log::info('File URL retrieved', [
                'public_id' => $publicId,
                'user_id' => auth()->user()?->id,
            ]);

            return response()->json([
                'url' => $url,
                'public_id' => $publicId,
            ]);

        } catch (\Exception $e) {
            Log::error('File URL retrieval failed', [
                'public_id' => $publicId,
                'error' => $e->getMessage(),
                'user_id' => auth()->user()?->id,
            ]);

            return response()->json([
                'message' => 'File URL retrieval failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteInvoiceAttachment(Request $request): JsonResponse
    {
        $request->validate([
            'public_id' => ['required', 'string'],
        ]);

        try {
            $publicId = $request->public_id;
            
            $result = $this->fileUploadService->deleteFile($publicId);

            if (!$result['success']) {
                return response()->json([
                    'message' => 'File deletion failed',
                    'error' => $result['error'],
                ], 500);
            }

            Log::info('Invoice attachment deleted from Cloudinary', [
                'public_id' => $publicId,
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'File deleted successfully',
                'result' => $result['result'],
            ]);

        } catch (\Exception $e) {
            Log::error('File deletion failed', [
                'public_id' => $request->public_id,
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'File deletion failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getOptimizedFileUrl(Request $request): JsonResponse
    {
        $request->validate([
            'public_id' => ['required', 'string'],
            'width' => ['nullable', 'integer', 'min:1', 'max:2000'],
            'height' => ['nullable', 'integer', 'min:1', 'max:2000'],
            'quality' => ['nullable', 'string', 'in:auto,best,good,eco,low'],
        ]);

        try {
            $transformations = [];
            
            if ($request->width || $request->height) {
                $transformations['width'] = $request->width;
                $transformations['height'] = $request->height;
                $transformations['crop'] = 'scale';
            }

            if ($request->quality) {
                $transformations['quality'] = $request->quality;
            }

            $url = $this->fileUploadService->getOptimizedUrl($request->public_id, $transformations);

            return response()->json([
                'optimized_url' => $url,
                'public_id' => $request->public_id,
                'transformations' => $transformations,
            ]);

        } catch (\Exception $e) {
            Log::error('Optimized URL generation failed', [
                'public_id' => $request->public_id,
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'Optimized URL generation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}