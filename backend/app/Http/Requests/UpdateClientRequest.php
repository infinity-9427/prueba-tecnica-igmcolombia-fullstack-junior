<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        $client = $this->route('client');
        return $this->user()->can('update', $client);
    }

    public function rules(): array
    {
        $clientId = $this->route('client');
        
        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'document_type' => ['sometimes', 'required', 'string', 'in:cedula,pasaporte,nit'],
            'document_number' => [
                'sometimes', 
                'required', 
                'string', 
                'max:255', 
                Rule::unique('clients')->ignore($clientId)
            ],
            'email' => [
                'sometimes', 
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('clients')->ignore($clientId)
            ],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
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
            'document_number.unique' => 'Document number already exists',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email already exists',
        ];
    }
}