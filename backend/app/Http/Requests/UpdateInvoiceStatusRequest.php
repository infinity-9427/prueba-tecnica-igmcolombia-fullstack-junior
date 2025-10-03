<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $invoice = $this->route('invoice');
        return $this->user()->can('updateStatus', $invoice);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:pending,paid,overdue'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status is required',
            'status.in' => 'Status must be pending, paid, or overdue',
        ];
    }
}