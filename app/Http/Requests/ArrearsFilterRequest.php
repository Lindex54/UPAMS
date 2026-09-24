<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ArrearsFilterRequest extends FormRequest
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
            'q' => ['nullable', 'string', 'max:255'],
            'campus_id' => ['nullable', 'integer', 'exists:campuses,id'],
            'beneficiary_id' => ['nullable', 'integer', 'exists:beneficiaries,id'],
            'property_reference' => ['nullable', 'string', 'max:255'],
        ];
    }
}
