@php
    $identityValue = static fn (string $field): mixed => old($field, $beneficiaryRecord?->{$field} ?? '');
    $storedNinLastFour = $beneficiaryRecord?->nin ? substr($beneficiaryRecord->nin, -4) : null;
@endphp

<label class="flex flex-col gap-2 text-sm font-semibold text-heading">
    <span class="inline-flex items-center gap-1">First Name <span class="text-red-600">*</span></span>
    <input class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" type="text" name="national_id_given_names" value="{{ $identityValue('national_id_given_names') }}" placeholder="Beneficiary first name" autocomplete="given-name" required>
    @error('national_id_given_names')<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
</label>

<label class="flex flex-col gap-2 text-sm font-semibold text-heading">
    <span class="inline-flex items-center gap-1">Last Name <span class="text-red-600">*</span></span>
    <input class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" type="text" name="national_id_surname" value="{{ $identityValue('national_id_surname') }}" placeholder="Beneficiary last name" autocomplete="family-name" required>
    @error('national_id_surname')<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
</label>

<label class="flex flex-col gap-2 text-sm font-semibold text-heading" x-data="{ showNin: false }">
    <span>NIN <span class="font-normal text-body-text">(optional)</span></span>
    <span class="relative">
        <input class="min-h-11 w-full rounded-lg border border-border bg-white px-3 pr-20 text-sm font-normal tracking-wide text-heading uppercase outline-none transition placeholder:normal-case placeholder:tracking-normal placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" :type="showNin ? 'text' : 'password'" name="nin" value="{{ old('nin') }}" placeholder="{{ $storedNinLastFour ? 'Stored NIN ending in '.$storedNinLastFour : 'Enter NIN' }}" autocomplete="off" autocapitalize="characters">
        <button class="absolute inset-y-1 right-1 rounded-md px-3 text-xs font-semibold text-busitema-blue hover:bg-blue-50" type="button" @click="showNin = ! showNin" x-text="showNin ? 'Hide' : 'Show'"></button>
    </span>
    @if ($storedNinLastFour && ! old('nin'))
        <span class="text-xs font-normal text-emerald-700">NIN ending in {{ $storedNinLastFour }} is stored. Leave blank to keep it unchanged.</span>
    @endif
    @error('nin')<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
    @error('nin_hash')<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
</label>

<label class="flex flex-col gap-2 text-sm font-semibold text-heading">
    <span>Sex <span class="font-normal text-body-text">(optional)</span></span>
    <select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none transition focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="national_id_sex">
        <option value="">Select sex</option>
        @foreach (['Male', 'Female'] as $sex)
            <option value="{{ $sex }}" @selected($sex === $identityValue('national_id_sex'))>{{ $sex }}</option>
        @endforeach
    </select>
    @error('national_id_sex')<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
</label>

<label class="flex flex-col gap-2 text-sm font-semibold text-heading">
    <span>Nationality <span class="font-normal text-body-text">(optional)</span></span>
    <input class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" type="text" name="nationality" value="{{ $identityValue('nationality') }}" placeholder="e.g. Ugandan" autocomplete="country-name">
    @error('nationality')<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
</label>
