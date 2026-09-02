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
            $paid = $invoice->exists ? $invoice->payments()->where('status', 'Recorded')->sum('amount') : 0;
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

    /** @param array<string, mixed> $data */
    public function recordPayment(array $data, User $actor): Payment
    {
        return DB::transaction(function () use ($data, $actor): Payment {
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($data['invoice_id']);
            if (in_array($invoice->status, ['Cancelled', 'Archived'], true) || (float) $data['amount'] > $invoice->balance()) {
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
            if ($payment->status === 'Reversed') {
                throw ValidationException::withMessages(['reason' => 'This payment has already been reversed.']);
            }

            $payment->update(['status' => 'Reversed', 'reversal_reason' => $reason, 'reversed_at' => now(), 'reversed_by' => $actor->id, 'updated_by' => $actor->id]);
            $this->syncInvoiceStatus(Invoice::query()->lockForUpdate()->findOrFail($payment->invoice_id));

            return $payment->refresh();
        });
    }

    public function syncInvoiceStatus(Invoice $invoice): void
    {
        if (in_array($invoice->status, ['Draft', 'Cancelled', 'Archived'], true)) {
            return;
        }

        $paid = (float) $invoice->payments()->where('status', 'Recorded')->sum('amount');
        $status = $paid >= (float) $invoice->total_amount ? 'Paid' : ($paid > 0 ? 'Partially Paid' : ($invoice->due_date->lt(today()) ? 'Overdue' : 'Issued'));
        if ($invoice->status !== $status) {
            $invoice->updateQuietly(['status' => $status]);
        }
    }
}
