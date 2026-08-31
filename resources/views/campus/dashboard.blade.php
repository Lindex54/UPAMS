@extends('layouts.app')

@section('title', 'Campus Property Dashboard | UPAMS')
@section('page-heading', 'Campus Property Operations')
@section('portal-label', 'UPAMS Campus Property Management')
@section('user-role', 'Campus Property Officer')
@section('user-initial', 'C')

@section('sidebar')
    @php
        $campusNavigationGroups = [
            ['label' => 'Campus Overview', 'items' => [
                ['label' => 'Dashboard', 'href' => route('campus.dashboard'), 'pattern' => 'campus.dashboard', 'route' => true],
                ['label' => 'Map/Location View', 'href' => url('/campus/map'), 'pattern' => 'campus/map*'],
            ]],
            ['label' => 'Property Records', 'items' => [
                ['label' => 'Asset Registry', 'href' => url('/campus/assets'), 'pattern' => 'campus/assets*'],
                ['label' => 'Land Management', 'href' => url('/campus/land'), 'pattern' => 'campus/land*'],
                ['label' => 'Buildings & Spaces', 'href' => url('/campus/buildings'), 'pattern' => 'campus/buildings*'],
                ['label' => 'Commercial Property', 'href' => url('/campus/commercial-property'), 'pattern' => 'campus/commercial-property*'],
                ['label' => 'Agricultural Property', 'href' => url('/campus/agricultural-property'), 'pattern' => 'campus/agricultural-property*'],
            ]],
            ['label' => 'Campus Operations', 'items' => [
                ['label' => 'Agreements & Allocations', 'href' => url('/campus/agreements'), 'pattern' => 'campus/agreements*'],
                ['label' => 'Tenants/Beneficiaries', 'href' => url('/campus/beneficiaries'), 'pattern' => 'campus/beneficiaries*'],
                ['label' => 'Inspections', 'href' => url('/campus/inspections'), 'pattern' => 'campus/inspections*'],
                ['label' => 'Maintenance', 'href' => url('/campus/maintenance'), 'pattern' => 'campus/maintenance*'],
                ['label' => 'Documents', 'href' => url('/campus/documents'), 'pattern' => 'campus/documents*'],
            ]],
            ['label' => 'Updates & Insight', 'items' => [
                ['label' => 'Notifications', 'href' => url('/campus/notifications'), 'pattern' => 'campus/notifications*'],
                ['label' => 'Reports', 'href' => url('/campus/reports'), 'pattern' => 'campus/reports*'],
            ]],
        ];
    @endphp

    <x-sidebar
        :navigation-groups="$campusNavigationGroups"
        aria-label="Campus Property Officer navigation"
        sidebar-id="campus-property-officer-sidebar"
    />
@endsection

