@props([
    'navigationGroups' => null,
    'ariaLabel' => 'System Administrator navigation',
    'sidebarId' => 'administrator-sidebar',
])

@php
    $navigationGroups ??= [
        ['label' => 'Overview', 'items' => [
            ['label' => 'Dashboard', 'href' => route('dashboard'), 'pattern' => 'dashboard', 'route' => true],
        ]],
        ['label' => 'Asset Management', 'items' => [
            ['label' => 'Asset Registry', 'href' => url('/assets'), 'pattern' => 'assets*'],
            ['label' => 'Land Management', 'href' => url('/land'), 'pattern' => 'land*'],
            ['label' => 'Buildings & Spaces', 'href' => url('/buildings'), 'pattern' => 'buildings*'],
            ['label' => 'Laboratories & Equipment', 'href' => url('/laboratories'), 'pattern' => 'laboratories*'],
            ['label' => 'Vehicles', 'href' => url('/vehicles'), 'pattern' => 'vehicles*'],
            ['label' => 'Commercial Property', 'href' => url('/commercial-property'), 'pattern' => 'commercial-property*'],
            ['label' => 'Agricultural Property', 'href' => url('/agricultural-property'), 'pattern' => 'agricultural-property*'],
        ]],
        ['label' => 'Operations', 'items' => [
            ['label' => 'Agreements & Allocations', 'href' => url('/agreements'), 'pattern' => 'agreements*'],
            ['label' => 'Tenants/Beneficiaries', 'href' => url('/beneficiaries'), 'pattern' => 'beneficiaries*'],
            ['label' => 'Inspections', 'href' => url('/inspections'), 'pattern' => 'inspections*'],
            ['label' => 'Maintenance', 'href' => url('/maintenance'), 'pattern' => 'maintenance*'],
            ['label' => 'Documents', 'href' => url('/documents'), 'pattern' => 'documents*'],
        ]],
        ['label' => 'Finance & Utilities', 'items' => [
            ['label' => 'Billing & Invoices', 'href' => url('/billing'), 'pattern' => 'billing*'],
            ['label' => 'Payments', 'href' => url('/payments'), 'pattern' => 'payments*'],
            ['label' => 'Arrears', 'href' => url('/arrears'), 'pattern' => 'arrears*'],
            ['label' => 'Utilities', 'href' => url('/utilities'), 'pattern' => 'utilities*'],
        ]],
        ['label' => 'Governance', 'items' => [
            ['label' => 'Approvals', 'href' => url('/approvals'), 'pattern' => 'approvals*'],
            ['label' => 'Notifications', 'href' => url('/notifications'), 'pattern' => 'notifications*'],
            ['label' => 'Reports', 'href' => url('/reports'), 'pattern' => 'reports*'],
            ['label' => 'Audit Trail', 'href' => url('/audit-trail'), 'pattern' => 'audit-trail*'],
        ]],
        ['label' => 'Administration', 'items' => [
            ['label' => 'Users', 'href' => route('users.index'), 'pattern' => 'users.*', 'route' => true],
            ['label' => 'Roles & Permissions', 'href' => url('/roles'), 'pattern' => 'roles*'],
            ['label' => 'Campuses', 'href' => url('/campuses'), 'pattern' => 'campuses*'],
            ['label' => 'Organizational Units', 'href' => url('/organizational-units'), 'pattern' => 'organizational-units*'],
            ['label' => 'Asset Categories', 'href' => url('/asset-categories'), 'pattern' => 'asset-categories*'],
            ['label' => 'System Settings', 'href' => url('/settings'), 'pattern' => 'settings*'],
        ]],
    ];
@endphp

