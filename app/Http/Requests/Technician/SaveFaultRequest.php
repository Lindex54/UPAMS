<?php

namespace App\Http\Requests\Technician;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveFaultRequest extends FormRequest
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
            'asset_id' => ['required', 'exists:assets,id'],
            'title' => ['required', 'string', 'max:255'],
            'fault_description' => ['required', 'string', 'max:5000'],
            'diagnosis' => ['nullable', 'string', 'max:5000'],
            'repair_notes' => ['nullable', 'string', 'max:5000'],
            'priority' => ['required', Rule::in(['Low', 'Medium', 'High', 'Critical'])],
            'status' => ['required', Rule::in(['Reported', 'Diagnosis', 'Awaiting Repair', 'Repair In Progress', 'Testing', 'Resolved'])],
            'assigned_technician_id' => ['nullable', 'exists:users,id'],
            'target_completion_at' => ['nullable', 'date'],
        ];
    }
}
