@extends('layouts.app')
@section('title', $asset->reference.' | ICT Equipment')
@section('portal-label', auth()->user()->role?->name === 'System Administrator' ? 'Property Management Administration · ICT Assets' : 'IT Technician Workspace')
@section('page-heading', $asset->reference.' · '.$asset->name)
@section('user-role', auth()->user()->role?->name ?? 'IT Technician')
@section('sidebar') @include('technician.partials.sidebar') @endsection
@section('content')
<div class="space-y-6">
    @include('technician.partials.messages')
    <div class="flex justify-end gap-3">
        <a href="{{ route('technician.activity.show', ['type' => 'equipment', 'record' => $asset->id]) }}" class="rounded-lg border border-border bg-white px-4 py-2.5 text-sm font-semibold text-heading dark:bg-slate-900">Activity / History</a>
        <a href="{{ route('technician.equipment.edit', $asset) }}" class="rounded-lg bg-busitema-blue px-4 py-2.5 text-sm font-semibold text-white">Edit Equipment</a>
    </div>

    @php
        $details = [
            ['Asset ID', $asset->reference], ['Equipment Type', $asset->type?->name],
            ['Serial Number', $asset->serial_number], ['Make', $asset->make], ['Model', $asset->model],
            ['Campus', $asset->campus?->name], ['Building', $asset->building],
            ['Room / Lab', $asset->computerLab?->name ?? $asset->room], ['Custodian', $asset->custodian],
            ['GPS Latitude', $asset->latitude], ['GPS Longitude', $asset->longitude],
            ['Condition', $asset->condition], ['Operational Status', $asset->operational_status],
            ['Purchase Date', $asset->acquired_at?->format('d M Y')],
            ['Purchase Cost', $asset->purchase_cost ? 'UGX '.number_format((float) $asset->purchase_cost) : null],
            ['Supplier', $asset->supplier], ['Warranty Expiry', $asset->warranty_expires_at?->format('d M Y')],
            ['Added By', $asset->createdBy?->name], ['Updated By', $asset->updatedBy?->name],
        ];
    @endphp
    <section class="rounded-xl border border-border bg-white p-6 shadow-sm dark:bg-slate-900">
        <div class="grid gap-px overflow-hidden rounded-xl border border-border bg-border sm:grid-cols-2 xl:grid-cols-4">
            @foreach($details as [$label, $detail])
                <div class="bg-white p-4 dark:bg-slate-900"><p class="text-xs font-semibold uppercase tracking-wide text-body-text">{{ $label }}</p><p class="mt-1.5 font-semibold text-heading {{ $label === 'Serial Number' || $label === 'Asset ID' ? 'font-mono' : '' }}">{{ $detail ?: '—' }}</p></div>
            @endforeach
        </div>
        <x-location-map class="mt-6" :latitude="$asset->latitude" :longitude="$asset->longitude" :title="$asset->reference.' · '.$asset->name" />
    </section>

    <section class="rounded-xl border border-border bg-white p-6 shadow-sm dark:bg-slate-900">
        <h2 class="text-lg font-semibold text-heading">Technical Specifications</h2>
        <div class="mt-4 grid gap-px overflow-hidden rounded-xl border border-border bg-border sm:grid-cols-2 xl:grid-cols-3">
            @forelse($technicalSpecifications as $specification)
                <div class="bg-white p-4 dark:bg-slate-900"><p class="text-xs font-semibold uppercase tracking-wide text-body-text">{{ $specification['label'] }}</p><p class="mt-1.5 font-semibold text-heading">{{ $specification['value'] }}</p></div>
            @empty
                <div class="bg-white p-4 text-sm text-body-text dark:bg-slate-900 sm:col-span-2 xl:col-span-3">No type-specific technical specifications have been recorded.</div>
            @endforelse
        </div>
    </section>

    <section class="rounded-xl border border-border bg-white p-6 shadow-sm dark:bg-slate-900"><h2 class="mb-4 text-lg font-semibold">Record Provenance</h2><x-record-provenance :record="$asset" /></section>
    <section class="rounded-xl border border-border bg-white p-6 shadow-sm dark:bg-slate-900"><h2 class="text-lg font-semibold">Fault & Repair History</h2><div class="mt-4 divide-y divide-border">@forelse($asset->maintenanceRequests as $fault)<a href="{{ route('technician.faults.show', $fault) }}" class="flex items-center justify-between gap-4 py-3"><div><p class="font-semibold text-heading">{{ $fault->reference }} · {{ $fault->title }}</p><p class="text-xs text-body-text">{{ $fault->status }} · {{ $fault->priority }}</p></div><span class="text-sm font-semibold text-busitema-blue">View</span></a>@empty<p class="py-4 text-sm text-body-text">No faults recorded for this equipment.</p>@endforelse</div></section>
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">@foreach([['Assignments', $asset->assignments->count(), route('technician.assignments.index')], ['Inspections', $asset->inspections->count(), route('technician.inspections.index')], ['Transfers', $asset->transfers->count(), route('technician.transfers.index')], ['Documents', $asset->documents->count(), route('technician.documents.index')]] as [$label, $count, $url])<a href="{{ $url }}" class="rounded-xl border border-border bg-white p-5 shadow-sm transition hover:border-busitema-blue dark:bg-slate-900"><p class="text-sm font-semibold text-body-text">{{ $label }}</p><p class="mt-2 text-2xl font-semibold text-heading">{{ $count }}</p><p class="mt-2 text-xs font-semibold text-busitema-blue">Open {{ strtolower($label) }}</p></a>@endforeach</section>
</div>
@endsection
