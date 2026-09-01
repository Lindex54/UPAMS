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
                </dl>
            </section>

            <section class="overflow-hidden rounded-xl border border-border bg-white shadow-sm">
                <div class="border-b border-border px-6 py-5"><h2 class="text-lg font-semibold text-heading">Record History</h2><p class="mt-1 text-sm text-body-text">Illustrative audit events for this record</p></div>
                <ol class="divide-y divide-border px-6">
                    <li class="flex gap-4 py-5"><span class="mt-1 flex size-8 shrink-0 items-center justify-center rounded-full bg-blue-50 text-busitema-blue"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z" /></svg></span><div class="min-w-0 flex-1"><p class="text-sm font-semibold text-heading">Record details updated</p><p class="mt-1 text-xs leading-5 text-body-text">{{ $record['updatedBy'] }} reviewed status, location, and administrative information.</p></div><time class="shrink-0 text-xs text-body-text">{{ $record['dateUpdated'] }}</time></li>
                    <li class="flex gap-4 py-5"><span class="mt-1 flex size-8 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-700"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6" /></svg></span><div class="min-w-0 flex-1"><p class="text-sm font-semibold text-heading">Record verified</p><p class="mt-1 text-xs leading-5 text-body-text">Supporting identifiers and campus assignment were confirmed.</p></div><time class="shrink-0 text-xs text-body-text">18 Jul 2026</time></li>
                    <li class="flex gap-4 py-5"><span class="mt-1 flex size-8 shrink-0 items-center justify-center rounded-full bg-violet-50 text-violet-700"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14" /></svg></span><div class="min-w-0 flex-1"><p class="text-sm font-semibold text-heading">Record added to UPAMS</p><p class="mt-1 text-xs leading-5 text-body-text">{{ $record['addedBy'] }} created the original {{ strtolower($design['singular']) }} record.</p></div><time class="shrink-0 text-xs text-body-text">{{ $record['dateAdded'] }}</time></li>
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
