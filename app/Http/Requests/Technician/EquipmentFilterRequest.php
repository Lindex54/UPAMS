<?php

namespace App\Http\Requests\Technician;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EquipmentFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array($this->user()?->role?->name, ['IT Technician', 'System Administrator'], true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:150'],
            'asset_id' => ['nullable', 'string', 'max:100'],
            'serial_number' => ['nullable', 'string', 'max:150'],
            'campus_id' => ['nullable', 'integer', 'exists:campuses,id'],
            'computer_lab_id' => ['nullable', 'integer', 'exists:computer_labs,id'],
            'asset_type_id' => ['nullable', 'integer', 'exists:asset_types,id'],
            'condition' => ['nullable', Rule::in(['Good', 'Fair', 'Poor', 'Critical'])],
            'status' => ['nullable', Rule::in(['Operational', 'Faulty', 'Under Maintenance', 'Unassigned', 'Retired'])],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ];
    }
}
