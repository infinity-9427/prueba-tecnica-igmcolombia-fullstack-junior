<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Interfaces\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(
        private UserServiceInterface $userService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            // Additional validation to ensure email uniqueness at controller level
            $existingUser = $this->userService->findUserByEmail($request->email);
            if ($existingUser) {
                Log::warning('Registration attempt with existing email', [
                    'email' => $request->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed - email already exists',
                    'error' => 'The email address is already registered in the system',
                    'error_code' => 'EMAIL_DUPLICATE',
                    'details' => [
                        'field' => 'email',
                        'value' => $request->email,
                        'timestamp' => now()->toISOString(),
                        'suggestion' => 'Try logging in instead or use a different email address'
                    ]
                ], 409);
            }

            $user = $this->userService->createUser($request->validated());
            
            $token = $user->createToken('auth_token')->plainTextToken;

            Log::info('User registered successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'created_at' => $user->created_at
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => null
                ]
            ], 201);

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error during registration', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'sql_code' => $e->getCode(),
                'ip' => $request->ip()
            ]);

            if ($e->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed - email already exists',
                    'error' => 'The email address is already registered in the system',
                    'error_code' => 'EMAIL_DUPLICATE',
                    'details' => [
                        'field' => 'email',
                        'value' => $request->email
                    ]
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'Registration failed due to database error',
                'error' => 'A database error occurred while creating your account',
                'error_code' => 'DATABASE_ERROR',
                'details' => [
                    'timestamp' => now()->toISOString(),
                    'contact_support' => true
                ]
            ], 500);

        } catch (\InvalidArgumentException $e) {
            Log::error('Invalid data during registration', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Registration failed - invalid data provided',
                'error' => $e->getMessage(),
                'error_code' => 'INVALID_DATA',
                'details' => [
                    'timestamp' => now()->toISOString()
                ]
            ], 422);

        } catch (\Exception $e) {
            Log::error('Unexpected error during registration', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Registration failed due to an unexpected error',
                'error' => 'An unexpected error occurred while creating your account',
                'error_code' => 'UNEXPECTED_ERROR',
                'details' => [
                    'timestamp' => now()->toISOString(),
                    'contact_support' => true,
                    'support_email' => 'support@example.com'
                ]
            ], 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->findUserByEmail($request->email);

            if (!$user) {
                Log::warning('Login attempt with non-existent email', [
                    'email' => $request->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Authentication failed',
                    'error' => 'Invalid email or password',
                    'error_code' => 'INVALID_CREDENTIALS',
                    'details' => [
                        'field' => 'email',
                        'timestamp' => now()->toISOString()
                    ]
                ], 401);
            }

            if (!Hash::check($request->password, $user->password)) {
                Log::warning('Login attempt with incorrect password', [
                    'user_id' => $user->id,
                    'email' => $request->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Authentication failed',
                    'error' => 'Invalid email or password',
                    'error_code' => 'INVALID_CREDENTIALS',
                    'details' => [
                        'field' => 'password',
                        'timestamp' => now()->toISOString()
                    ]
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            Log::info('User logged in successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'last_login' => now()->toISOString()
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => null
                ]
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error during login', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'sql_code' => $e->getCode(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Login failed due to database error',
                'error' => 'A database error occurred while processing your login',
                'error_code' => 'DATABASE_ERROR',
                'details' => [
                    'timestamp' => now()->toISOString(),
                    'contact_support' => true
                ]
            ], 500);

        } catch (\Exception $e) {
            Log::error('Unexpected error during login', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Login failed due to an unexpected error',
                'error' => 'An unexpected error occurred while processing your login',
                'error_code' => 'UNEXPECTED_ERROR',
                'details' => [
                    'timestamp' => now()->toISOString(),
                    'contact_support' => true,
                    'support_email' => 'support@example.com'
                ]
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                Log::warning('Logout attempt without authenticated user', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Logout failed - user not authenticated',
                    'error' => 'No authenticated user found',
                    'error_code' => 'USER_NOT_AUTHENTICATED',
                    'details' => [
                        'timestamp' => now()->toISOString()
                    ]
                ], 401);
            }

            $token = $user->currentAccessToken();
            if ($token) {
                $token->delete();
            }

            Log::info('User logged out successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Logout successful',
                'data' => [
                    'logged_out_at' => now()->toISOString(),
                    'user_id' => $user->id
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Unexpected error during logout', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Logout failed due to an unexpected error',
                'error' => 'An unexpected error occurred while logging out',
                'error_code' => 'UNEXPECTED_ERROR',
                'details' => [
                    'timestamp' => now()->toISOString(),
                    'contact_support' => true
                ]
            ], 500);
        }
    }

    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                Log::warning('Me endpoint accessed without authenticated user', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'User profile access failed - not authenticated',
                    'error' => 'No authenticated user found',
                    'error_code' => 'USER_NOT_AUTHENTICATED',
                    'details' => [
                        'timestamp' => now()->toISOString()
                    ]
                ], 401);
            }

            Log::info('User profile accessed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User profile retrieved successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'email_verified_at' => $user->email_verified_at,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Unexpected error retrieving user profile', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user profile',
                'error' => 'An unexpected error occurred while retrieving your profile',
                'error_code' => 'UNEXPECTED_ERROR',
                'details' => [
                    'timestamp' => now()->toISOString(),
                    'contact_support' => true
                ]
            ], 500);
        }
    }

    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                Log::warning('Token refresh attempted without authenticated user', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Token refresh failed - user not authenticated',
                    'error' => 'No authenticated user found',
                    'error_code' => 'USER_NOT_AUTHENTICATED',
                    'details' => [
                        'timestamp' => now()->toISOString()
                    ]
                ], 401);
            }

            // Delete all existing tokens for this user
            $deletedCount = $user->tokens()->delete();
            
            // Create new token
            $token = $user->createToken('auth_token')->plainTextToken;

            Log::info('Token refreshed successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'deleted_tokens' => $deletedCount,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => null,
                    'refreshed_at' => now()->toISOString(),
                    'old_tokens_revoked' => $deletedCount
                ]
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error during token refresh', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'sql_code' => $e->getCode(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed due to database error',
                'error' => 'A database error occurred while refreshing your token',
                'error_code' => 'DATABASE_ERROR',
                'details' => [
                    'timestamp' => now()->toISOString(),
                    'contact_support' => true
                ]
            ], 500);

        } catch (\Exception $e) {
            Log::error('Unexpected error during token refresh', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed due to an unexpected error',
                'error' => 'An unexpected error occurred while refreshing your token',
                'error_code' => 'UNEXPECTED_ERROR',
                'details' => [
                    'timestamp' => now()->toISOString(),
                    'contact_support' => true,
                    'support_email' => 'support@example.com'
                ]
            ], 500);
        }
    }
}