<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;

class ArrearsCalculationService
{
    /**
     * Return one row per overdue invoice whose calculated balance remains positive.
     *
     * @param  array<string, mixed>  $filters
     * @return Builder<Invoice>
     */
    public function query(array $filters = []): Builder
    {
        return $this->applyConditions(Invoice::query(), $filters)
            ->with(['beneficiary:id,full_name_organization', 'campus:id,name'])
            ->withSum('validPayments as payments_sum_amount', 'amount')
            ->withMax('validPayments as last_payment_at', 'paid_at')
            ->orderBy('due_date')
            ->orderBy('id');
    }

    /**
     * Calculate the four aging-card totals from the same filtered arrears projection.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, float>
     */
    public function summary(array $filters = []): array
    {
        $buckets = array_fill_keys(Invoice::AGING_BUCKETS, 0.0);

        $this->applyConditions(
            Invoice::query()->select(['invoices.id', 'invoices.due_date', 'invoices.total_amount']),
            $filters,
        )
            ->withSum('validPayments as payments_sum_amount', 'amount')
            ->get()
            ->each(function (Invoice $invoice) use (&$buckets): void {
                $bucket = $invoice->agingBucket();
                $buckets[$bucket] += $invoice->balance();
            });

        return $buckets;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<Invoice>
     */
    private function applyConditions(Builder $query, array $filters): Builder
    {
        return $query
            ->whereDate('invoices.due_date', '<', today())
            ->whereNotIn('invoices.status', ['Cancelled', 'Archived', 'Draft'])
            ->whereRaw(
                'invoices.total_amount > (select coalesce(sum(payments.amount), 0) from payments where payments.invoice_id = invoices.id and payments.status = ?)',
                [Payment::STATUS_RECORDED],
            )
            ->when($filters['campus_id'] ?? null, fn (Builder $query, mixed $campus): Builder => $query->where('campus_id', $campus))
            ->when($filters['beneficiary_id'] ?? null, fn (Builder $query, mixed $beneficiary): Builder => $query->where('beneficiary_id', $beneficiary))
            ->when($filters['property_reference'] ?? null, function (Builder $query, string $reference): void {
                $query->where(function (Builder $references) use ($reference): void {
                    $references->where('property_reference', 'like', "%{$reference}%")
                        ->orWhere('agreement_reference', 'like', "%{$reference}%")
                        ->orWhere('asset_reference', 'like', "%{$reference}%");
                });
            })
            ->when($filters['q'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $nested) use ($search): void {
                    $nested->where('reference', 'like', "%{$search}%")
                        ->orWhereHas('beneficiary', fn (Builder $beneficiary): Builder => $beneficiary->where('full_name_organization', 'like', "%{$search}%"));
                });
            });
    }
}
