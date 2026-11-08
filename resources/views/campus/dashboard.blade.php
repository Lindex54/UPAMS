@extends('layouts.app')

@section('title', 'Campus Property Dashboard | Property Management')
@section('page-heading', 'Campus Property Operations')
@section('portal-label', 'Property Management · Campus')
@section('user-role', 'Campus Property Officer')
@section('user-initial', 'C')

@section('sidebar')
    <x-campus-sidebar />

    @php
        $quickActions = [
            ['label' => 'Register Asset', 'route' => 'asset-management.assets.create', 'path' => 'M12 6v12M6 12h12', 'classes' => 'bg-blue-50 text-busitema-blue'],
            ['label' => 'Add Land Record', 'route' => 'asset-management.land.create', 'path' => 'M3 6.5 8 4l8 3 5-2.5v13L16 20l-8-3-5 2.5v-13ZM8 4v13M16 7v13', 'classes' => 'bg-emerald-50 text-emerald-700'],
            ['label' => 'Add Building/Space', 'route' => 'asset-management.buildings.create', 'path' => 'M4 21V5l8-2v18M4 21h16M16 8h4v13M8 8h.01M8 12h.01M8 16h.01', 'classes' => 'bg-violet-50 text-violet-700'],
            ['label' => 'Create Inspection', 'route' => 'operations.inspections.create', 'path' => 'M9 11.5 11 14l4-5M7 3h10v18H7V3Z', 'classes' => 'bg-amber-50 text-amber-700'],
            ['label' => 'Create Maintenance Request', 'route' => 'operations.maintenance.create', 'path' => 'm14.7 6.3 3-3a4 4 0 0 1-5 5l-7.4 7.4a2.1 2.1 0 0 0 3 3l7.4-7.4a4 4 0 0 0 5-5l-3 3-3-3Z', 'classes' => 'bg-orange-50 text-orange-700'],
            ['label' => 'Add Agreement/Allocation', 'route' => 'operations.agreements.create', 'path' => 'M7 3h8l4 4v14H7V3Zm8 0v5h4M10 13h6M10 17h6', 'classes' => 'bg-indigo-50 text-indigo-700'],
            ['label' => 'Upload Document', 'route' => 'operations.documents.create', 'path' => 'M12 16V4m0 0L8 8m4-4 4 4M5 14v6h14v-6', 'classes' => 'bg-slate-100 text-slate-700'],
        ];
    @endphp
@endsection

