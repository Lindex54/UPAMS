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
    $fieldsAllowingOther = ['Asset Category', 'Tenure Type', 'Building Type', 'Equipment Type', 'Vehicle Type', 'Fuel Type', 'Property Type', 'Current Use'];
    $otherOptionValue = '__other';
    $hasFunctionalWorkflow = isset($campuses, $orgUnits);
    $campuses = $campuses ?? collect();
    $orgUnits = $orgUnits ?? collect();
    $formAction = $hasFunctionalWorkflow
        ? ($isEdit ? route("asset-management.{$module}.update", ['record' => $record['reference']]) : route("asset-management.{$module}.store"))
        : '#';
@endphp

<div class="mx-auto flex max-w-6xl flex-col gap-6" x-data="{ section: 'record' }">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-body-text" aria-label="Breadcrumb">
        <a class="font-medium hover:text-busitema-blue" href="{{ route("asset-management.{$module}.index") }}">{{ $design['title'] }}</a><span aria-hidden="true">/</span><span class="font-semibold text-heading">{{ $isEdit ? 'Edit '.$record['reference'] : $design['action'] }}</span>
    </nav>

    <section class="overflow-hidden rounded-2xl bg-busitema-blue shadow-sm">
        <div class="relative px-6 py-7 sm:px-8">
            <div class="absolute -top-20 right-4 size-52 rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>
            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div><p class="text-xs font-semibold tracking-[0.14em] text-busitema-gold uppercase">{{ $design['title'] }}</p><h2 class="mt-2 text-2xl font-semibold text-white sm:text-3xl">{{ $isEdit ? 'Edit '.$design['singular'] : $design['action'] }}</h2><p class="mt-2 max-w-2xl text-sm leading-6 text-white/70">{{ $isEdit ? 'Review and update the saved record details below.' : 'Capture the core identification, location, ownership, and operational details for this record.' }}</p></div>
                <span class="inline-flex w-fit rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-medium text-white/80">Campus-controlled property record</span>
            </div>
        </div>
    </section>

    <div>
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                <p class="font-semibold">Please correct the highlighted information.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form class="flex flex-col gap-6" method="POST" action="{{ $formAction }}" @if (! $hasFunctionalWorkflow) onsubmit="return false" @endif>
            @csrf
            @if ($isEdit) @method('PUT') @endif
            <section class="rounded-xl border border-border bg-white p-6 shadow-sm sm:p-8">
                <div class="border-b border-border pb-5"><p class="text-xs font-semibold tracking-[0.12em] text-busitema-blue uppercase">Record Information</p><h2 class="mt-1.5 text-xl font-semibold text-heading">{{ $design['singular'] }} details</h2><p class="mt-1 text-sm text-body-text">Fields marked required illustrate the expected information for this module.</p></div>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    @foreach ($design['fields'] as $field)
                        @php
                            [$label, $type, $placeholder, $editValue, $options] = array_pad($field, 5, []);
                            $isRequired = in_array($label, $requiredFields, true);
                            $fieldName = str($label)->slug('_')->toString();
                            $allowsOther = $type === 'select' && in_array($label, $fieldsAllowingOther, true);
                            $storedValue = old($fieldName, $isEdit ? ($record['fieldValues'][$fieldName] ?? '') : '');
                            $hasCustomValue = $allowsOther && filled($storedValue) && ! in_array($storedValue, $options, true);
                            $initialChoice = $hasCustomValue ? $otherOptionValue : $storedValue;
                        @endphp
                        <div class="flex flex-col gap-2 {{ in_array($label, ['Physical Location', 'Current Use', 'Primary Function', 'Assigned Unit / Driver', 'Area / Capacity']) ? 'sm:col-span-2' : '' }}" @if ($allowsOther) x-data="{ choice: @js($initialChoice) }" @endif>
                            <label class="flex flex-col gap-2 text-sm font-semibold text-heading">
                                <span>{{ $label }} @if ($isRequired)<span class="text-red-600">*</span>@else<span class="font-normal text-body-text">(optional)</span>@endif</span>
                                @if ($type === 'select')
                                    <select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none transition focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="{{ $fieldName }}" @required($isRequired) @if ($allowsOther) x-model="choice" x-on:change="if (choice === '{{ $otherOptionValue }}') $nextTick(() => $refs.otherInput.focus())" @endif>
                                        @if (! $isEdit || $allowsOther)<option value="">Select {{ strtolower($label) }}</option>@endif
                                        @foreach ($options as $option)<option value="{{ $option }}" @selected($initialChoice === $option)>{{ $option }}</option>@endforeach
                                        @if ($allowsOther)<option value="{{ $otherOptionValue }}" @selected($hasCustomValue)>Other (specify)</option>@endif
                                    </select>
                                @else
                                    <input class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" type="{{ $type }}" name="{{ $fieldName }}" placeholder="{{ $placeholder }}" value="{{ $storedValue }}" @required($isRequired)>
                                @endif
                            </label>
                            @if ($allowsOther)
                                <div class="flex flex-col gap-1.5" x-show="choice === '{{ $otherOptionValue }}'" x-cloak x-transition.opacity>
                                    <label class="sr-only" for="{{ $fieldName }}_other">Specify other {{ strtolower($label) }}</label>
                                    <input class="min-h-11 rounded-lg border border-busitema-blue/40 bg-white px-3 text-sm font-normal text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" type="text" id="{{ $fieldName }}_other" name="{{ $fieldName }}_other" maxlength="120" placeholder="Type the {{ strtolower($label) }} not listed above" value="{{ old($fieldName.'_other', $hasCustomValue ? $storedValue : '') }}" x-bind:required="choice === '{{ $otherOptionValue }}'" x-bind:disabled="choice !== '{{ $otherOptionValue }}'" x-ref="otherInput">
                                    <p class="text-xs text-body-text">Not in the list? Type it here and it will be recorded as a new {{ strtolower($label) }}.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <div class="sm:col-span-2">
                        <div class="rounded-xl border border-border bg-light-background p-4" x-data="gpsLocationPreview(@js(['latitude' => old('latitude', $isEdit ? ($record['latitude'] ?? '') : ''), 'longitude' => old('longitude', $isEdit ? ($record['longitude'] ?? '') : '')]))">
                            <div>
                                <p class="text-sm font-semibold text-heading">GPS location</p>
                                <p class="mt-1 text-xs leading-5 text-body-text">Enter latitude and longitude together to identify this item or property's precise location.</p>
                            </div>
                            <div class="mt-4 grid gap-5 sm:grid-cols-2">
                                <label class="flex flex-col gap-2 text-sm font-semibold text-heading">
                                    <span>GPS Latitude <span class="font-normal text-body-text">(optional)</span></span>
                                    <input class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" type="number" name="latitude" x-model.debounce.400ms="latitude" min="-90" max="90" step="0.0000001" inputmode="decimal" placeholder="1.2345678" value="{{ old('latitude', $isEdit ? ($record['latitude'] ?? '') : '') }}">
                                </label>
                                <label class="flex flex-col gap-2 text-sm font-semibold text-heading">
                                    <span>GPS Longitude <span class="font-normal text-body-text">(optional)</span></span>
                                    <input class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" type="number" name="longitude" x-model.debounce.400ms="longitude" min="-180" max="180" step="0.0000001" inputmode="decimal" placeholder="33.1234567" value="{{ old('longitude', $isEdit ? ($record['longitude'] ?? '') : '') }}">
                                </label>
                            </div>
                            <x-location-map-live class="mt-4" :title="$isEdit ? $record['name'] : 'New '.strtolower($design['singular'])" />
                        </div>
                    </div>

                </div>
            </section>

            <section class="rounded-xl border border-border bg-white p-6 shadow-sm sm:p-8">
                <div class="border-b border-border pb-5"><p class="text-xs font-semibold tracking-[0.12em] text-busitema-blue uppercase">Assignment & Governance</p><h2 class="mt-1.5 text-xl font-semibold text-heading">Location and record status</h2></div>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <label class="flex flex-col gap-2 text-sm font-semibold text-heading"><span class="inline-flex items-center gap-1">Campus <span class="text-red-600">*</span></span><select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="campus_id" required>@foreach ($campuses as $campus)<option value="{{ $campus->id }}" @selected((string) old('campus_id', $isEdit ? $record['campusId'] : auth()->user()->campus_id) === (string) $campus->id)>{{ $campus->name }}</option>@endforeach</select></label>
                    <label class="flex flex-col gap-2 text-sm font-semibold text-heading"><span class="inline-flex items-center gap-1">Organizational Unit <span class="font-normal text-body-text">(optional)</span></span><select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="org_unit_id"><option value="">Not assigned</option>@foreach ($orgUnits as $orgUnit)<option value="{{ $orgUnit->id }}" @selected((string) old('org_unit_id', $isEdit ? $record['orgUnitId'] : auth()->user()->org_unit_id) === (string) $orgUnit->id)>{{ $orgUnit->name }}</option>@endforeach</select></label>
                    <label class="flex flex-col gap-2 text-sm font-semibold text-heading"><span class="inline-flex items-center gap-1">Record Status <span class="text-red-600">*</span></span><select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="record_status" required>@foreach (['Active', 'Draft', 'Attention required', 'Inactive', 'Archived'] as $status)<option value="{{ $status }}" @selected(old('record_status', $isEdit ? $record['status'] : 'Active') === $status)>{{ $status }}</option>@endforeach</select></label>
                    <label class="flex flex-col gap-2 text-sm font-semibold text-heading sm:col-span-2"><span class="inline-flex items-center gap-1">Administrative Notes <span class="font-normal text-body-text">(optional)</span></span><textarea class="min-h-28 rounded-lg border border-border bg-white px-3 py-3 text-sm font-normal text-heading outline-none placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="administrative_notes" placeholder="Add context, verification notes, or special handling instructions">{{ old('administrative_notes', $isEdit ? ($record['fieldValues']['administrative_notes'] ?? '') : '') }}</textarea></label>
                </div>
            </section>

            <div class="flex flex-wrap items-center justify-end gap-3 rounded-xl border border-border bg-white p-4 shadow-sm">
                <a class="inline-flex min-h-11 items-center rounded-lg border border-border px-5 text-sm font-semibold text-busitema-deep-blue transition hover:border-busitema-blue hover:text-busitema-blue" href="{{ route("asset-management.{$module}.index") }}">Cancel</a>
                <button class="inline-flex min-h-11 items-center rounded-lg border border-busitema-blue px-5 text-sm font-semibold text-busitema-blue" type="{{ $hasFunctionalWorkflow ? 'submit' : 'button' }}" name="intent" value="draft">Save as draft</button>
                <button class="inline-flex min-h-11 items-center rounded-lg bg-busitema-gold px-5 text-sm font-semibold text-busitema-navy shadow-sm transition hover:bg-busitema-yellow" type="{{ $hasFunctionalWorkflow ? 'submit' : 'button' }}" name="intent" value="save">{{ $isEdit ? 'Save changes' : $design['action'] }}</button>
            </div>
        </form>

    </div>
</div>
