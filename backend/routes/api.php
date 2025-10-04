<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('clients', ClientController::class);
    
    Route::apiResource('invoices', InvoiceController::class);
    Route::patch('invoices/{invoice}/status', [InvoiceController::class, 'updateStatus']);
    Route::get('invoices-recent', [InvoiceController::class, 'recent']);
    
    // PDF-related routes
    Route::get('invoices/{invoice}/pdf/download', [InvoiceController::class, 'downloadPdf']);
    Route::post('invoices/{invoice}/pdf/regenerate', [InvoiceController::class, 'regeneratePdf']);
    Route::get('invoices/{invoice}/pdf/info', [InvoiceController::class, 'getPdfInfo']);
    
    Route::apiResource('users', UserController::class);
    Route::patch('users/{user}/role', [UserController::class, 'updateRole']);
    
    Route::prefix('files')->group(function () {
        Route::post('upload/invoice-attachment', [FileUploadController::class, 'uploadInvoiceAttachment']);
        Route::get('download/invoice-attachment/{filename}', [FileUploadController::class, 'downloadInvoiceAttachment']);
        Route::delete('delete/invoice-attachment', [FileUploadController::class, 'deleteInvoiceAttachment']);
    });
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'environment' => app()->environment(),
    ]);
});