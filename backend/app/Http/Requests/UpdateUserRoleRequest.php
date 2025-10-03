<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateRole', \App\Models\User::class);
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'string', 'in:admin,user'],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'Role is required',
            'role.in' => 'Role must be either admin or user',
        ];
    }
}