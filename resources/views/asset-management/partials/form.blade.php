@php
    $requiredFields = [
        'assets' => ['Asset Name', 'Asset Category', 'Acquisition Date', 'Condition', 'Physical Location'],
        'land' => ['Parcel Name', 'Tenure Type', 'Area (Acres)', 'Boundary Status'],
        'buildings' => ['Building Name', 'Building Type', 'Condition'],
        'laboratories' => ['Equipment Name', 'Equipment Type', 'Laboratory', 'Condition'],
        'vehicles' => ['Vehicle Description', 'Registration Number', 'Vehicle Type'],
        'commercial-property' => ['Property / Unit Name', 'Property Type', 'Occupancy Status'],
        'agricultural-property' => ['Property Name', 'Property Type', 'Current Use', 'Infrastructure Condition'],
    ][$module] ?? [];
@endphp

<div class="mx-auto flex max-w-6xl flex-col gap-6" x-data="{ section: 'record' }">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-body-text" aria-label="Breadcrumb">
        <a class="font-medium hover:text-busitema-blue" href="{{ route("asset-management.{$module}.index") }}">{{ $design['title'] }}</a><span aria-hidden="true">/</span><span class="font-semibold text-heading">{{ $isEdit ? 'Edit '.$record['reference'] : $design['action'] }}</span>
    </nav>

    <section class="overflow-hidden rounded-2xl bg-busitema-blue shadow-sm">
        <div class="relative px-6 py-7 sm:px-8">
            <div class="absolute -top-20 right-4 size-52 rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>
            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div><p class="text-xs font-semibold tracking-[0.14em] text-busitema-gold uppercase">{{ $design['title'] }}</p><h2 class="mt-2 text-2xl font-semibold text-white sm:text-3xl">{{ $isEdit ? 'Edit '.$design['singular'] : $design['action'] }}</h2><p class="mt-2 max-w-2xl text-sm leading-6 text-white/70">{{ $isEdit ? 'Review and update the placeholder record details below.' : 'Capture the core identification, location, ownership, and operational details for this record.' }}</p></div>
                <span class="inline-flex w-fit rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-medium text-white/80">Design preview · No data will be saved</span>
            </div>
        </div>
    </section>

    <div>
        <form class="flex flex-col gap-6" onsubmit="return false">
            <section class="rounded-xl border border-border bg-white p-6 shadow-sm sm:p-8">
                <div class="border-b border-border pb-5"><p class="text-xs font-semibold tracking-[0.12em] text-busitema-blue uppercase">Record Information</p><h2 class="mt-1.5 text-xl font-semibold text-heading">{{ $design['singular'] }} details</h2><p class="mt-1 text-sm text-body-text">Fields marked required illustrate the expected information for this module.</p></div>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    @foreach ($design['fields'] as $field)
                        @php
                            [$label, $type, $placeholder, $editValue, $options] = array_pad($field, 5, []);
                            $isRequired = in_array($label, $requiredFields, true);
                            $fieldName = str($label)->slug('_');
                        @endphp
                        <label class="flex flex-col gap-2 text-sm font-semibold text-heading {{ in_array($label, ['Physical Location', 'Current Use', 'Primary Function', 'Assigned Unit / Driver', 'Area / Capacity']) ? 'sm:col-span-2' : '' }}">
                            <span>{{ $label }} @if ($isRequired)<span class="text-red-600">*</span>@else<span class="font-normal text-body-text">(optional)</span>@endif</span>
                            @if ($type === 'select')
                                <select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none transition focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="{{ $fieldName }}" @required($isRequired)>
                                    @if (! $isEdit)<option value="">Select {{ strtolower($label) }}</option>@endif
                                    @foreach ($options as $option)<option @selected($isEdit && $option === $editValue)>{{ $option }}</option>@endforeach
                                </select>
                            @else
                                <input class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" type="{{ $type }}" name="{{ $fieldName }}" placeholder="{{ $placeholder }}" value="{{ $isEdit ? ($type === 'number' ? str_replace(',', '', $editValue) : $editValue) : '' }}" @required($isRequired)>
                            @endif
                        </label>
                    @endforeach

                    <div class="sm:col-span-2">
                        <div class="rounded-xl border border-border bg-light-background p-4" x-data="gpsLocationPreview(@js(['latitude' => $isEdit ? ($record['latitude'] ?? '') : '', 'longitude' => $isEdit ? ($record['longitude'] ?? '') : '']))">
                            <div>
                                <p class="text-sm font-semibold text-heading">GPS location</p>
                                <p class="mt-1 text-xs leading-5 text-body-text">Enter latitude and longitude together to identify this item or property's precise location.</p>
                            </div>
                            <div class="mt-4 grid gap-5 sm:grid-cols-2">
                                <label class="flex flex-col gap-2 text-sm font-semibold text-heading">
                                    <span>GPS Latitude <span class="font-normal text-body-text">(optional)</span></span>
                                    <input class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" type="number" name="latitude" x-model.debounce.400ms="latitude" min="-90" max="90" step="0.0000001" inputmode="decimal" placeholder="1.2345678" value="{{ $isEdit ? ($record['latitude'] ?? '') : '' }}">
                                </label>
                                <label class="flex flex-col gap-2 text-sm font-semibold text-heading">
                                    <span>GPS Longitude <span class="font-normal text-body-text">(optional)</span></span>
                                    <input class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" type="number" name="longitude" x-model.debounce.400ms="longitude" min="-180" max="180" step="0.0000001" inputmode="decimal" placeholder="33.1234567" value="{{ $isEdit ? ($record['longitude'] ?? '') : '' }}">
                                </label>
                            </div>
                            <x-location-map-live class="mt-4" :title="$isEdit ? $record['name'] : 'New '.strtolower($design['singular'])" />
                        </div>
                    </div>

                    @if ($module === 'assets')
                        <div
                            class="sm:col-span-2"
                            x-data="{
                                imageUrl: null,
                                loadImage(event) {
                                    if (this.imageUrl) {
                                        URL.revokeObjectURL(this.imageUrl);
                                    }

                                    const image = event.target.files[0];
                                    this.imageUrl = image ? URL.createObjectURL(image) : null;
                                },
                            }"
                        >
                            <label class="flex flex-col gap-2 text-sm font-semibold text-heading">
                                <span>Asset Image <span class="font-normal text-body-text">(optional)</span></span>
                                <span class="grid gap-4 rounded-xl border border-dashed border-border bg-light-background p-4 sm:grid-cols-[8rem_1fr] sm:items-center">
                                    <span class="flex aspect-4/3 items-center justify-center overflow-hidden rounded-lg border border-border bg-white text-body-text">
                                        <img x-cloak x-show="imageUrl" :src="imageUrl" class="size-full object-cover" alt="Selected asset preview">
                                        <svg x-show="! imageUrl" class="size-8" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16v14H4zM4 15l4-4 4 4 2-2 6 6M15.5 8.5h.01" />
                                        </svg>
                                    </span>
                                    <span class="flex min-w-0 flex-col gap-2">
                                        <input
                                            class="min-h-11 w-full rounded-lg border border-border bg-white px-3 py-2 text-sm font-normal text-body-text file:mr-3 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-busitema-blue"
                                            type="file"
                                            name="asset_image"
                                            accept="image/jpeg,image/png,image/webp"
                                            @change="loadImage($event)"
                                        >
                                        <span class="text-xs font-normal leading-5 text-body-text">JPEG, PNG, or WebP. Choose a clear photograph of the asset.</span>
                                    </span>
                                </span>
                            </label>
                        </div>
                    @endif
                </div>
            </section>

            <section class="rounded-xl border border-border bg-white p-6 shadow-sm sm:p-8">
                <div class="border-b border-border pb-5"><p class="text-xs font-semibold tracking-[0.12em] text-busitema-blue uppercase">Assignment & Governance</p><h2 class="mt-1.5 text-xl font-semibold text-heading">Location and record status</h2></div>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <label class="flex flex-col gap-2 text-sm font-semibold text-heading"><span class="inline-flex items-center gap-1">Campus <span class="text-red-600">*</span></span><select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="campus" required><option @selected($isEdit && $record['campus'] === 'Main Campus')>Main Campus</option><option @selected($isEdit && $record['campus'] === 'Nagongera Campus')>Nagongera Campus</option><option @selected($isEdit && $record['campus'] === 'Arapai Campus')>Arapai Campus</option><option @selected($isEdit && $record['campus'] === 'Namasagali Campus')>Namasagali Campus</option><option>Mbale Campus</option><option>Pallisa Campus</option></select></label>
                    <label class="flex flex-col gap-2 text-sm font-semibold text-heading"><span class="inline-flex items-center gap-1">Organizational Unit <span class="font-normal text-body-text">(optional)</span></span><select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="organizational_unit"><option value="">Not assigned</option><option>University Administration</option><option>Directorate of ICT</option><option>Estates Office</option><option>Faculty of Agriculture</option><option>Faculty of Engineering</option></select></label>
                    <label class="flex flex-col gap-2 text-sm font-semibold text-heading"><span class="inline-flex items-center gap-1">Record Status <span class="text-red-600">*</span></span><select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="record_status" required><option>{{ $isEdit ? $record['status'] : 'Active / In use' }}</option><option>Attention required</option><option>Inactive / Archived</option></select></label>
                    <label class="flex flex-col gap-2 text-sm font-semibold text-heading"><span class="inline-flex items-center gap-1">Supporting Document <span class="font-normal text-body-text">(optional)</span></span><input class="min-h-11 rounded-lg border border-border bg-white px-3 py-2 text-sm font-normal text-body-text file:mr-3 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-busitema-blue" type="file" name="supporting_document"></label>
                    <label class="flex flex-col gap-2 text-sm font-semibold text-heading sm:col-span-2"><span class="inline-flex items-center gap-1">Administrative Notes <span class="font-normal text-body-text">(optional)</span></span><textarea class="min-h-28 rounded-lg border border-border bg-white px-3 py-3 text-sm font-normal text-heading outline-none placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="administrative_notes" placeholder="Add context, verification notes, or special handling instructions"></textarea></label>
                </div>
            </section>

            <div class="flex flex-wrap items-center justify-end gap-3 rounded-xl border border-border bg-white p-4 shadow-sm">
                <a class="inline-flex min-h-11 items-center rounded-lg border border-border px-5 text-sm font-semibold text-busitema-deep-blue transition hover:border-busitema-blue hover:text-busitema-blue" href="{{ route("asset-management.{$module}.index") }}">Cancel</a>
                <button class="inline-flex min-h-11 items-center rounded-lg border border-busitema-blue px-5 text-sm font-semibold text-busitema-blue" type="button">Save as draft</button>
                <button class="inline-flex min-h-11 items-center rounded-lg bg-busitema-gold px-5 text-sm font-semibold text-busitema-navy shadow-sm transition hover:bg-busitema-yellow" type="button">{{ $isEdit ? 'Save changes' : $design['action'] }}</button>
            </div>
        </form>

    </div>
</div>
