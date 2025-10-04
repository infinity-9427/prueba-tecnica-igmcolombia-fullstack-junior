<?php

namespace App\Http\Requests;

class StoreClientRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Client::class);
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'string', 'in:cedula,pasaporte,nit'],
            'document_number' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'document_type.required' => 'Document type is required',
            'document_type.in' => 'Document type must be cedula, pasaporte, or nit',
            'document_number.required' => 'Document number is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
        ];
    }
}