<?php

namespace App\Http\Controllers\Governance;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\AuditLog;
use App\Models\Beneficiary;
use App\Models\Campus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\UtilityBilling;
use App\Support\CsvExporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $arrears = Invoice::query()->withSum(['payments as payments_sum_amount' => fn (Builder $query) => $query->where('status', 'Recorded')], 'amount')->whereDate('due_date', '<', today())->whereNotIn('status', ['Paid', 'Cancelled', 'Archived', 'Draft'])->get();

        return view('admin.reports.index', ['reports' => $this->reports(), 'metrics' => ['Campuses' => Campus::query()->count(), 'Beneficiaries' => Beneficiary::query()->count(), 'Invoices' => Invoice::query()->count(), 'Payments (UGX)' => Payment::query()->where('status', 'Recorded')->sum('amount'), 'Open arrears (UGX)' => $arrears->sum(fn (Invoice $invoice): float => $invoice->balance()), 'Utility charges (UGX)' => UtilityBilling::query()->sum('charge'), 'Pending approvals' => Approval::query()->where('status', 'Pending')->count(), 'Audit events' => AuditLog::query()->count()]]);
    }

    public function show(Request $request, string $report): View
    {
        abort_unless(array_key_exists($report, $this->reports()), 404);
        [$headings, $rows] = $this->dataset($report, $request);

        return view('admin.reports.show', ['report' => $report, 'title' => $this->reports()[$report], 'headings' => $headings, 'rows' => $rows, 'campuses' => Campus::query()->orderBy('name')->get()]);
    }

    public function export(Request $request, string $report): mixed
    {
        abort_unless(array_key_exists($report, $this->reports()), 404);
        [$headings, $rows] = $this->dataset($report, $request, false);

        return CsvExporter::download("upams-{$report}-".today()->format('Y-m-d').'.csv', $headings, $rows);
    }

    /** @return array<string, string> */
    public function reports(): array
    {
        return ['campuses' => 'Campuses', 'beneficiaries' => 'Tenants / Beneficiaries', 'rent-collection' => 'Rent Collection', 'arrears' => 'Arrears', 'utilities' => 'Utilities', 'approvals' => 'Approvals', 'audit-activity' => 'Audit Activity'];
    }

    /** @return array{0: list<string>, 1: mixed} */
    private function dataset(string $report, Request $request, bool $paginate = true): array
    {
        $campusId = $request->integer('campus_id');
        $datasets = [
            'campuses' => [['Campus', 'Users', 'Beneficiaries'], Campus::query()->withCount(['users'])->orderBy('name')->get()->map(fn (Campus $campus): array => [$campus->name, $campus->users_count, Beneficiary::query()->where('campus_id', $campus->id)->count()])],
            'beneficiaries' => [['Reference', 'Name', 'Category', 'Campus', 'Telephone', 'Status'], Beneficiary::query()->with('campus')->when($campusId, fn (Builder $q, int $id): Builder => $q->where('campus_id', $id))->latest()],
            'rent-collection' => [['Receipt', 'Invoice', 'Beneficiary', 'Paid At', 'Amount', 'Method', 'Status'], Payment::query()->with(['invoice', 'beneficiary'])->when($campusId, fn (Builder $q, int $id): Builder => $q->where('campus_id', $id))->latest('paid_at')],
            'arrears' => [['Invoice', 'Beneficiary', 'Due Date', 'Total', 'Status'], Invoice::query()->with('beneficiary')->whereDate('due_date', '<', today())->whereNotIn('status', ['Paid', 'Cancelled', 'Archived', 'Draft'])->when($campusId, fn (Builder $q, int $id): Builder => $q->where('campus_id', $id))->orderBy('due_date')],
            'utilities' => [['Reference', 'Meter', 'Period', 'Consumption', 'Charge', 'Status', 'Abnormal'], UtilityBilling::query()->with('meter')->when($campusId, fn (Builder $q, int $id): Builder => $q->where('campus_id', $id))->latest('billing_period')],
            'approvals' => [['Reference', 'Type', 'Subject', 'Title', 'Status', 'Decision At'], Approval::query()->when($campusId, fn (Builder $q, int $id): Builder => $q->where('campus_id', $id))->latest()],
            'audit-activity' => [['Created At', 'User', 'Action', 'Description', 'IP Address'], AuditLog::query()->when($campusId, fn (Builder $q, int $id): Builder => $q->where('campus_id', $id))->latest('created_at')],
        ];
        [$headings, $source] = $datasets[$report];
        if ($report === 'campuses') {
            return [$headings, $source];
        }
        $mapper = match ($report) {
            'beneficiaries' => fn (Beneficiary $r): array => [$r->reference, $r->full_name_organization, $r->category, $r->campus?->name, $r->telephone, $r->record_status],
            'rent-collection' => fn (Payment $r): array => [$r->receipt_number, $r->invoice->reference, $r->beneficiary->full_name_organization, $r->paid_at->format('Y-m-d'), $r->amount, $r->payment_method, $r->status],
            'arrears' => fn (Invoice $r): array => [$r->reference, $r->beneficiary->full_name_organization, $r->due_date->format('Y-m-d'), $r->total_amount, $r->status],
            'utilities' => fn (UtilityBilling $r): array => [$r->reference, $r->meter->meter_number, $r->billing_period->format('Y-m'), $r->consumption, $r->charge, $r->payment_status, $r->is_abnormal ? 'Yes' : 'No'],
            'approvals' => fn (Approval $r): array => [$r->reference, $r->approval_type, $r->subject_reference, $r->title, $r->status, $r->decision_at?->format('Y-m-d H:i')],
            default => fn (AuditLog $r): array => [$r->created_at->format('Y-m-d H:i'), $r->user_name, $r->action, $r->description, $r->ip_address],
        };
        if (! $paginate) {
            return [$headings, $source->cursor()->map($mapper)];
        }
        $page = $source->paginate(25)->withQueryString();
        $page->setCollection($page->getCollection()->map($mapper));

        return [$headings, $page];
    }
}
