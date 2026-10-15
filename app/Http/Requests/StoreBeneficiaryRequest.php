<?php

namespace App\Http\Requests;

use App\Models\Beneficiary;
use App\Models\BeneficiaryDocument;
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
        $beneficiary = $this->route('beneficiary');
        $documentFile = ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'];

        return [
            'full_name_organization' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(['Commercial Tenant', 'Staff Tenant', 'Student Beneficiary', 'University Unit', 'External Partner'])],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:30', 'regex:/^\+?[0-9\s().-]{7,30}$/'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'nin' => ['nullable', 'string', 'min:8', 'max:30', 'regex:/^[A-Z0-9]+$/'],
            'nin_hash' => [
                'nullable',
                'string',
                'size:64',
                Rule::unique('beneficiaries', 'nin_hash')->ignore($beneficiary instanceof Beneficiary ? $beneficiary->getKey() : null),
            ],
            'national_id_given_names' => ['required', 'string', 'max:255'],
            'national_id_surname' => ['required', 'string', 'max:255'],
            'national_id_sex' => ['nullable', Rule::in(['Male', 'Female'])],
            'nationality' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'tin' => ['nullable', 'string', 'max:30'],
            'tax_returns' => ['nullable', 'string', 'max:255'],
            'director_names' => ['nullable', 'string', 'max:2000'],
            'owner_names' => ['nullable', 'string', 'max:2000'],
            'local_council_permit' => ['nullable', 'string', 'max:255'],
            'documents' => ['nullable', 'array:'.implode(',', array_keys(BeneficiaryDocument::STANDARD_TYPES))],
            ...collect(BeneficiaryDocument::STANDARD_TYPES)->keys()->mapWithKeys(fn (string $type): array => ["documents.{$type}" => $documentFile])->all(),
            'other_documents' => ['nullable', 'array', 'max:20'],
            'other_documents.*.name' => ['nullable', 'string', 'max:150', 'required_with:other_documents.*.file'],
            'other_documents.*.file' => [...$documentFile, 'required_with:other_documents.*.name'],
            'remove_documents' => ['nullable', 'array'],
            'remove_documents.*' => ['integer'],
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
    public function messages(): array
    {
        return [
            'other_documents.*.name.required_with' => 'Give this document a name so it can be identified later.',
            'other_documents.*.file.required_with' => 'Choose the file for this named document, or remove the row.',
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
            'nin' => 'national identification number (NIN)',
            'nin_hash' => 'national identification number (NIN)',
            'national_id_given_names' => 'first name',
            'national_id_surname' => 'last name',
            'photo' => "person's photo",
            'tin' => 'TIN',
            'local_council_permit' => 'local council permit',
            'other_documents.*.name' => 'document name',
            'other_documents.*.file' => 'document file',
            ...collect(BeneficiaryDocument::STANDARD_TYPES)->mapWithKeys(fn (string $label, string $type): array => ["documents.{$type}" => $label])->all(),
        ];
    }

    protected function prepareForValidation(): void
    {
        $nin = Str::upper(preg_replace('/[\s-]+/', '', $this->string('nin')->toString()) ?? '');

        $this->merge([
            'email' => Str::lower(trim($this->string('email')->toString())) ?: null,
            'telephone' => trim($this->string('telephone')->toString()),
            'nin' => $nin ?: null,
            'nin_hash' => $nin ? hash('sha256', $nin) : null,
        ]);
    }
}
