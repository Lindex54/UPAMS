<div class="mx-auto flex max-w-6xl flex-col gap-6" x-data="{ section: 'record' }">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-body-text" aria-label="Breadcrumb">
        <a class="font-medium hover:text-busitema-blue" href="{{ route("asset-management.{$module}.index") }}">{{ $design['title'] }}</a><span aria-hidden="true">/</span><span class="font-semibold text-heading">{{ $isEdit ? 'Edit '.$record['reference'] : $design['action'] }}</span>
    </nav>

    <section class="overflow-hidden rounded-2xl bg-busitema-deep-blue shadow-sm">
        <div class="relative px-6 py-7 sm:px-8">
            <div class="absolute -top-20 right-4 size-52 rounded-full bg-busitema-blue/30 blur-3xl" aria-hidden="true"></div>
            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div><p class="text-xs font-semibold tracking-[0.14em] text-busitema-gold uppercase">{{ $design['title'] }}</p><h2 class="mt-2 text-2xl font-semibold text-white sm:text-3xl">{{ $isEdit ? 'Edit '.$design['singular'] : $design['action'] }}</h2><p class="mt-2 max-w-2xl text-sm leading-6 text-white/70">{{ $isEdit ? 'Review and update the placeholder record details below.' : 'Capture the core identification, location, ownership, and operational details for this record.' }}</p></div>
                <span class="inline-flex w-fit rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-medium text-white/80">Design preview · No data will be saved</span>
            </div>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-[1fr_17rem]">
        <form class="flex flex-col gap-6" onsubmit="return false">
            <section class="rounded-xl border border-border bg-white p-6 shadow-sm sm:p-8">
                <div class="border-b border-border pb-5"><p class="text-xs font-semibold tracking-[0.12em] text-busitema-blue uppercase">Record Information</p><h2 class="mt-1.5 text-xl font-semibold text-heading">{{ $design['singular'] }} details</h2><p class="mt-1 text-sm text-body-text">Fields marked required illustrate the expected information for this module.</p></div>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    @foreach ($design['fields'] as $field)
                        @php
                            [$label, $type, $placeholder, $editValue, $options] = array_pad($field, 5, []);
                        @endphp
                        <label class="flex flex-col gap-2 text-sm font-semibold text-heading {{ in_array($label, ['Physical Location', 'Current Use', 'Primary Function', 'Assigned Unit / Driver', 'Area / Capacity']) ? 'sm:col-span-2' : '' }}">
                            <span>{{ $label }} @if (! in_array($label, ['GPS / Coordinates', 'Safety Classification']))<span class="text-red-600">*</span>@else<span class="font-normal text-body-text">(optional)</span>@endif</span>
                            @if ($type === 'select')
                                <select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none transition focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15">
                                    @if (! $isEdit)<option value="">Select {{ strtolower($label) }}</option>@endif
                                    @foreach ($options as $option)<option @selected($isEdit && $option === $editValue)>{{ $option }}</option>@endforeach
                                </select>
                            @else
                                <input class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" type="{{ $type }}" placeholder="{{ $placeholder }}" value="{{ $isEdit ? ($type === 'number' ? str_replace(',', '', $editValue) : $editValue) : '' }}">
                            @endif
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="rounded-xl border border-border bg-white p-6 shadow-sm sm:p-8">
                <div class="border-b border-border pb-5"><p class="text-xs font-semibold tracking-[0.12em] text-busitema-blue uppercase">Assignment & Governance</p><h2 class="mt-1.5 text-xl font-semibold text-heading">Location and record status</h2></div>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <label class="flex flex-col gap-2 text-sm font-semibold text-heading">Campus <span class="text-red-600">*</span><select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15"><option @selected($isEdit && $record['campus'] === 'Main Campus')>Main Campus</option><option @selected($isEdit && $record['campus'] === 'Nagongera Campus')>Nagongera Campus</option><option @selected($isEdit && $record['campus'] === 'Arapai Campus')>Arapai Campus</option><option @selected($isEdit && $record['campus'] === 'Namasagali Campus')>Namasagali Campus</option><option>Mbale Campus</option><option>Pallisa Campus</option></select></label>
                    <label class="flex flex-col gap-2 text-sm font-semibold text-heading">Organizational Unit <span class="text-red-600">*</span><select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15"><option>University Administration</option><option>Directorate of ICT</option><option>Estates Office</option><option>Faculty of Agriculture</option><option>Faculty of Engineering</option></select></label>
                    <label class="flex flex-col gap-2 text-sm font-semibold text-heading">Record Status <span class="text-red-600">*</span><select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15"><option>{{ $isEdit ? $record['status'] : 'Active / In use' }}</option><option>Attention required</option><option>Inactive / Archived</option></select></label>
                    <label class="flex flex-col gap-2 text-sm font-semibold text-heading">Supporting Document <span class="font-normal text-body-text">(optional)</span><input class="min-h-11 rounded-lg border border-border bg-white px-3 py-2 text-sm font-normal text-body-text file:mr-3 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-busitema-blue" type="file"></label>
                    <label class="flex flex-col gap-2 text-sm font-semibold text-heading sm:col-span-2">Administrative Notes <span class="font-normal text-body-text">(optional)</span><textarea class="min-h-28 rounded-lg border border-border bg-white px-3 py-3 text-sm font-normal text-heading outline-none placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" placeholder="Add context, verification notes, or special handling instructions"></textarea></label>
                </div>
            </section>

            <div class="flex flex-wrap items-center justify-end gap-3 rounded-xl border border-border bg-white p-4 shadow-sm">
                <a class="inline-flex min-h-11 items-center rounded-lg border border-border px-5 text-sm font-semibold text-busitema-deep-blue transition hover:border-busitema-blue hover:text-busitema-blue" href="{{ route("asset-management.{$module}.index") }}">Cancel</a>
                <button class="inline-flex min-h-11 items-center rounded-lg border border-busitema-blue px-5 text-sm font-semibold text-busitema-blue" type="button">Save as draft</button>
                <button class="inline-flex min-h-11 items-center rounded-lg bg-busitema-gold px-5 text-sm font-semibold text-busitema-navy shadow-sm transition hover:bg-busitema-yellow" type="button">{{ $isEdit ? 'Save changes' : $design['action'] }}</button>
            </div>
        </form>

        <aside class="flex flex-col gap-4 lg:sticky lg:top-6 lg:self-start">
            <section class="rounded-xl border border-border bg-white p-5 shadow-sm"><h2 class="text-base font-semibold text-heading">Record checklist</h2><ul class="mt-4 flex flex-col gap-3 text-xs leading-5 text-body-text"><li class="flex gap-2"><span class="mt-1 size-2 shrink-0 rounded-full bg-busitema-blue"></span>Confirm official identifiers and references.</li><li class="flex gap-2"><span class="mt-1 size-2 shrink-0 rounded-full bg-busitema-blue"></span>Select the correct campus and responsible unit.</li><li class="flex gap-2"><span class="mt-1 size-2 shrink-0 rounded-full bg-busitema-blue"></span>Check condition and operational status.</li><li class="flex gap-2"><span class="mt-1 size-2 shrink-0 rounded-full bg-busitema-blue"></span>Attach supporting evidence where available.</li></ul></section>
            @if ($isEdit)
                <section class="rounded-xl border border-border bg-white p-5 shadow-sm"><h2 class="text-base font-semibold text-heading">Record provenance</h2><dl class="mt-4 flex flex-col gap-3 text-xs"><div><dt class="text-body-text">Added By</dt><dd class="mt-0.5 font-semibold text-heading">{{ $record['addedBy'] }}</dd></div><div><dt class="text-body-text">Date Added</dt><dd class="mt-0.5 font-semibold text-heading">{{ $record['dateAdded'] }}</dd></div><div><dt class="text-body-text">Last Updated By</dt><dd class="mt-0.5 font-semibold text-heading">{{ $record['updatedBy'] }}</dd></div><div><dt class="text-body-text">Last Updated Date</dt><dd class="mt-0.5 font-semibold text-heading">{{ $record['dateUpdated'] }}</dd></div></dl></section>
            @endif
            <section class="rounded-xl border border-blue-100 bg-blue-50 p-5"><p class="text-xs font-semibold tracking-wide text-busitema-blue uppercase">Design Preview</p><p class="mt-2 text-xs leading-5 text-body-text">These controls demonstrate the intended administrator workflow. No submission or file upload is active.</p></section>
        </aside>
    </div>
</div>
