<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\Campus;
use App\Models\Invoice;
use App\Services\ArrearsCalculationService;
use App\Support\CsvExporter;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArrearsController extends Controller
{
    public function __construct(private readonly ArrearsCalculationService $arrears) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'campus_id', 'beneficiary_id', 'property_reference']);
        $all = $this->arrears->query($filters)->get();
        $buckets = collect(['Under 30 days', '30-60 days', '60-90 days', 'Over 90 days'])->mapWithKeys(fn (string $bucket): array => [$bucket => $all->filter(fn (Invoice $invoice): bool => $invoice->agingBucket() === $bucket)->sum(fn (Invoice $invoice): float => $invoice->balance())]);

        return view('admin.arrears.index', ['invoices' => $this->arrears->query($filters)->paginate(15)->withQueryString(), 'buckets' => $buckets, 'campuses' => Campus::query()->orderBy('name')->get(), 'beneficiaries' => Beneficiary::query()->orderBy('full_name_organization')->get()]);
    }

    public function statement(Invoice $invoice): View
    {
        abort_unless($invoice->due_date->isPast() && $invoice->balance() > 0, 404);

        return view('admin.arrears.statement', ['invoice' => $invoice->load(['beneficiary', 'campus', 'payments.creator'])]);
    }

    public function export(Request $request): mixed
    {
        $rows = $this->arrears->query($request->only(['q', 'campus_id', 'beneficiary_id', 'property_reference']))->cursor()->map(fn (Invoice $invoice): array => [$invoice->reference, $invoice->beneficiary->full_name_organization, $invoice->campus->name, $invoice->property_reference, $invoice->due_date->toDateString(), $invoice->total_amount, $invoice->paidAmount(), $invoice->balance(), $invoice->agingBucket()]);

        return CsvExporter::download('upams-arrears-'.today()->format('Y-m-d').'.csv', ['Invoice', 'Beneficiary', 'Campus', 'Property', 'Due Date', 'Total', 'Paid', 'Balance', 'Aging'], $rows);
    }
}