@section('content')
    <div class="flex flex-col gap-6" x-data="{ activityFilter: 'All activity' }">
        <section class="overflow-hidden rounded-2xl border border-border bg-white shadow-sm">
            <div class="flex flex-col gap-5 border-l-4 border-busitema-gold px-6 py-6 sm:px-8 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-semibold tracking-[0.14em] text-busitema-blue uppercase">Assigned campus</p>
                    <h2 class="mt-2 text-2xl font-semibold text-heading sm:text-3xl">Main Campus property operations</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-body-text">Track today’s property records, occupancy, inspections, agreements, documents, and maintenance activity for your campus.</p>
                </div>
                <div class="flex shrink-0 items-center gap-3 rounded-xl bg-light-background px-4 py-3">
                    <span class="flex size-10 items-center justify-center rounded-lg bg-busitema-blue text-white" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.2 7-12a7 7 0 1 0-14 0c0 6.8 7 12 7 12Zm0-9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" /></svg></span>
                    <div><p class="text-xs text-body-text">Campus scope</p><p class="text-sm font-semibold text-heading">Main Campus only</p></div>
                </div>
            </div>
        </section>

        <section>
            <div><h2 class="text-lg font-semibold text-heading">Quick actions</h2><p class="mt-1 text-sm text-body-text">Start a common campus property task.</p></div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4 2xl:grid-cols-7">
                @foreach ([
                    ['Register Asset', 'M12 6v12M6 12h12', 'bg-blue-50 text-busitema-blue'],
                    ['Add Land Record', 'M3 6.5 8 4l8 3 5-2.5v13L16 20l-8-3-5 2.5v-13ZM8 4v13M16 7v13', 'bg-emerald-50 text-emerald-700'],
                    ['Add Building/Space', 'M4 21V5l8-2v18M4 21h16M16 8h4v13M8 8h.01M8 12h.01M8 16h.01', 'bg-violet-50 text-violet-700'],
                    ['Create Inspection', 'M9 11.5 11 14l4-5M7 3h10v18H7V3Z', 'bg-amber-50 text-amber-700'],
                    ['Create Maintenance Request', 'm14.7 6.3 3-3a4 4 0 0 1-5 5l-7.4 7.4a2.1 2.1 0 0 0 3 3l7.4-7.4a4 4 0 0 0 5-5l-3 3-3-3Z', 'bg-orange-50 text-orange-700'],
                    ['Add Agreement/Allocation', 'M7 3h8l4 4v14H7V3Zm8 0v5h4M10 13h6M10 17h6', 'bg-indigo-50 text-indigo-700'],
                    ['Upload Document', 'M12 16V4m0 0L8 8m4-4 4 4M5 14v6h14v-6', 'bg-slate-100 text-slate-700'],
                ] as [$action, $path, $classes])
                    <button type="button" class="group flex min-h-24 items-center gap-3 rounded-xl border border-border bg-white p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-busitema-blue hover:shadow-md 2xl:flex-col 2xl:items-start">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-lg {{ $classes }}"><svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}" /></svg></span>
                        <span class="text-sm font-semibold leading-5 text-heading group-hover:text-busitema-blue">{{ $action }}</span>
                    </button>
                @endforeach
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Campus property key performance indicators">
            <x-stat-card label="Total Campus Assets" value="816" detail="Across 12 asset categories" tone="blue"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7.5 12 3l8 4.5M5 9v9l7 3 7-3V9M12 12l8-4.5M12 12 4 7.5M12 12v9" /></svg></x-stat-card>
            <x-stat-card label="Available Land" value="186 acres" detail="24% of campus land" tone="green"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6.5 8 4l8 3 5-2.5v13L16 20l-8-3-5 2.5v-13ZM8 4v13M16 7v13" /></svg></x-stat-card>
            <x-stat-card label="Vacant Spaces" value="18" detail="Ready for allocation" tone="gold"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 21V5l8-2v18M4 21h16M16 8h4v13" /></svg></x-stat-card>
            <x-stat-card label="Occupied Spaces" value="142" detail="86% campus occupancy" tone="purple"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 11h10M7 15h10M9 7h6M5 3h14v18H5V3Z" /></svg></x-stat-card>
            <x-stat-card label="Assets Under Maintenance" value="14" detail="5 currently in progress" tone="orange"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.7 6.3 3-3a4 4 0 0 1-5 5l-7.4 7.4a2.1 2.1 0 0 0 3 3l7.4-7.4a4 4 0 0 0 5-5l-3 3-3-3Z" /></svg></x-stat-card>
            <x-stat-card label="Expiring Agreements" value="6" detail="Due within the next 90 days" tone="gold"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3v3M18 3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Zm7 7v4l2 1" /></svg></x-stat-card>
            <x-stat-card label="Open Maintenance Requests" value="21" detail="4 require assignment today" tone="orange"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h10v18H7V3Zm3 5h4M10 12h4M10 16h2" /></svg></x-stat-card>
            <x-stat-card label="Poor/Critical Condition Assets" value="9" detail="3 awaiting inspection" tone="red"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3 2.5 20h19L12 3Zm0 6v5M12 17.5h.01" /></svg></x-stat-card>
        </section>

        <section class="grid gap-6 xl:grid-cols-5">
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm xl:col-span-3">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div><h2 class="text-lg font-semibold text-heading">Recent Asset Activity</h2><p class="mt-1 text-sm text-body-text">Latest changes within Main Campus</p></div>
                    <select class="rounded-lg border border-border bg-white px-3 py-2 text-xs font-semibold text-heading outline-none focus:border-busitema-blue" x-model="activityFilter"><option>All activity</option><option>Registrations</option><option>Inspections</option><option>Maintenance</option></select>
                </div>
                <ol class="mt-5 divide-y divide-border">
                    @foreach ([['Asset registered', 'Water pump · AST-MC-0816', 'Today, 10:42', 'bg-busitema-blue'], ['Inspection completed', 'Administration Block · Good condition', 'Today, 09:18', 'bg-emerald-500'], ['Space allocation updated', 'Commercial Unit CU-014 · Occupied', 'Yesterday, 15:26', 'bg-violet-500'], ['Condition changed', 'Generator GEN-009 · Fair to Poor', 'Yesterday, 11:03', 'bg-orange-500'], ['Document uploaded', 'Land title copy · Parcel MC-003', 'Mon, 16:40', 'bg-slate-500']] as [$activity, $subject, $time, $color])
                        <li class="flex gap-3 py-3 first:pt-0 last:pb-0"><span class="mt-1.5 size-2.5 shrink-0 rounded-full {{ $color }}"></span><div class="min-w-0 flex-1"><p class="text-sm font-semibold text-heading">{{ $activity }}</p><p class="mt-0.5 truncate text-xs text-body-text">{{ $subject }}</p></div><time class="shrink-0 text-xs text-body-text">{{ $time }}</time></li>
                    @endforeach
                </ol>
            </article>

            <article class="rounded-xl border border-border bg-white p-6 shadow-sm xl:col-span-2">
                <div class="flex items-start justify-between gap-4"><div><h2 class="text-lg font-semibold text-heading">Maintenance Status</h2><p class="mt-1 text-sm text-body-text">21 open campus requests</p></div><span class="rounded-full bg-orange-50 px-2.5 py-1 text-xs font-semibold text-orange-700">4 unassigned</span></div>
                <div class="mt-6 flex flex-col gap-4">
                    @foreach ([['Reported', 7, 33, 'bg-slate-400'], ['Assigned', 5, 24, 'bg-busitema-blue'], ['In progress', 5, 24, 'bg-busitema-gold'], ['Awaiting parts', 3, 14, 'bg-orange-500'], ['Overdue', 1, 5, 'bg-red-500']] as [$status, $count, $width, $color])
                        <div><div class="mb-1.5 flex items-center justify-between text-sm"><span class="text-body-text">{{ $status }}</span><span class="font-semibold text-heading">{{ $count }}</span></div><div class="h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full {{ $color }}" style="width: {{ $width }}%"></div></div></div>
                    @endforeach
                </div>
            </article>
        </section>

        <section class="grid gap-6 lg:grid-cols-2 xl:grid-cols-3">
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-heading">Agreement Expiries</h2><p class="mt-1 text-sm text-body-text">Renewals requiring campus follow-up</p>
                <div class="mt-6 grid grid-cols-3 divide-x divide-border rounded-xl border border-border bg-light-background py-4 text-center"><div><p class="text-xl font-semibold text-red-600">2</p><p class="mt-1 text-xs text-body-text">0–30 days</p></div><div><p class="text-xl font-semibold text-orange-600">1</p><p class="mt-1 text-xs text-body-text">31–60 days</p></div><div><p class="text-xl font-semibold text-amber-600">3</p><p class="mt-1 text-xs text-body-text">61–90 days</p></div></div>
                <p class="mt-5 rounded-lg bg-red-50 px-4 py-3 text-xs leading-5 text-body-text"><span class="font-semibold text-red-700">Action required:</span> Bookshop tenancy and Staff House 08 lease expire this month.</p>
            </article>

            <article class="rounded-xl border border-border bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-heading">Asset Condition</h2><p class="mt-1 text-sm text-body-text">Current campus portfolio health</p>
                <div class="mt-6 flex items-center gap-5"><div class="relative size-28 shrink-0 rounded-full" style="background: conic-gradient(#10b981 0 72%, #f9d028 72% 89%, #f97316 89% 98%, #ef4444 98% 100%);"><div class="absolute inset-5 flex items-center justify-center rounded-full bg-white text-xl font-semibold text-heading">816</div></div><div class="grid flex-1 gap-2.5 text-sm">@foreach ([['Good', '72%', 'bg-emerald-500'], ['Fair', '17%', 'bg-busitema-gold'], ['Poor', '9%', 'bg-orange-500'], ['Critical', '2%', 'bg-red-500']] as [$label, $value, $color])<div class="flex items-center justify-between"><span class="flex items-center gap-2 text-body-text"><span class="size-2 rounded-full {{ $color }}"></span>{{ $label }}</span><span class="font-semibold text-heading">{{ $value }}</span></div>@endforeach</div></div>
            </article>

            <article class="rounded-xl border border-border bg-white p-6 shadow-sm lg:col-span-2 xl:col-span-1">
                <h2 class="text-lg font-semibold text-heading">Occupancy Overview</h2><p class="mt-1 text-sm text-body-text">165 managed campus spaces</p>
                <div class="mt-6 flex items-center gap-5"><div class="flex size-28 shrink-0 items-center justify-center rounded-full border-[10px] border-busitema-blue bg-blue-50"><span class="text-2xl font-semibold text-heading">86%</span></div><div class="flex flex-1 flex-col gap-3"><div class="flex justify-between text-sm"><span class="text-body-text">Occupied</span><span class="font-semibold text-heading">142</span></div><div class="flex justify-between text-sm"><span class="text-body-text">Vacant</span><span class="font-semibold text-heading">18</span></div><div class="flex justify-between text-sm"><span class="text-body-text">Reserved</span><span class="font-semibold text-heading">5</span></div></div></div>
                <p class="mt-5 text-xs text-body-text">6 vacant spaces have pending allocation requests.</p>
            </article>
        </section>

        <section class="overflow-hidden rounded-xl border border-border bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-border px-6 py-5 sm:flex-row sm:items-center sm:justify-between"><div><h2 class="text-lg font-semibold text-heading">Upcoming Document Expiries</h2><p class="mt-1 text-sm text-body-text">Campus records that need renewal or replacement</p></div><button type="button" class="self-start rounded-lg border border-border px-3.5 py-2 text-sm font-semibold text-heading transition hover:border-busitema-blue hover:text-busitema-blue">View all documents</button></div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-3xl text-left text-sm">
                    <thead class="bg-light-background text-xs font-semibold tracking-wide text-body-text uppercase"><tr><th class="px-6 py-3">Document</th><th class="px-6 py-3">Related property</th><th class="px-6 py-3">Owner</th><th class="px-6 py-3">Expiry date</th><th class="px-6 py-3">Time remaining</th></tr></thead>
                    <tbody class="divide-y divide-border">
                        @foreach ([['Fire safety certificate', 'Science Block A', 'Facilities Office', '12 Sep 2026', '16 days', 'bg-red-50 text-red-700'], ['Motor vehicle insurance', 'Toyota Hilux · UBF 214K', 'Transport Office', '26 Sep 2026', '30 days', 'bg-orange-50 text-orange-700'], ['Trading licence', 'Campus Cafeteria', 'Commercial Services', '18 Oct 2026', '52 days', 'bg-amber-50 text-amber-700'], ['Environmental permit', 'Waste holding facility', 'Estates Office', '08 Nov 2026', '73 days', 'bg-blue-50 text-busitema-blue']] as [$document, $property, $owner, $date, $remaining, $classes])
                            <tr class="transition hover:bg-light-background/70"><td class="whitespace-nowrap px-6 py-4 font-semibold text-heading">{{ $document }}</td><td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $property }}</td><td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $owner }}</td><td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $date }}</td><td class="px-6 py-4"><span class="whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold {{ $classes }}">{{ $remaining }}</span></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
