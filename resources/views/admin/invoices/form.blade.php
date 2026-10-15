@php
    $isLocked = $invoice->hasLockedFinancialTerms();
    $heading = match (true) {
        ! $invoice->exists => 'Create invoice',
        $isLocked => 'Update invoice details',
        default => 'Edit draft invoice',
    };
    $field = 'mt-2 w-full rounded-lg border border-border bg-transparent p-3 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-body-text dark:disabled:bg-slate-800/60';
@endphp
@extends('layouts.app')
@section('title', $heading.' | Property Management')
@section('portal-label', 'Finance & Utilities')
@section('page-heading', $heading)
@section('content')
@include('admin.partials.flash')
<form method="POST" action="{{ $invoice->exists ? route('billing.update',$invoice) : route('billing.store') }}" class="mx-auto max-w-5xl rounded-2xl border border-border bg-white p-6 shadow-sm dark:bg-slate-900">@csrf @if($invoice->exists) @method('PUT') @endif
    @if($isLocked)
        <div class="mb-6 flex gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-400/35 dark:text-amber-100">
            <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M7 11V7a5 5 0 0 1 10 0v4M5 11h14v10H5V11Z" /></svg>
            <div><p class="font-semibold">Invoice {{ $invoice->reference }} has been issued, so its financial terms are locked.</p><p class="mt-1 text-amber-900/80 dark:text-amber-100/80">Beneficiary, campus, dates, amounts, and status cannot be changed. You can still update the description and registry references. If the amount is wrong, cancel the invoice (only possible before any payment) and issue a new one.</p></div>
        </div>
    @else
        <p class="mb-6 text-sm text-body-text">Amounts are recalculated on the server. Registry references link this invoice to existing property, asset, and agreement records. Once issued, the financial terms can no longer be edited.</p>
    @endif
    <div class="grid gap-5 md:grid-cols-2">
        <label class="text-sm font-semibold">Beneficiary *<select name="beneficiary_id" class="{{ $field }}" @disabled($isLocked)><option value="">Select beneficiary</option>@foreach($beneficiaries as $beneficiary)<option value="{{ $beneficiary->id }}" @selected(old('beneficiary_id',$invoice->beneficiary_id)==$beneficiary->id)>{{ $beneficiary->full_name_organization }}</option>@endforeach</select></label>
        <label class="text-sm font-semibold">Campus *<select name="campus_id" class="{{ $field }}" @disabled($isLocked)><option value="">Select campus</option>@foreach($campuses as $campus)<option value="{{ $campus->id }}" @selected(old('campus_id',$invoice->campus_id)==$campus->id)>{{ $campus->name }}</option>@endforeach</select></label>
        @foreach(['property_reference'=>'Property reference','asset_reference'=>'Asset reference','agreement_reference'=>'Agreement reference'] as $name=>$label)<label class="text-sm font-semibold">{{ $label }} (optional)<input name="{{ $name }}" value="{{ old($name,$invoice->$name) }}" class="{{ $field }}"></label>@endforeach
        <label class="text-sm font-semibold">Issue date *<input type="date" name="issue_date" value="{{ old('issue_date',$invoice->issue_date?->format('Y-m-d') ?? today()->format('Y-m-d')) }}" class="{{ $field }}" @disabled($isLocked)></label>
        <label class="text-sm font-semibold">Due date *<input type="date" name="due_date" value="{{ old('due_date',$invoice->due_date?->format('Y-m-d')) }}" class="{{ $field }}" @disabled($isLocked)></label>
        <label class="text-sm font-semibold">Subtotal (UGX) *<input type="number" min="0.01" step="0.01" name="subtotal" value="{{ old('subtotal',$invoice->subtotal) }}" class="{{ $field }}" @disabled($isLocked)></label>
        <label class="text-sm font-semibold">Tax (UGX)<input type="number" min="0" step="0.01" name="tax_amount" value="{{ old('tax_amount',$invoice->tax_amount ?? 0) }}" class="{{ $field }}" @disabled($isLocked)></label>
        <label class="text-sm font-semibold">Status *<select name="status" class="{{ $field }}" @disabled($isLocked)>@foreach($isLocked ? [$invoice->paymentStatus()] : ['Draft','Issued'] as $status)<option @selected(old('status',$invoice->status ?? 'Issued')===$status)>{{ $status }}</option>@endforeach</select></label>
        <label class="text-sm font-semibold md:col-span-2">Description *<textarea name="description" rows="4" class="{{ $field }}">{{ old('description',$invoice->description) }}</textarea></label>
    </div><div class="mt-6 flex justify-end gap-3"><a class="rounded-lg border border-border px-5 py-3 font-semibold" href="{{ $invoice->exists ? route('billing.show',$invoice) : route('billing.index') }}">Cancel</a><button class="cursor-pointer rounded-lg bg-busitema-gold px-5 py-3 font-bold text-slate-950">{{ match (true) { ! $invoice->exists => 'Create invoice', $isLocked => 'Save details', default => 'Save draft changes' } }}</button></div>
</form>
@endsection
