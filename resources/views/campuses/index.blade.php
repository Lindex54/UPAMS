@extends('layouts.app')

@section('title', 'Campuses | Property Management')
@section('page-heading', 'Campuses')

@section('content')
    <div class="flex flex-col gap-6">
        <section class="rounded-xl border border-border bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold tracking-[0.12em] text-busitema-blue uppercase">System Administration</p>
                    <h2 class="mt-2 text-2xl font-semibold text-heading">University campuses</h2>
                    <p class="mt-2 text-sm leading-6 text-body-text">Busitema University's campuses, where they are, and the faculty each one hosts.</p>
                </div>

                @can('create', App\Models\Campus::class)
                    <a
                        class="inline-flex min-h-11 items-center justify-center gap-2 self-start rounded-lg bg-busitema-gold px-5 text-sm font-semibold text-busitema-navy shadow-sm transition hover:bg-busitema-yellow focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-busitema-blue"
                        href="{{ route('campuses.create') }}"
                    >
                        <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                        </svg>
                        Add campus
                    </a>
                @endcan
            </div>
        </section>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                {{ session('status') }}
            </div>
        @endif

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3" aria-label="Campus list">
            @forelse ($campuses as $campus)
                <a class="group flex flex-col rounded-xl border border-border bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-busitema-blue/40 hover:shadow-md" href="{{ route('campuses.show', $campus) }}">
                    <div class="flex items-start justify-between gap-3">
                        <span class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-busitema-blue font-mono text-xs font-bold text-white">{{ $campus->code ?? '—' }}</span>
                        @if ($campus->isActive())
                            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Active</span>
                        @else
                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">Inactive</span>
                        @endif
                    </div>

                    <h3 class="mt-4 text-lg font-semibold text-heading group-hover:text-busitema-blue">{{ $campus->name }}</h3>
                    <p class="mt-1 text-sm text-body-text">{{ collect([$campus->location, $campus->district?->name ? $campus->district->name.' District' : null])->filter()->implode(' · ') ?: 'Location not recorded' }}</p>
                    @if ($campus->faculty)
                        <p class="mt-3 text-sm font-medium text-heading">{{ $campus->faculty }}</p>
                    @endif

                    <div class="mt-auto pt-5"><div class="flex items-center gap-4 border-t border-border pt-4 text-xs text-body-text">
                        <span><strong class="font-semibold text-heading">{{ number_format($campus->assets_count) }}</strong> assets</span>
                        <span><strong class="font-semibold text-heading">{{ number_format($campus->users_count) }}</strong> users</span>
                        @if ($campus->latitude !== null)
                            <span class="ml-auto inline-flex items-center gap-1 text-busitema-blue">
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 21s-7-5.5-7-11a7 7 0 1 1 14 0c0 5.5-7 11-7 11Z" /><circle cx="12" cy="10" r="2.5" /></svg>
                                On map
                            </span>
                        @endif
                    </div></div>
                </a>
            @empty
                <div class="rounded-xl border border-dashed border-border bg-white px-6 py-12 text-center text-sm text-body-text sm:col-span-2 xl:col-span-3">
                    No campuses have been added yet.
                </div>
            @endforelse
        </section>
    </div>
@endsection
