<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArrearsFilterRequest;
use App\Models\Beneficiary;
use App\Models\Campus;
use App\Models\Invoice;
use App\Services\ArrearsCalculationService;
use App\Support\CsvExporter;
use Illuminate\View\View;

class ArrearsController extends Controller
{
    public function __construct(private readonly ArrearsCalculationService $arrears) {}

    public function index(ArrearsFilterRequest $request): View
    {
        $filters = $request->validated();

        return view('admin.arrears.index', [
            'invoices' => $this->arrears->query($filters)->paginate(15)->withQueryString(),
            'buckets' => $this->arrears->summary($filters),
            'campuses' => Campus::query()->orderBy('name')->get(),
            'beneficiaries' => Beneficiary::query()->orderBy('full_name_organization')->get(),
        ]);
    }

    public function statement(Invoice $invoice): View
    {
        $invoice = $this->arrears->query()->whereKey($invoice->getKey())->firstOrFail();

        return view('admin.arrears.statement', ['invoice' => $invoice->load('payments.creator')]);
    }

    public function export(ArrearsFilterRequest $request): mixed
    {
        $rows = $this->arrears->query($request->validated())->lazy()->map(fn (Invoice $invoice): array => [
            $invoice->reference,
            $invoice->beneficiary->full_name_organization,
            $invoice->property_reference,
            $invoice->agreement_reference,
            $invoice->campus->name,
            $invoice->due_date->toDateString(),
            $invoice->daysOverdue(),
            $invoice->agingBucket(),
            $invoice->balance(),
            $invoice->last_payment_at?->toDateString(),
        ]);

        return CsvExporter::download('upams-arrears-'.today()->format('Y-m-d').'.csv', [
            'Invoice Number',
            'Beneficiary',
            'Property',
            'Agreement',
            'Campus',
            'Original Due Date',
            'Days Overdue',
            'Aging Category',
            'Outstanding Balance',
            'Last Payment Date',
        ], $rows);
    }
}
