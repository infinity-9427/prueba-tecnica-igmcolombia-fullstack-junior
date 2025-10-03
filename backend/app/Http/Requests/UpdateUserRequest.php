<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->route('user');
        return $this->user()->can('update', $user);
    }

    public function rules(): array
    {
        $userId = $this->route('user');
        
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes', 
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users')->ignore($userId)
            ],
            'password' => ['sometimes', 'nullable', 'string', Password::defaults(), 'confirmed'],
            'role' => ['sometimes', 'required', 'string', 'in:admin,user'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email is already taken',
            'password.confirmed' => 'Password confirmation does not match',
            'role.required' => 'Role is required',
            'role.in' => 'Role must be either admin or user',
        ];
    }
}