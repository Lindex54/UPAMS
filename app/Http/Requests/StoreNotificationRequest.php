<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
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
            'notification_template_id' => ['nullable', 'exists:notification_templates,id'],
            'recipient_user_id' => ['nullable', 'exists:users,id', 'required_without_all:target_role,campus_id'],
            'campus_id' => ['nullable', 'exists:campuses,id'],
            'target_role' => ['nullable', 'string', 'max:100'],
            'event_type' => ['required', 'in:Expiry,Arrears,Servicing,Calibration,Inspection,Maintenance,Approval,General'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'channels' => ['required', 'array', 'min:1'],
            'channels.*' => ['in:in_system,email,sms'],
        ];
    }
}
