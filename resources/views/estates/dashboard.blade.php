@extends('layouts.app')

@section('title', 'Estates Dashboard | UPAMS')
@section('page-heading', 'Property & Estates Overview')
@section('portal-label', 'UPAMS Estates Management')
@section('user-role', 'University Estates Manager')
@section('user-initial', 'E')

@section('sidebar')
    @php
        $estatesNavigationGroups = [
            ['label' => 'Overview', 'items' => [
                ['label' => 'Dashboard', 'href' => route('estates.dashboard'), 'pattern' => 'estates.dashboard', 'route' => true],
                ['label' => 'Maps/Location View', 'href' => url('/estates/maps'), 'pattern' => 'estates/maps*'],
            ]],
            ['label' => 'Property Portfolio', 'items' => [
                ['label' => 'Asset Registry', 'href' => url('/estates/assets'), 'pattern' => 'estates/assets*'],
                ['label' => 'Land Management', 'href' => url('/estates/land'), 'pattern' => 'estates/land*'],
                ['label' => 'Buildings & Spaces', 'href' => url('/estates/buildings'), 'pattern' => 'estates/buildings*'],
                ['label' => 'Laboratories & Equipment', 'href' => url('/estates/laboratories'), 'pattern' => 'estates/laboratories*'],
                ['label' => 'Vehicles', 'href' => url('/estates/vehicles'), 'pattern' => 'estates/vehicles*'],
                ['label' => 'Commercial Property', 'href' => url('/estates/commercial-property'), 'pattern' => 'estates/commercial-property*'],
                ['label' => 'Agricultural Property', 'href' => url('/estates/agricultural-property'), 'pattern' => 'estates/agricultural-property*'],
            ]],
            ['label' => 'Occupancy & Operations', 'items' => [
                ['label' => 'Agreements & Allocations', 'href' => url('/estates/agreements'), 'pattern' => 'estates/agreements*'],
                ['label' => 'Tenants/Beneficiaries', 'href' => url('/estates/beneficiaries'), 'pattern' => 'estates/beneficiaries*'],
                ['label' => 'Inspections', 'href' => url('/estates/inspections'), 'pattern' => 'estates/inspections*'],
                ['label' => 'Maintenance', 'href' => url('/estates/maintenance'), 'pattern' => 'estates/maintenance*'],
                ['label' => 'Documents', 'href' => url('/estates/documents'), 'pattern' => 'estates/documents*'],
            ]],
            ['label' => 'Governance & Insight', 'items' => [
                ['label' => 'Approvals', 'href' => url('/estates/approvals'), 'pattern' => 'estates/approvals*'],
                ['label' => 'Notifications', 'href' => url('/estates/notifications'), 'pattern' => 'estates/notifications*'],
                ['label' => 'Reports', 'href' => url('/estates/reports'), 'pattern' => 'estates/reports*'],
                ['label' => 'Audit History', 'href' => url('/estates/audit-history'), 'pattern' => 'estates/audit-history*'],
            ]],
        ];
    @endphp

    <x-sidebar
        :navigation-groups="$estatesNavigationGroups"
        aria-label="University Property / Estates Manager navigation"
        sidebar-id="estates-manager-sidebar"
    />
@endsection

