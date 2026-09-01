@extends('layouts.app')

@section('title', 'Finance Dashboard | UPAMS')
@section('page-heading', 'Financial Operations')
@section('portal-label', 'UPAMS Property Finance')
@section('user-role', 'Finance Officer')
@section('user-initial', 'F')

@section('sidebar')
    @php
        $financeNavigationGroups = [
            ['label' => 'Overview', 'items' => [
                ['label' => 'Dashboard', 'href' => route('finance.dashboard'), 'pattern' => 'finance.dashboard', 'route' => true],
                ['label' => 'Notifications', 'href' => url('/finance/notifications'), 'pattern' => 'finance/notifications*'],
            ]],
            ['label' => 'Revenue Operations', 'items' => [
                ['label' => 'Billing & Invoices', 'href' => url('/finance/invoices'), 'pattern' => 'finance/invoices*'],
                ['label' => 'Payments', 'href' => url('/finance/payments'), 'pattern' => 'finance/payments*'],
                ['label' => 'Deposits', 'href' => url('/finance/deposits'), 'pattern' => 'finance/deposits*'],
                ['label' => 'Arrears', 'href' => url('/finance/arrears'), 'pattern' => 'finance/arrears*'],
                ['label' => 'Utilities & Charges', 'href' => url('/finance/utilities'), 'pattern' => 'finance/utilities*'],
            ]],
            ['label' => 'Accounts & Reporting', 'items' => [
                ['label' => 'Tenants/Beneficiaries', 'href' => url('/finance/beneficiaries'), 'pattern' => 'finance/beneficiaries*'],
                ['label' => 'Agreements', 'href' => url('/finance/agreements'), 'pattern' => 'finance/agreements*'],
                ['label' => 'Financial Reports', 'href' => url('/finance/reports'), 'pattern' => 'finance/reports*'],
            ]],
        ];
    @endphp

    <x-sidebar
        :navigation-groups="$financeNavigationGroups"
        aria-label="Finance Officer navigation"
        sidebar-id="finance-officer-sidebar"
    />
@endsection

