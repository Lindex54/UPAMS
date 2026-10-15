<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $invoice = $this->route('invoice');

        return $invoice instanceof Invoice
            ? (bool) $this->user()?->can('update', $invoice)
            : (bool) $this->user()?->can('create', Invoice::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
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

        $invoice = $this->route('invoice');

        if (! $invoice instanceof Invoice || ! $invoice->hasLockedFinancialTerms()) {
            return $rules;
        }

        // Once issued, the financial terms are frozen; only descriptive fields may be corrected.
        foreach (array_keys($rules) as $field) {
            if (! in_array($field, Invoice::EDITABLE_AFTER_ISSUE, true)) {
                $rules[$field] = ['prohibited'];
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            '*.prohibited' => 'This field is locked because the invoice has already been issued.',
        ];
    }
}
