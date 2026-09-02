<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;

class ArrearsCalculationService
{
    /** @param array<string, mixed> $filters */
    public function query(array $filters = []): Builder
    {
        return Invoice::query()
            ->with(['beneficiary', 'campus', 'creator', 'updater'])
            ->withSum(['payments as payments_sum_amount' => fn (Builder $query) => $query->where('status', 'Recorded')], 'amount')
            ->whereDate('due_date', '<', today())
            ->whereNotIn('status', ['Cancelled', 'Archived', 'Paid', 'Draft'])
            ->when($filters['campus_id'] ?? null, fn (Builder $query, mixed $campus): Builder => $query->where('campus_id', $campus))
            ->when($filters['beneficiary_id'] ?? null, fn (Builder $query, mixed $beneficiary): Builder => $query->where('beneficiary_id', $beneficiary))
            ->when($filters['property_reference'] ?? null, fn (Builder $query, string $property): Builder => $query->where('property_reference', 'like', "%{$property}%"))
            ->when($filters['q'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $nested) use ($search): void {
                    $nested->where('reference', 'like', "%{$search}%")
                        ->orWhereHas('beneficiary', fn (Builder $beneficiary): Builder => $beneficiary->where('full_name_organization', 'like', "%{$search}%"));
                });
            })
            ->orderBy('due_date');
    }
}