@section('content')
    <div class="flex flex-col gap-6" x-data="{ period: 'Current financial year' }">
        <section class="overflow-hidden rounded-2xl bg-busitema-blue shadow-sm">
            <div class="relative px-6 py-7 sm:px-8">
                <div class="absolute inset-y-0 right-0 w-1/2 bg-linear-to-l from-white/10 to-transparent" aria-hidden="true"></div>
                <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold tracking-[0.14em] text-busitema-gold uppercase">Property finance operations</p>
                        <h2 class="mt-2 text-2xl font-semibold text-white sm:text-3xl">Revenue, receivables, and collections</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-white/70">Monitor billing performance, incoming payments, arrears exposure, deposits, and utility charges across the university property portfolio.</p>
                    </div>
                    <label class="flex min-w-56 flex-col gap-1.5 text-xs font-medium text-white/70">
                        Reporting period
                        <select class="rounded-lg border border-white/20 bg-white/10 px-3 py-2.5 text-sm font-semibold text-white outline-none focus:border-busitema-gold focus:ring-2 focus:ring-busitema-gold/25" x-model="period">
                            <option class="text-heading">Current financial year</option>
                            <option class="text-heading">Current quarter</option>
                            <option class="text-heading">Current month</option>
                        </select>
                    </label>
                </div>
            </div>
        </section>

        <section>
            <div class="flex items-center justify-between gap-4"><div><h2 class="text-lg font-semibold text-heading">Quick actions</h2><p class="mt-1 text-sm text-body-text">Start a common finance task.</p></div><span class="hidden text-xs text-body-text sm:block" x-text="period"></span></div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ([
                    ['Create Invoice', 'M7 3h8l4 4v14H7V3Zm8 0v5h4M10 13h6M10 17h6', 'bg-blue-50 text-busitema-blue'],
                    ['Record Payment', 'M4 6h16v12H4V6Zm4 3h.01M16 15h.01M12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z', 'bg-emerald-50 text-emerald-700'],
                    ['View Arrears', 'M12 3v18M16 7.5c0-1.4-1.8-2.5-4-2.5S8 6.1 8 7.5 9.8 10 12 10s4 1.1 4 2.5S14.2 15 12 15s-4-1.1-4-2.5', 'bg-orange-50 text-orange-700'],
                    ['Generate Report', 'M5 3h14v18H5V3Zm4 5h6M9 12h6M9 16h4', 'bg-violet-50 text-violet-700'],
                ] as [$action, $path, $classes])
                    <button type="button" class="group flex min-h-20 items-center gap-4 rounded-xl border border-border bg-white p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-busitema-blue hover:shadow-md">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg {{ $classes }}"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}" /></svg></span>
                        <span class="text-sm font-semibold text-heading group-hover:text-busitema-blue">{{ $action }}</span>
                    </button>
                @endforeach
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Financial operations key performance indicators">
            <x-stat-card label="Expected Revenue" value="UGX 7.84B" detail="Annual billed property revenue" tone="blue"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V9m5 10V5m5 14v-7m5 7V3" /></svg></x-stat-card>
            <x-stat-card label="Revenue Collected" value="UGX 6.80B" detail="86.7% of expected revenue" tone="green"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4V6Zm4 3h.01M16 15h.01M12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" /></svg></x-stat-card>
            <x-stat-card label="Outstanding Balance" value="UGX 1.04B" detail="13.3% remains uncollected" tone="orange"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M16 7.5c0-1.4-1.8-2.5-4-2.5S8 6.1 8 7.5 9.8 10 12 10s4 1.1 4 2.5S14.2 15 12 15s-4-1.1-4-2.5" /></svg></x-stat-card>
            <x-stat-card label="Overdue Invoices" value="68" detail="UGX 692M overdue value" tone="red"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3v3M18 3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Zm7 7v4l2 1" /></svg></x-stat-card>
            <x-stat-card label="Total Arrears" value="UGX 514M" detail="Across 47 debtor accounts" tone="red"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3 2.5 20h19L12 3Zm0 6v5M12 17.5h.01" /></svg></x-stat-card>
            <x-stat-card label="Deposits Held" value="UGX 286M" detail="124 active security deposits" tone="purple"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8h16v11H4V8Zm3-3h10v3M8 12h8" /></svg></x-stat-card>
            <x-stat-card label="Utility Charges" value="UGX 147M" detail="Current month billed charges" tone="gold"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 2 5 14h7l-1 8 8-12h-7l1-8Z" /></svg></x-stat-card>
            <x-stat-card label="Recent Payments" value="42" detail="UGX 318M received this week" tone="green"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" /></svg></x-stat-card>
        </section>

        <section class="grid gap-6 xl:grid-cols-5">
            <article class="rounded-xl border border-border bg-white p-6 shadow-sm xl:col-span-3">
                <div class="flex items-start justify-between gap-4"><div><h2 class="text-lg font-semibold text-heading">Revenue vs Expected</h2><p class="mt-1 text-sm text-body-text">Performance by revenue stream</p></div><span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">86.7% collected</span></div>
                <div class="mt-6 grid grid-cols-3 divide-x divide-border rounded-xl border border-border bg-light-background py-4 text-center"><div><p class="text-lg font-semibold text-heading">UGX 7.84B</p><p class="mt-1 text-xs text-body-text">Expected</p></div><div><p class="text-lg font-semibold text-emerald-700">UGX 6.80B</p><p class="mt-1 text-xs text-body-text">Collected</p></div><div><p class="text-lg font-semibold text-orange-700">UGX 1.04B</p><p class="mt-1 text-xs text-body-text">Balance</p></div></div>
                <div class="mt-6 flex flex-col gap-4">
                    @foreach ([['Commercial rent', '2.86B', 93], ['Staff housing', '1.74B', 89], ['Land leases', '1.28B', 82], ['Utilities & service charges', '920M', 76]] as [$stream, $amount, $width])
                        <div><div class="mb-1.5 flex items-center justify-between text-sm"><span class="text-body-text">{{ $stream }}</span><span class="font-semibold text-heading">{{ $amount }} · {{ $width }}%</span></div><div class="h-2.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-busitema-blue" style="width: {{ $width }}%"></div></div></div>
                    @endforeach
                </div>
            </article>

            <article class="rounded-xl border border-border bg-white p-6 shadow-sm xl:col-span-2">
                <h2 class="text-lg font-semibold text-heading">Arrears Aging</h2><p class="mt-1 text-sm text-body-text">UGX 514M outstanding arrears</p>
                <div class="mt-6 flex h-4 overflow-hidden rounded-full bg-slate-100" aria-label="Arrears aging distribution"><span class="w-[28%] bg-busitema-gold"></span><span class="w-[24%] bg-orange-400"></span><span class="w-[18%] bg-orange-600"></span><span class="w-[30%] bg-red-600"></span></div>
                <div class="mt-6 flex flex-col gap-4">
                    @foreach ([['1–30 days', 'UGX 144M', '28%', 'bg-busitema-gold'], ['31–60 days', 'UGX 123M', '24%', 'bg-orange-400'], ['61–90 days', 'UGX 92M', '18%', 'bg-orange-600'], ['Over 90 days', 'UGX 155M', '30%', 'bg-red-600']] as [$age, $amount, $share, $color])
                        <div class="flex items-center justify-between gap-3 text-sm"><span class="flex items-center gap-2 text-body-text"><span class="size-2.5 rounded-full {{ $color }}"></span>{{ $age }}</span><span class="font-semibold text-heading">{{ $amount }} <small class="font-normal text-body-text">({{ $share }})</small></span></div>
                    @endforeach
                </div>
                <div class="mt-6 rounded-lg border-l-4 border-red-500 bg-red-50 px-4 py-3 text-xs leading-5 text-body-text"><span class="font-semibold text-red-700">Attention:</span> 15 accounts represent 72% of arrears older than 90 days.</div>
            </article>
        </section>

        <section class="overflow-hidden rounded-xl border border-border bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-border px-6 py-5 sm:flex-row sm:items-center sm:justify-between"><div><h2 class="text-lg font-semibold text-heading">Recent Payments</h2><p class="mt-1 text-sm text-body-text">Latest payments recorded across property accounts</p></div><button type="button" class="self-start text-sm font-semibold text-busitema-blue">View all payments</button></div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-4xl text-left text-sm">
                    <thead class="bg-light-background text-xs font-semibold tracking-wide text-body-text uppercase"><tr><th class="px-6 py-3">Receipt</th><th class="px-6 py-3">Payer</th><th class="px-6 py-3">Payment for</th><th class="px-6 py-3">Method</th><th class="px-6 py-3">Date</th><th class="px-6 py-3 text-right">Amount</th></tr></thead>
                    <tbody class="divide-y divide-border">
                        @foreach ([['RCT-2026-1842', 'Campus Bookshop', 'August commercial rent', 'Bank transfer', 'Today, 10:24', 'UGX 12,500,000'], ['RCT-2026-1841', 'Staff Member A.', 'Staff housing rent', 'Payroll', 'Today, 09:56', 'UGX 850,000'], ['RCT-2026-1840', 'Agro Research Partner', 'Land lease instalment', 'Bank transfer', 'Yesterday, 15:42', 'UGX 24,000,000'], ['RCT-2026-1839', 'Campus Cafeteria', 'Utilities and rent', 'Mobile money', 'Yesterday, 13:18', 'UGX 7,280,000']] as [$receipt, $payer, $purpose, $method, $date, $amount])
                            <tr class="transition hover:bg-light-background/70"><td class="whitespace-nowrap px-6 py-4 font-semibold text-busitema-blue">{{ $receipt }}</td><td class="whitespace-nowrap px-6 py-4 font-medium text-heading">{{ $payer }}</td><td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $purpose }}</td><td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $method }}</td><td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $date }}</td><td class="whitespace-nowrap px-6 py-4 text-right font-semibold text-heading">{{ $amount }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <article class="overflow-hidden rounded-xl border border-border bg-white shadow-sm">
                <div class="border-b border-border px-6 py-5"><h2 class="text-lg font-semibold text-heading">Overdue Invoices</h2><p class="mt-1 text-sm text-body-text">Highest-value invoices requiring follow-up</p></div>
                <div class="divide-y divide-border">
                    @foreach ([['INV-2026-0814', 'University Guest House', 'UGX 48.2M', '42 days', 'bg-red-50 text-red-700'], ['INV-2026-0772', 'Agro Research Partner', 'UGX 36.0M', '67 days', 'bg-red-50 text-red-700'], ['INV-2026-0861', 'Campus Cafeteria', 'UGX 18.4M', '24 days', 'bg-orange-50 text-orange-700'], ['INV-2026-0903', 'Staff Housing Account', 'UGX 12.8M', '11 days', 'bg-amber-50 text-amber-700']] as [$invoice, $debtor, $amount, $overdue, $classes])
                        <div class="flex items-center gap-4 px-6 py-4"><div class="min-w-0 flex-1"><p class="text-sm font-semibold text-heading">{{ $invoice }} · {{ $debtor }}</p><p class="mt-1 text-xs text-body-text">{{ $amount }} outstanding</p></div><span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $classes }}">{{ $overdue }}</span></div>
                    @endforeach
                </div>
            </article>

            <article class="rounded-xl border border-border bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4"><div><h2 class="text-lg font-semibold text-heading">Collection Trends</h2><p class="mt-1 text-sm text-body-text">Monthly collections against target</p></div><span class="text-xs font-semibold text-emerald-700">↑ 8.4%</span></div>
                <div class="mt-7 grid grid-cols-6 items-end gap-3 border-b border-border pb-1">
                    @foreach ([['Mar', 62, 72], ['Apr', 68, 75], ['May', 74, 78], ['Jun', 69, 79], ['Jul', 81, 84], ['Aug', 87, 90]] as [$month, $collected, $target])
                        <div class="flex flex-col items-center gap-2"><div class="flex h-28 items-end gap-1"><span class="w-3 rounded-t bg-busitema-blue" style="height: {{ $collected }}%"></span><span class="w-3 rounded-t bg-slate-200" style="height: {{ $target }}%"></span></div><span class="text-xs text-body-text">{{ $month }}</span></div>
                    @endforeach
                </div>
                <div class="mt-4 flex gap-5 text-xs text-body-text"><span class="flex items-center gap-2"><span class="size-2.5 rounded-sm bg-busitema-blue"></span>Collected</span><span class="flex items-center gap-2"><span class="size-2.5 rounded-sm bg-slate-200"></span>Target</span></div>
            </article>
        </section>

        <section class="rounded-xl border border-border bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4"><div><h2 class="text-lg font-semibold text-heading">Financial Alerts</h2><p class="mt-1 text-sm text-body-text">Items requiring finance follow-up</p></div><span class="flex size-8 items-center justify-center rounded-full bg-red-50 text-sm font-semibold text-red-700">4</span></div>
            <div class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                @foreach ([['Critical', '15 long-outstanding accounts', 'UGX 112M requires escalation', 'border-red-500 bg-red-50', 'text-red-700'], ['High', '68 overdue invoices', 'UGX 692M awaiting collection', 'border-orange-500 bg-orange-50', 'text-orange-700'], ['Review', 'Unallocated payment received', 'UGX 8.4M needs reconciliation', 'border-busitema-gold bg-amber-50', 'text-amber-700'], ['Due soon', 'Monthly utility billing', 'Meter readings due in 3 days', 'border-busitema-blue bg-blue-50', 'text-busitema-blue']] as [$level, $alert, $context, $classes, $labelClass])
                    <div class="rounded-lg border-l-4 p-4 {{ $classes }}"><p class="text-[0.68rem] font-bold tracking-wide uppercase {{ $labelClass }}">{{ $level }}</p><p class="mt-1.5 text-sm font-semibold text-heading">{{ $alert }}</p><p class="mt-1 text-xs leading-5 text-body-text">{{ $context }}</p></div>
                @endforeach
            </div>
        </section>
    </div>
@endsection
