<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreApprovalRequest extends FormRequest
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
            'campus_id' => ['nullable', 'exists:campuses,id'],
            'approval_type' => ['required', 'in:Allocation,Lease,Renewal,Rate Change,Maintenance Expenditure,Asset Transfer,Termination,Disposal'],
            'subject_reference' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'request_details' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
