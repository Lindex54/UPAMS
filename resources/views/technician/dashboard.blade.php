@extends('layouts.app')

@section('title', 'IT Technician Dashboard | Property Management')
@section('portal-label', auth()->user()->role?->name === 'System Administrator' ? 'University-wide ICT Operations' : ((auth()->user()->campus?->name ?? 'Assigned Campus').' · ICT Operations'))
@section('page-heading', 'IT Technician Dashboard')
@section('user-role', auth()->user()->role?->name ?? 'IT Technician')
@section('sidebar') @include('technician.partials.sidebar') @endsection

@section('content')
<div class="space-y-7" x-data="{ statusView: 'all' }">
    @include('technician.partials.messages')
    <section class="flex flex-col gap-4 rounded-2xl bg-busitema-deep-blue p-6 text-white shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <div><p class="text-sm font-semibold text-sky-200">ICT asset command centre</p><h2 class="mt-1 text-2xl font-semibold text-white">Keep every lab ready for teaching and service</h2><p class="mt-2 max-w-2xl text-sm text-blue-100">Live equipment health, repair workflow and warranty coverage for your authorised campus and unit.</p></div>
        <div class="flex flex-wrap gap-2"><a href="{{ route('technician.faults.create') }}" class="rounded-lg border border-white/30 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/10">Record fault</a><a href="{{ route('technician.equipment.create') }}" class="rounded-lg bg-busitema-gold px-4 py-2.5 text-sm font-semibold text-busitema-navy hover:bg-yellow-300">Register equipment</a></div>
    </section>

    @php $cards = [['Total ICT Equipment','total','bg-blue-500'],['Operational Equipment','operational','bg-emerald-500'],['Faulty Equipment','faulty','bg-red-500'],['Under Maintenance','maintenance','bg-orange-500'],['Unassigned Equipment','unassigned','bg-violet-500'],['Warranties Expiring Soon','warranties','bg-amber-500']]; @endphp
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
        @foreach ($cards as [$label,$key,$tone])
            <article class="rounded-xl border border-border bg-white p-5 shadow-sm"><div class="flex items-start justify-between"><p class="text-sm font-semibold text-body-text">{{ $label }}</p><span class="size-2.5 rounded-full {{ $tone }}"></span></div><p class="mt-4 text-3xl font-semibold text-heading">{{ number_format($metrics[$key]) }}</p><p class="mt-2 text-xs text-body-text">Current authorised scope</p></article>
        @endforeach
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
        <article class="rounded-xl border border-border bg-white p-6 shadow-sm"><h2 class="text-lg font-semibold">ICT Equipment by Type</h2><p class="mt-1 text-sm text-body-text">Distribution of registered devices</p><div class="mt-6 space-y-4">@forelse($equipmentByType as $row)<div><div class="mb-1.5 flex justify-between text-sm"><span>{{ $row->label }}</span><strong class="text-heading">{{ $row->total }}</strong></div><div class="h-2.5 rounded-full bg-slate-100"><div class="h-full rounded-full bg-busitema-blue" style="width: {{ $metrics['total'] ? max(5, round(($row->total / $metrics['total']) * 100)) : 0 }}%"></div></div></div>@empty<p class="rounded-lg bg-light-background p-5 text-sm">No ICT equipment has been registered in this scope.</p>@endforelse</div></article>
        <article class="rounded-xl border border-border bg-white p-6 shadow-sm"><h2 class="text-lg font-semibold">Equipment Condition / Status</h2><p class="mt-1 text-sm text-body-text">Operational readiness at a glance</p><div class="mt-6 grid gap-3 sm:grid-cols-2">@forelse($equipmentByStatus as $row)<div class="rounded-xl border border-border bg-light-background p-4"><p class="text-sm text-body-text">{{ $row->label }}</p><p class="mt-2 text-2xl font-semibold text-heading">{{ $row->total }}</p><p class="mt-1 text-xs text-body-text">{{ $metrics['total'] ? round(($row->total / $metrics['total']) * 100) : 0 }}% of equipment</p></div>@empty<p class="text-sm">Status data will appear after equipment registration.</p>@endforelse</div></article>
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
        @foreach ([['Recent Faults',$recentFaults,'fault'],['Equipment Requiring Attention',$attentionEquipment,'attention'],['Recent Repairs',$recentRepairs,'repair'],['Warranty Expiries',$warrantyExpiries,'warranty']] as [$title,$rows,$kind])
        <article class="rounded-xl border border-border bg-white p-6 shadow-sm"><h2 class="text-lg font-semibold">{{ $title }}</h2><div class="mt-4 divide-y divide-border">@forelse($rows as $row)<div class="flex items-center justify-between gap-4 py-3 first:pt-0"><div class="min-w-0"><p class="truncate text-sm font-semibold text-heading">{{ $kind === 'attention' || $kind === 'warranty' ? $row->reference.' · '.$row->name : ($row->asset?->reference ?? 'Unlinked asset').' · '.$row->title }}</p><p class="mt-1 truncate text-xs text-body-text">@if($kind === 'fault'){{ $row->status }} · {{ $row->priority }} priority @elseif($kind === 'attention'){{ $row->condition }} · {{ $row->operational_status }} @elseif($kind === 'repair')Resolved {{ $row->resolved_at?->diffForHumans() }} @else{{ $row->type?->name ?? 'ICT equipment' }} · expires {{ $row->warranty_expires_at?->format('d M Y') }}@endif</p></div><a href="{{ $kind === 'fault' || $kind === 'repair' ? route('technician.faults.show',$row) : route('technician.equipment.show',$row) }}" class="text-xs font-semibold text-busitema-blue">View</a></div>@empty<p class="py-5 text-sm text-body-text">Nothing to show right now.</p>@endforelse</div></article>
        @endforeach
    </section>
</div>
@endsection
