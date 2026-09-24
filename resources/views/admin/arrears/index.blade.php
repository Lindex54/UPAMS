@extends('layouts.app')

@section('title', 'Arrears | Property Management')
@section('portal-label', 'Finance & Utilities')
@section('page-heading', 'Arrears')

@section('content')
    <p class="mb-5 text-sm text-body-text">Balances and aging are calculated automatically from issued invoices and valid payments. They cannot be edited manually.</p>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($buckets as $bucket => $amount)
            <div class="rounded-2xl border border-border bg-white p-5 shadow-sm dark:bg-slate-900">
                <p class="text-xs font-semibold tracking-wider text-body-text uppercase">{{ $bucket }}</p>
                <p class="mt-2 text-xl font-bold text-heading">UGX {{ number_format($amount, 2) }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 rounded-2xl border border-border bg-white p-5 shadow-sm dark:bg-slate-900">
        <form class="grid gap-2 md:grid-cols-5">
            <input name="q" value="{{ request('q') }}" class="rounded-lg border border-border bg-transparent p-2" placeholder="Invoice or beneficiary">
            <select name="campus_id" class="rounded-lg border border-border bg-transparent p-2">
                <option value="">All campuses</option>
                @foreach ($campuses as $campus)
                    <option value="{{ $campus->id }}" @selected(request('campus_id') == $campus->id)>{{ $campus->name }}</option>
                @endforeach
            </select>
            <select name="beneficiary_id" class="rounded-lg border border-border bg-transparent p-2">
                <option value="">All beneficiaries</option>
                @foreach ($beneficiaries as $beneficiary)
                    <option value="{{ $beneficiary->id }}" @selected(request('beneficiary_id') == $beneficiary->id)>{{ $beneficiary->full_name_organization }}</option>
                @endforeach
            </select>
            <input name="property_reference" value="{{ request('property_reference') }}" class="rounded-lg border border-border bg-transparent p-2" placeholder="Property or agreement">
            <button class="rounded-lg bg-busitema-blue px-4 py-2 font-semibold text-white">Apply filters</button>
        </form>

        <div class="mt-3 text-right">
            <a href="{{ route('arrears.export', request()->query()) }}" class="rounded-lg border border-border px-4 py-2 font-semibold">Export CSV</a>
        </div>

        <div class="mt-5 overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border text-xs text-body-text uppercase">
                    <tr>
                        <th class="p-3">Invoice Number</th>
                        <th class="p-3">Beneficiary</th>
                        <th class="p-3">Property / Agreement</th>
                        <th class="p-3">Campus</th>
                        <th class="p-3">Original Due Date</th>
                        <th class="p-3 text-right">Days Overdue</th>
                        <th class="p-3">Aging Category</th>
                        <th class="p-3 text-right">Outstanding Balance</th>
                        <th class="p-3">Last Payment Date</th>
                        <th class="p-3">Statement</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($invoices as $invoice)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800">
                            <td class="p-3 font-semibold">{{ $invoice->reference }}</td>
                            <td class="p-3">{{ $invoice->beneficiary->full_name_organization }}</td>
                            <td class="p-3">
                                <span class="block">{{ $invoice->property_reference ?: '—' }}</span>
                                @if ($invoice->agreement_reference)
                                    <span class="block text-xs text-body-text">Agreement: {{ $invoice->agreement_reference }}</span>
                                @endif
                            </td>
                            <td class="p-3">{{ $invoice->campus->name }}</td>
                            <td class="p-3">{{ $invoice->due_date->format('d M Y') }}</td>
                            <td class="p-3 text-right">{{ number_format($invoice->daysOverdue()) }}</td>
                            <td class="p-3"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold dark:bg-slate-700">{{ $invoice->agingBucket() }}</span></td>
                            <td class="p-3 text-right font-bold">UGX {{ number_format($invoice->balance(), 2) }}</td>
                            <td class="p-3">{{ $invoice->last_payment_at?->format('d M Y') ?? '—' }}</td>
                            <td class="p-3"><a class="font-semibold text-busitema-blue dark:text-sky-300" href="{{ route('arrears.statement', $invoice) }}">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="p-8 text-center text-body-text">No arrears match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">{{ $invoices->links() }}</div>
    </div>
@endsection
