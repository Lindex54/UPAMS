@extends('layouts.app')
@php
    $editing = isset($asset);
    $value = fn (string $key) => old($key, $editing ? $asset->{$key} : null);
    $input = 'mt-1.5 w-full rounded-lg border border-border bg-white px-3 py-2.5 text-heading outline-none focus:border-busitema-blue dark:bg-slate-900';
@endphp
@section('title', ($editing ? 'Edit' : 'Register').' ICT Equipment | Property Management')
@section('portal-label', auth()->user()->role?->name === 'System Administrator' ? 'Property Management Administration · ICT Assets' : 'IT Technician Workspace')
@section('page-heading', $editing ? 'Edit ICT Equipment' : 'Register ICT Equipment')
@section('user-role', auth()->user()->role?->name ?? 'IT Technician')
@section('sidebar') @include('technician.partials.sidebar') @endsection
@section('content')
<div class="mx-auto max-w-6xl">
    @include('technician.partials.messages')

    <form
        method="POST"
        action="{{ $editing ? route('technician.equipment.update', $asset) : route('technician.equipment.store') }}"
        class="space-y-6"
        x-data="{
            selectedType: @js((string) old('asset_type_id', $editing ? $asset->asset_type_id : '')),
            schemas: @js($typeSpecificationSchemas),
            specifications: @js(old('specifications', $specificationValues)),
            isVisible(field) {
                if (! field.show_when) return true;
                return field.show_when.values.includes(this.specifications[field.show_when.key]);
            }
        }"
    >
        @csrf
        @if($editing) @method('PUT') @endif

        <section class="rounded-xl border border-border bg-white p-6 shadow-sm dark:bg-slate-900">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-heading">Identification</h2>
                    <p class="mt-1 text-sm text-body-text">Equipment Name, Make and Serial Number are the only required registration fields.</p>
                </div>
                <div class="rounded-lg border border-border bg-light-background px-4 py-3 sm:text-right">
                    <p class="text-xs font-semibold uppercase tracking-wide text-body-text">Asset ID</p>
                    <p class="mt-1 font-mono text-sm font-semibold text-heading">{{ $editing ? $asset->reference : 'Generated automatically after saving' }}</p>
                </div>
            </div>

            <div class="mt-5 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <label class="text-sm font-semibold text-heading">Equipment Name *<input class="{{ $input }}" name="name" value="{{ $value('name') }}" placeholder="Desktop Computer" required></label>
                <label class="text-sm font-semibold text-heading">Make *<input class="{{ $input }}" name="make" value="{{ $value('make') }}" placeholder="Dell" required></label>
                <label class="text-sm font-semibold text-heading">Serial Number *<input class="{{ $input }} font-mono" name="serial_number" value="{{ $value('serial_number') }}" placeholder="Manufacturer serial number" required></label>
                <label class="text-sm font-semibold text-heading">Equipment Type
                    <select class="{{ $input }}" name="asset_type_id" x-model="selectedType">
                        <option value="">Select type to add technical specifications</option>
                        @foreach($types as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach
                    </select>
                </label>
                <label class="text-sm font-semibold text-heading">Model<input class="{{ $input }}" name="model" value="{{ $value('model') }}" placeholder="OptiPlex 7090"></label>
            </div>
        </section>

        <section class="rounded-xl border border-border bg-white p-6 shadow-sm dark:bg-slate-900" x-show="selectedType && (schemas[selectedType] ?? []).length" x-cloak>
            <h2 class="text-lg font-semibold text-heading">Technical specifications</h2>
            <p class="mt-1 text-sm text-body-text">These optional fields are tailored to the selected equipment type.</p>
            <div class="mt-5 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <template x-for="field in (schemas[selectedType] ?? [])" :key="field.key">
                    <label class="text-sm font-semibold text-heading" x-show="isVisible(field)">
                        <span x-text="field.label + (field.required ? ' *' : '')"></span>
                        <template x-if="field.type === 'select'">
                            <select class="{{ $input }}" :name="`specifications[${field.key}]`" x-model="specifications[field.key]" :required="field.required ?? false">
                                <option value="">Not specified</option>
                                <template x-for="option in field.options" :key="option"><option :value="option" x-text="option"></option></template>
                            </select>
                        </template>
                        <template x-if="field.type !== 'select'">
                            <input class="{{ $input }}" :type="field.type" :name="`specifications[${field.key}]`" :placeholder="field.placeholder ?? ''" x-model="specifications[field.key]" :min="field.type === 'number' ? 0 : null" :required="field.required ?? false">
                        </template>
                    </label>
                </template>
            </div>
        </section>

        <section class="rounded-xl border border-border bg-white p-6 shadow-sm dark:bg-slate-900" x-data="gpsLocationPreview(@js(['latitude' => $value('latitude'), 'longitude' => $value('longitude')]))">
            <h2 class="text-lg font-semibold text-heading">Location & responsibility</h2>
            <p class="mt-1 text-sm text-body-text">Record both GPS coordinates together when the equipment location is known.</p>
            <div class="mt-5 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <label class="text-sm font-semibold text-heading">Campus<select class="{{ $input }}" name="campus_id"><option value="">No campus selected</option>@foreach($campuses as $campus)<option value="{{ $campus->id }}" @selected((string) $value('campus_id') === (string) $campus->id)>{{ $campus->name }}</option>@endforeach</select></label>
                <label class="text-sm font-semibold text-heading">Unit<select class="{{ $input }}" name="org_unit_id"><option value="">No unit</option>@foreach($orgUnits as $unit)<option value="{{ $unit->id }}" @selected((string) $value('org_unit_id') === (string) $unit->id)>{{ $unit->name }}</option>@endforeach</select></label>
                <label class="text-sm font-semibold text-heading">Room / Lab<select class="{{ $input }}" name="computer_lab_id"><option value="">Not assigned to a lab</option>@foreach($labs as $lab)<option value="{{ $lab->id }}" @selected((string) $value('computer_lab_id') === (string) $lab->id)>{{ $lab->name }} · {{ $lab->building }}/{{ $lab->room }}</option>@endforeach</select></label>
                @foreach([['building', 'Building'], ['room', 'Room'], ['custodian', 'Custodian']] as [$key, $label])<label class="text-sm font-semibold text-heading">{{ $label }}<input class="{{ $input }}" name="{{ $key }}" value="{{ $value($key) }}"></label>@endforeach
                <label class="text-sm font-semibold text-heading">GPS Latitude <span class="font-normal text-body-text">(optional)</span><input type="number" class="{{ $input }}" name="latitude" value="{{ $value('latitude') }}" x-model.debounce.400ms="latitude" min="-90" max="90" step="0.0000001" inputmode="decimal" placeholder="1.2345678"></label>
                <label class="text-sm font-semibold text-heading">GPS Longitude <span class="font-normal text-body-text">(optional)</span><input type="number" class="{{ $input }}" name="longitude" value="{{ $value('longitude') }}" x-model.debounce.400ms="longitude" min="-180" max="180" step="0.0000001" inputmode="decimal" placeholder="33.1234567"></label>
            </div>
            <x-location-map-live class="mt-5" :title="$editing ? $asset->reference.' · '.$asset->name : 'New equipment'" />
        </section>

        <section class="rounded-xl border border-border bg-white p-6 shadow-sm dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-heading">Condition, purchase & warranty</h2>
            <div class="mt-5 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <label class="text-sm font-semibold text-heading">Condition<select class="{{ $input }}" name="condition"><option value="">Use default (Good)</option>@foreach(['Good', 'Fair', 'Poor', 'Critical'] as $option)<option @selected($value('condition') === $option)>{{ $option }}</option>@endforeach</select></label>
                <label class="text-sm font-semibold text-heading">Operational Status<select class="{{ $input }}" name="operational_status"><option value="">Use default (Operational)</option>@foreach(['Operational', 'Faulty', 'Under Maintenance', 'Unassigned', 'Retired'] as $option)<option @selected($value('operational_status') === $option)>{{ $option }}</option>@endforeach</select></label>
                @foreach([['acquired_at', 'Purchase Date', 'date'], ['purchase_cost', 'Purchase Cost (UGX)', 'number'], ['supplier', 'Supplier', 'text'], ['warranty_expires_at', 'Warranty Expiry', 'date']] as [$key, $label, $type])
                    <label class="text-sm font-semibold text-heading">{{ $label }}<input type="{{ $type }}" class="{{ $input }}" name="{{ $key }}" value="{{ $value($key) instanceof \Carbon\CarbonInterface ? $value($key)->format('Y-m-d') : $value($key) }}" @if($key === 'purchase_cost') min="0" step="0.01" @endif></label>
                @endforeach
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('technician.equipment.index') }}" class="rounded-lg border border-border bg-white px-5 py-2.5 text-sm font-semibold text-heading dark:bg-slate-900">Cancel</a>
            <button class="rounded-lg bg-busitema-blue px-5 py-2.5 text-sm font-semibold text-white">{{ $editing ? 'Save Changes' : 'Register Equipment' }}</button>
        </div>
    </form>
</div>
@endsection
