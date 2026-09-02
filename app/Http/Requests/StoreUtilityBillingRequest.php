<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUtilityBillingRequest extends FormRequest
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
            'utility_meter_id' => ['required', 'exists:utility_meters,id'],
            'billing_period' => ['required', 'date'],
            'previous_reading' => ['required', 'numeric', 'min:0'],
            'current_reading' => ['required', 'numeric', 'gte:previous_reading'],
            'payer_name' => ['required', 'string', 'max:255'],
            'payment_status' => ['required', 'in:Unpaid,Partially Paid,Paid'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
