@extends('layouts.app')

@section('title', 'University Management Dashboard | UPAMS')
@section('page-heading', 'Executive Property Overview')
@section('portal-label', 'UPAMS University Management')
@section('user-role', 'University Management')
@section('user-initial', 'M')

@section('sidebar')
    @php
        $managementNavigationGroups = [
            ['label' => 'Executive View', 'items' => [
                ['label' => 'Dashboard', 'href' => route('management.dashboard'), 'pattern' => 'management.dashboard', 'route' => true],
                ['label' => 'Approvals', 'href' => url('/management/approvals'), 'pattern' => 'management/approvals*'],
                ['label' => 'Reports', 'href' => url('/management/reports'), 'pattern' => 'management/reports*'],
                ['label' => 'Maps', 'href' => url('/management/maps'), 'pattern' => 'management/maps*'],
            ]],
            ['label' => 'University Portfolio', 'items' => [
                ['label' => 'Asset Overview', 'href' => url('/management/assets'), 'pattern' => 'management/assets*'],
                ['label' => 'Land Overview', 'href' => url('/management/land'), 'pattern' => 'management/land*'],
                ['label' => 'Buildings & Spaces', 'href' => url('/management/buildings'), 'pattern' => 'management/buildings*'],
                ['label' => 'Laboratories', 'href' => url('/management/laboratories'), 'pattern' => 'management/laboratories*'],
                ['label' => 'Vehicles', 'href' => url('/management/vehicles'), 'pattern' => 'management/vehicles*'],
            ]],
            ['label' => 'Performance & Risk', 'items' => [
                ['label' => 'Revenue & Collections', 'href' => url('/management/revenue'), 'pattern' => 'management/revenue*'],
                ['label' => 'Arrears Overview', 'href' => url('/management/arrears'), 'pattern' => 'management/arrears*'],
                ['label' => 'Maintenance Overview', 'href' => url('/management/maintenance'), 'pattern' => 'management/maintenance*'],
                ['label' => 'Asset Condition', 'href' => url('/management/asset-condition'), 'pattern' => 'management/asset-condition*'],
                ['label' => 'Agreement Expiries', 'href' => url('/management/agreement-expiries'), 'pattern' => 'management/agreement-expiries*'],
            ]],
        ];
    @endphp

    <x-sidebar
        :navigation-groups="$managementNavigationGroups"
        aria-label="University Management navigation"
        sidebar-id="university-management-sidebar"
    />
@endsection

