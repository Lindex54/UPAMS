@php
    // Preserve the selected hierarchy after validation errors and while editing an existing beneficiary.
    $selectedLocations = [
        'district' => (string) old('district_id', $beneficiaryRecord?->district_id ?? ''),
        'county' => (string) old('county_id', $beneficiaryRecord?->county_id ?? ''),
        'subCounty' => (string) old('sub_county_id', $beneficiaryRecord?->sub_county_id ?? ''),
        'parish' => (string) old('parish_id', $beneficiaryRecord?->parish_id ?? ''),
        'village' => (string) old('village_id', $beneficiaryRecord?->village_id ?? ''),
    ];
@endphp

{{-- Districts are rendered initially; the remaining options are loaded from local database-backed endpoints. --}}
<section
    class="rounded-xl border border-border bg-white p-6 shadow-sm sm:p-8"
    x-data="ugandaLocationSelector(@js([
        'selected' => $selectedLocations,
        'options' => [
            'counties' => $counties ?? [],
            'subCounties' => $subCounties ?? [],
            'parishes' => $parishes ?? [],
            'villages' => $villages ?? [],
        ],
        'urls' => [
            'counties' => url('/api/locations/districts/__PARENT__/counties'),
            'subCounties' => url('/api/locations/counties/__PARENT__/sub-counties'),
            'parishes' => url('/api/locations/sub-counties/__PARENT__/parishes'),
            'villages' => url('/api/locations/parishes/__PARENT__/villages'),
        ],
    ]))"
>
    <div class="border-b border-border pb-5">
        <p class="text-xs font-semibold tracking-[0.12em] text-busitema-blue uppercase">Uganda administrative location</p>
        <h2 class="mt-1.5 text-xl font-semibold text-heading">District to village</h2>
        <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-body-text">Choose each level in order. Changing a parent automatically clears all selections below it.</p>
        </div>
    </div>

    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <label class="flex flex-col gap-2 text-sm font-semibold text-heading">
            <span class="inline-flex items-center gap-1">District <span class="text-red-600">*</span></span>
            <select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="district_id" x-model="selected.district" x-on:change="districtChanged" required>
                <option value="">Select district</option>
                @foreach ($districts ?? [] as $district)
                    <option value="{{ $district->id }}">{{ $district->name }}{{ $district->code ? ' · '.$district->code : '' }}</option>
                @endforeach
            </select>
            @error('district_id')<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
        </label>

        <label class="flex flex-col gap-2 text-sm font-semibold text-heading">
            <span class="inline-flex items-center gap-1">County / Constituency <span class="text-red-600">*</span></span>
            <select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none disabled:cursor-not-allowed disabled:opacity-60" name="county_id" x-model="selected.county" x-on:change="countyChanged" x-bind:disabled="!selected.district || loading.counties" required>
                <option value="" x-text="loading.counties ? 'Loading counties…' : 'Select county'"></option>
                <template x-for="option in options.counties" x-bind:key="option.id"><option x-bind:value="String(option.id)" x-text="option.code ? `${option.name} · ${option.code}` : option.name"></option></template>
            </select>
            <span class="text-xs font-normal text-body-text" x-show="loading.counties">Loading counties…</span>
            <span class="text-xs font-normal text-amber-700" x-show="messages.counties" x-text="messages.counties"></span>
            @error('county_id')<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
        </label>

        <label class="flex flex-col gap-2 text-sm font-semibold text-heading">
            <span class="inline-flex items-center gap-1">Sub-county / Division <span class="text-red-600">*</span></span>
            <select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none disabled:cursor-not-allowed disabled:opacity-60" name="sub_county_id" x-model="selected.subCounty" x-on:change="subCountyChanged" x-bind:disabled="!selected.county || loading.subCounties" required>
                <option value="" x-text="loading.subCounties ? 'Loading sub-counties…' : 'Select sub-county'"></option>
                <template x-for="option in options.subCounties" x-bind:key="option.id"><option x-bind:value="String(option.id)" x-text="option.code ? `${option.name} · ${option.code}` : option.name"></option></template>
            </select>
            <span class="text-xs font-normal text-body-text" x-show="loading.subCounties">Loading sub-counties…</span>
            <span class="text-xs font-normal text-amber-700" x-show="messages.subCounties" x-text="messages.subCounties"></span>
            @error('sub_county_id')<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
        </label>

        <label class="flex flex-col gap-2 text-sm font-semibold text-heading">
            <span class="inline-flex items-center gap-1">Parish / Ward <span class="text-red-600">*</span></span>
            <select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none disabled:cursor-not-allowed disabled:opacity-60" name="parish_id" x-model="selected.parish" x-on:change="parishChanged" x-bind:disabled="!selected.subCounty || loading.parishes" required>
                <option value="" x-text="loading.parishes ? 'Loading parishes…' : 'Select parish'"></option>
                <template x-for="option in options.parishes" x-bind:key="option.id"><option x-bind:value="String(option.id)" x-text="option.code ? `${option.name} · ${option.code}` : option.name"></option></template>
            </select>
            <span class="text-xs font-normal text-body-text" x-show="loading.parishes">Loading parishes…</span>
            <span class="text-xs font-normal text-amber-700" x-show="messages.parishes" x-text="messages.parishes"></span>
            @error('parish_id')<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
        </label>

        <label class="flex flex-col gap-2 text-sm font-semibold text-heading">
            <span class="inline-flex items-center gap-1">Village / Cell <span class="text-red-600">*</span></span>
            <select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none disabled:cursor-not-allowed disabled:opacity-60" name="village_id" x-model="selected.village" x-bind:disabled="!selected.parish || loading.villages" required>
                <option value="" x-text="loading.villages ? 'Loading villages…' : 'Select village'"></option>
                <template x-for="option in options.villages" x-bind:key="option.id"><option x-bind:value="String(option.id)" x-text="option.code ? `${option.name} · ${option.code}` : option.name"></option></template>
            </select>
            <span class="text-xs font-normal text-body-text" x-show="loading.villages">Loading villages…</span>
            <span class="text-xs font-normal text-amber-700" x-show="messages.villages" x-text="messages.villages"></span>
            @error('village_id')<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
        </label>

        <label class="flex flex-col gap-2 text-sm font-semibold text-heading sm:col-span-2 lg:col-span-3">
            <span class="inline-flex items-center gap-1">Physical Address / Landmark <span class="font-normal text-body-text">(optional)</span></span>
            <textarea class="min-h-24 rounded-lg border border-border bg-white px-3 py-3 text-sm font-normal text-heading outline-none placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="physical_address_landmark" placeholder="Road, building, trading centre, or nearby landmark">{{ old('physical_address_landmark', $beneficiaryRecord?->physical_address_landmark) }}</textarea>
            @error('physical_address_landmark')<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
        </label>
    </div>
</section>
