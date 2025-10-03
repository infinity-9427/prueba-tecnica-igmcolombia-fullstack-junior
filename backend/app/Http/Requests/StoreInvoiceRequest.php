<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Invoice::class);
    }

    public function rules(): array
    {
        return [
            'invoice_number' => ['nullable', 'string', 'max:255', 'unique:invoices'],
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'description' => ['nullable', 'string'],
            'additional_notes' => ['nullable', 'string'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'status' => ['sometimes', 'string', 'in:pending,paid,overdue'],
            'attachment_path' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['sometimes', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_number.unique' => 'Invoice number already exists',
            'client_id.required' => 'Client is required',
            'client_id.exists' => 'Selected client does not exist',
            'issue_date.required' => 'Issue date is required',
            'due_date.required' => 'Due date is required',
            'due_date.after_or_equal' => 'Due date must be on or after issue date',
            'items.required' => 'At least one item is required',
            'items.*.name.required' => 'Item name is required',
            'items.*.quantity.required' => 'Item quantity is required',
            'items.*.quantity.min' => 'Item quantity must be at least 1',
            'items.*.unit_price.required' => 'Item unit price is required',
            'items.*.unit_price.min' => 'Item unit price must be at least 0',
        ];
    }
}