@section('content')
    <div class="flex flex-col gap-6">
        <section class="overflow-hidden rounded-2xl border border-border bg-white shadow-sm">
            <div class="flex flex-col gap-5 border-l-4 border-busitema-gold px-6 py-6 sm:px-8 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-semibold tracking-[0.14em] text-busitema-blue uppercase">Assigned campus</p>
                    <h2 class="mt-2 text-2xl font-semibold text-heading sm:text-3xl">{{ $campus->name }} property operations</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-body-text">Live property records, agreements, documents, and maintenance activity for your assigned campus.</p>
                </div>
                <div class="flex shrink-0 items-center gap-3 rounded-xl bg-light-background px-4 py-3">
                    <span class="flex size-10 items-center justify-center rounded-lg bg-busitema-blue text-white" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.2 7-12a7 7 0 1 0-14 0c0 6.8 7 12 7 12Zm0-9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" /></svg></span>
                    <div><p class="text-xs text-body-text">Campus scope</p><p class="text-sm font-semibold text-heading">{{ $campus->name }} only</p></div>
                </div>
            </div>
        </section>

        <section>
            <div><h2 class="text-lg font-semibold text-heading">Quick actions</h2><p class="mt-1 text-sm text-body-text">Start a common campus property task.</p></div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4 2xl:grid-cols-7">
                @foreach ($quickActions as $action)
                    <a href="{{ route($action['route']) }}" class="group flex min-h-24 items-center gap-3 rounded-xl border border-border bg-white p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-busitema-blue hover:shadow-md 2xl:flex-col 2xl:items-start">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-lg {{ $action['classes'] }}"><svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $action['path'] }}" /></svg></span>
                        <span class="text-sm font-semibold leading-5 text-heading group-hover:text-busitema-blue">{{ $action['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Campus property key performance indicators">
            <x-stat-card label="Total Campus Assets" :value="$metrics['totalAssets']" :detail="$metrics['assetCategories'].' represented categories'" tone="blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7.5 12 3l8 4.5M5 9v9l7 3 7-3V9M12 12l8-4.5M12 12 4 7.5M12 12v9" /></svg></x-stat-card>
            <x-stat-card label="Active Assets" :value="$metrics['activeAssets']" detail="Currently active or in use" tone="green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" /></svg></x-stat-card>
            <x-stat-card label="ICT Equipment" :value="$metrics['ictEquipment']" detail="Equipment registered as ICT" tone="purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16v11H4V5Zm5 15h6M12 16v4" /></svg></x-stat-card>
            <x-stat-card label="Current Documents" :value="$metrics['currentDocuments']" detail="Current controlled records" tone="blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l4 4v14H7V3Zm8 0v5h4M10 13h6M10 17h6" /></svg></x-stat-card>
            <x-stat-card label="Assets Under Maintenance" :value="$metrics['underMaintenance']" detail="Based on current asset status" tone="orange"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.7 6.3 3-3a4 4 0 0 1-5 5l-7.4 7.4a2.1 2.1 0 0 0 3 3l7.4-7.4a4 4 0 0 0 5-5l-3 3-3-3Z" /></svg></x-stat-card>
            <x-stat-card label="Expiring Agreements" :value="$metrics['expiringAgreements']" detail="Due within the next 90 days" tone="gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3v3M18 3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Zm7 7v4l2 1" /></svg></x-stat-card>
            <x-stat-card label="Open Maintenance Requests" :value="$metrics['openMaintenance']" :detail="$metrics['unassignedMaintenance'].' currently unassigned'" tone="orange"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h10v18H7V3Zm3 5h4M10 12h4M10 16h2" /></svg></x-stat-card>
            <x-stat-card label="Poor/Critical Condition Assets" :value="$metrics['poorCritical']" detail="Require condition follow-up" tone="red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3 2.5 20h19L12 3Zm0 6v5M12 17.5h.01" /></svg></x-stat-card>
        </section>

        <section class="grid gap-6 xl:grid-cols-5">
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm xl:col-span-3">
                <div><h2 class="text-lg font-semibold text-heading">Recent Campus Activity</h2><p class="mt-1 text-sm text-body-text">Latest recorded changes within {{ $campus->name }}</p></div>
                <ol class="mt-5 divide-y divide-border">
                    @forelse ($recentActivity as $activity)
                        <li class="flex gap-3 py-3 first:pt-0 last:pb-0"><span class="mt-1.5 size-2.5 shrink-0 rounded-full bg-busitema-blue"></span><div class="min-w-0 flex-1"><p class="text-sm font-semibold text-heading">{{ str($activity->action)->replace('_', ' ')->title() }}</p><p class="mt-0.5 truncate text-xs text-body-text">{{ $activity->description ?: ($activity->user_name ?: 'System activity') }}</p></div><time class="shrink-0 text-xs text-body-text" datetime="{{ $activity->created_at?->toIso8601String() }}">{{ $activity->created_at?->diffForHumans() }}</time></li>
                    @empty
                        <li class="py-8 text-center text-sm text-body-text">No campus activity has been recorded yet.</li>
                    @endforelse
                </ol>
            </article>

            <article class="rounded-xl border border-border bg-white p-6 shadow-sm xl:col-span-2">
                <div class="flex items-start justify-between gap-4"><div><h2 class="text-lg font-semibold text-heading">Maintenance Status</h2><p class="mt-1 text-sm text-body-text">{{ $metrics['openMaintenance'] }} open campus requests</p></div><span class="rounded-full bg-orange-50 px-2.5 py-1 text-xs font-semibold text-orange-700">{{ $metrics['unassignedMaintenance'] }} unassigned</span></div>
                <div class="mt-6 flex flex-col gap-4">
                    @forelse ($maintenanceStatuses as $status)
                        <div><div class="mb-1.5 flex items-center justify-between text-sm"><span class="text-body-text">{{ $status['status'] }}</span><span class="font-semibold text-heading">{{ $status['count'] }}</span></div><div class="h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full {{ $status['color'] }}" style="width: {{ $status['percentage'] }}%"></div></div></div>
                    @empty
                        <p class="rounded-lg bg-light-background px-4 py-6 text-center text-sm text-body-text">No open maintenance requests.</p>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="grid gap-6 lg:grid-cols-2 xl:grid-cols-3">
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-heading">Agreement Expiries</h2><p class="mt-1 text-sm text-body-text">Renewals requiring campus follow-up</p>
                <div class="mt-6 grid grid-cols-3 divide-x divide-border rounded-xl border border-border bg-light-background py-4 text-center">
                    @foreach ($agreementExpiries as $expiry)
                        <div><p class="text-xl font-semibold {{ $expiry['classes'] }}">{{ $expiry['count'] }}</p><p class="mt-1 text-xs text-body-text">{{ $expiry['label'] }}</p></div>
                    @endforeach
                </div>
                <p class="mt-5 rounded-lg bg-amber-50 px-4 py-3 text-xs leading-5 text-body-text"><span class="font-semibold text-amber-700">Live schedule:</span> These totals are calculated from campus agreement expiry dates.</p>
            </article>

            <article class="rounded-xl border border-border bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-heading">Asset Condition</h2><p class="mt-1 text-sm text-body-text">Current campus portfolio health</p>
                <div class="mt-6 flex flex-col gap-4">
                    @foreach ($conditionChart as $condition)
                        <div><div class="mb-1.5 flex items-center justify-between text-sm"><span class="flex items-center gap-2 text-body-text"><span class="size-2 rounded-full {{ $condition['color'] }}"></span>{{ $condition['label'] }}</span><span class="font-semibold text-heading">{{ $condition['count'] }} ({{ $condition['percentage'] }}%)</span></div><div class="h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full {{ $condition['color'] }}" style="width: {{ $condition['percentage'] }}%"></div></div></div>
                    @endforeach
                </div>
            </article>

            <article class="rounded-xl border border-border bg-white p-6 shadow-sm lg:col-span-2 xl:col-span-1">
                <h2 class="text-lg font-semibold text-heading">Campus Registry</h2><p class="mt-1 text-sm text-body-text">Records currently assigned to {{ $campus->name }}</p>
                <div class="mt-6 grid grid-cols-2 gap-3">
                    @foreach ($registryCounts as $record)
                        <div class="rounded-xl border border-border bg-light-background p-4"><p class="text-2xl font-semibold text-heading">{{ $record['count'] }}</p><p class="mt-1 text-xs text-body-text">{{ $record['label'] }}</p></div>
                    @endforeach
                </div>
            </article>
        </section>

        <section class="overflow-hidden rounded-xl border border-border bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-border px-6 py-5 sm:flex-row sm:items-center sm:justify-between"><div><h2 class="text-lg font-semibold text-heading">Upcoming Document Expiries</h2><p class="mt-1 text-sm text-body-text">Campus records expiring within 90 days</p></div><a href="{{ route('operations.documents.index') }}" class="self-start rounded-lg border border-border px-3.5 py-2 text-sm font-semibold text-heading transition hover:border-busitema-blue hover:text-busitema-blue">View all documents</a></div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-3xl text-left text-sm">
                    <thead class="bg-light-background text-xs font-semibold tracking-wide text-body-text uppercase"><tr><th class="px-6 py-3">Document</th><th class="px-6 py-3">Related record</th><th class="px-6 py-3">Type</th><th class="px-6 py-3">Expiry date</th><th class="px-6 py-3">Time remaining</th></tr></thead>
                    <tbody class="divide-y divide-border">
                        @forelse ($upcomingDocuments as $document)
                            <tr class="transition hover:bg-light-background/70"><td class="whitespace-nowrap px-6 py-4 font-semibold text-heading">{{ $document->title }}</td><td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $document->related_reference ?: '—' }}</td><td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $document->document_type }}</td><td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $document->expires_at->format('d M Y') }}</td><td class="px-6 py-4"><span class="whitespace-nowrap rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">{{ $document->expires_at->diffForHumans() }}</span></td></tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-body-text">No campus documents expire within the next 90 days.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
