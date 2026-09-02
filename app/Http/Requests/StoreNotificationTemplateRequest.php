<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationTemplateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:150', 'unique:notification_templates,name'],
            'event_type' => ['required', 'in:Expiry,Arrears,Servicing,Calibration,Inspection,Maintenance,Approval,General'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'channels' => ['required', 'array', 'min:1'],
            'channels.*' => ['in:in_system,email,sms'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
