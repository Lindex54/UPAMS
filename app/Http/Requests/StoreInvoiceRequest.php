<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_active;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'beneficiary_id' => ['required', 'integer', 'exists:beneficiaries,id'],
            'campus_id' => ['required', 'integer', 'exists:campuses,id'],
            'property_reference' => ['nullable', 'string', 'max:255'],
            'asset_reference' => ['nullable', 'string', 'max:255'],
            'agreement_reference' => ['nullable', 'string', 'max:255'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'description' => ['required', 'string', 'max:3000'],
            'subtotal' => ['required', 'numeric', 'min:0.01', 'max:999999999999.99'],
            'tax_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
            'status' => ['required', 'in:Draft,Issued'],
        ];
    }
}
