<?php

namespace App\Http\Requests;

use App\Models\Campus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCampusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $campus = $this->route('campus');

        return $campus instanceof Campus
            ? (bool) $this->user()?->can('update', $campus)
            : (bool) $this->user()?->can('create', Campus::class);
    }

    /**
     * Normalise the campus code before validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('code')) {
            $this->merge(['code' => str($this->input('code'))->trim()->upper()->toString()]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $campus = $this->route('campus');

        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('campuses', 'name')->ignore($campus)],
            'code' => ['required', 'string', 'max:20', 'regex:/^[A-Z0-9-]+$/', Rule::unique('campuses', 'code')->ignore($campus)],
            'district_id' => ['required', 'integer', Rule::exists('districts', 'id')],
            'location' => ['nullable', 'string', 'max:255'],
            'faculty' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
            'status' => ['required', Rule::in(Campus::STATUSES)],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.regex' => 'Use capital letters, numbers, and hyphens only, for example NAG or BU-MAIN.',
        ];
    }
}
