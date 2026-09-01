@extends('layouts.app')

@section('title', 'Dashboard | UPAMS')
@section('page-heading', 'System Overview')

@section('content')
    <div class="flex flex-col gap-6">
        <section class="overflow-hidden rounded-2xl bg-busitema-deep-blue shadow-sm">
            <div class="relative px-6 py-7 sm:px-8">
                <div class="absolute -top-20 right-6 size-56 rounded-full bg-busitema-blue/30 blur-3xl" aria-hidden="true"></div>
                <div class="absolute -right-12 -bottom-28 size-56 rounded-full bg-busitema-gold/15 blur-3xl" aria-hidden="true"></div>
                <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold tracking-[0.14em] text-busitema-gold uppercase">System Administrator</p>
                        <h2 class="mt-2 text-2xl font-semibold text-white sm:text-3xl">University assets and system operations</h2>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-white/70">A consolidated view of asset records, account activity, operational attention items, and platform status across UPAMS.</p>
                    </div>
                    <span class="inline-flex w-fit items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-medium text-white/80"><span class="size-2 rounded-full bg-emerald-400"></span>Preview data · Updated moments ago</span>
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-border bg-white p-5 shadow-sm" aria-labelledby="dashboard-filters-heading">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end">
                <div class="xl:w-44 xl:shrink-0"><h2 class="text-base font-semibold text-heading" id="dashboard-filters-heading">Dashboard filters</h2><p class="mt-1 text-xs leading-5 text-body-text">Refine this overview.</p></div>
                <div class="grid flex-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                    @foreach ([
                        ['Campus', ['All campuses', 'Main Campus', 'Nagongera Campus', 'Arapai Campus']],
                        ['Asset Type', ['All asset types', 'Buildings & spaces', 'Equipment', 'Vehicles']],
                        ['Status', ['All statuses', 'Active / In use', 'Under maintenance', 'Poor / Critical']],
                        ['Faculty / Department', ['All units', 'Faculty of Engineering', 'Faculty of Agriculture', 'University Administration']],
                        ['Date Range', ['Current financial year', 'Last 30 days', 'Last quarter', 'Last 12 months']],
                    ] as [$filter, $options])
                        <label class="flex flex-col gap-1.5 text-xs font-semibold text-body-text">
                            {{ $filter }}
                            <select class="min-h-10 rounded-lg border border-border bg-white px-3 text-sm font-medium text-heading outline-none transition focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15">
                                @foreach ($options as $option)<option>{{ $option }}</option>@endforeach
                            </select>
                        </label>
                    @endforeach
                </div>
                <button class="inline-flex min-h-10 shrink-0 items-center justify-center rounded-lg border border-border px-4 text-sm font-semibold text-busitema-deep-blue transition hover:border-busitema-blue hover:text-busitema-blue" type="button">Reset</button>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="System administration key performance indicators">
            <x-stat-card label="Total Registered Assets" value="12,684" detail="Across 6 university campuses" tone="blue"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7.5 12 3l8 4.5M5 9v9l7 3 7-3V9M12 12l8-4.5M12 12 4 7.5M12 12v9" /></svg></x-stat-card>
            <x-stat-card label="Total System Users" value="248" detail="231 active user accounts" tone="purple"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" /></svg></x-stat-card>
            <x-stat-card label="Assets Under Maintenance" value="184" detail="27 high-priority work items" tone="orange"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.7 6.3 3-3a4 4 0 0 1-5 5l-7.4 7.4a2.1 2.1 0 0 0 3 3l7.4-7.4a4 4 0 0 0 5-5l-3 3-3-3Z" /></svg></x-stat-card>
            <x-stat-card label="Poor / Critical Assets" value="96" detail="0.8% of registered assets" tone="red"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3 2.5 20h19L12 3Zm0 6v5M12 17.5h.01" /></svg></x-stat-card>
            <x-stat-card label="Active Agreements" value="326" detail="Leases, allocations, and MOUs" tone="green"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l4 4v14H7V3Zm8 0v5h4M10 13h6M10 17h6" /></svg></x-stat-card>
            <x-stat-card label="Pending Approvals" value="31" detail="9 awaiting administrator review" tone="gold"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 11 12 14 22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" /></svg></x-stat-card>
            <x-stat-card label="Expiring Documents" value="42" detail="Due within the next 90 days" tone="orange"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3v3M18 3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Zm7 7v4l2 1" /></svg></x-stat-card>
            <x-stat-card label="Outstanding Alerts" value="17" detail="5 items marked as critical" tone="red"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" /></svg></x-stat-card>
        </section>

        <section class="grid gap-6 xl:grid-cols-5">
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm xl:col-span-2">
                <div class="flex items-start justify-between gap-4"><div><h2 class="text-lg font-semibold text-heading">Assets by Category</h2><p class="mt-1 text-sm text-body-text">Portfolio composition</p></div><span class="text-xs font-semibold text-busitema-blue">12,684 total</span></div>
                <div class="mt-6 flex flex-col items-center gap-6 sm:flex-row xl:flex-col 2xl:flex-row">
                    <div class="relative size-44 shrink-0 rounded-full" role="img" aria-label="Donut chart showing the distribution of assets by category" style="background: conic-gradient(#1e73be 0 32%, #13294b 32% 56%, #f9d028 56% 73%, #10b981 73% 88%, #f97316 88% 100%);"><div class="absolute inset-8 flex flex-col items-center justify-center rounded-full bg-white"><span class="text-2xl font-semibold text-heading">12.7K</span><span class="text-xs text-body-text">assets</span></div></div>
                    <div class="grid w-full gap-3">
                        @foreach ([['Buildings & spaces', '32%', '4,059', 'bg-busitema-blue'], ['Equipment & machinery', '24%', '3,044', 'bg-busitema-deep-blue'], ['Furniture & fittings', '17%', '2,156', 'bg-busitema-gold'], ['Vehicles', '15%', '1,903', 'bg-emerald-500'], ['Land & other property', '12%', '1,522', 'bg-orange-500']] as [$category, $share, $count, $color])
                            <div class="flex items-center justify-between gap-3 text-sm"><span class="flex min-w-0 items-center gap-2 text-body-text"><span class="size-2.5 shrink-0 rounded-full {{ $color }}"></span><span class="truncate">{{ $category }}</span></span><span class="shrink-0 font-semibold text-heading">{{ $share }} <small class="font-normal text-body-text">· {{ $count }}</small></span></div>
                        @endforeach
                    </div>
                </div>
            </article>

            <article class="rounded-xl border border-border bg-white p-6 shadow-sm xl:col-span-3">
                <div class="flex items-start justify-between gap-4"><div><h2 class="text-lg font-semibold text-heading">Assets by Campus</h2><p class="mt-1 text-sm text-body-text">Registered assets across university locations</p></div><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-busitema-blue">All campuses</span></div>
                <div class="mt-7 flex h-56 items-end gap-3 border-b border-border sm:gap-5" role="img" aria-label="Bar chart showing registered assets by campus">
                    @foreach ([['Main', 92, '3,842'], ['Nagongera', 71, '2,874'], ['Arapai', 59, '2,316'], ['Namasagali', 43, '1,628'], ['Mbale', 33, '1,274'], ['Pallisa', 20, '750']] as [$campus, $height, $count])
                        <div class="group flex h-full min-w-0 flex-1 flex-col items-center justify-end gap-2"><span class="text-[0.65rem] font-semibold text-heading opacity-0 transition group-hover:opacity-100 sm:text-xs">{{ $count }}</span><div class="w-full max-w-12 rounded-t-md bg-busitema-blue transition group-hover:bg-busitema-deep-blue" style="height: {{ $height }}%"></div><span class="w-full truncate pb-2 text-center text-[0.62rem] text-body-text sm:text-xs">{{ $campus }}</span></div>
                    @endforeach
                </div>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-5">
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm xl:col-span-3">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between"><div><h2 class="text-lg font-semibold text-heading">Asset Registration Trend</h2><p class="mt-1 text-sm text-body-text">New asset records added over the last 12 months</p></div><span class="text-xs font-semibold text-emerald-700">↑ 12.4% year over year</span></div>
                <div class="mt-6 overflow-hidden" role="img" aria-label="Line graph showing a gradual rise in monthly asset registrations">
                    <svg class="h-56 w-full" viewBox="0 0 720 230" preserveAspectRatio="none">
                        <defs><linearGradient id="registration-area" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#1e73be" stop-opacity="0.22" /><stop offset="100%" stop-color="#1e73be" stop-opacity="0" /></linearGradient></defs>
                        <g stroke="#e5e4e7" stroke-width="1"><line x1="42" y1="20" x2="704" y2="20" /><line x1="42" y1="72" x2="704" y2="72" /><line x1="42" y1="124" x2="704" y2="124" /><line x1="42" y1="176" x2="704" y2="176" /></g>
                        <path d="M42 176 L102 158 L162 164 L222 132 L282 142 L342 111 L402 119 L462 87 L522 98 L582 65 L642 74 L704 39 L704 198 L42 198 Z" fill="url(#registration-area)" />
                        <path d="M42 176 L102 158 L162 164 L222 132 L282 142 L342 111 L402 119 L462 87 L522 98 L582 65 L642 74 L704 39" fill="none" stroke="#1e73be" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        <g fill="#fff" stroke="#1e73be" stroke-width="3">@foreach ([[42,176],[102,158],[162,164],[222,132],[282,142],[342,111],[402,119],[462,87],[522,98],[582,65],[642,74],[704,39]] as [$x, $y])<circle cx="{{ $x }}" cy="{{ $y }}" r="4" />@endforeach</g>
                    </svg>
                    <div class="grid grid-cols-6 gap-2 text-center text-[0.65rem] text-body-text sm:grid-cols-12 sm:text-xs">@foreach (['Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'] as $month)<span class="{{ $loop->index < 6 ? 'hidden sm:block' : '' }}">{{ $month }}</span>@endforeach</div>
                </div>
            </article>

            <article class="rounded-xl border border-border bg-white p-6 shadow-sm xl:col-span-2">
                <div><h2 class="text-lg font-semibold text-heading">Asset Condition / Status</h2><p class="mt-1 text-sm text-body-text">Current recorded condition across the portfolio</p></div>
                <div class="mt-7 flex h-4 overflow-hidden rounded-full bg-slate-100" role="img" aria-label="Asset condition chart: 63 percent good, 22 percent fair, 10 percent under maintenance, 5 percent poor or critical"><span class="w-[63%] bg-emerald-500"></span><span class="w-[22%] bg-busitema-blue"></span><span class="w-[10%] bg-busitema-gold"></span><span class="w-[5%] bg-red-500"></span></div>
                <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2">
                    @foreach ([['Good / Operational', '7,991', '63%', 'bg-emerald-500'], ['Fair condition', '2,790', '22%', 'bg-busitema-blue'], ['Under maintenance', '1,269', '10%', 'bg-busitema-gold'], ['Poor / Critical', '634', '5%', 'bg-red-500']] as [$label, $count, $share, $color])
                        <div class="rounded-lg bg-light-background p-3"><p class="flex items-center gap-2 text-xs text-body-text"><span class="size-2.5 rounded-full {{ $color }}"></span>{{ $label }}</p><p class="mt-1.5 text-lg font-semibold text-heading">{{ $count }} <small class="font-normal text-body-text">({{ $share }})</small></p></div>
                    @endforeach
                </div>
                <p class="mt-5 rounded-lg border-l-4 border-red-500 bg-red-50 px-4 py-3 text-xs leading-5 text-body-text"><span class="font-semibold text-red-700">Review recommended:</span> 96 assets carry a critical-condition flag.</p>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-3">
            <article class="overflow-hidden rounded-xl border border-border bg-white shadow-sm xl:col-span-2">
                <div class="flex items-start justify-between gap-4 border-b border-border px-6 py-5"><div><h2 class="text-lg font-semibold text-heading">Attention Required</h2><p class="mt-1 text-sm text-body-text">Items approaching deadlines or awaiting action</p></div><span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-red-50 text-sm font-semibold text-red-700">38</span></div>
                <div class="grid divide-y divide-border md:grid-cols-2 md:divide-x md:divide-y-0">
                    <div class="divide-y divide-border">
                        @foreach ([['Expiring agreements', '7 renewals due within 30 days', '7', 'bg-red-50 text-red-700'], ['Documents & licences', '12 records expire within 60 days', '12', 'bg-orange-50 text-orange-700'], ['Vehicles due for service', '6 vehicles reached service thresholds', '6', 'bg-amber-50 text-amber-700']] as [$title, $detail, $count, $classes])
                            <div class="flex items-center gap-4 px-6 py-4"><span class="flex size-9 shrink-0 items-center justify-center rounded-lg text-sm font-semibold {{ $classes }}">{{ $count }}</span><div><p class="text-sm font-semibold text-heading">{{ $title }}</p><p class="mt-0.5 text-xs leading-5 text-body-text">{{ $detail }}</p></div></div>
                        @endforeach
                    </div>
                    <div class="divide-y divide-border">
                        @foreach ([['Laboratory calibration', '4 instruments require calibration', '4', 'bg-violet-50 text-violet-700'], ['Maintenance requests', '5 high-priority requests are open', '5', 'bg-blue-50 text-busitema-blue'], ['Approvals', '4 submissions await administrator review', '4', 'bg-emerald-50 text-emerald-700']] as [$title, $detail, $count, $classes])
                            <div class="flex items-center gap-4 px-6 py-4"><span class="flex size-9 shrink-0 items-center justify-center rounded-lg text-sm font-semibold {{ $classes }}">{{ $count }}</span><div><p class="text-sm font-semibold text-heading">{{ $title }}</p><p class="mt-0.5 text-xs leading-5 text-body-text">{{ $detail }}</p></div></div>
                        @endforeach
                    </div>
                </div>
            </article>

            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-1">
                <article class="rounded-xl border border-border bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4"><div><h2 class="text-lg font-semibold text-heading">User Accounts</h2><p class="mt-1 text-sm text-body-text">Account status overview</p></div><span class="text-xl font-semibold text-heading">248</span></div>
                    <div class="mt-5 flex h-3 overflow-hidden rounded-full bg-slate-100"><span class="w-[82%] bg-emerald-500"></span><span class="w-[11%] bg-busitema-gold"></span><span class="w-[7%] bg-slate-300"></span></div>
                    <div class="mt-5 grid grid-cols-3 gap-3 text-center"><div><p class="text-lg font-semibold text-emerald-700">203</p><p class="text-xs text-body-text">Active</p></div><div><p class="text-lg font-semibold text-amber-700">28</p><p class="text-xs text-body-text">New</p></div><div><p class="text-lg font-semibold text-slate-600">17</p><p class="text-xs text-body-text">Inactive</p></div></div>
                </article>
                <article class="rounded-xl border border-border bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4"><div><h2 class="text-lg font-semibold text-heading">System Health</h2><p class="mt-1 text-sm text-body-text">Platform services</p></div><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700"><span class="size-1.5 rounded-full bg-emerald-500"></span>Operational</span></div>
                    <dl class="mt-5 grid grid-cols-2 gap-3"><div class="rounded-lg bg-light-background p-3"><dt class="text-xs text-body-text">Availability</dt><dd class="mt-1 text-base font-semibold text-heading">99.98%</dd></div><div class="rounded-lg bg-light-background p-3"><dt class="text-xs text-body-text">Response</dt><dd class="mt-1 text-base font-semibold text-heading">184 ms</dd></div><div class="rounded-lg bg-light-background p-3"><dt class="text-xs text-body-text">Last backup</dt><dd class="mt-1 text-sm font-semibold text-heading">02:00 today</dd></div><div class="rounded-lg bg-light-background p-3"><dt class="text-xs text-body-text">Open incidents</dt><dd class="mt-1 text-sm font-semibold text-heading">0 critical</dd></div></dl>
                </article>
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-border bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-border px-6 py-5 sm:flex-row sm:items-center sm:justify-between"><div><h2 class="text-lg font-semibold text-heading">Recent System Activity / Audit Trail</h2><p class="mt-1 text-sm text-body-text">Latest illustrative actions recorded across UPAMS</p></div><span class="self-start rounded-full bg-light-background px-3 py-1.5 text-xs font-medium text-body-text">Showing recent activity</span></div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-4xl text-left text-sm">
                    <thead class="bg-light-background text-xs font-semibold tracking-wide text-body-text uppercase"><tr><th class="px-6 py-3">Date & Time</th><th class="px-6 py-3">User</th><th class="px-6 py-3">Action</th><th class="px-6 py-3">Module</th><th class="px-6 py-3">Reference</th><th class="px-6 py-3">Result</th></tr></thead>
                    <tbody class="divide-y divide-border">
                        @foreach ([['Today, 10:42', 'Sarah Namukasa', 'Updated asset condition', 'Asset Registry', 'AST-004821', 'Completed', 'bg-emerald-50 text-emerald-700'], ['Today, 09:18', 'Daniel Okello', 'Created user account', 'User Management', 'USR-0248', 'Completed', 'bg-emerald-50 text-emerald-700'], ['Yesterday, 16:34', 'Grace Atim', 'Submitted maintenance request', 'Maintenance', 'MNT-2026-184', 'Pending', 'bg-amber-50 text-amber-700'], ['Yesterday, 14:06', 'Peter Mugisha', 'Uploaded insurance document', 'Documents', 'DOC-2026-592', 'Completed', 'bg-emerald-50 text-emerald-700'], ['31 Aug, 11:27', 'System Service', 'Generated scheduled backup', 'System', 'BKP-20260901', 'Successful', 'bg-blue-50 text-busitema-blue']] as [$date, $user, $action, $module, $reference, $result, $classes])
                            <tr class="transition hover:bg-light-background/70"><td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $date }}</td><td class="whitespace-nowrap px-6 py-4 font-semibold text-heading">{{ $user }}</td><td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $action }}</td><td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $module }}</td><td class="whitespace-nowrap px-6 py-4 font-medium text-busitema-blue">{{ $reference }}</td><td class="whitespace-nowrap px-6 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $classes }}">{{ $result }}</span></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
