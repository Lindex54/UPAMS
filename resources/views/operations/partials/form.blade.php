@php
    $beneficiaryRecord = $beneficiary ?? null;
    $isPersistentBeneficiary = $module === 'beneficiaries';
    $canPersist = $isPersistentBeneficiary && (! $isEdit || $beneficiaryRecord !== null);
@endphp

<div class="mx-auto flex max-w-6xl flex-col gap-6">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-body-text" aria-label="Breadcrumb">
        <a class="font-medium hover:text-busitema-blue" href="{{ route("operations.{$module}.index") }}">{{ $design['title'] }}</a><span aria-hidden="true">/</span><span class="font-semibold text-heading">{{ $isEdit ? 'Edit '.$record['reference'] : $design['action'] }}</span>
    </nav>

    <section class="overflow-hidden rounded-2xl bg-busitema-blue shadow-sm transition-colors dark:bg-blue-950">
        <div class="relative px-6 py-7 sm:px-8">
            <div class="absolute -top-16 right-4 size-52 rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>
            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div><p class="text-xs font-semibold tracking-[0.14em] text-busitema-gold uppercase">System Administrator · {{ $design['title'] }}</p><h2 class="mt-2 text-2xl font-semibold text-white sm:text-3xl">{{ $isEdit ? 'Edit '.$design['singular'] : $design['action'] }}</h2><p class="mt-2 max-w-3xl text-sm leading-6 text-white/75">{{ $isEdit ? 'Review metadata, update status, and manage this record across its operational lifecycle.' : 'Capture a new university-wide operations record using realistic administrative fields.' }}</p></div>
                <span class="inline-flex w-fit rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-medium text-white/80">{{ $canPersist ? 'Laravel form · Location validation active' : 'Frontend preview · No data will be saved' }}</span>
            </div>
        </div>
    </section>

    @if (isset($design['workflow']))
        <section class="rounded-xl border border-border bg-white p-5 shadow-sm sm:p-6">
            <div><p class="text-xs font-semibold tracking-[0.12em] text-busitema-blue uppercase">Maintenance lifecycle</p><h2 class="mt-1.5 text-lg font-semibold text-heading">Problem Reported → Closed</h2></div>
            <ol class="mt-5 grid gap-2 sm:grid-cols-4 xl:grid-cols-7">
                @foreach ($design['workflow'] as $step)
                    <li class="rounded-lg border p-3 {{ $loop->index <= $design['activeWorkflowStep'] ? 'border-busitema-blue/40 bg-blue-50' : 'border-border bg-light-background' }}"><span class="flex size-6 items-center justify-center rounded-full text-xs font-bold {{ $loop->index <= $design['activeWorkflowStep'] ? 'bg-busitema-blue text-white' : 'bg-slate-200 text-body-text' }}">{{ $loop->iteration }}</span><p class="mt-2 text-xs font-semibold text-heading">{{ $step }}</p></li>
                @endforeach
            </ol>
        </section>
    @endif

    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800" role="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800" role="alert">
            <p class="font-semibold">Please correct the highlighted beneficiary information.</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form class="flex flex-col gap-6" enctype="multipart/form-data" @if ($canPersist) method="POST" action="{{ $isEdit ? route('operations.beneficiaries.update', $beneficiaryRecord) : route('operations.beneficiaries.store') }}" @else onsubmit="return false" @endif>
        @if ($canPersist)
            @csrf
            @if ($isEdit) @method('PUT') @endif
        @endif
        <section class="rounded-xl border border-border bg-white p-6 shadow-sm sm:p-8">
            <div class="border-b border-border pb-5"><p class="text-xs font-semibold tracking-[0.12em] text-busitema-blue uppercase">Record information</p><h2 class="mt-1.5 text-xl font-semibold text-heading">{{ $design['singular'] }} details</h2><p class="mt-1 text-sm text-body-text">Required markers identify the minimum information expected in this design.</p></div>
            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                @foreach ($design['fields'] as $field)
                    @php
                        [$label, $type, $placeholder, $editValue, $options, $isRequired] = array_pad($field, 6, false);
                        $fieldName = str($label)->slug('_')->toString();
                        $isWide = in_array($type, ['textarea', 'file'], true);
                        $fieldValue = old($fieldName, $beneficiaryRecord?->{$fieldName} ?? ($isEdit ? $editValue : ''));
                    @endphp
                    <label class="flex flex-col gap-2 text-sm font-semibold text-heading {{ $isWide ? 'sm:col-span-2' : '' }}">
                        <span class="inline-flex items-center gap-1">{{ $label }} @if ($isRequired)<span class="text-red-600">*</span>@else<span class="font-normal text-body-text">(optional)</span>@endif</span>
                        @if ($type === 'select')
                            <select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none transition focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="{{ $fieldName }}" @required($isRequired)>
                                <option value="">Select {{ strtolower($label) }}</option>
                                @foreach ($options as $option)<option @selected($option === $fieldValue)>{{ $option }}</option>@endforeach
                            </select>
                        @elseif ($type === 'textarea')
                            <textarea class="min-h-28 rounded-lg border border-border bg-white px-3 py-3 text-sm font-normal text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="{{ $fieldName }}" placeholder="{{ $placeholder }}" @required($isRequired)>{{ $fieldValue }}</textarea>
                        @elseif ($type === 'file')
                            <span class="rounded-xl border border-dashed border-border bg-light-background p-4"><input class="min-h-11 w-full rounded-lg border border-border bg-white px-3 py-2 text-sm font-normal text-body-text file:mr-3 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-busitema-blue" type="file" name="{{ $fieldName }}" accept="image/*,.pdf,.doc,.docx" @required($isRequired)><span class="mt-2 block text-xs font-normal text-body-text">Upload photographs, PDF reports, or supporting files as applicable.</span></span>
                        @else
                            <input class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" type="{{ $type }}" name="{{ $fieldName }}" placeholder="{{ $placeholder }}" value="{{ $fieldValue }}" @required($isRequired) @if ($type === 'number') min="0" step="0.01" @endif>
                        @endif
                        @error($fieldName)<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
                    </label>
                @endforeach
                @if ($isPersistentBeneficiary)
                    @include('operations.partials.beneficiary-national-identification-fields')
                    @include('operations.partials.beneficiary-photo-field')
                @endif
            </div>
        </section>

        @if ($isPersistentBeneficiary)
            @include('operations.partials.beneficiary-location-fields')
        @endif

        <section class="rounded-xl border border-border bg-white p-6 shadow-sm sm:p-8">
            <div class="border-b border-border pb-5"><p class="text-xs font-semibold tracking-[0.12em] text-busitema-blue uppercase">Administration &amp; control</p><h2 class="mt-1.5 text-xl font-semibold text-heading">Campus, status, and ownership</h2></div>
            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                @if ($isPersistentBeneficiary)
                    <label class="flex flex-col gap-2 text-sm font-semibold text-heading"><span class="inline-flex items-center gap-1">Campus <span class="text-red-600">*</span></span><select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="campus_id" required><option value="">Select campus</option>@foreach ($campuses ?? [] as $campus)<option value="{{ $campus->id }}" @selected((string) $campus->id === (string) old('campus_id', $beneficiaryRecord?->campus_id))>{{ $campus->name }}</option>@endforeach</select>@error('campus_id')<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror</label>
                @else
                    <label class="flex flex-col gap-2 text-sm font-semibold text-heading"><span class="inline-flex items-center gap-1">Campus <span class="text-red-600">*</span></span><select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="campus" required><option @selected($isEdit && $record['campus'] === 'Main Campus')>Main Campus</option><option @selected($isEdit && $record['campus'] === 'Nagongera Campus')>Nagongera Campus</option><option @selected($isEdit && $record['campus'] === 'Arapai Campus')>Arapai Campus</option><option @selected($isEdit && $record['campus'] === 'Namasagali Campus')>Namasagali Campus</option><option>Mbale Campus</option><option>Pallisa Campus</option></select></label>
                @endif
                <label class="flex flex-col gap-2 text-sm font-semibold text-heading"><span class="inline-flex items-center gap-1">Record Status <span class="text-red-600">*</span></span><select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="record_status" required>@foreach ($design['statusOptions'] as $status)<option @selected($status === old('record_status', $beneficiaryRecord?->record_status ?? ($isEdit ? $record['status'] : '')))>{{ $status }}</option>@endforeach</select>@error('record_status')<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror</label>
                <label class="flex flex-col gap-2 text-sm font-semibold text-heading"><span class="inline-flex items-center gap-1">Responsible Unit <span class="font-normal text-body-text">(optional)</span></span><select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="responsible_unit"><option value="">Select responsible unit</option>@foreach (['University Administration', 'Estates Office', 'Directorate of ICT', 'Finance Department', 'Faculty / Campus Office'] as $unit)<option @selected($unit === old('responsible_unit', $beneficiaryRecord?->responsible_unit))>{{ $unit }}</option>@endforeach</select></label>
                <label class="flex flex-col gap-2 text-sm font-semibold text-heading"><span class="inline-flex items-center gap-1">Record Owner <span class="font-normal text-body-text">(optional)</span></span><input class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" type="text" name="record_owner" placeholder="Responsible officer" value="{{ old('record_owner', $beneficiaryRecord?->record_owner ?? ($isEdit ? $record['updatedBy'] : '')) }}"></label>
                <label class="flex flex-col gap-2 text-sm font-semibold text-heading sm:col-span-2"><span class="inline-flex items-center gap-1">Administrative Notes <span class="font-normal text-body-text">(optional)</span></span><textarea class="min-h-28 rounded-lg border border-border bg-white px-3 py-3 text-sm font-normal text-heading outline-none placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" name="administrative_notes" placeholder="Add approval context, follow-up notes, or handling instructions">{{ old('administrative_notes', $beneficiaryRecord?->administrative_notes) }}</textarea></label>
            </div>
        </section>

        @if ($isEdit)
            <section class="rounded-xl border border-border bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"><div><p class="text-xs font-semibold tracking-[0.12em] text-busitema-blue uppercase">Audit provenance</p><h2 class="mt-1 text-lg font-semibold text-heading">Record accountability</h2></div><span class="w-fit rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-busitema-blue">{{ $record['reference'] }}</span></div>
                <dl class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4"><div><dt class="text-xs text-body-text">Created By</dt><dd class="mt-1 text-sm font-semibold text-heading">{{ $record['createdBy'] }}</dd></div><div><dt class="text-xs text-body-text">Created At</dt><dd class="mt-1 text-sm font-semibold text-heading">{{ $record['createdAt'] }}</dd></div><div><dt class="text-xs text-body-text">Last Updated By</dt><dd class="mt-1 text-sm font-semibold text-heading">{{ $record['updatedBy'] }}</dd></div><div><dt class="text-xs text-body-text">Last Updated At</dt><dd class="mt-1 text-sm font-semibold text-heading">{{ $record['updatedAt'] }}</dd></div></dl>
            </section>
        @endif

        <div class="flex flex-wrap items-center justify-end gap-3 rounded-xl border border-border bg-white p-4 shadow-sm">
            <a class="inline-flex min-h-11 items-center rounded-lg border border-border px-5 text-sm font-semibold text-busitema-deep-blue transition hover:border-busitema-blue hover:text-busitema-blue" href="{{ route("operations.{$module}.index") }}">Cancel</a>
            <button class="inline-flex min-h-11 items-center rounded-lg border border-busitema-blue px-5 text-sm font-semibold text-busitema-blue" type="button" @disabled($isPersistentBeneficiary) title="{{ $isPersistentBeneficiary ? 'Draft workflow is not connected yet' : '' }}">Save as draft</button>
            <button class="inline-flex min-h-11 items-center rounded-lg bg-busitema-gold px-5 text-sm font-semibold text-busitema-navy shadow-sm transition hover:bg-busitema-yellow" type="{{ $canPersist ? 'submit' : 'button' }}">{{ $isEdit ? 'Save changes' : $design['action'] }}</button>
        </div>
    </form>
</div>
