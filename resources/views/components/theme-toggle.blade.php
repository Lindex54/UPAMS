<button
    {{ $attributes->merge(['class' => 'inline-flex size-10 shrink-0 items-center justify-center rounded-lg border border-border bg-white text-busitema-deep-blue shadow-sm transition-colors hover:border-busitema-blue hover:text-busitema-blue focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-busitema-blue']) }}
    type="button"
    data-theme-toggle
    aria-label="Switch color theme"
    aria-pressed="false"
    title="Switch color theme"
>
    <svg class="size-5 dark:hidden" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20.4 15.2A8.5 8.5 0 0 1 8.8 3.6 8.5 8.5 0 1 0 20.4 15.2Z" />
    </svg>
    <svg class="hidden size-5 dark:block" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <circle cx="12" cy="12" r="4" />
        <path stroke-linecap="round" d="M12 2v2M12 20v2M4.93 4.93l1.42 1.42M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.42-1.42M17.66 6.34l1.41-1.41" />
    </svg>
    <span class="sr-only">Toggle color theme</span>
</button>
