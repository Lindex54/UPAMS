@props([
    'label',
    'value' => '—',
    'detail' => null,
    'tone' => 'blue',
])

@php
    $tones = [
        'blue' => [
            'card' => 'bg-blue-50 border-blue-200 dark:border-blue-500/30',
            'orb' => 'bg-busitema-blue/10',
            'icon' => 'bg-busitema-blue text-white',
            'label' => 'text-blue-900/80 dark:text-blue-200',
            'value' => 'text-busitema-deep-blue dark:text-white',
            'detail' => 'text-blue-900/70 dark:text-blue-200/80',
            'dot' => 'bg-busitema-blue',
        ],
        'gold' => [
            'card' => 'bg-amber-50 border-amber-200 dark:border-amber-500/30',
            'orb' => 'bg-amber-400/15',
            'icon' => 'bg-busitema-gold text-amber-950',
            'label' => 'text-amber-900/80 dark:text-amber-200',
            'value' => 'text-amber-950 dark:text-white',
            'detail' => 'text-amber-900/70 dark:text-amber-200/80',
            'dot' => 'bg-amber-500',
        ],
        'green' => [
            'card' => 'bg-emerald-50 border-emerald-200 dark:border-emerald-500/30',
            'orb' => 'bg-emerald-500/10',
            'icon' => 'bg-emerald-600 text-white',
            'label' => 'text-emerald-900/80 dark:text-emerald-200',
            'value' => 'text-emerald-950 dark:text-white',
            'detail' => 'text-emerald-900/70 dark:text-emerald-200/80',
            'dot' => 'bg-emerald-500',
        ],
        'orange' => [
            'card' => 'bg-orange-50 border-orange-200 dark:border-orange-500/30',
            'orb' => 'bg-orange-500/10',
            'icon' => 'bg-orange-500 text-white',
            'label' => 'text-orange-900/80 dark:text-orange-200',
            'value' => 'text-orange-950 dark:text-white',
            'detail' => 'text-orange-900/70 dark:text-orange-200/80',
            'dot' => 'bg-orange-500',
        ],
        'red' => [
            'card' => 'bg-red-50 border-red-200 dark:border-red-500/30',
            'orb' => 'bg-red-500/10',
            'icon' => 'bg-red-600 text-white',
            'label' => 'text-red-900/80 dark:text-red-200',
            'value' => 'text-red-950 dark:text-white',
            'detail' => 'text-red-900/70 dark:text-red-200/80',
            'dot' => 'bg-red-500',
        ],
        'purple' => [
            'card' => 'bg-violet-50 border-violet-200 dark:border-violet-500/30',
            'orb' => 'bg-violet-500/10',
            'icon' => 'bg-violet-600 text-white',
            'label' => 'text-violet-900/80 dark:text-violet-200',
            'value' => 'text-violet-950 dark:text-white',
            'detail' => 'text-violet-900/70 dark:text-violet-200/80',
            'dot' => 'bg-violet-500',
        ],
    ];
    $selectedTone = $tones[$tone] ?? $tones['blue'];
@endphp

<article {{ $attributes->merge(['class' => 'relative isolate flex flex-col gap-5 overflow-hidden rounded-xl border py-5 shadow-xs transition duration-200 hover:-translate-y-0.5 hover:shadow-md '.$selectedTone['card']]) }}>
    <div class="absolute -top-10 -right-10 -z-10 size-32 rounded-full {{ $selectedTone['orb'] }}" aria-hidden="true"></div>

    <header class="grid grid-cols-[1fr_auto] items-start gap-x-3 gap-y-1.5 px-5">
        <p class="text-sm font-medium leading-5 {{ $selectedTone['label'] }}">{{ $label }}</p>

        <div class="row-span-2 flex size-10 items-center justify-center rounded-lg shadow-sm [&>svg]:size-5 {{ $selectedTone['icon'] }}" aria-hidden="true">
            {{ $slot }}
        </div>

        <p class="text-3xl font-semibold tracking-tight tabular-nums {{ $selectedTone['value'] }}">{{ $value }}</p>
    </header>

    @if ($detail)
        <footer class="flex items-center gap-2 px-5 text-sm {{ $selectedTone['detail'] }}">
            <span class="size-1.5 shrink-0 rounded-full {{ $selectedTone['dot'] }}" aria-hidden="true"></span>
            <span class="truncate">{{ $detail }}</span>
        </footer>
    @endif
</article>