@section('content')
    <div class="flex flex-col gap-6" x-data="{ period: 'This financial year' }">
        <section class="overflow-hidden rounded-2xl bg-busitema-deep-blue shadow-sm">
            <div class="relative px-6 py-7 sm:px-8">
                <div class="absolute top-0 right-0 h-full w-2/5 bg-linear-to-l from-busitema-blue/25 to-transparent" aria-hidden="true"></div>
                <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold tracking-[0.14em] text-busitema-gold uppercase">Executive property brief</p>
                        <h2 class="mt-2 text-2xl font-semibold text-white sm:text-3xl">University assets, performance, and risk</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-white/70">A consolidated view for monitoring institutional property performance and prioritising management decisions.</p>
                    </div>
                    <label class="flex min-w-56 flex-col gap-1.5 text-xs font-medium text-white/70">
                        Reporting period
                        <select class="rounded-lg border border-white/20 bg-white/10 px-3 py-2.5 text-sm font-semibold text-white outline-none focus:border-busitema-gold focus:ring-2 focus:ring-busitema-gold/25" x-model="period">
                            <option class="text-heading">This financial year</option>
                            <option class="text-heading">This quarter</option>
                            <option class="text-heading">This month</option>
                        </select>
                    </label>
                </div>
            </div>
        </section>

        <div class="flex items-center justify-between gap-4">
            <div><h2 class="text-lg font-semibold text-heading">Institutional snapshot</h2><p class="mt-1 text-sm text-body-text">Illustrative management indicators for <span class="font-semibold text-heading" x-text="period.toLowerCase()"></span>.</p></div>
            <span class="hidden text-xs font-medium text-body-text sm:block">Executive dashboard · Preview data</span>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="University management key performance indicators">
            <x-stat-card label="Total University Assets" value="UGX 418.6B" detail="2,486 registered assets" tone="blue"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7.5 12 3l8 4.5M5 9v9l7 3 7-3V9M12 12l8-4.5M12 12 4 7.5M12 12v9" /></svg></x-stat-card>
            <x-stat-card label="Land Utilization" value="71%" detail="1,818 of 2,560 acres utilised" tone="green"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6.5 8 4l8 3 5-2.5v13L16 20l-8-3-5 2.5v-13ZM8 4v13M16 7v13" /></svg></x-stat-card>
            <x-stat-card label="Occupancy Rate" value="81%" detail="3 percentage points above target" tone="purple"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 21V5l8-2v18M4 21h16M16 8h4v13M8 8h.01M8 12h.01M8 16h.01" /></svg></x-stat-card>
            <x-stat-card label="Revenue Collection" value="UGX 6.8B" detail="87% of expected revenue" tone="green"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4V6Zm4 3h.01M16 15h.01M12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" /></svg></x-stat-card>
            <x-stat-card label="Outstanding Arrears" value="UGX 1.04B" detail="13% of annual billings" tone="orange"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M16 7.5c0-1.4-1.8-2.5-4-2.5S8 6.1 8 7.5 9.8 10 12 10s4 1.1 4 2.5S14.2 15 12 15s-4-1.1-4-2.5" /></svg></x-stat-card>
            <x-stat-card label="Assets in Poor/Critical Condition" value="23" detail="0.9% of the asset portfolio" tone="red"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3 2.5 20h19L12 3Zm0 6v5M12 17.5h.01" /></svg></x-stat-card>
            <x-stat-card label="Expiring Agreements" value="14" detail="Due within the next 90 days" tone="gold"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3v3M18 3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Zm7 7v4l2 1" /></svg></x-stat-card>
            <x-stat-card label="Pending Management Approvals" value="9" detail="3 classified as high priority" tone="purple"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 11.5 11 14l4-5M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3Z" /></svg></x-stat-card>
        </section>

        <section class="grid gap-6 xl:grid-cols-5">
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm xl:col-span-3">
                <div class="flex items-center justify-between gap-4"><div><h2 class="text-lg font-semibold text-heading">Assets by Campus</h2><p class="mt-1 text-sm text-body-text">Share of total university asset value</p></div><span class="text-xs font-semibold text-busitema-blue">UGX 418.6B</span></div>
                <div class="mt-6 grid h-52 grid-cols-6 items-end gap-3 sm:gap-5">
                    @foreach ([['Main', 92, '46%'], ['Nagongera', 70, '21%'], ['Arapai', 58, '14%'], ['Namasagali', 45, '10%'], ['Mbale', 31, '6%'], ['Pallisa', 20, '3%']] as [$campus, $height, $share])
                        <div class="flex h-full flex-col items-center justify-end gap-2"><span class="text-xs font-semibold text-heading">{{ $share }}</span><div class="w-full max-w-12 rounded-t-md bg-busitema-blue" style="height: {{ $height }}%"></div><span class="max-w-full truncate text-[0.68rem] text-body-text">{{ $campus }}</span></div>
                    @endforeach
                </div>
            </article>
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm xl:col-span-2">
                <h2 class="text-lg font-semibold text-heading">Asset Distribution</h2><p class="mt-1 text-sm text-body-text">Portfolio value by major category</p>
                <div class="mt-6 flex flex-col items-center gap-6 sm:flex-row xl:flex-col 2xl:flex-row">
                    <div class="relative size-40 shrink-0 rounded-full" style="background: conic-gradient(#13294b 0 43%, #1e73be 43% 69%, #f9d028 69% 83%, #10b981 83% 93%, #f97316 93% 100%);"><div class="absolute inset-7 flex flex-col items-center justify-center rounded-full bg-white"><span class="text-xl font-semibold text-heading">UGX</span><span class="text-sm text-body-text">418.6B</span></div></div>
                    <div class="grid w-full gap-3">
                        @foreach ([['Buildings', '43%', 'bg-busitema-deep-blue'], ['Land', '26%', 'bg-busitema-blue'], ['Equipment', '14%', 'bg-busitema-gold'], ['Vehicles', '10%', 'bg-emerald-500'], ['Other', '7%', 'bg-orange-500']] as [$category, $share, $color])
                            <div class="flex items-center justify-between text-sm"><span class="flex items-center gap-2 text-body-text"><span class="size-2.5 rounded-full {{ $color }}"></span>{{ $category }}</span><span class="font-semibold text-heading">{{ $share }}</span></div>
                        @endforeach
                    </div>
                </div>
            </article>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4"><div><h2 class="text-lg font-semibold text-heading">Revenue vs Expected</h2><p class="mt-1 text-sm text-body-text">Cumulative property revenue performance</p></div><span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">87% achieved</span></div>
                <div class="mt-6 grid grid-cols-2 gap-4"><div class="rounded-lg bg-light-background p-4"><p class="text-xs text-body-text">Collected</p><p class="mt-1 text-xl font-semibold text-heading">UGX 6.8B</p></div><div class="rounded-lg bg-light-background p-4"><p class="text-xs text-body-text">Expected</p><p class="mt-1 text-xl font-semibold text-heading">UGX 7.84B</p></div></div>
                <div class="mt-6 flex items-end justify-between gap-2" aria-label="Monthly revenue performance">
                    @foreach ([['Sep', 52, 63], ['Oct', 65, 72], ['Nov', 71, 78], ['Dec', 62, 70], ['Jan', 78, 84], ['Feb', 82, 90]] as [$month, $actual, $expected])
                        <div class="flex flex-1 flex-col items-center gap-2"><div class="flex h-24 items-end gap-1"><span class="w-2.5 rounded-t bg-busitema-blue" style="height: {{ $actual }}%"></span><span class="w-2.5 rounded-t bg-slate-200" style="height: {{ $expected }}%"></span></div><span class="text-[0.68rem] text-body-text">{{ $month }}</span></div>
                    @endforeach
                </div>
            </article>
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-heading">Land Utilization</h2><p class="mt-1 text-sm text-body-text">Strategic use of the university land portfolio</p>
                <div class="mt-7 flex h-4 overflow-hidden rounded-full bg-slate-100"><span class="w-[42%] bg-busitema-deep-blue"></span><span class="w-[29%] bg-busitema-blue"></span><span class="w-[18%] bg-busitema-gold"></span><span class="w-[11%] bg-slate-300"></span></div>
                <div class="mt-6 grid grid-cols-2 gap-5">
                    @foreach ([['Developed', '42%', '1,075 acres', 'bg-busitema-deep-blue'], ['Available', '29%', '742 acres', 'bg-busitema-blue'], ['Agricultural', '18%', '461 acres', 'bg-busitema-gold'], ['Protected/Other', '11%', '282 acres', 'bg-slate-300']] as [$label, $share, $acreage, $color])
                        <div><p class="flex items-center gap-2 text-xs text-body-text"><span class="size-2 rounded-full {{ $color }}"></span>{{ $label }}</p><p class="mt-1 font-semibold text-heading">{{ $share }} <span class="text-xs font-normal text-body-text">· {{ $acreage }}</span></p></div>
                    @endforeach
                </div>
                <div class="mt-6 rounded-lg border-l-4 border-busitema-blue bg-blue-50 px-4 py-3 text-xs leading-5 text-body-text"><span class="font-semibold text-heading">Management note:</span> 126 available acres are currently under allocation review.</div>
            </article>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between"><div><h2 class="text-lg font-semibold text-heading">Maintenance Trends</h2><p class="mt-1 text-sm text-body-text">Six-month work order movement</p></div><span class="text-xs font-semibold text-emerald-700">↓ 18% open items</span></div>
                <div class="mt-7 grid grid-cols-6 items-end gap-3 border-b border-border pb-1">
                    @foreach ([['Mar', 84, 62], ['Apr', 72, 58], ['May', 66, 61], ['Jun', 78, 70], ['Jul', 59, 72], ['Aug', 48, 76]] as [$month, $opened, $closed])
                        <div class="flex flex-col items-center gap-2"><div class="flex h-28 items-end gap-1"><span class="w-3 rounded-t bg-orange-300" style="height: {{ $opened }}%"></span><span class="w-3 rounded-t bg-busitema-blue" style="height: {{ $closed }}%"></span></div><span class="text-xs text-body-text">{{ $month }}</span></div>
                    @endforeach
                </div>
                <div class="mt-4 flex gap-5 text-xs text-body-text"><span class="flex items-center gap-2"><span class="size-2.5 rounded-sm bg-orange-300"></span>Opened</span><span class="flex items-center gap-2"><span class="size-2.5 rounded-sm bg-busitema-blue"></span>Resolved</span></div>
            </article>
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between"><div><h2 class="text-lg font-semibold text-heading">Asset Condition Trends</h2><p class="mt-1 text-sm text-body-text">Portfolio health movement</p></div><span class="text-xs font-semibold text-busitema-blue">Quarterly view</span></div>
                <div class="mt-7 flex flex-col gap-5">
                    @foreach ([['Good', '74%', '+4.2%', 'bg-emerald-500', 74], ['Fair', '25.1%', '-3.5%', 'bg-busitema-gold', 25], ['Poor/Critical', '0.9%', '-0.7%', 'bg-red-500', 6]] as [$condition, $share, $change, $color, $width])
                        <div><div class="mb-2 flex items-center justify-between text-sm"><span class="font-medium text-heading">{{ $condition }}</span><span><strong class="text-heading">{{ $share }}</strong> <span class="ml-2 text-xs text-body-text">{{ $change }}</span></span></div><div class="h-2.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full {{ $color }}" style="width: {{ $width }}%"></div></div></div>
                    @endforeach
                </div>
                <p class="mt-6 rounded-lg bg-light-background px-4 py-3 text-xs leading-5 text-body-text">Condition performance is improving, but 23 assets remain in the intervention category.</p>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-5">
            <article class="overflow-hidden rounded-xl border border-border bg-white shadow-sm xl:col-span-3">
                <div class="flex items-center justify-between border-b border-border px-6 py-5"><div><h2 class="text-lg font-semibold text-heading">Recent Approvals</h2><p class="mt-1 text-sm text-body-text">Latest management decisions</p></div><button type="button" class="text-sm font-semibold text-busitema-blue">Review pending</button></div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-2xl text-left text-sm">
                        <thead class="bg-light-background text-xs font-semibold tracking-wide text-body-text uppercase"><tr><th class="px-6 py-3">Decision</th><th class="px-6 py-3">Category</th><th class="px-6 py-3">Value</th><th class="px-6 py-3">Status</th></tr></thead>
                        <tbody class="divide-y divide-border">
                            @foreach ([['Rehabilitation of Engineering Block', 'Capital works', 'UGX 840M', 'Approved', 'bg-emerald-50 text-emerald-700'], ['Renewal of commercial leases', 'Agreements', 'UGX 126M', 'Approved', 'bg-emerald-50 text-emerald-700'], ['Allocation of Arapai research land', 'Land', '48 acres', 'Deferred', 'bg-amber-50 text-amber-700'], ['Fleet replacement proposal', 'Vehicles', 'UGX 1.2B', 'Approved', 'bg-emerald-50 text-emerald-700']] as [$decision, $category, $value, $status, $statusClass])
                                <tr><td class="px-6 py-4 font-medium text-heading">{{ $decision }}</td><td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $category }}</td><td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $value }}</td><td class="px-6 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $status }}</span></td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </article>
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm xl:col-span-2">
                <div class="flex items-start justify-between gap-4"><div><h2 class="text-lg font-semibold text-heading">Key Institutional Alerts</h2><p class="mt-1 text-sm text-body-text">Issues requiring management attention</p></div><span class="flex size-8 items-center justify-center rounded-full bg-red-50 text-sm font-semibold text-red-700">4</span></div>
                <div class="mt-5 flex flex-col gap-3">
                    @foreach ([['Critical', 'Structural assessment overdue', 'Old Administration Block · Main Campus', 'border-red-500 bg-red-50', 'text-red-700'], ['High', 'Revenue collection below target', 'Commercial units · Namasagali', 'border-orange-500 bg-orange-50', 'text-orange-700'], ['High', 'Three agreements expire this month', 'Management decision required', 'border-orange-500 bg-orange-50', 'text-orange-700'], ['Advisory', 'Land allocation review pending', '126 available acres under review', 'border-busitema-blue bg-blue-50', 'text-busitema-blue']] as [$level, $alert, $context, $classes, $labelClass])
                        <div class="rounded-lg border-l-4 p-3 {{ $classes }}"><p class="text-[0.68rem] font-bold tracking-wide uppercase {{ $labelClass }}">{{ $level }}</p><p class="mt-1 text-sm font-semibold text-heading">{{ $alert }}</p><p class="mt-1 text-xs text-body-text">{{ $context }}</p></div>
                    @endforeach
                </div>
            </article>
        </section>
    </div>
@endsection
