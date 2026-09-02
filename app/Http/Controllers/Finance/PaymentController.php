<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReversePaymentRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\BillingService;
use App\Support\CsvExporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private readonly BillingService $billing) {}

    public function index(Request $request): View
    {
        $payments = Payment::query()->with(['invoice', 'beneficiary', 'campus', 'creator', 'updater'])
            ->when($request->string('q')->toString(), fn (Builder $query, string $q): Builder => $query->where(fn (Builder $nested): Builder => $nested->where('receipt_number', 'like', "%{$q}%")->orWhere('payment_reference', 'like', "%{$q}%")))
            ->when($request->string('status')->toString(), fn (Builder $query, string $status): Builder => $query->where('status', $status))->latest('paid_at');

        return view('admin.payments.index', ['payments' => $payments->paginate(15)->withQueryString(), 'recordedTotal' => Payment::query()->where('status', 'Recorded')->sum('amount')]);
    }

    public function create(Request $request): View
    {
        return view('admin.payments.form', ['invoices' => Invoice::query()->with('beneficiary')->whereNotIn('status', ['Paid', 'Cancelled', 'Archived'])->orderByDesc('issue_date')->get(), 'selectedInvoice' => $request->integer('invoice_id')]);
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $payment = $this->billing->recordPayment($request->validated(), $request->user());

        return redirect()->route('payments.show', $payment)->with('success', 'Payment recorded and receipt generated.');
    }

    public function show(Payment $payment): View
    {
        return view('admin.payments.show', ['payment' => $payment->load(['invoice', 'beneficiary', 'campus', 'creator', 'updater', 'reverser'])]);
    }

    public function reverse(ReversePaymentRequest $request, Payment $payment): RedirectResponse
    {
        $this->billing->reversePayment($payment, $request->validated('reason'), $request->user());

        return back()->with('success', 'Payment reversed. The original record remains in the audit history.');
    }

    public function export(): mixed
    {
        $rows = Payment::query()->with(['invoice', 'beneficiary', 'campus'])->latest('paid_at')->cursor()->map(fn (Payment $payment): array => [$payment->receipt_number, $payment->invoice->reference, $payment->beneficiary->full_name_organization, $payment->campus->name, $payment->paid_at->format('Y-m-d H:i'), $payment->amount, $payment->payment_method, $payment->payment_reference, $payment->status]);

        return CsvExporter::download('upams-payments-'.today()->format('Y-m-d').'.csv', ['Receipt', 'Invoice', 'Beneficiary', 'Campus', 'Paid At', 'Amount', 'Method', 'Reference', 'Status'], $rows);
    }
}
