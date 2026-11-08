@props([
    'navigationGroups' => null,
    'ariaLabel' => 'System Administrator navigation',
    'sidebarId' => 'administrator-sidebar',
])

@php
    $navigationGroups ??= [
        ['label' => 'Overview', 'items' => [
            ['label' => 'Dashboard', 'href' => route('dashboard'), 'pattern' => 'dashboard', 'route' => true],
            ['label' => 'GIS Map', 'href' => route('gis.index'), 'pattern' => 'gis.*', 'route' => true],
        ]],
        ['label' => 'Asset Management', 'items' => [
            ['label' => 'Asset Registry', 'href' => url('/assets'), 'pattern' => 'assets*'],
            ['label' => 'Land Management', 'href' => url('/land'), 'pattern' => 'land*'],
            ['label' => 'Buildings & Spaces', 'href' => url('/buildings'), 'pattern' => 'buildings*'],
            ['label' => 'Laboratories & Equipment', 'href' => url('/laboratories'), 'pattern' => 'laboratories*'],
            ['label' => 'ICT Equipment & Computer Labs', 'href' => route('technician.dashboard'), 'pattern' => 'technician.*', 'route' => true],
            ['label' => 'Vehicles', 'href' => url('/vehicles'), 'pattern' => 'vehicles*'],
            ['label' => 'Commercial Property', 'href' => url('/commercial-property'), 'pattern' => 'commercial-property*'],
            ['label' => 'Agricultural Property', 'href' => url('/agricultural-property'), 'pattern' => 'agricultural-property*'],
        ]],
        ['label' => 'Operations', 'items' => [
            ['label' => 'Agreements & Allocations', 'href' => route('operations.agreements.index'), 'pattern' => 'agreements*'],
            ['label' => 'Tenants/Beneficiaries', 'href' => route('operations.beneficiaries.index'), 'pattern' => 'beneficiaries*'],
            ['label' => 'Inspections', 'href' => route('operations.inspections.index'), 'pattern' => 'inspections*'],
            ['label' => 'Maintenance', 'href' => route('operations.maintenance.index'), 'pattern' => 'maintenance*'],
            ['label' => 'Documents', 'href' => route('operations.documents.index'), 'pattern' => 'documents*'],
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
    <header class="fixed inset-x-0 top-0 z-30 flex h-16 items-center justify-between border-b border-border bg-white px-4 shadow-sm dark:border-slate-700 dark:bg-slate-900 lg:hidden">
        <img class="h-auto w-44" src="{{ asset('images/busitema-logo.png') }}" alt="Busitema University">

        <button
            class="inline-flex size-10 items-center justify-center rounded-lg text-busitema-deep-blue transition hover:bg-light-background focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-busitema-blue dark:text-sky-300 dark:hover:bg-slate-800"
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
        class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-busitema-blue shadow-xl transition-[width,transform,background-color] duration-200 ease-out dark:bg-slate-950 lg:translate-x-0 lg:shadow-none"
        :class="[
            sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
            sidebarCollapsed ? 'lg:w-20' : 'lg:w-72'
        ]"
        aria-label="{{ $ariaLabel }}"
    >
        <div class="flex min-h-24 items-center gap-3 border-b border-white/15 px-4 py-4">
            <div
                class="theme-brand-surface min-w-0 flex-1 overflow-hidden rounded-xl bg-white p-3"
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
            <div class="flex flex-col gap-2">
                @foreach ($navigationGroups as $group)
                    @php
                        $groupHasActiveItem = collect($group['items'])->contains(function (array $item): bool {
                            return ($item['route'] ?? false)
                                ? request()->routeIs($item['pattern'])
                                : request()->is($item['pattern']);
                        });
                        $groupId = $sidebarId.'-group-'.$loop->index;
                    @endphp

                    <section x-data="{ expanded: {{ $groupHasActiveItem ? 'true' : 'false' }} }">
                        <button
                            @class([
                                'group flex min-h-11 w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-base font-bold transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white',
                                'bg-white/10 text-white' => $groupHasActiveItem,
                                'text-white/80 hover:bg-white/10 hover:text-white' => ! $groupHasActiveItem,
                            ])
                            type="button"
                            @click="expanded = ! expanded"
                            :aria-expanded="expanded"
                            aria-controls="{{ $groupId }}"
                            title="{{ $group['label'] }}"
                        >
                            <span class="flex size-7 shrink-0 items-center justify-center rounded-md bg-white/10 text-white/85" aria-hidden="true">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h6v6H4V5Zm10 0h6v6h-6V5ZM4 15h6v4H4v-4Zm10 0h6v4h-6v-4Z" />
                                </svg>
                            </span>
                            <span x-show="! sidebarCollapsed || sidebarOpen" class="min-w-0 flex-1 truncate" x-transition.opacity>
                                {{ $group['label'] }}
                            </span>
                            <span x-show="sidebarCollapsed && ! sidebarOpen" class="sr-only">
                                {{ $group['label'] }}
                            </span>
                            <svg
                                x-show="! sidebarCollapsed || sidebarOpen"
                                class="size-4 shrink-0 transition-transform duration-200"
                                :class="expanded ? 'rotate-90' : ''"
                                aria-hidden="true"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                            </svg>
                        </button>

                        <ul
                            id="{{ $groupId }}"
                            x-cloak
                            x-show="expanded"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="mt-1 ml-4 flex flex-col gap-1 border-l border-white/15 pl-3"
                            :class="sidebarCollapsed && ! sidebarOpen ? 'lg:ml-0 lg:border-l-0 lg:pl-0' : ''"
                        >
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
                                            'border-busitema-gold bg-white text-busitema-blue shadow-sm dark:bg-slate-800 dark:text-sky-300' => $isActive,
                                            'border-transparent text-white/75 hover:bg-white/10 hover:text-white' => ! $isActive,
                                        ])
                                        @if ($isActive) aria-current="page" @endif
                                        @click="sidebarOpen = false"
                                    >
                                        <span
                                            x-show="sidebarCollapsed && ! sidebarOpen"
                                            x-cloak
                                            @class([
                                                'w-full text-center text-xs font-bold tracking-wide transition',
                                                'text-busitema-blue dark:text-sky-300' => $isActive,
                                                'text-white/70 group-hover:text-white' => ! $isActive,
                                            ])
                                            aria-hidden="true"
                                        >{{ str($item['label'])->replace(['&', '/', '-'], ' ')->squish()->explode(' ')->take(2)->map(fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)))->implode('') }}</span>
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
