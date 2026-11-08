<div class="flex flex-col gap-6" x-data="{ filtersOpen: true }">
    <section class="overflow-hidden rounded-2xl bg-busitema-blue shadow-sm">
        <div class="relative px-6 py-7 sm:px-8">
            <div class="absolute -top-16 right-6 size-48 rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>
            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold tracking-[0.14em] text-busitema-gold uppercase">Asset Management</p>
                    <h2 class="mt-2 text-2xl font-semibold text-white sm:text-3xl">{{ $design['title'] }}</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-white/70">{{ $design['description'] }}</p>
                </div>
                @if ($canManagePropertyRecords ?? true)
                    <a class="inline-flex min-h-11 items-center justify-center gap-2 self-start rounded-lg bg-busitema-gold px-5 text-sm font-semibold text-busitema-navy shadow-sm transition hover:bg-busitema-yellow" href="{{ route("asset-management.{$module}.create") }}">
                        <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                        {{ $design['action'] }}
                    </a>
                @endif
            </div>
        </div>
    </section>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
    @endif

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="{{ $design['title'] }} summary">
        @foreach ($design['summary'] as [$label, $value])
            <x-stat-card :label="$label" :value="$value" detail="Current portfolio summary" :tone="['blue', 'green', 'orange', 'red'][$loop->index]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7.5 12 3l8 4.5M5 9v9l7 3 7-3V9M12 12v9" /></svg></x-stat-card>
        @endforeach
    </section>

    @if (isset($liveRecords))
        <form class="rounded-xl border border-border bg-white p-5 shadow-sm" method="GET">
            <div class="flex items-center justify-between gap-4">
                <div><h2 class="text-base font-semibold text-heading">Search and filters</h2><p class="mt-1 text-xs text-body-text">Campus access rules are applied automatically.</p></div>
                <button class="inline-flex min-h-9 items-center gap-2 rounded-lg border border-border px-3 text-xs font-semibold text-busitema-deep-blue lg:hidden" type="button" @click="filtersOpen = ! filtersOpen"><span x-text="filtersOpen ? 'Hide filters' : 'Show filters'"></span></button>
            </div>
            <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4" x-show="filtersOpen" x-transition>
                <label class="relative sm:col-span-2"><span class="sr-only">Search</span><input class="min-h-11 w-full rounded-lg border border-border bg-white px-4 text-sm text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" type="search" name="search" value="{{ request('search') }}" placeholder="Search by name, code, or reference"></label>
                <label><span class="sr-only">Campus</span><select class="min-h-11 w-full rounded-lg border border-border bg-white px-3 text-sm text-heading" name="campus_id"><option value="">All campuses</option>@foreach ($campuses as $campus)<option value="{{ $campus->id }}" @selected((string) request('campus_id') === (string) $campus->id)>{{ $campus->name }}</option>@endforeach</select></label>
                <label><span class="sr-only">Status</span><select class="min-h-11 w-full rounded-lg border border-border bg-white px-3 text-sm text-heading" name="status"><option value="">All statuses</option>@foreach (['Active', 'Draft', 'Attention required', 'Inactive', 'Archived'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>@endforeach</select></label>
            </div>
            <div class="mt-4 flex justify-end gap-2 border-t border-border pt-4" x-show="filtersOpen"><a class="inline-flex min-h-9 items-center rounded-lg border border-border px-3 text-xs font-semibold text-body-text" href="{{ route("asset-management.{$module}.index") }}">Clear</a><button class="min-h-9 rounded-lg bg-busitema-blue px-4 text-xs font-semibold text-white" type="submit">Apply filters</button></div>
        </form>
    @endif

    <section class="overflow-hidden rounded-xl border border-border bg-white shadow-sm">
        <div class="border-b border-border px-6 py-5">
            <h2 class="text-lg font-semibold text-heading">{{ $design['title'] }} Register</h2>
            <p class="mt-1 text-sm text-body-text">Showing {{ isset($liveRecords) ? $liveRecords->count() : count($design['records']) }} of {{ isset($liveRecords) ? $liveRecords->total() : $design['summary'][0][1] }} records</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-7xl text-left text-sm">
                <thead class="bg-light-background text-xs font-semibold tracking-wide text-body-text uppercase"><tr><th class="px-6 py-3">Record</th><th class="px-6 py-3">Campus</th><th class="px-6 py-3">{{ $design['primaryLabel'] }}</th><th class="px-6 py-3">{{ $design['secondaryLabel'] }}</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Audit / Provenance</th><th class="px-6 py-3 text-right">Actions</th></tr></thead>
                <tbody class="divide-y divide-border">
                    @forelse ($design['records'] as $item)
                        <tr class="align-top transition hover:bg-light-background/70">
                            <td class="px-6 py-4"><a class="font-semibold text-heading hover:text-busitema-blue" href="{{ route("asset-management.{$module}.show", ['record' => $item['reference']]) }}">{{ $item['name'] }}</a><p class="mt-1 text-xs font-medium text-busitema-blue">{{ $item['reference'] }}</p></td>
                            <td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $item['campus'] }}</td>
                            <td class="px-6 py-4 text-body-text">{{ $item['primary'] }}</td>
                            <td class="px-6 py-4 text-body-text">{{ $item['secondary'] }}</td>
                            <td class="whitespace-nowrap px-6 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $item['statusClass'] }}">{{ $item['status'] }}</span></td>
                            <td class="whitespace-nowrap px-6 py-4 text-xs text-body-text"><p><span class="font-semibold text-heading">Added:</span> {{ $item['addedBy'] }} · {{ $item['dateAdded'] }}</p><p class="mt-1"><span class="font-semibold text-heading">Updated:</span> {{ $item['updatedBy'] }} · {{ $item['dateUpdated'] }}</p></td>
                            <td class="px-6 py-4"><div class="flex items-center justify-end gap-2"><a class="inline-flex min-h-9 items-center rounded-lg border border-border px-3 text-xs font-semibold text-busitema-deep-blue" href="{{ route("asset-management.{$module}.show", ['record' => $item['reference']]) }}">View</a>@if ($canManagePropertyRecords ?? true)<a class="inline-flex min-h-9 items-center rounded-lg border border-border px-3 text-xs font-semibold text-busitema-deep-blue" href="{{ route("asset-management.{$module}.edit", ['record' => $item['reference']]) }}">Edit</a>@endif</div></td>
                        </tr>
                    @empty
                        <tr><td class="px-6 py-10 text-center text-sm text-body-text" colspan="7">No records match the current filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($liveRecords) && $liveRecords->hasPages())
            <div class="border-t border-border px-6 py-4">{{ $liveRecords->links() }}</div>
        @endif
    </section>
</div>
