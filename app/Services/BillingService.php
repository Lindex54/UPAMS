<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BillingService
{
    /** @param array<string, mixed> $data */
    public function saveInvoice(array $data, User $actor, ?Invoice $invoice = null): Invoice
    {
        return DB::transaction(function () use ($data, $actor, $invoice): Invoice {
            $invoice = $invoice?->newQuery()->lockForUpdate()->findOrFail($invoice->getKey()) ?? new Invoice;

            if ($invoice->isClosed()) {
                throw ValidationException::withMessages(['status' => "A {$invoice->status} invoice can no longer be changed."]);
            }

            if ($invoice->hasLockedFinancialTerms()) {
                return $this->correctIssuedInvoice($invoice, $data, $actor);
            }

            $paid = $invoice->exists ? $invoice->validPayments()->sum('amount') : 0;
            $total = round((float) $data['subtotal'] + (float) ($data['tax_amount'] ?? 0), 2);

            if ($total < (float) $paid) {
                throw ValidationException::withMessages(['subtotal' => 'The corrected invoice total cannot be less than payments already recorded.']);
            }

            $invoice->fill($data + ['total_amount' => $total, 'updated_by' => $actor->id]);
            $invoice->created_by ??= $actor->id;
            $invoice->reference ??= 'INV-'.now()->format('Ymd').'-'.str()->upper(str()->random(6));
            $invoice->save();
            $this->syncInvoiceStatus($invoice);

            return $invoice->refresh();
        });
    }

    /**
     * Cancel an unpaid invoice or archive a settled one.
     *
     * @param  'Cancelled'|'Archived'  $status
     */
    public function closeInvoice(Invoice $invoice, string $status, string $reason, User $actor): Invoice
    {
        return DB::transaction(function () use ($invoice, $status, $reason, $actor): Invoice {
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($invoice->getKey());

            if ($invoice->isClosed()) {
                throw ValidationException::withMessages(['status' => "This invoice is already {$invoice->status}."]);
            }

            if ($status === Invoice::STATUS_CANCELLED && ! $invoice->canBeCancelled()) {
                throw ValidationException::withMessages(['status' => 'This invoice has payments recorded and cannot be cancelled. Reverse the payments first if the invoice was raised in error.']);
            }

            if ($status === Invoice::STATUS_ARCHIVED && ! $invoice->canBeArchived()) {
                throw ValidationException::withMessages(['status' => 'Only fully paid invoices can be archived. Open balances must stay visible in arrears.']);
            }

            $invoice->update(['status' => $status, 'cancellation_reason' => $reason, 'cancelled_at' => now(), 'updated_by' => $actor->id]);

            return $invoice->refresh();
        });
    }

    /**
     * Apply descriptive corrections to an issued invoice without touching its financial terms.
     *
     * @param  array<string, mixed>  $data
     */
    private function correctIssuedInvoice(Invoice $invoice, array $data, User $actor): Invoice
    {
        $lockedFields = array_diff(array_keys($data), Invoice::EDITABLE_AFTER_ISSUE);

        if ($lockedFields !== []) {
            throw ValidationException::withMessages(array_fill_keys($lockedFields, 'This field is locked because the invoice has already been issued.'));
        }

        $invoice->fill($data + ['updated_by' => $actor->id])->save();

        return $invoice->refresh();
    }

    /** @param array<string, mixed> $data */
    public function recordPayment(array $data, User $actor): Payment
    {
        return DB::transaction(function () use ($data, $actor): Payment {
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($data['invoice_id']);
            if ($invoice->isDraft()) {
                throw ValidationException::withMessages(['invoice_id' => 'Issue this invoice before recording payments against it.']);
            }

            if ($invoice->isClosed() || (float) $data['amount'] > $invoice->balance()) {
                throw ValidationException::withMessages(['amount' => 'Payment must not exceed the open balance on an active invoice.']);
            }

            $payment = Payment::query()->create($data + [
                'receipt_number' => 'RCT-'.now()->format('YmdHis').'-'.str()->upper(str()->random(4)),
                'beneficiary_id' => $invoice->beneficiary_id,
                'campus_id' => $invoice->campus_id,
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
            ]);
            $this->syncInvoiceStatus($invoice);

            return $payment;
        });
    }

    public function reversePayment(Payment $payment, string $reason, User $actor): Payment
    {
        return DB::transaction(function () use ($payment, $reason, $actor): Payment {
            $payment = Payment::query()->lockForUpdate()->findOrFail($payment->id);
            if ($payment->status === Payment::STATUS_REVERSED) {
                throw ValidationException::withMessages(['reason' => 'This payment has already been reversed.']);
            }

            if ($payment->wasRecordedBy($actor)) {
                throw ValidationException::withMessages(['reason' => 'You recorded this payment, so another Finance Officer or System Administrator must reverse it.']);
            }

            $payment->update(['status' => Payment::STATUS_REVERSED, 'reversal_reason' => $reason, 'reversed_at' => now(), 'reversed_by' => $actor->id, 'updated_by' => $actor->id]);
            $this->syncInvoiceStatus(Invoice::query()->lockForUpdate()->findOrFail($payment->invoice_id));

            return $payment->refresh();
        });
    }

    public function syncInvoiceStatus(Invoice $invoice): void
    {
        if (in_array($invoice->status, ['Draft', 'Cancelled', 'Archived'], true)) {
            return;
        }

        $paid = (float) $invoice->validPayments()->sum('amount');
        $status = $paid >= (float) $invoice->total_amount ? 'Paid' : ($paid > 0 ? 'Partially Paid' : ($invoice->due_date->lt(today()) ? 'Overdue' : 'Issued'));
        if ($invoice->status !== $status) {
            $invoice->updateQuietly(['status' => $status]);
        }
    }
}
