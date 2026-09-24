@props([
    'latitude' => null,
    'longitude' => null,
    'title' => 'Property location',
])

@php
    $hasLocation = filled($latitude) && filled($longitude);
@endphp

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl border border-border bg-white']) }} x-data="gpsLocationPreview(@js(['latitude' => $latitude, 'longitude' => $longitude]))">
    @if ($hasLocation)
        <button type="button" class="group relative block h-56 w-full overflow-hidden bg-light-background text-left" x-on:click="openMap()" aria-label="Open the map for {{ $title }}">
            <iframe class="pointer-events-none size-full border-0" x-bind:src="embedUrl()" title="Map preview of {{ $title }}" loading="lazy" tabindex="-1"></iframe>
            <span class="absolute inset-0 bg-slate-950/0 transition group-hover:bg-slate-950/10"></span>
            <span class="absolute right-3 bottom-3 inline-flex items-center gap-1.5 rounded-lg bg-white/95 px-3 py-1.5 text-xs font-semibold text-heading shadow-md ring-1 ring-black/5 transition group-hover:bg-busitema-blue group-hover:text-white">
                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7" /></svg>
                View full map
            </span>
        </button>

        <div class="flex flex-col gap-3 border-t border-border px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <button type="button" class="flex min-w-0 items-center gap-2.5 text-left" x-on:click="openMap()">
                <span class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-busitema-blue text-white">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-5.5-7-11a7 7 0 1 1 14 0c0 5.5-7 11-7 11Z" /><circle cx="12" cy="10" r="2.5" /></svg>
                </span>
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-heading">Property location</span>
                    <span class="block truncate font-mono text-xs text-body-text" x-text="coordinateLabel">{{ $latitude }}, {{ $longitude }}</span>
                </span>
            </button>
            <a class="inline-flex min-h-9 w-fit items-center gap-1.5 rounded-lg border border-border px-3 text-sm font-semibold text-busitema-blue transition hover:border-busitema-blue" x-bind:href="googleMapsUrl" target="_blank" rel="noopener">
                Google Maps
                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17 17 7M8 7h9v9" /></svg>
            </a>
        </div>

        <x-location-map-modal :title="$title" />
    @else
        <div class="flex items-center gap-3 px-4 py-5">
            <span class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-dashed border-border text-body-text">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-5.5-7-11a7 7 0 1 1 14 0c0 5.5-7 11-7 11Z" /><circle cx="12" cy="10" r="2.5" /></svg>
            </span>
            <div>
                <p class="text-sm font-semibold text-heading">No GPS location recorded</p>
                <p class="mt-0.5 text-xs text-body-text">Add latitude and longitude to see this property on a map.</p>
            </div>
        </div>
    @endif
</div>
