@extends('layouts.app')

@section('title', $campus->name.' | Property Management')
@section('page-heading', $campus->name)

@section('content')
    <div class="mx-auto flex max-w-5xl flex-col gap-6">
        <nav class="flex flex-wrap items-center gap-2 text-sm text-body-text" aria-label="Breadcrumb">
            <a class="font-medium hover:text-busitema-blue" href="{{ route('campuses.index') }}">Campuses</a><span aria-hidden="true">/</span><span class="font-semibold text-heading">{{ $campus->name }}</span>
        </nav>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-xl border border-border bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-4">
                    <span class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-busitema-blue font-mono text-sm font-bold text-white">{{ $campus->code ?? '—' }}</span>
                    <div>
                        <p class="text-sm font-semibold tracking-[0.12em] text-busitema-blue uppercase">Campus</p>
                        <h2 class="mt-1 text-2xl font-semibold text-heading">{{ $campus->name }}</h2>
                        <p class="mt-1 text-sm text-body-text">{{ $campus->faculty ?? 'Faculty not recorded' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if ($campus->isActive())
                        <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Active</span>
                    @else
                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">Inactive</span>
                    @endif
                    @can('update', $campus)
                        <a class="inline-flex min-h-10 items-center rounded-lg border border-border px-4 text-sm font-semibold text-busitema-deep-blue transition hover:border-busitema-blue hover:text-busitema-blue" href="{{ route('campuses.edit', $campus) }}">Edit campus</a>
                    @endcan
                </div>
            </div>

            <dl class="mt-8 grid gap-x-8 gap-y-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    'Campus code' => $campus->code,
                    'District' => $campus->district?->name,
                    'Physical location' => $campus->location,
                    'Registered assets' => number_format($campus->assets_count),
                    'User accounts' => number_format($campus->users_count),
                    'Computer labs' => number_format($campus->computer_labs_count),
                ] as $label => $value)
                    <div class="border-b border-border pb-4">
                        <dt class="text-xs font-semibold tracking-wide text-body-text uppercase">{{ $label }}</dt>
                        <dd class="mt-1.5 text-sm font-semibold text-heading">{{ filled($value) ? $value : 'Not recorded' }}</dd>
                    </div>
                @endforeach
            </dl>

            <x-location-map class="mt-6" :latitude="$campus->latitude" :longitude="$campus->longitude" :title="$campus->name" />
        </section>
    </div>
@endsection
