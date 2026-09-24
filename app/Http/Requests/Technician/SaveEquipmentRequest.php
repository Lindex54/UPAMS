<?php

namespace App\Http\Requests\Technician;

use App\Models\Asset;
use App\Models\AssetType;
use App\Services\IctEquipmentSpecificationService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveEquipmentRequest extends FormRequest
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
    public function rules(IctEquipmentSpecificationService $specifications): array
    {
        $type = $this->filled('asset_type_id') ? AssetType::query()->find($this->integer('asset_type_id')) : null;

        return [
            'serial_number' => ['required', 'string', 'max:150', Rule::unique(Asset::class)->ignore($this->route('asset'))],
            'name' => ['required', 'string', 'max:255'],
            'make' => ['required', 'string', 'max:100'],
            'asset_type_id' => ['nullable', 'exists:asset_types,id'],
            'campus_id' => ['nullable', 'exists:campuses,id'],
            'org_unit_id' => ['nullable', 'exists:org_units,id'],
            'computer_lab_id' => ['nullable', 'exists:computer_labs,id'],
            'model' => ['nullable', 'string', 'max:100'],
            'building' => ['nullable', 'string', 'max:150'],
            'room' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
            'custodian' => ['nullable', 'string', 'max:255'],
            'condition' => ['nullable', Rule::in(['Good', 'Fair', 'Poor', 'Critical'])],
            'operational_status' => ['nullable', Rule::in(['Operational', 'Faulty', 'Under Maintenance', 'Unassigned', 'Retired'])],
            'acquired_at' => ['nullable', 'date'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'warranty_expires_at' => ['nullable', 'date'],
            ...$specifications->validationRules($type),
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if (! $this->filled('asset_type_id') || $validator->errors()->has('asset_type_id')) {
                return;
            }

            $isIctType = AssetType::query()
                ->whereKey($this->integer('asset_type_id'))
                ->whereHas('category', fn ($query) => $query->where('name', 'ICT Equipment'))
                ->exists();

            if (! $isIctType) {
                $validator->errors()->add('asset_type_id', 'The selected equipment type is not an ICT equipment type.');
            }
        }];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'serial_number' => str($this->input('serial_number'))->trim()->upper()->toString(),
            'name' => str($this->input('name'))->trim()->toString(),
            'make' => str($this->input('make'))->trim()->toString(),
        ]);
    }
}
