<?php

namespace App\Http\Requests;

use App\Services\PropertyModuleRegistry;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SavePropertyRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_active === true && in_array($this->user()?->role?->name, [
            'System Administrator',
            'University Property / Estates Manager',
            'Campus Property Officer',
        ], true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(PropertyModuleRegistry $registry): array
    {
        $configuration = $registry->configuration($this->route('module'));
        $rules = [
            'campus_id' => ['required', 'exists:campuses,id'],
            'org_unit_id' => ['nullable', 'exists:org_units,id'],
            'record_status' => ['required', Rule::in(['Active', 'Draft', 'Attention required', 'Inactive', 'Archived'])],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
            'administrative_notes' => ['nullable', 'string', 'max:2000'],
            'intent' => ['nullable', Rule::in(['save', 'draft'])],
        ];

        foreach ($configuration['fields'] as $field) {
            $rules[$field] = [
                in_array($field, $configuration['required'], true) ? 'required' : 'nullable',
                ...$this->fieldRules($field),
            ];
            $rules[$field.'_other'] = ['nullable', 'string', 'max:120', Rule::requiredIf($this->input($field) === '__other')];
        }

        return $rules;
    }

    /** @return list<string> */
    private function fieldRules(string $field): array
    {
        if (in_array($field, ['area_acres', 'number_of_floors', 'gross_floor_area_m2', 'year_completed', 'floor_area_m2'], true)) {
            return ['numeric', 'min:0'];
        }

        if (in_array($field, ['acquisition_date', 'agreement_expiry'], true)) {
            return ['date'];
        }

        return ['string', 'max:255'];
    }
}
