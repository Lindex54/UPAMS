@php
    $businessInput = 'min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15';
    $businessFields = [
        'company_name' => ['Company Name', 'Registered business or organisation name'],
        'registration_number' => ['Registration Number', 'URSB registration number'],
        'tin' => ['TIN Number', 'URA Tax Identification Number'],
        'tax_returns' => ['Tax Returns', 'e.g. Filed for FY 2025/26, URA acknowledgement no.'],
        'local_council_permit' => ['Local Council Government Permit', 'Permit number or reference'],
    ];
@endphp

<section class="rounded-xl border border-border bg-white p-6 shadow-sm sm:p-8">
    <div class="border-b border-border pb-5"><p class="text-xs font-semibold tracking-[0.12em] text-busitema-blue uppercase">Business &amp; registration</p><h2 class="mt-1.5 text-xl font-semibold text-heading">Company details</h2></div>
    <div class="mt-6 grid gap-5 sm:grid-cols-2">
        @foreach ($businessFields as $name => [$label, $placeholder])
            <label class="flex flex-col gap-2 text-sm font-semibold text-heading">
                <span>{{ $label }} <span class="font-normal text-body-text">(optional)</span></span>
                <input class="{{ $businessInput }}" type="text" name="{{ $name }}" placeholder="{{ $placeholder }}" value="{{ old($name, $beneficiaryRecord?->{$name}) }}" maxlength="{{ $name === 'tin' ? 30 : 255 }}">
                @error($name)<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
            </label>
        @endforeach

        @foreach (['director_names' => 'Name of the Directors', 'owner_names' => 'Name of the Owners'] as $name => $label)
            <label class="flex flex-col gap-2 text-sm font-semibold text-heading">
                <span>{{ $label }} <span class="font-normal text-body-text">(optional)</span></span>
                <textarea class="min-h-28 rounded-lg border border-border bg-white px-3 py-3 text-sm font-normal text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="{{ $name }}" placeholder="One name per line" maxlength="2000">{{ old($name, $beneficiaryRecord?->{$name}) }}</textarea>
                @error($name)<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
            </label>
        @endforeach
    </div>
</section>