@section('content')
    <div class="flex flex-col gap-6" x-data="{ campus: 'All campuses' }">
        <section class="overflow-hidden rounded-2xl bg-busitema-blue text-white shadow-sm">
            <div class="relative px-6 py-7 sm:px-8">
                <div class="absolute -top-16 right-4 size-48 rounded-full bg-white/10 blur-2xl" aria-hidden="true"></div>
                <div class="absolute -right-8 -bottom-24 size-52 rounded-full bg-busitema-gold/15 blur-2xl" aria-hidden="true"></div>
                <div class="relative flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-white sm:text-3xl">Property portfolio at a glance</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-white/70">Monitor land, buildings, facilities, equipment, vehicles, occupancy, agreements, and maintenance across every campus.</p>
                    </div>
                    <label class="flex min-w-56 flex-col gap-1.5 text-xs font-medium text-white/70">
                        Campus view
                        <select class="rounded-lg border border-white/20 bg-white/10 px-3 py-2.5 text-sm font-semibold text-white outline-none focus:border-busitema-gold focus:ring-2 focus:ring-busitema-gold/25" x-model="campus">
                            <option class="text-heading">All campuses</option>
                            <option class="text-heading">Main Campus</option>
                            <option class="text-heading">Nagongera Campus</option>
                            <option class="text-heading">Namasagali Campus</option>
                            <option class="text-heading">Arapai Campus</option>
                        </select>
                    </label>
                </div>
            </div>
        </section>

        <div class="flex items-center justify-between gap-4">
            <div><h2 class="text-lg font-semibold text-heading">Portfolio summary</h2><p class="mt-1 text-sm text-body-text">Illustrative figures for <span class="font-semibold text-heading" x-text="campus"></span>.</p></div>
            <span class="hidden rounded-full border border-border bg-white px-3 py-1.5 text-xs font-medium text-body-text sm:inline-flex">Last refreshed · Preview data</span>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Property portfolio key performance indicators">
            <x-stat-card label="Total Assets" value="2,486" detail="Across all asset categories" tone="blue"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7.5 12 3l8 4.5M5 9v9l7 3 7-3V9M12 12l8-4.5M12 12 4 7.5M12 12v9" /></svg></x-stat-card>
            <x-stat-card label="Total Campuses" value="6" detail="Portfolio coverage locations" tone="purple"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m3 21 9-18 9 18M7 14h10M5 18h14" /></svg></x-stat-card>
            <x-stat-card label="Available Land" value="742 acres" detail="29% of surveyed land" tone="green"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6.5 8 4l8 3 5-2.5v13L16 20l-8-3-5 2.5v-13ZM8 4v13M16 7v13" /></svg></x-stat-card>
            <x-stat-card label="Occupied Spaces" value="81%" detail="428 of 528 managed spaces" tone="gold"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 21V5l8-2v18M4 21h16M16 8h4v13M8 8h.01M8 12h.01M8 16h.01" /></svg></x-stat-card>
            <x-stat-card label="Assets Under Maintenance" value="37" detail="12 marked as high priority" tone="orange"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.7 6.3 3-3a4 4 0 0 1-5 5l-7.4 7.4a2.1 2.1 0 0 0 3 3l7.4-7.4a4 4 0 0 0 5-5l-3 3-3-3Z" /></svg></x-stat-card>
            <x-stat-card label="Active Agreements" value="164" detail="Leases, allocations, and MOUs" tone="green"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l4 4v14H7V3Zm8 0v5h4M10 13h6M10 17h6" /></svg></x-stat-card>
            <x-stat-card label="Expiring Agreements" value="14" detail="Due within the next 90 days" tone="gold"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3v3M18 3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Zm7 7v4l2 1" /></svg></x-stat-card>
            <x-stat-card label="Poor/Critical Condition Assets" value="23" detail="Requires assessment or intervention" tone="red"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3 2.5 20h19L12 3Zm0 6v5M12 17.5h.01" /></svg></x-stat-card>
        </section>

        <section class="grid gap-6 xl:grid-cols-5">
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm xl:col-span-3">
                <div class="flex items-center justify-between gap-4"><div><h2 class="text-lg font-semibold text-heading">Assets by Campus</h2><p class="mt-1 text-sm text-body-text">Registered portfolio distribution</p></div><span class="text-xs font-semibold text-busitema-blue">2,486 total</span></div>
                <div class="mt-6 flex flex-col gap-4">
                    @foreach ([['Main Campus', 82, '816'], ['Nagongera', 63, '624'], ['Arapai', 49, '487'], ['Namasagali', 34, '338'], ['Mbale', 16, '159'], ['Pallisa', 6, '62']] as [$campusName, $width, $count])
                        <div class="grid grid-cols-[6.5rem_1fr_2.5rem] items-center gap-3 text-sm sm:grid-cols-[8rem_1fr_3rem]"><span class="truncate font-medium text-heading">{{ $campusName }}</span><div class="h-2.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-busitema-blue" style="width: {{ $width }}%"></div></div><span class="text-right text-xs font-semibold text-body-text">{{ $count }}</span></div>
                    @endforeach
                </div>
            </article>
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm xl:col-span-2">
                <h2 class="text-lg font-semibold text-heading">Assets by Category</h2><p class="mt-1 text-sm text-body-text">Composition of registered assets</p>
                <div class="mt-6 flex flex-col items-center gap-6 sm:flex-row xl:flex-col 2xl:flex-row">
                    <div class="relative size-40 shrink-0 rounded-full" style="background: conic-gradient(#1e73be 0 36%, var(--chart-secondary-blue) 36% 61%, #f9d028 61% 78%, #10b981 78% 91%, #f97316 91% 100%);"><div class="absolute inset-7 flex flex-col items-center justify-center rounded-full bg-white"><span class="text-2xl font-semibold text-heading">2,486</span><span class="text-xs text-body-text">assets</span></div></div>
                    <div class="grid w-full gap-3">
                        @foreach ([['Buildings & spaces', '36%', 'bg-busitema-blue'], ['Equipment', '25%', 'bg-busitema-deep-blue'], ['Land parcels', '17%', 'bg-busitema-gold'], ['Vehicles', '13%', 'bg-emerald-500'], ['Other property', '9%', 'bg-orange-500']] as [$category, $share, $color])
                            <div class="flex items-center justify-between gap-3 text-sm"><span class="flex items-center gap-2 text-body-text"><span class="size-2.5 rounded-full {{ $color }}"></span>{{ $category }}</span><span class="font-semibold text-heading">{{ $share }}</span></div>
                        @endforeach
                    </div>
                </div>
            </article>
        </section>

        <section class="grid gap-6 lg:grid-cols-2 xl:grid-cols-3">
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-heading">Land Utilization</h2><p class="mt-1 text-sm text-body-text">2,560 surveyed acres</p>
                <div class="mt-6 flex h-3 overflow-hidden rounded-full bg-slate-100" aria-label="Land utilization breakdown"><span class="w-[42%] bg-busitema-blue"></span><span class="w-[29%] bg-emerald-500"></span><span class="w-[18%] bg-busitema-gold"></span><span class="w-[11%] bg-slate-300"></span></div>
                <div class="mt-5 grid grid-cols-2 gap-4">
                    @foreach ([['Developed', '1,075 acres', 'bg-busitema-blue'], ['Available', '742 acres', 'bg-emerald-500'], ['Agricultural', '461 acres', 'bg-busitema-gold'], ['Restricted', '282 acres', 'bg-slate-300']] as [$label, $value, $color])
                        <div><p class="flex items-center gap-2 text-xs text-body-text"><span class="size-2 rounded-full {{ $color }}"></span>{{ $label }}</p><p class="mt-1 text-sm font-semibold text-heading">{{ $value }}</p></div>
                    @endforeach
                </div>
            </article>
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-heading">Occupancy Overview</h2><p class="mt-1 text-sm text-body-text">Managed buildings and rentable spaces</p>
                <div class="mt-6 flex items-center gap-5"><div class="flex size-28 shrink-0 items-center justify-center rounded-full border-[10px] border-busitema-blue bg-blue-50"><span class="text-2xl font-semibold text-heading">81%</span></div><div class="flex flex-1 flex-col gap-3"><div class="flex justify-between text-sm"><span class="text-body-text">Occupied</span><span class="font-semibold text-heading">428</span></div><div class="flex justify-between text-sm"><span class="text-body-text">Available</span><span class="font-semibold text-heading">74</span></div><div class="flex justify-between text-sm"><span class="text-body-text">Reserved</span><span class="font-semibold text-heading">26</span></div></div></div>
                <p class="mt-5 rounded-lg bg-light-background px-4 py-3 text-xs leading-5 text-body-text">Commercial units have the highest occupancy rate at <span class="font-semibold text-heading">92%</span>.</p>
            </article>
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm lg:col-span-2 xl:col-span-1">
                <h2 class="text-lg font-semibold text-heading">Maintenance Status</h2><p class="mt-1 text-sm text-body-text">Open work across the portfolio</p>
                <div class="mt-5 flex flex-col gap-4">
                    @foreach ([['Scheduled', 18, 49, 'bg-busitema-blue'], ['In progress', 11, 30, 'bg-busitema-gold'], ['Awaiting parts', 5, 14, 'bg-orange-500'], ['Overdue', 3, 8, 'bg-red-500']] as [$status, $count, $width, $color])
                        <div><div class="mb-1.5 flex items-center justify-between text-sm"><span class="text-body-text">{{ $status }}</span><span class="font-semibold text-heading">{{ $count }}</span></div><div class="h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full {{ $color }}" style="width: {{ $width }}%"></div></div></div>
                    @endforeach
                </div>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-3">
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4"><div><h2 class="text-lg font-semibold text-heading">Agreement Expiries</h2><p class="mt-1 text-sm text-body-text">Renewal pipeline by period</p></div><span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">14 upcoming</span></div>
                <div class="mt-6 grid grid-cols-3 divide-x divide-border rounded-xl border border-border bg-light-background py-4 text-center"><div><p class="text-xl font-semibold text-red-600">3</p><p class="mt-1 text-xs text-body-text">0–30 days</p></div><div><p class="text-xl font-semibold text-orange-600">5</p><p class="mt-1 text-xs text-body-text">31–60 days</p></div><div><p class="text-xl font-semibold text-amber-600">6</p><p class="mt-1 text-xs text-body-text">61–90 days</p></div></div>
                <div class="mt-5 rounded-lg border-l-4 border-busitema-gold bg-amber-50 px-4 py-3"><p class="text-sm font-semibold text-heading">Next action</p><p class="mt-1 text-xs leading-5 text-body-text">Three agreements need renewal decisions before the end of this month.</p></div>
            </article>
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm xl:col-span-2">
                <div class="flex items-center justify-between gap-4"><div><h2 class="text-lg font-semibold text-heading">Recent Activity</h2><p class="mt-1 text-sm text-body-text">Latest portfolio updates</p></div><button class="text-sm font-semibold text-busitema-blue hover:text-busitema-deep-blue" type="button">View all</button></div>
                <ol class="mt-5 divide-y divide-border">
                    @foreach ([['Inspection completed', 'Engineering Block · Main Campus', '12 min ago', 'bg-emerald-500'], ['Land parcel record updated', 'Parcel NG-014 · Nagongera', '48 min ago', 'bg-busitema-blue'], ['Maintenance request escalated', 'Science Laboratory 2 · Arapai', '2 hrs ago', 'bg-orange-500'], ['Agreement submitted for approval', 'Commercial Unit CU-028', 'Yesterday', 'bg-violet-500']] as [$activity, $subject, $time, $color])
                        <li class="flex gap-3 py-3 first:pt-0 last:pb-0"><span class="mt-1.5 size-2.5 shrink-0 rounded-full {{ $color }}"></span><div class="min-w-0 flex-1"><p class="text-sm font-semibold text-heading">{{ $activity }}</p><p class="mt-0.5 truncate text-xs text-body-text">{{ $subject }}</p></div><time class="shrink-0 text-xs text-body-text">{{ $time }}</time></li>
                    @endforeach
                </ol>
            </article>
        </section>

        <section class="overflow-hidden rounded-xl border border-border bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-border px-6 py-5 sm:flex-row sm:items-center sm:justify-between"><div><h2 class="text-lg font-semibold text-heading">Upcoming Expiries</h2><p class="mt-1 text-sm text-body-text">Agreements and documents requiring timely action</p></div><button class="self-start rounded-lg border border-border bg-white px-3.5 py-2 text-sm font-semibold text-heading transition hover:border-busitema-blue hover:text-busitema-blue" type="button">Review all expiries</button></div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-3xl text-left">
                    <thead class="bg-light-background text-xs font-semibold tracking-wide text-body-text uppercase"><tr><th class="px-6 py-3">Agreement / Item</th><th class="px-6 py-3">Property</th><th class="px-6 py-3">Beneficiary</th><th class="px-6 py-3">Expiry date</th><th class="px-6 py-3">Status</th></tr></thead>
                    <tbody class="divide-y divide-border text-sm">
                        @foreach ([['Lease · AG-2026-041', 'Staff Housing SH-12', 'University Staff Member', '08 Sep 2026', '12 days', 'bg-red-50 text-red-700'], ['Allocation · AL-2025-118', 'Agricultural Plot AP-07', 'Faculty of Agriculture', '21 Sep 2026', '25 days', 'bg-orange-50 text-orange-700'], ['Tenancy · TN-2026-009', 'Commercial Unit CU-028', 'Campus Bookshop', '14 Oct 2026', '48 days', 'bg-amber-50 text-amber-700'], ['MOU · MO-2024-016', 'Research Laboratory 3', 'Research Partner', '02 Nov 2026', '67 days', 'bg-blue-50 text-busitema-blue']] as [$reference, $property, $beneficiary, $date, $remaining, $statusClass])
                            <tr class="transition hover:bg-light-background/70"><td class="whitespace-nowrap px-6 py-4 font-semibold text-heading">{{ $reference }}</td><td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $property }}</td><td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $beneficiary }}</td><td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $date }}</td><td class="whitespace-nowrap px-6 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $remaining }}</span></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
