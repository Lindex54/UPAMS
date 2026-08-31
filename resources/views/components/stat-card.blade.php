@props([
    'label',
    'value' => '—',
    'detail' => null,
    'tone' => 'blue',
])

@php
    $tones = [
        'blue' => ['icon' => 'bg-blue-50 text-busitema-blue', 'accent' => 'bg-busitema-blue'],
        'gold' => ['icon' => 'bg-amber-50 text-amber-700', 'accent' => 'bg-busitema-gold'],
        'green' => ['icon' => 'bg-emerald-50 text-emerald-700', 'accent' => 'bg-emerald-500'],
        'orange' => ['icon' => 'bg-orange-50 text-orange-700', 'accent' => 'bg-orange-500'],
        'red' => ['icon' => 'bg-red-50 text-red-700', 'accent' => 'bg-red-500'],
        'purple' => ['icon' => 'bg-violet-50 text-violet-700', 'accent' => 'bg-violet-500'],
    ];
    $selectedTone = $tones[$tone] ?? $tones['blue'];
@endphp

<article {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-xl border border-border bg-white p-5 shadow-sm']) }}>
    <div class="absolute inset-y-0 left-0 w-1 {{ $selectedTone['accent'] }}" aria-hidden="true"></div>

    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-sm font-medium leading-5 text-body-text">{{ $label }}</p>
            <p class="mt-2 text-2xl font-semibold tracking-tight text-heading">{{ $value }}</p>
        </div>

        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg {{ $selectedTone['icon'] }}" aria-hidden="true">
            {{ $slot }}
        </div>
    </div>

    @if ($detail)
        <p class="mt-3 text-xs text-body-text">{{ $detail }}</p>
    @endif
</article>
