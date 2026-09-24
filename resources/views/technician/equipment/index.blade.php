@extends('layouts.app')
@section('title', 'ICT Equipment | Property Management')
@section('portal-label', auth()->user()->role?->name === 'System Administrator' ? 'Property Management Administration · ICT Assets' : 'IT Technician Workspace')
@section('page-heading', 'ICT Equipment Register')
@section('user-role', auth()->user()->role?->name ?? 'IT Technician')
@section('sidebar') @include('technician.partials.sidebar') @endsection

@section('content')
<div class="space-y-6" x-data="{ filtersOpen: {{ request()->hasAny(['campus_id','computer_lab_id','asset_type_id','condition','created_by','date_from','date_to']) ? 'true' : 'false' }} }">
    @include('technician.partials.messages')

    <section class="rounded-xl border border-border bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <form class="grid flex-1 gap-3 sm:grid-cols-2 lg:grid-cols-[minmax(10rem,1fr)_minmax(10rem,1fr)_11rem_auto]" method="GET">
                <label class="text-sm font-semibold text-heading">Asset ID<input name="asset_id" value="{{ request('asset_id') }}" placeholder="ICT-001" class="mt-1.5 w-full rounded-lg border border-border bg-white px-3 py-2.5 font-normal outline-none focus:border-busitema-blue"></label>
                <label class="text-sm font-semibold text-heading">Serial Number<input name="serial_number" value="{{ request('serial_number') }}" placeholder="Manufacturer serial" class="mt-1.5 w-full rounded-lg border border-border bg-white px-3 py-2.5 font-normal outline-none focus:border-busitema-blue"></label>
                <label class="text-sm font-semibold text-heading">Status<select name="status" class="mt-1.5 w-full rounded-lg border border-border bg-white px-3 py-2.5 font-normal"><option value="">All statuses</option>@foreach(['Operational','Faulty','Under Maintenance','Unassigned','Retired'] as $status)<option @selected(request('status')===$status)>{{ $status }}</option>@endforeach</select></label>
                <div class="flex items-end gap-2"><button class="rounded-lg bg-busitema-blue px-4 py-2.5 text-sm font-semibold text-white">Filter</button><button type="button" @click="filtersOpen=!filtersOpen" class="rounded-lg border border-border px-4 py-2.5 text-sm font-semibold text-heading">More</button></div>

                <div x-show="filtersOpen" x-cloak class="grid gap-3 sm:col-span-2 sm:grid-cols-2 lg:col-span-4 lg:grid-cols-4">
                    <label class="text-sm font-semibold text-heading">Campus<select name="campus_id" class="mt-1.5 w-full rounded-lg border border-border bg-white px-3 py-2.5 font-normal"><option value="">All campuses</option>@foreach($filterCampuses as $campus)<option value="{{ $campus->id }}" @selected(request('campus_id')==$campus->id)>{{ $campus->name }}</option>@endforeach</select></label>
                    <label class="text-sm font-semibold text-heading">Lab<select name="computer_lab_id" class="mt-1.5 w-full rounded-lg border border-border bg-white px-3 py-2.5 font-normal"><option value="">All labs</option>@foreach($filterLabs as $lab)<option value="{{ $lab->id }}" @selected(request('computer_lab_id')==$lab->id)>{{ $lab->name }}</option>@endforeach</select></label>
                    <label class="text-sm font-semibold text-heading">Equipment Type<select name="asset_type_id" class="mt-1.5 w-full rounded-lg border border-border bg-white px-3 py-2.5 font-normal"><option value="">All types</option>@foreach($filterTypes as $type)<option value="{{ $type->id }}" @selected(request('asset_type_id')==$type->id)>{{ $type->name }}</option>@endforeach</select></label>
                    <label class="text-sm font-semibold text-heading">Condition<select name="condition" class="mt-1.5 w-full rounded-lg border border-border bg-white px-3 py-2.5 font-normal"><option value="">All conditions</option>@foreach(['Good','Fair','Poor','Critical'] as $condition)<option @selected(request('condition')===$condition)>{{ $condition }}</option>@endforeach</select></label>
                    <label class="text-sm font-semibold text-heading">Technician / Added By<select name="created_by" class="mt-1.5 w-full rounded-lg border border-border bg-white px-3 py-2.5 font-normal"><option value="">All users</option>@foreach($filterUsers as $filterUser)<option value="{{ $filterUser->id }}" @selected(request('created_by')==$filterUser->id)>#{{ $filterUser->id }} · {{ $filterUser->name }} · {{ $filterUser->role?->name }}</option>@endforeach</select></label>
                    <label class="text-sm font-semibold text-heading">Date Added From<input type="date" name="date_from" value="{{ request('date_from') }}" class="mt-1.5 w-full rounded-lg border border-border bg-white px-3 py-2.5 font-normal"></label>
                    <label class="text-sm font-semibold text-heading">Date Added To<input type="date" name="date_to" value="{{ request('date_to') }}" class="mt-1.5 w-full rounded-lg border border-border bg-white px-3 py-2.5 font-normal"></label>
                    <a href="{{ route('technician.equipment.index') }}" class="self-end rounded-lg border border-border px-4 py-2.5 text-center text-sm font-semibold text-heading">Clear Filters</a>
                </div>
            </form>
            <a href="{{ route('technician.equipment.create') }}" class="rounded-lg bg-busitema-gold px-4 py-2.5 text-center text-sm font-semibold text-busitema-navy">Register ICT Equipment</a>
        </div>
    </section>

    <div class="overflow-hidden rounded-xl border border-border bg-white shadow-sm">
        <div class="overflow-x-auto"><table class="w-full min-w-[1350px] text-left text-sm"><thead class="bg-light-background text-xs uppercase text-body-text"><tr><th class="px-5 py-3">Asset ID</th><th class="px-5 py-3">Serial Number</th><th class="px-5 py-3">Equipment</th><th class="px-5 py-3">Campus / Lab</th><th class="px-5 py-3">Custodian</th><th class="px-5 py-3">Condition</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Added By</th><th class="px-5 py-3">Updated By</th><th class="px-5 py-3">Actions</th></tr></thead>
            <tbody class="divide-y divide-border">@forelse($equipment as $asset)<tr class="hover:bg-light-background/60"><td class="px-5 py-4 font-semibold text-heading">{{ $asset->reference }}</td><td class="px-5 py-4 font-mono text-xs text-heading">{{ $asset->serial_number }}</td><td class="px-5 py-4"><p class="font-semibold text-heading">{{ $asset->name }}</p><p class="text-xs text-body-text">{{ $asset->type?->name }} · {{ $asset->make }} {{ $asset->model }}</p></td><td class="px-5 py-4"><p>{{ $asset->campus?->name }}</p><p class="text-xs">{{ $asset->computerLab?->name ?? trim($asset->building.' '.$asset->room) ?: 'Unassigned' }}</p></td><td class="px-5 py-4">{{ $asset->custodian ?: '—' }}</td><td class="px-5 py-4">{{ $asset->condition }}</td><td class="px-5 py-4"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-busitema-blue">{{ $asset->operational_status }}</span></td><td class="px-5 py-4"><p class="font-semibold text-heading">#{{ $asset->created_by ?? '—' }} · {{ $asset->createdBy?->name ?? 'System' }}</p><p class="text-xs text-body-text">{{ $asset->createdBy?->role?->name ?? '—' }} · {{ $asset->created_at?->format('d M Y H:i') }}</p></td><td class="px-5 py-4"><p class="font-semibold text-heading">#{{ $asset->updated_by ?? '—' }} · {{ $asset->updatedBy?->name ?? 'System' }}</p><p class="text-xs text-body-text">{{ $asset->updatedBy?->role?->name ?? '—' }} · {{ $asset->updated_at?->format('d M Y H:i') }}</p></td><td class="px-5 py-4"><div class="flex gap-3"><a class="font-semibold text-busitema-blue" href="{{ route('technician.equipment.show',$asset) }}">View</a><a class="font-semibold text-body-text" href="{{ route('technician.activity.show',['type'=>'equipment','record'=>$asset->id]) }}">History</a></div></td></tr>@empty<tr><td colspan="10" class="px-5 py-12 text-center text-body-text">No ICT equipment matches this view.</td></tr>@endforelse</tbody></table></div>
        <div class="border-t border-border px-5 py-4">{{ $equipment->links() }}</div>
    </div>
</div>
@endsection
