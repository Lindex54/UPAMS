<?php

namespace App\Http\Requests\Technician;

use App\Models\ComputerLab;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveComputerLabRequest extends FormRequest
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
        $campusId = $this->user()?->role?->name === 'System Administrator'
            ? $this->integer('campus_id')
            : $this->user()?->campus_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'campus_id' => ['required', 'exists:campuses,id'],
            'org_unit_id' => ['nullable', 'exists:org_units,id'],
            'building' => ['required', 'string', 'max:150'],
            'room' => ['required', 'string', 'max:100', Rule::unique(ComputerLab::class)->where(fn ($query) => $query->where('campus_id', $campusId)->where('building', $this->string('building')->toString()))->ignore($this->route('lab'))],
            'capacity' => ['required', 'integer', 'min:0'],
            'responsible_technician_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
