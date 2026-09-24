<div class="mx-auto flex max-w-7xl flex-col gap-6">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-body-text" aria-label="Breadcrumb"><a class="font-medium hover:text-busitema-blue" href="{{ route("asset-management.{$module}.index") }}">{{ $design['title'] }}</a><span aria-hidden="true">/</span><span class="font-semibold text-heading">{{ $record['reference'] }}</span></nav>

    <section class="overflow-hidden rounded-2xl bg-busitema-blue shadow-sm">
        <div class="relative px-6 py-7 sm:px-8">
            <div class="absolute -top-16 right-4 size-52 rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>
            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div><div class="flex flex-wrap items-center gap-3"><p class="text-xs font-semibold tracking-[0.14em] text-busitema-gold uppercase">{{ $design['singular'] }} · {{ $record['reference'] }}</p><span class="rounded-full bg-white/10 px-2.5 py-1 text-xs font-semibold text-white">{{ $record['status'] }}</span></div><h2 class="mt-3 text-2xl font-semibold text-white sm:text-3xl">{{ $record['name'] }}</h2><p class="mt-2 text-sm text-white/70">{{ $record['campus'] }} · {{ $record['primary'] }}</p></div>
                <div class="flex flex-wrap gap-3"><button class="inline-flex min-h-11 items-center rounded-lg border border-white/20 bg-white/10 px-4 text-sm font-semibold text-white transition hover:bg-white/15" type="button">Print record</button><a class="inline-flex min-h-11 items-center rounded-lg bg-busitema-gold px-5 text-sm font-semibold text-busitema-navy transition hover:bg-busitema-yellow" href="{{ route("asset-management.{$module}.edit", ['record' => $record['reference']]) }}">Edit {{ $design['singular'] }}</a></div>
            </div>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Record provenance summary">
        @foreach ([['Added By', $record['addedBy']], ['Date Added', $record['dateAdded']], ['Last Updated By', $record['updatedBy']], ['Last Updated Date', $record['dateUpdated']]] as [$label, $value])
            <article class="rounded-xl border border-border bg-white p-5 shadow-sm"><p class="text-xs font-semibold tracking-wide text-body-text uppercase">{{ $label }}</p><p class="mt-2 text-base font-semibold text-heading">{{ $value }}</p></article>
        @endforeach
    </section>

    <div class="grid gap-6 xl:grid-cols-[1fr_20rem]">
        <div class="flex flex-col gap-6">
            <section class="overflow-hidden rounded-xl border border-border bg-white shadow-sm">
                <div class="flex flex-col gap-1 border-b border-border px-6 py-5">
                    <p class="text-xs font-semibold tracking-[0.12em] text-busitema-blue uppercase">Property image</p>
                    <h2 class="text-lg font-semibold text-heading">Current property photograph</h2>
                </div>

                @if (! empty($record['image']))
                    <figure>
                        <img
                            class="aspect-video w-full object-cover"
                            src="{{ asset($record['image']) }}"
                            alt="{{ $record['imageAlt'] ?? $record['name'].' property photograph' }}"
                        >
                        <figcaption class="border-t border-border px-6 py-4 text-sm text-body-text">
                            {{ $record['name'] }} · Updated {{ $record['dateUpdated'] }}
                        </figcaption>
                    </figure>
                @else
                    <div class="flex aspect-video flex-col items-center justify-center gap-3 bg-light-background px-6 text-center text-body-text">
                        <svg class="size-10" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5.75A1.75 1.75 0 0 1 4.75 4h14.5A1.75 1.75 0 0 1 21 5.75v12.5A1.75 1.75 0 0 1 19.25 20H4.75A1.75 1.75 0 0 1 3 18.25V5.75Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m3 16 4.5-4.5 3.25 3.25 2.5-2.5L21 20M15.5 8.5h.01" />
                        </svg>
                        <p class="text-sm font-semibold">No image available for this record.</p>
                    </div>
                @endif
            </section>

            <section class="rounded-xl border border-border bg-white p-6 shadow-sm sm:p-8">
                <div class="flex flex-col gap-2 border-b border-border pb-5 sm:flex-row sm:items-center sm:justify-between"><div><p class="text-xs font-semibold tracking-[0.12em] text-busitema-blue uppercase">Record Overview</p><h2 class="mt-1.5 text-xl font-semibold text-heading">Core information</h2></div><span class="w-fit rounded-full px-2.5 py-1 text-xs font-semibold {{ $record['statusClass'] }}">{{ $record['status'] }}</span></div>
                <dl class="mt-6 grid gap-x-8 gap-y-6 sm:grid-cols-2">
                    @foreach ($design['fields'] as $field)
                        @php
                            [$label, $type, $placeholder, $editValue] = array_pad($field, 4, '—');
                            $displayValue = $loop->first ? $record['name'] : $editValue;
                        @endphp
                        <div class="border-b border-border pb-4"><dt class="text-xs font-semibold tracking-wide text-body-text uppercase">{{ $label }}</dt><dd class="mt-1.5 text-sm font-semibold leading-6 text-heading">{{ $displayValue }}</dd></div>
                    @endforeach
                    <div class="border-b border-border pb-4"><dt class="text-xs font-semibold tracking-wide text-body-text uppercase">Campus</dt><dd class="mt-1.5 text-sm font-semibold text-heading">{{ $record['campus'] }}</dd></div>
                    <div class="border-b border-border pb-4"><dt class="text-xs font-semibold tracking-wide text-body-text uppercase">Record Reference</dt><dd class="mt-1.5 text-sm font-semibold text-busitema-blue">{{ $record['reference'] }}</dd></div>
                    <div class="border-b border-border pb-4"><dt class="text-xs font-semibold tracking-wide text-body-text uppercase">GPS Latitude</dt><dd class="mt-1.5 text-sm font-semibold text-heading">{{ $record['latitude'] ?? 'Not recorded' }}</dd></div>
                    <div class="border-b border-border pb-4"><dt class="text-xs font-semibold tracking-wide text-body-text uppercase">GPS Longitude</dt><dd class="mt-1.5 text-sm font-semibold text-heading">{{ $record['longitude'] ?? 'Not recorded' }}</dd></div>
                </dl>
                <x-location-map class="mt-6" :latitude="$record['latitude'] ?? null" :longitude="$record['longitude'] ?? null" :title="$record['name']" />
            </section>

            <section class="overflow-hidden rounded-xl border border-border bg-white shadow-sm">
                <div class="border-b border-border px-6 py-5"><h2 class="text-lg font-semibold text-heading">Record History</h2><p class="mt-1 text-sm text-body-text">Illustrative audit events for this record</p></div>
                <ol class="divide-y divide-border px-6">
                    <li class="flex gap-4 py-5"><span class="mt-1 flex size-8 shrink-0 items-center justify-center rounded-full bg-blue-50 text-busitema-blue"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z" /></svg></span><div class="min-w-0 flex-1"><p class="text-sm font-semibold text-heading">Record details updated</p><p class="mt-1 text-xs leading-5 text-body-text">{{ $record['updatedBy'] }} reviewed status, location, and administrative information.</p></div><time class="shrink-0 text-xs text-body-text">{{ $record['dateUpdated'] }}</time></li>
                    <li class="flex gap-4 py-5"><span class="mt-1 flex size-8 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-700"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6" /></svg></span><div class="min-w-0 flex-1"><p class="text-sm font-semibold text-heading">Record verified</p><p class="mt-1 text-xs leading-5 text-body-text">Supporting identifiers and campus assignment were confirmed.</p></div><time class="shrink-0 text-xs text-body-text">18 Jul 2026</time></li>
                    <li class="flex gap-4 py-5"><span class="mt-1 flex size-8 shrink-0 items-center justify-center rounded-full bg-violet-50 text-violet-700"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14" /></svg></span><div class="min-w-0 flex-1"><p class="text-sm font-semibold text-heading">Record added to Property Management</p><p class="mt-1 text-xs leading-5 text-body-text">{{ $record['addedBy'] }} created the original {{ strtolower($design['singular']) }} record.</p></div><time class="shrink-0 text-xs text-body-text">{{ $record['dateAdded'] }}</time></li>
                </ol>
            </section>
        </div>

        <aside class="flex flex-col gap-6 xl:sticky xl:top-6 xl:self-start">
            <section class="rounded-xl border border-border bg-white p-5 shadow-sm"><h2 class="text-base font-semibold text-heading">Administrative Details</h2><dl class="mt-4 flex flex-col gap-4 text-sm"><div><dt class="text-xs text-body-text">Campus</dt><dd class="mt-1 font-semibold text-heading">{{ $record['campus'] }}</dd></div><div><dt class="text-xs text-body-text">Record status</dt><dd class="mt-1"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $record['statusClass'] }}">{{ $record['status'] }}</span></dd></div><div><dt class="text-xs text-body-text">Responsible unit</dt><dd class="mt-1 font-semibold text-heading">University Estates Office</dd></div><div><dt class="text-xs text-body-text">Data classification</dt><dd class="mt-1 font-semibold text-heading">Internal administrative record</dd></div></dl></section>
            <section class="rounded-xl border border-border bg-white p-5 shadow-sm"><div class="flex items-center justify-between gap-3"><h2 class="text-base font-semibold text-heading">Documents</h2><span class="rounded-full bg-light-background px-2 py-1 text-xs font-semibold text-body-text">2</span></div><div class="mt-4 flex flex-col gap-3"><button class="flex items-center gap-3 rounded-lg border border-border p-3 text-left" type="button"><span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-red-50 text-xs font-bold text-red-700">PDF</span><span class="min-w-0"><span class="block truncate text-xs font-semibold text-heading">Ownership / acquisition record</span><span class="mt-0.5 block text-[0.68rem] text-body-text">Uploaded 18 Jul 2026</span></span></button><button class="flex items-center gap-3 rounded-lg border border-border p-3 text-left" type="button"><span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-xs font-bold text-busitema-blue">IMG</span><span class="min-w-0"><span class="block truncate text-xs font-semibold text-heading">Current condition photograph</span><span class="mt-0.5 block text-[0.68rem] text-body-text">Uploaded {{ $record['dateUpdated'] }}</span></span></button></div></section>
            <a class="inline-flex min-h-11 items-center justify-center rounded-lg border border-border bg-white px-4 text-sm font-semibold text-busitema-deep-blue shadow-sm transition hover:border-busitema-blue hover:text-busitema-blue" href="{{ route("asset-management.{$module}.index") }}">Back to {{ $design['title'] }}</a>
        </aside>
    </div>
</div>
