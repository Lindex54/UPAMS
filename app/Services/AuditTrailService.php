<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditTrailService
{
    /** @param array<string, mixed>|null $oldValues @param array<string, mixed>|null $newValues */
    public function record(string $action, string $description, ?Model $record = null, ?array $oldValues = null, ?array $newValues = null): AuditLog
    {
        $user = Auth::user();

        return AuditLog::query()->create([
            'user_id' => $user?->getAuthIdentifier(),
            'user_name' => $user?->name,
            'user_role' => $user?->role?->name,
            'campus_id' => $record?->getAttribute('campus_id') ?? $user?->campus_id,
            'action' => $action,
            'auditable_type' => $record?->getMorphClass(),
            'auditable_id' => $record?->getKey(),
            'description' => $description,
            'old_values' => $this->sanitize($oldValues),
            'new_values' => $this->sanitize($newValues),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    /** @param array<string, mixed>|null $values @return array<string, mixed>|null */
    private function sanitize(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        foreach (['password', 'remember_token', 'nin', 'nin_hash', 'photo_path'] as $sensitiveAttribute) {
            unset($values[$sensitiveAttribute]);
        }

        return $values;
    }
}
