@props([
    'title' => 'Property location',
])

{{-- Requires a parent gpsLocationPreview Alpine scope. --}}
<template x-teleport="body">
    <div class="fixed inset-0 z-50 flex items-end justify-center p-0 sm:items-center sm:p-6" x-show="mapOpen" x-cloak x-on:keydown.escape.window="closeMap()" role="dialog" aria-modal="true" aria-label="{{ $title }}">
        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" x-show="mapOpen" x-transition.opacity x-on:click="closeMap()"></div>

        <div class="relative flex max-h-[92vh] w-full max-w-4xl flex-col overflow-hidden rounded-t-2xl border border-border bg-white shadow-2xl sm:rounded-2xl" x-show="mapOpen" x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="translate-y-4 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <header class="flex items-start justify-between gap-4 border-b border-border px-5 py-4">
                <div class="min-w-0">
                    <p class="text-xs font-semibold tracking-[0.12em] text-busitema-blue uppercase">GPS location</p>
                    <h2 class="mt-1 truncate text-lg font-semibold text-heading">{{ $title }}</h2>
                    <p class="mt-0.5 font-mono text-xs text-body-text" x-text="coordinateLabel"></p>
                </div>
                <button type="button" class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-border text-body-text transition hover:text-heading" x-on:click="closeMap()" aria-label="Close map">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12" /></svg>
                </button>
            </header>

            <div class="h-[60vh] min-h-80 bg-light-background">
                <template x-if="mapOpen">
                    <iframe class="size-full border-0" x-bind:src="embedUrl(0.0025)" title="Map of {{ $title }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </template>
            </div>

            <footer class="flex flex-col gap-3 border-t border-border px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-body-text">Map data © OpenStreetMap contributors</p>
                <div class="flex flex-wrap gap-2">
                    <a class="inline-flex min-h-9 items-center gap-2 rounded-lg border border-border px-3 text-sm font-semibold text-heading transition hover:border-busitema-blue hover:text-busitema-blue" x-bind:href="openStreetMapUrl" target="_blank" rel="noopener">OpenStreetMap</a>
                    <a class="inline-flex min-h-9 items-center gap-2 rounded-lg bg-busitema-blue px-3 text-sm font-semibold text-white transition hover:bg-busitema-deep-blue" x-bind:href="googleMapsUrl" target="_blank" rel="noopener">
                        Open in Google Maps
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17 17 7M8 7h9v9" /></svg>
                    </a>
                </div>
            </footer>
        </div>
    </div>
</template>
