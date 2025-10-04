<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

abstract class BaseApiRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->toArray();
        
        Log::warning('API validation failed', [
            'errors' => $errors,
            'url' => $this->fullUrl(),
            'method' => $this->method(),
            'ip' => $this->ip(),
            'user_id' => $this->user()?->id,
            'user_agent' => $this->userAgent(),
        ]);

        $response = response()->json([
            'success' => false,
            'message' => 'The given data was invalid.',
            'error' => 'Validation failed - please check your input data',
            'error_code' => 'VALIDATION_ERROR',
            'errors' => $errors,
            'details' => [
                'timestamp' => now()->toISOString(),
                'request_id' => uniqid('req_'),
                'url' => $this->fullUrl(),
                'method' => $this->method(),
            ]
        ], 422);

        throw new HttpResponseException($response);
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        Log::warning('API authorization failed', [
            'url' => $this->fullUrl(),
            'method' => $this->method(),
            'ip' => $this->ip(),
            'user_id' => $this->user()?->id,
            'user_agent' => $this->userAgent(),
        ]);

        $response = response()->json([
            'success' => false,
            'message' => 'Access denied.',
            'error' => 'You do not have permission to perform this action.',
            'error_code' => 'INSUFFICIENT_PERMISSIONS',
            'details' => [
                'timestamp' => now()->toISOString(),
                'request_id' => uniqid('req_'),
                'user_role' => $this->user()?->role,
            ]
        ], 403);

        throw new HttpResponseException($response);
    }
}