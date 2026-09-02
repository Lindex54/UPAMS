<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Beneficiary;
use App\Models\Campus;
use App\Models\Invoice;
use App\Services\BillingService;
use App\Support\CsvExporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(private readonly BillingService $billing) {}

    public function index(Request $request): View
    {
        $query = $this->filteredQuery($request);
        $openInvoices = Invoice::query()->withSum(['payments as payments_sum_amount' => fn (Builder $payments) => $payments->where('status', 'Recorded')], 'amount')->whereNotIn('status', ['Paid', 'Cancelled', 'Archived', 'Draft'])->get();

        return view('admin.invoices.index', [
            'invoices' => $query->paginate(15)->withQueryString(),
            'campuses' => Campus::query()->orderBy('name')->get(),
            'summary' => ['count' => Invoice::query()->count(), 'outstanding' => $openInvoices->sum(fn (Invoice $invoice): float => $invoice->balance()), 'overdue' => $openInvoices->filter(fn (Invoice $invoice): bool => $invoice->due_date->lt(today()) && $invoice->balance() > 0)->count()],
        ]);
    }

    public function create(): View
    {
        return $this->formView(new Invoice);
    }

    public function edit(Invoice $invoice): View
    {
        return $this->formView($invoice);
    }

    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $invoice = $this->billing->saveInvoice($request->validated(), $request->user());

        return redirect()->route('billing.show', $invoice)->with('success', 'Invoice created successfully.');
    }

    public function update(StoreInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $invoice = $this->billing->saveInvoice($request->validated(), $request->user(), $invoice);

        return redirect()->route('billing.show', $invoice)->with('success', 'Invoice corrected successfully.');
    }

    public function show(Invoice $invoice): View
    {
        return view('admin.invoices.show', ['invoice' => $invoice->load(['beneficiary', 'campus', 'payments.creator', 'creator', 'updater'])]);
    }

    public function archive(Request $request, Invoice $invoice): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:Archived,Cancelled'], 'reason' => ['required', 'string', 'min:10', 'max:2000']]);
        $invoice->update(['status' => $data['status'], 'cancellation_reason' => $data['reason'], 'cancelled_at' => now(), 'updated_by' => $request->user()->id]);

        return back()->with('success', "Invoice {$data['status']} successfully.");
    }

    public function export(Request $request): mixed
    {
        $rows = $this->filteredQuery($request)->cursor()->map(fn (Invoice $invoice): array => [$invoice->reference, $invoice->beneficiary->full_name_organization, $invoice->campus->name, $invoice->issue_date->toDateString(), $invoice->due_date->toDateString(), $invoice->total_amount, $invoice->paidAmount(), $invoice->balance(), $invoice->paymentStatus()]);

        return CsvExporter::download('upams-invoices-'.today()->format('Y-m-d').'.csv', ['Reference', 'Beneficiary', 'Campus', 'Issue Date', 'Due Date', 'Total', 'Paid', 'Balance', 'Status'], $rows);
    }

    private function formView(Invoice $invoice): View
    {
        return view('admin.invoices.form', ['invoice' => $invoice, 'beneficiaries' => Beneficiary::query()->orderBy('full_name_organization')->get(), 'campuses' => Campus::query()->orderBy('name')->get()]);
    }

    private function filteredQuery(Request $request): Builder
    {
        return Invoice::query()->with(['beneficiary', 'campus', 'creator', 'updater'])->withSum(['payments as payments_sum_amount' => fn (Builder $query) => $query->where('status', 'Recorded')], 'amount')
            ->when($request->string('q')->toString(), fn (Builder $query, string $q): Builder => $query->where(fn (Builder $nested): Builder => $nested->where('reference', 'like', "%{$q}%")->orWhereHas('beneficiary', fn (Builder $beneficiary): Builder => $beneficiary->where('full_name_organization', 'like', "%{$q}%"))))
            ->when($request->integer('campus_id'), fn (Builder $query, int $id): Builder => $query->where('campus_id', $id))
            ->when($request->string('status')->toString(), fn (Builder $query, string $status): Builder => $query->where('status', $status))->latest();
    }
}
