@extends('layouts.app')
@section('title', 'Record Activity | Property Management')
@section('portal-label', auth()->user()->role?->name === 'System Administrator' ? 'Property Management Administration · ICT Assets' : 'IT Technician Workspace')
@section('page-heading', $recordType.' #'.$record->getKey().' Activity')
@section('user-role', auth()->user()->role?->name ?? 'IT Technician')
@section('sidebar') @include('technician.partials.sidebar') @endsection

@section('content')
<div class="space-y-6">
    <section class="rounded-xl border border-border bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div><h2 class="text-lg font-semibold">Record Provenance</h2><p class="mt-1 text-sm text-body-text">Current creator and last editor information for this shared ICT record.</p></div>
            <a href="{{ url()->previous() }}" class="rounded-lg border border-border px-4 py-2 text-sm font-semibold text-heading">Back</a>
        </div>
        <x-record-provenance :record="$record" class="mt-5" />
    </section>

    <section class="overflow-hidden rounded-xl border border-border bg-white shadow-sm">
        <div class="border-b border-border px-6 py-5"><h2 class="text-lg font-semibold">Immutable Audit History</h2><p class="mt-1 text-sm text-body-text">Create and update events recorded by the existing Property Management audit trail.</p></div>
        <div class="divide-y divide-border">
            @forelse ($logs as $log)
                <article class="grid gap-4 px-6 py-5 lg:grid-cols-[12rem_16rem_minmax(0,1fr)]">
                    <div><p class="text-xs font-semibold uppercase text-body-text">Event</p><p class="mt-1 font-semibold text-heading">{{ $log->action }}</p><time class="mt-1 block text-xs text-body-text">{{ $log->created_at->format('d M Y H:i:s') }}</time></div>
                    <div><p class="text-xs font-semibold uppercase text-body-text">Actor</p><p class="mt-1 font-semibold text-heading">#{{ $log->user_id ?? '—' }} · {{ $log->user_name ?? 'System' }}</p><p class="text-xs text-body-text">{{ $log->user_role ?? 'System process' }}</p></div>
                    <div><p class="text-xs font-semibold uppercase text-body-text">Change</p><p class="mt-1 text-sm text-heading">{{ $log->description }}</p><details class="mt-2"><summary class="cursor-pointer text-xs font-semibold text-busitema-blue">View changed values</summary><div class="mt-2 grid gap-2 xl:grid-cols-2"><pre class="overflow-auto rounded-lg bg-slate-950 p-3 text-xs text-slate-100">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}' }}</pre><pre class="overflow-auto rounded-lg bg-slate-950 p-3 text-xs text-slate-100">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}' }}</pre></div></details></div>
                </article>
            @empty
                <p class="px-6 py-10 text-center text-sm text-body-text">No audit events were recorded before ICT auditing was enabled.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
