@extends('layouts.app')

@section('title', 'GIS Map | Property Management')
@section('page-heading', 'GIS Map')

@section('content')
    <div class="flex flex-col gap-6" x-data="gisExplorer(@js(['features' => $features, 'layers' => $layers]))">
        <section class="rounded-xl border border-border bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold tracking-[0.12em] text-busitema-blue uppercase">Geographic Information</p>
                    <h2 class="mt-2 text-2xl font-semibold text-heading">University properties on the map</h2>                </div>
                <div class="flex flex-wrap gap-3">
                    @foreach ($layers as $key => $layer)
                        <div class="flex items-center gap-3 rounded-lg border border-border px-4 py-2.5">
                            <span class="size-3 rounded-full" style="background-color: {{ $layer['color'] }}" aria-hidden="true"></span>
                            <span class="text-sm text-body-text">{{ $layer['label'] }}</span>
                            <span class="text-lg font-semibold text-heading tabular-nums">{{ number_format($layer['count']) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="grid overflow-hidden rounded-xl border border-border bg-white shadow-sm lg:grid-cols-[20rem_1fr]">
            <aside class="flex flex-col border-b border-border lg:max-h-[calc(100vh-15rem)] lg:min-h-[36rem] lg:border-r lg:border-b-0" aria-label="Map filters and items">
                <div class="flex flex-col gap-3 border-b border-border p-4">
                    <label class="sr-only" for="gis-search">Search the map</label>
                    <input class="min-h-10 w-full rounded-lg border border-border bg-white px-3 text-sm text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" id="gis-search" type="search" placeholder="Search name, code, or campus" x-model.debounce.200ms="search">

                    <label class="sr-only" for="gis-campus">Filter by campus</label>
                    <select class="min-h-10 w-full rounded-lg border border-border bg-white px-3 text-sm text-heading outline-none focus:border-busitema-blue" id="gis-campus" x-model="campus">
                        <option value="">All campuses</option>
                        <template x-for="name in campuses" :key="name">
                            <option :value="name" x-text="name"></option>
                        </template>
                    </select>

                    <div>
                        <p class="mb-1.5 text-xs font-semibold text-body-text">Layers</p>
                        <div class="grid grid-cols-3 gap-1 rounded-lg bg-light-background p-1" role="radiogroup" aria-label="Map layers">
                            <button class="flex min-h-9 items-center justify-center gap-1 rounded-md px-1.5 text-xs font-semibold whitespace-nowrap transition focus-visible:outline-2 focus-visible:outline-busitema-blue" type="button" role="radio" x-on:click="setLayerFilter('all')" :aria-checked="layerFilter === 'all'" :class="layerFilter === 'all' ? 'bg-white text-heading shadow-sm dark:bg-slate-800' : 'text-body-text hover:text-heading'">
                                All
                                <span class="rounded bg-light-background px-1 py-0.5 text-[0.6875rem] tabular-nums" x-text="features.length"></span>
                            </button>
                            <template x-for="(layer, key) in layers" :key="key">
                                <button class="flex min-h-9 items-center justify-center gap-1 rounded-md px-1.5 text-xs font-semibold whitespace-nowrap transition focus-visible:outline-2 focus-visible:outline-busitema-blue disabled:cursor-not-allowed disabled:opacity-50" type="button" role="radio" x-on:click="setLayerFilter(key)" :disabled="layerCount(key) === 0" :aria-checked="layerFilter === key" :title="layerCount(key) === 0 ? `No ${layer.label.toLowerCase()} have GPS coordinates yet` : `Show only ${layer.label.toLowerCase()}`" :class="layerFilter === key ? 'bg-white text-heading shadow-sm dark:bg-slate-800' : 'text-body-text hover:text-heading'">
                                    <span class="size-2 shrink-0 rounded-full" :style="`background-color: ${layer.color}`"></span>
                                    <span x-text="layer.label"></span>
                                    <span class="rounded bg-light-background px-1 py-0.5 text-[0.6875rem] tabular-nums" x-text="layerCount(key)"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div>
                        <p class="mb-1.5 flex items-center justify-between text-xs font-semibold text-body-text">
                            Base map
                            <span class="inline-flex items-center gap-1.5 font-normal" x-show="baseLayerLoading" x-cloak>
                                <svg class="size-3 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M21 12a9 9 0 1 1-6.2-8.56" /></svg>
                                Loading tiles
                            </span>
                        </p>
                        <div class="grid grid-cols-2 gap-1 rounded-lg bg-light-background p-1" role="radiogroup" aria-label="Base map">
                            @foreach (['streets' => 'Streets', 'satellite' => 'Satellite'] as $baseLayer => $baseLabel)
                                <button class="flex min-h-9 items-center justify-center gap-2 rounded-md px-2 text-xs font-semibold transition focus-visible:outline-2 focus-visible:outline-busitema-blue" type="button" role="radio" x-on:click="setBaseLayer('{{ $baseLayer }}')" :aria-checked="baseLayer === '{{ $baseLayer }}'" :class="baseLayer === '{{ $baseLayer }}' ? 'bg-white text-heading shadow-sm dark:bg-slate-800' : 'text-body-text hover:text-heading'">
                                    @if ($baseLayer === 'streets')
                                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6ZM9 3v15M15 6v15" /></svg>
                                    @else
                                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18" /></svg>
                                    @endif
                                    {{ $baseLabel }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <button class="inline-flex min-h-9 w-full items-center justify-center gap-1.5 rounded-lg border border-border text-xs font-semibold text-busitema-deep-blue transition hover:border-busitema-blue hover:text-busitema-blue dark:text-sky-300" type="button" x-on:click="fitVisible()">
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7" /></svg>
                        Show all on map
                    </button>
                </div>

                <p class="px-4 pt-3 text-xs text-body-text" aria-live="polite"><span class="font-semibold text-heading" x-text="visibleFeatures.length"></span> of {{ number_format($features->count()) }} items shown</p>

                <ul class="max-h-80 flex-1 overflow-y-auto p-2 lg:max-h-none">
                    <template x-for="feature in visibleFeatures" :key="feature.id">
                        <li>
                            <button class="flex w-full items-start gap-3 rounded-lg p-2.5 text-left transition hover:bg-light-background" type="button" :id="`gis-item-${feature.id}`" x-on:click="focus(feature)" x-on:mouseenter="preview(feature)" x-on:mouseleave="clearPreview()" :class="selectedId === feature.id && 'bg-light-background ring-1 ring-busitema-blue/30'">
                                <span class="mt-1 size-2.5 shrink-0 rounded-full" :style="`background-color: ${layers[feature.layer].color}`"></span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-semibold text-heading" x-text="feature.title"></span>
                                    <span class="block truncate text-xs text-body-text" x-text="[feature.badge, feature.campus].filter(Boolean).join(' · ')"></span>
                                </span>
                            </button>
                        </li>
                    </template>
                    <li class="px-3 py-8 text-center text-sm text-body-text" x-show="visibleFeatures.length === 0" x-cloak>
                        {{ $features->isEmpty() ? 'Nothing has GPS coordinates yet. Add latitude and longitude to a campus or asset and it will appear here.' : 'No items match these filters.' }}
                    </li>
                </ul>
            </aside>

            <div class="relative isolate h-[28rem] lg:h-auto lg:min-h-[36rem]">
                <div class="absolute inset-0 z-0" x-ref="map" role="application" aria-label="Interactive map of university properties"></div>

                <div class="absolute inset-0 z-10 flex items-center justify-center bg-light-background text-sm text-body-text" x-show="status === 'loading'">Loading map…</div>
                <div class="absolute inset-0 z-10 flex items-center justify-center bg-light-background p-6 text-center text-sm text-body-text" x-show="status === 'failed'" x-cloak>The map could not be loaded. Check your internet connection and refresh the page.</div>
            </div>
        </section>
    </div>
@endsection
