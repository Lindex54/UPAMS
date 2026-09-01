<div class="flex flex-col gap-6" x-data="{ filtersOpen: true }">
    <section class="overflow-hidden rounded-2xl bg-busitema-blue shadow-sm transition-colors dark:bg-blue-950">
        <div class="relative px-6 py-7 sm:px-8">
            <div class="absolute -top-16 right-6 size-48 rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>
            <div class="absolute -right-14 -bottom-24 size-48 rounded-full bg-busitema-gold/15 blur-3xl" aria-hidden="true"></div>
            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold tracking-[0.14em] text-busitema-gold uppercase">System Administrator · Operations</p>
                    <h2 class="mt-2 text-2xl font-semibold text-white sm:text-3xl">{{ $design['title'] }}</h2>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-white/75">{{ $design['description'] }}</p>
                </div>
                <a class="inline-flex min-h-11 items-center justify-center gap-2 self-start rounded-lg bg-busitema-gold px-5 text-sm font-semibold text-busitema-navy shadow-sm transition hover:bg-busitema-yellow focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white" href="{{ route("operations.{$module}.create") }}">
                    <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                    {{ $design['action'] }}
                </a>
            </div>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="{{ $design['title'] }} overview">
        @foreach ($design['summary'] as [$label, $value, $detail])
            <article class="relative overflow-hidden rounded-xl border border-border bg-white p-5 shadow-sm">
                <div class="absolute inset-y-0 left-0 w-1 {{ $loop->index === 2 ? 'bg-amber-500' : ($loop->index === 3 ? 'bg-red-500' : 'bg-busitema-blue') }}" aria-hidden="true"></div>
                <p class="text-sm font-medium text-body-text">{{ $label }}</p>
                <p class="mt-2 text-2xl font-semibold tracking-tight text-heading">{{ $value }}</p>
                <p class="mt-2 text-xs text-body-text">{{ $detail }}</p>
            </article>
        @endforeach
    </section>

    <section class="rounded-xl border border-border bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-4">
            <div><h2 class="text-base font-semibold text-heading">Search and filters</h2><p class="mt-1 text-xs text-body-text">Super Admin view across all university campuses.</p></div>
            <button class="inline-flex min-h-9 items-center rounded-lg border border-border px-3 text-xs font-semibold text-busitema-deep-blue lg:hidden" type="button" @click="filtersOpen = ! filtersOpen" :aria-expanded="filtersOpen"><span x-text="filtersOpen ? 'Hide filters' : 'Show filters'"></span></button>
        </div>
        <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-5" x-show="filtersOpen" x-transition>
            <label class="relative sm:col-span-2">
                <span class="sr-only">Search {{ strtolower($design['title']) }}</span>
                <svg class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-body-text" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7" /><path d="m20 20-3.5-3.5" /></svg>
                <input class="min-h-11 w-full rounded-lg border border-border bg-white pr-4 pl-10 text-sm text-heading outline-none placeholder:text-body-text/65 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" type="search" placeholder="Search name, reference, asset, or person">
            </label>
            <label><span class="sr-only">Campus</span><select class="min-h-11 w-full rounded-lg border border-border bg-white px-3 text-sm text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15"><option>All campuses</option><option>Main Campus</option><option>Nagongera Campus</option><option>Arapai Campus</option><option>Namasagali Campus</option><option>Mbale Campus</option><option>Pallisa Campus</option></select></label>
            <label><span class="sr-only">{{ $design['filterLabel'] }}</span><select class="min-h-11 w-full rounded-lg border border-border bg-white px-3 text-sm text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15"><option>All {{ strtolower($design['filterLabel']) }}</option>@foreach ($design['filterOptions'] as $option)<option>{{ $option }}</option>@endforeach</select></label>
            <label><span class="sr-only">Status</span><select class="min-h-11 w-full rounded-lg border border-border bg-white px-3 text-sm text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15"><option>All statuses</option>@foreach ($design['statusOptions'] as $status)<option>{{ $status }}</option>@endforeach</select></label>
        </div>
        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-border pt-4" x-show="filtersOpen" x-transition>
            <p class="text-xs text-body-text">Placeholder register · Search and filters are visual only.</p>
            <div class="flex gap-2"><button class="min-h-9 rounded-lg border border-border px-3 text-xs font-semibold text-body-text" type="button">Clear</button><button class="min-h-9 rounded-lg bg-busitema-blue px-4 text-xs font-semibold text-white" type="button">Apply filters</button></div>
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-border bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-border px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div><h2 class="text-lg font-semibold text-heading">{{ $design['title'] }} Register</h2><p class="mt-1 text-sm text-body-text">University-wide records with complete provenance.</p></div>
            <div class="flex flex-wrap gap-2"><button class="inline-flex min-h-9 items-center rounded-lg border border-border px-3 text-xs font-semibold text-busitema-deep-blue" type="button">Bulk manage</button><button class="inline-flex min-h-9 items-center gap-2 rounded-lg border border-border px-3 text-xs font-semibold text-busitema-deep-blue" type="button"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 19h14" /></svg>Export</button></div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-7xl text-left text-sm">
                <thead class="bg-light-background text-xs font-semibold tracking-wide text-body-text uppercase"><tr><th class="px-6 py-3">Record</th><th class="px-6 py-3">Campus</th><th class="px-6 py-3">{{ $design['primaryLabel'] }}</th><th class="px-6 py-3">{{ $design['secondaryLabel'] }}</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Created / Updated</th><th class="px-6 py-3 text-right">Super Admin Actions</th></tr></thead>
                <tbody class="divide-y divide-border">
                    @foreach ($design['records'] as $item)
                        <tr class="align-top transition hover:bg-light-background/70">
                            <td class="px-6 py-4"><a class="font-semibold text-heading hover:text-busitema-blue" href="{{ route("operations.{$module}.show", ['record' => $item['reference']]) }}">{{ $item['name'] }}</a><p class="mt-1 text-xs font-medium text-busitema-blue">{{ $item['reference'] }}</p></td>
                            <td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $item['campus'] }}</td>
                            <td class="px-6 py-4 text-body-text">{{ $item['primary'] }}</td>
                            <td class="px-6 py-4 text-body-text">{{ $item['secondary'] }}</td>
                            <td class="whitespace-nowrap px-6 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $item['statusClass'] }}">{{ $item['status'] }}</span></td>
                            <td class="whitespace-nowrap px-6 py-4"><p class="text-xs text-body-text"><span class="font-semibold text-heading">Created By:</span> {{ $item['createdBy'] }}</p><p class="mt-1 text-xs text-body-text"><span class="font-semibold text-heading">Created At:</span> {{ $item['createdAt'] }}</p><p class="mt-1 text-xs text-body-text"><span class="font-semibold text-heading">Last Updated By:</span> {{ $item['updatedBy'] }}</p><p class="mt-1 text-xs text-body-text"><span class="font-semibold text-heading">Last Updated At:</span> {{ $item['updatedAt'] }}</p></td>
                            <td class="px-6 py-4"><div class="flex items-center justify-end gap-2"><a class="inline-flex min-h-9 items-center rounded-lg border border-border px-3 text-xs font-semibold text-busitema-deep-blue hover:border-busitema-blue hover:text-busitema-blue" href="{{ route("operations.{$module}.show", ['record' => $item['reference']]) }}">View</a><a class="inline-flex min-h-9 items-center rounded-lg border border-border px-3 text-xs font-semibold text-busitema-deep-blue hover:border-busitema-blue hover:text-busitema-blue" href="{{ route("operations.{$module}.edit", ['record' => $item['reference']]) }}">Edit</a><button class="inline-flex min-h-9 items-center rounded-lg border border-border px-3 text-xs font-semibold text-body-text" type="button">Manage</button></div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="flex flex-col gap-3 border-t border-border px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-body-text">Page 1 of 12 · Showing 1–25 of {{ $design['summary'][0][1] }}</p>
            <nav class="flex items-center gap-1" aria-label="Register pagination"><button class="inline-flex min-h-9 items-center rounded-lg border border-border px-3 text-xs font-semibold text-body-text" type="button">Previous</button><button class="inline-flex size-9 items-center justify-center rounded-lg bg-busitema-blue text-xs font-semibold text-white" type="button" aria-current="page">1</button><button class="inline-flex size-9 items-center justify-center rounded-lg text-xs font-semibold text-body-text hover:bg-light-background" type="button">2</button><button class="inline-flex size-9 items-center justify-center rounded-lg text-xs font-semibold text-body-text hover:bg-light-background" type="button">3</button><span class="px-1 text-xs text-body-text">…</span><button class="inline-flex min-h-9 items-center rounded-lg border border-border px-3 text-xs font-semibold text-busitema-deep-blue" type="button">Next</button></nav>
        </div>
    </section>
</div>
