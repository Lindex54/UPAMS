@php
    $input = 'block w-full rounded-lg border bg-white px-4 py-3 text-base text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-3 focus:ring-busitema-blue/15';
@endphp

<div class="grid gap-6 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="mb-2 block text-sm font-semibold text-heading" for="name">Campus Name</label>
        <input class="{{ $input }} @error('name') border-red-400 @else border-border @enderror" id="name" name="name" type="text" value="{{ old('name', $campus?->name) }}" placeholder="e.g. Nagongera Campus" maxlength="120" required autofocus @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
        @error('name')
            <p class="mt-2 text-sm text-red-600" id="name-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-heading" for="code">Campus Code</label>
        <input class="{{ $input }} font-mono uppercase @error('code') border-red-400 @else border-border @enderror" id="code" name="code" type="text" value="{{ old('code', $campus?->code) }}" placeholder="e.g. NAG" maxlength="20" required @error('code') aria-invalid="true" aria-describedby="code-error" @else aria-describedby="code-help" @enderror>
        @error('code')
            <p class="mt-2 text-sm text-red-600" id="code-error">{{ $message }}</p>
        @else
            <p class="mt-2 text-xs text-body-text" id="code-help">A short unique code used on references and reports.</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-heading" for="district_id">District</label>
        <select class="{{ $input }} @error('district_id') border-red-400 @else border-border @enderror" id="district_id" name="district_id" required @error('district_id') aria-invalid="true" aria-describedby="district-error" @enderror>
            <option value="">Select district</option>
            @foreach ($districts as $district)
                <option value="{{ $district->id }}" @selected((string) old('district_id', $campus?->district_id) === (string) $district->id)>{{ $district->name }}</option>
            @endforeach
        </select>
        @error('district_id')
            <p class="mt-2 text-sm text-red-600" id="district-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-heading" for="location">Physical Location <span class="font-normal text-body-text">(optional)</span></label>
        <input class="{{ $input }} @error('location') border-red-400 @else border-border @enderror" id="location" name="location" type="text" value="{{ old('location', $campus?->location) }}" placeholder="e.g. Tororo–Busolwe Road" maxlength="255">
        @error('location')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-heading" for="faculty">Faculty Hosted <span class="font-normal text-body-text">(optional)</span></label>
        <input class="{{ $input }} @error('faculty') border-red-400 @else border-border @enderror" id="faculty" name="faculty" type="text" value="{{ old('faculty', $campus?->faculty) }}" placeholder="e.g. Faculty of Science and Education" maxlength="255">
        @error('faculty')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <span class="mb-2 block text-sm font-semibold text-heading">Status</span>
        <div class="grid gap-3 sm:grid-cols-2">
            @foreach (['Active' => 'Campus is operating and can be assigned users and assets.', 'Inactive' => 'Campus is closed or not yet operating.'] as $status => $hint)
                <label class="flex cursor-pointer gap-3 rounded-lg border border-border p-4 transition has-checked:border-busitema-blue has-checked:bg-blue-50">
                    <input class="mt-1 accent-busitema-blue" type="radio" name="status" value="{{ $status }}" @checked(old('status', $campus?->status ?? 'Active') === $status)>
                    <span><span class="block text-sm font-semibold text-heading">{{ $status }}</span><span class="mt-0.5 block text-xs text-body-text">{{ $hint }}</span></span>
                </label>
            @endforeach
        </div>
        @error('status')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2 rounded-xl border border-border bg-light-background p-4" x-data="gpsLocationPreview(@js(['latitude' => old('latitude', $campus?->latitude), 'longitude' => old('longitude', $campus?->longitude)]))">
        <p class="text-sm font-semibold text-heading">GPS location <span class="font-normal text-body-text">(optional)</span></p>
        <p class="mt-1 text-xs leading-5 text-body-text">Enter both coordinates to pin the campus on the map.</p>
        <div class="mt-4 grid gap-5 sm:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-heading" for="latitude">Latitude</label>
                <input class="{{ $input }} @error('latitude') border-red-400 @else border-border @enderror" id="latitude" name="latitude" type="number" min="-90" max="90" step="0.0000001" inputmode="decimal" placeholder="0.7700000" value="{{ old('latitude', $campus?->latitude) }}" x-model.debounce.400ms="latitude">
                @error('latitude')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-heading" for="longitude">Longitude</label>
                <input class="{{ $input }} @error('longitude') border-red-400 @else border-border @enderror" id="longitude" name="longitude" type="number" min="-180" max="180" step="0.0000001" inputmode="decimal" placeholder="34.0261000" value="{{ old('longitude', $campus?->longitude) }}" x-model.debounce.400ms="longitude">
                @error('longitude')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <x-location-map-live class="mt-4" :title="$campus?->name ?? 'New campus'" />
    </div>
</div>
