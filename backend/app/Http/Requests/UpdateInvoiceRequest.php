<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $invoice = $this->route('invoice');
        return $this->user()->can('update', $invoice);
    }

    public function rules(): array
    {
        $invoiceId = $this->route('invoice');
        
        return [
            'invoice_number' => [
                'sometimes', 
                'required', 
                'string', 
                'max:255', 
                Rule::unique('invoices')->ignore($invoiceId)
            ],
            'client_id' => ['sometimes', 'required', 'integer', 'exists:clients,id'],
            'description' => ['sometimes', 'nullable', 'string'],
            'additional_notes' => ['sometimes', 'nullable', 'string'],
            'issue_date' => ['sometimes', 'required', 'date'],
            'due_date' => ['sometimes', 'required', 'date', 'after_or_equal:issue_date'],
            'status' => ['sometimes', 'string', 'in:pending,paid,overdue'],
            'attachment_path' => ['sometimes', 'nullable', 'string'],
            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.name' => ['required_with:items', 'string', 'max:255'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0'],
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
            'items.min' => 'At least one item is required',
            'items.*.name.required_with' => 'Item name is required',
            'items.*.quantity.required_with' => 'Item quantity is required',
            'items.*.quantity.min' => 'Item quantity must be at least 1',
            'items.*.unit_price.required_with' => 'Item unit price is required',
            'items.*.unit_price.min' => 'Item unit price must be at least 0',
        ];
    }
}