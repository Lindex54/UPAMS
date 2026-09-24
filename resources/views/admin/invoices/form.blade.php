@extends('layouts.app')
@section('title', ($invoice->exists ? 'Correct' : 'Create').' Invoice | Property Management')
@section('portal-label', 'Finance & Utilities')
@section('page-heading', $invoice->exists ? 'Correct invoice' : 'Create invoice')
@section('content')
@include('admin.partials.flash')
<form method="POST" action="{{ $invoice->exists ? route('billing.update',$invoice) : route('billing.store') }}" class="mx-auto max-w-5xl rounded-2xl border border-border bg-white p-6 shadow-sm dark:bg-slate-900">@csrf @if($invoice->exists) @method('PUT') @endif
    <p class="mb-6 text-sm text-body-text">Amounts are recalculated on the server. Registry references link this invoice to existing property, asset, and agreement records.</p>
    <div class="grid gap-5 md:grid-cols-2">
        <label class="text-sm font-semibold">Beneficiary *<select name="beneficiary_id" class="mt-2 w-full rounded-lg border border-border bg-transparent p-3"><option value="">Select beneficiary</option>@foreach($beneficiaries as $beneficiary)<option value="{{ $beneficiary->id }}" @selected(old('beneficiary_id',$invoice->beneficiary_id)==$beneficiary->id)>{{ $beneficiary->full_name_organization }}</option>@endforeach</select></label>
        <label class="text-sm font-semibold">Campus *<select name="campus_id" class="mt-2 w-full rounded-lg border border-border bg-transparent p-3"><option value="">Select campus</option>@foreach($campuses as $campus)<option value="{{ $campus->id }}" @selected(old('campus_id',$invoice->campus_id)==$campus->id)>{{ $campus->name }}</option>@endforeach</select></label>
        @foreach(['property_reference'=>'Property reference','asset_reference'=>'Asset reference','agreement_reference'=>'Agreement reference'] as $name=>$label)<label class="text-sm font-semibold">{{ $label }} (optional)<input name="{{ $name }}" value="{{ old($name,$invoice->$name) }}" class="mt-2 w-full rounded-lg border border-border bg-transparent p-3"></label>@endforeach
        <label class="text-sm font-semibold">Issue date *<input type="date" name="issue_date" value="{{ old('issue_date',$invoice->issue_date?->format('Y-m-d') ?? today()->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-border bg-transparent p-3"></label>
        <label class="text-sm font-semibold">Due date *<input type="date" name="due_date" value="{{ old('due_date',$invoice->due_date?->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-border bg-transparent p-3"></label>
        <label class="text-sm font-semibold">Subtotal (UGX) *<input type="number" min="0.01" step="0.01" name="subtotal" value="{{ old('subtotal',$invoice->subtotal) }}" class="mt-2 w-full rounded-lg border border-border bg-transparent p-3"></label>
        <label class="text-sm font-semibold">Tax (UGX)<input type="number" min="0" step="0.01" name="tax_amount" value="{{ old('tax_amount',$invoice->tax_amount ?? 0) }}" class="mt-2 w-full rounded-lg border border-border bg-transparent p-3"></label>
        <label class="text-sm font-semibold">Status *<select name="status" class="mt-2 w-full rounded-lg border border-border bg-transparent p-3">@foreach(['Draft','Issued'] as $status)<option @selected(old('status',$invoice->status ?? 'Issued')===$status)>{{ $status }}</option>@endforeach</select></label>
        <label class="text-sm font-semibold md:col-span-2">Description *<textarea name="description" rows="4" class="mt-2 w-full rounded-lg border border-border bg-transparent p-3">{{ old('description',$invoice->description) }}</textarea></label>
    </div><div class="mt-6 flex justify-end gap-3"><a class="rounded-lg border border-border px-5 py-3 font-semibold" href="{{ route('billing.index') }}">Cancel</a><button class="cursor-pointer rounded-lg bg-busitema-gold px-5 py-3 font-bold text-slate-950">{{ $invoice->exists ? 'Save correction' : 'Create invoice' }}</button></div>
</form>
@endsection