<div {{ $attributes->merge(['class' => 'contents']) }}>
    <header class="fixed inset-x-0 top-0 z-30 flex h-16 items-center justify-between border-b border-border bg-white px-4 shadow-sm lg:hidden">
        <img class="h-auto w-44" src="{{ asset('images/busitema-logo.png') }}" alt="Busitema University">

        <button
            class="inline-flex size-10 items-center justify-center rounded-lg text-busitema-deep-blue transition hover:bg-light-background focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-busitema-blue"
            type="button"
            @click="sidebarOpen = true"
            aria-label="Open navigation"
            aria-controls="{{ $sidebarId }}"
            :aria-expanded="sidebarOpen"
        >
            <svg class="size-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
            </svg>
        </button>
    </header>

    <div
        x-cloak
        x-show="sidebarOpen"
        x-transition.opacity
        class="fixed inset-0 z-40 bg-busitema-black/45 lg:hidden"
        @click="sidebarOpen = false"
        aria-hidden="true"
    ></div>

    <aside
        id="{{ $sidebarId }}"
        class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-busitema-blue shadow-xl transition-[width,transform] duration-200 ease-out lg:translate-x-0 lg:shadow-none"
        :class="[
            sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
            sidebarCollapsed ? 'lg:w-20' : 'lg:w-72'
        ]"
        aria-label="{{ $ariaLabel }}"
    >
        <div class="flex min-h-24 items-center gap-3 border-b border-white/15 px-4 py-4">
            <div
                class="min-w-0 flex-1 overflow-hidden rounded-xl bg-white p-3"
                :class="sidebarCollapsed && ! sidebarOpen ? 'lg:p-2' : ''"
            >
                <img
                    class="h-11 w-full object-contain object-left"
                    :class="sidebarCollapsed && ! sidebarOpen ? 'lg:object-cover' : ''"
                    src="{{ asset('images/busitema-logo.png') }}"
                    alt="Busitema University"
                >
            </div>

            <button
                class="inline-flex size-9 shrink-0 items-center justify-center rounded-lg text-white transition hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white lg:hidden"
                type="button"
                @click="sidebarOpen = false"
                aria-label="Close navigation"
            >
                <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" />
                </svg>
            </button>
        </div>

        <nav class="scrollbar-hidden flex-1 overflow-y-auto px-3 py-5" aria-label="Primary navigation">
            <div class="flex flex-col gap-6">
                @foreach ($navigationGroups as $group)
                    <section>
                        <p
                            x-show="! sidebarCollapsed || sidebarOpen"
                            class="px-3 text-[0.7rem] font-semibold tracking-[0.16em] text-white/60 uppercase"
                        >
                            {{ $group['label'] }}
                        </p>

                        <div
                            x-show="sidebarCollapsed && ! sidebarOpen"
                            class="mx-3 hidden border-t border-white/20 lg:block"
                            aria-hidden="true"
                        ></div>

                        <ul class="mt-2 flex flex-col gap-1">
                            @foreach ($group['items'] as $item)
                                @php
                                    $isActive = ($item['route'] ?? false)
                                        ? request()->routeIs($item['pattern'])
                                        : request()->is($item['pattern']);
                                @endphp

                                <li>
                                    <a
                                        href="{{ $item['href'] }}"
                                        title="{{ $item['label'] }}"
                                        @class([
                                            'group flex min-h-10 items-center gap-3 rounded-lg border-l-4 px-3 py-2 text-sm font-medium transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white',
                                            'border-busitema-gold bg-white text-busitema-blue shadow-sm' => $isActive,
                                            'border-transparent text-white/85 hover:bg-white/10 hover:text-white' => ! $isActive,
                                        ])
                                        @if ($isActive) aria-current="page" @endif
                                        @click="sidebarOpen = false"
                                    >
                                        <span
                                            @class([
                                                'size-2 shrink-0 rounded-sm transition',
                                                'bg-busitema-gold' => $isActive,
                                                'bg-white/55 group-hover:bg-white' => ! $isActive,
                                            ])
                                            aria-hidden="true"
                                        ></span>
                                        <span x-show="! sidebarCollapsed || sidebarOpen" x-transition.opacity>
                                            {{ $item['label'] }}
                                        </span>
                                        <span x-show="sidebarCollapsed && ! sidebarOpen" class="sr-only">
                                            {{ $item['label'] }}
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endforeach
            </div>
        </nav>

        <div class="hidden border-t border-white/15 p-3 lg:block">
            <button
                class="flex min-h-10 w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-white/85 transition hover:bg-white/10 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                type="button"
                @click="toggleSidebar"
                :aria-label="sidebarCollapsed ? 'Expand navigation' : 'Collapse navigation'"
                :title="sidebarCollapsed ? 'Expand navigation' : 'Collapse navigation'"
            >
                <svg
                    class="size-5 shrink-0 transition-transform"
                    :class="sidebarCollapsed ? 'rotate-180' : ''"
                    aria-hidden="true"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
                </svg>
                <span x-show="! sidebarCollapsed" x-transition.opacity>Collapse sidebar</span>
            </button>
        </div>
    </aside>
</div>
