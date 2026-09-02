<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUtilityMeterRequest extends FormRequest
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
            'utility_type_id' => ['required', 'exists:utility_types,id'],
            'campus_id' => ['required', 'exists:campuses,id'],
            'beneficiary_id' => ['nullable', 'exists:beneficiaries,id'],
            'property_reference' => ['nullable', 'string', 'max:255'],
            'meter_number' => ['required', 'string', 'max:100', 'unique:utility_meters,meter_number'],
            'unit' => ['required', 'string', 'max:40'],
            'rate' => ['required', 'numeric', 'min:0'],
            'abnormal_threshold' => ['nullable', 'numeric', 'gt:0'],
            'status' => ['required', 'in:Active,Inactive,Under Maintenance'],
        ];
    }
}
