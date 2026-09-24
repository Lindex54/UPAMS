@props(['record', 'compact' => false])

@php
    $createdBy = $record->createdBy;
    $updatedBy = $record->updatedBy;
    $fields = [
        ['Added By User ID', $record->created_by],
        ['Added By Name', $createdBy?->name],
        ['Added By Role', $createdBy?->role?->name],
        ['Added At', $record->created_at?->format('d M Y H:i:s')],
        ['Last Updated By User ID', $record->updated_by],
        ['Last Updated By Name', $updatedBy?->name],
        ['Last Updated By Role', $updatedBy?->role?->name],
        ['Last Updated At', $record->updated_at?->format('d M Y H:i:s')],
    ];
@endphp

<div {{ $attributes->class(['grid gap-px overflow-hidden rounded-xl border border-border bg-border', 'sm:grid-cols-2 xl:grid-cols-4' => ! $compact, 'sm:grid-cols-2' => $compact]) }}>
    @foreach ($fields as [$label, $value])
        <div class="bg-white p-3 dark:bg-slate-900">
            <p class="text-[0.68rem] font-semibold tracking-wide text-body-text uppercase">{{ $label }}</p>
            <p class="mt-1 break-words text-sm font-semibold text-heading">{{ $value ?: '—' }}</p>
        </div>
    @endforeach
</div>
