<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreBeneficiaryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'full_name_organization' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(['Commercial Tenant', 'Staff Tenant', 'Student Beneficiary', 'University Unit', 'External Partner'])],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:30', 'regex:/^\+?[0-9\s().-]{7,30}$/'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'district_id' => ['required', 'integer', Rule::exists('districts', 'id')->where('is_active', true)],
            'county_id' => ['required', 'integer', Rule::exists('counties', 'id')->where(fn ($query) => $query->where('district_id', $this->integer('district_id'))->where('is_active', true))],
            'sub_county_id' => ['required', 'integer', Rule::exists('sub_counties', 'id')->where(fn ($query) => $query->where('county_id', $this->integer('county_id'))->where('is_active', true))],
            'parish_id' => ['required', 'integer', Rule::exists('parishes', 'id')->where(fn ($query) => $query->where('sub_county_id', $this->integer('sub_county_id'))->where('is_active', true))],
            'village_id' => ['required', 'integer', Rule::exists('villages', 'id')->where(fn ($query) => $query->where('parish_id', $this->integer('parish_id'))->where('is_active', true))],
            'physical_address_landmark' => ['nullable', 'string', 'max:500'],
            'current_property_allocation' => ['nullable', 'string', 'max:255'],
            'agreement_reference' => ['nullable', 'string', 'max:100'],
            'billing_cycle' => ['nullable', Rule::in(['Monthly', 'Quarterly', 'Annually', 'Not Applicable'])],
            'opening_balance' => ['nullable', 'numeric', 'min:0', 'max:9999999999999999.99'],
            'campus_id' => ['required', 'integer', Rule::exists('campuses', 'id')],
            'record_status' => ['required', Rule::in(['Active', 'Pending Verification', 'In Arrears', 'Suspended', 'Exited', 'Archived'])],
            'responsible_unit' => ['nullable', 'string', 'max:255'],
            'record_owner' => ['nullable', 'string', 'max:255'],
            'administrative_notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'full_name_organization' => 'full name / organization',
            'sub_county_id' => 'sub-county',
            'physical_address_landmark' => 'physical address / landmark',
            'campus_id' => 'campus',
            'record_status' => 'record status',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => Str::lower(trim($this->string('email')->toString())) ?: null,
            'telephone' => trim($this->string('telephone')->toString()),
        ]);
    }
}
