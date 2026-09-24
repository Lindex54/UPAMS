@props([
    'title' => 'Property location',
])

{{-- Live map preview for GPS inputs; requires a parent gpsLocationPreview Alpine scope. --}}
<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl border border-border bg-white']) }}>
    <template x-if="hasLocation">
        <div>
            <button type="button" class="group relative block h-52 w-full overflow-hidden bg-light-background" x-on:click="openMap()" aria-label="Open the full map">
                <iframe class="pointer-events-none size-full border-0" x-bind:src="embedUrl()" title="Map preview of the entered coordinates" loading="lazy" tabindex="-1"></iframe>
                <span class="absolute inset-0 transition group-hover:bg-slate-950/10"></span>
                <span class="absolute right-3 bottom-3 rounded-lg bg-white/95 px-3 py-1.5 text-xs font-semibold text-heading shadow-md ring-1 ring-black/5 transition group-hover:bg-busitema-blue group-hover:text-white">View full map</span>
            </button>
            <p class="flex items-center gap-2 border-t border-border px-4 py-2.5 text-xs text-body-text">
                <span class="size-1.5 rounded-full bg-emerald-500"></span>
                Pin placed at <span class="font-mono font-semibold text-heading" x-text="coordinateLabel"></span>. Check that it matches the property before saving.
            </p>
        </div>
    </template>

    <template x-if="!hasLocation">
        <div class="flex items-center gap-3 px-4 py-4 text-xs text-body-text">
            <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-5.5-7-11a7 7 0 1 1 14 0c0 5.5-7 11-7 11Z" /><circle cx="12" cy="10" r="2.5" /></svg>
            Enter both a valid latitude and longitude to preview the location on a map.
        </div>
    </template>

    <x-location-map-modal :title="$title" />
</div>
