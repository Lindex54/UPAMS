<?php

namespace App\Observers;

use App\Models\Approval;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\AuditTrailService;
use Illuminate\Database\Eloquent\Model;

class AuditableObserver
{
    public function __construct(private readonly AuditTrailService $auditTrail) {}

    public function created(Model $model): void
    {
        $this->auditTrail->record($this->action($model, 'created'), class_basename($model).' created', $model, null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $changes = collect($model->getChanges())->except('updated_at')->all();
        if ($changes === []) {
            return;
        }

        $oldValues = collect($changes)->mapWithKeys(fn (mixed $value, string $key): array => [$key => $model->getOriginal($key)])->all();
        $this->auditTrail->record($this->action($model, 'updated'), class_basename($model).' updated', $model, $oldValues, $changes);
    }

    private function action(Model $model, string $event): string
    {
        if ($model instanceof Payment && $model->status === 'Reversed') {
            return 'payment_reversed';
        }

        if ($model instanceof Approval && in_array($model->status, ['Approved', 'Rejected', 'Returned'], true)) {
            return 'approval_'.strtolower($model->status);
        }

        if ($model instanceof Invoice && in_array($model->status, ['Cancelled', 'Archived'], true)) {
            return 'invoice_'.strtolower($model->status);
        }

        return str(class_basename($model))->snake().'_'.$event;
    }
}
