@php
    $actionDesigns = [
        'approval' => [
            'eyebrow' => 'Agreement approval',
            'title' => 'Review and approve agreement',
            'description' => 'Confirm the approval route, supporting evidence, financial terms, and authorization decision.',
            'sectionTitle' => 'Approval decision',
            'fields' => [
                ['Review Outcome', 'select', ['Approve', 'Return for amendment', 'Reject']],
                ['Approval Authority', 'select', ['Management Committee', 'University Secretary', 'University Council']],
                ['Decision Date', 'date', []],
                ['Approval Reference', 'text', []],
            ],
            'notes' => 'Decision Notes',
            'button' => 'Confirm approval decision',
        ],
        'renewal' => [
            'eyebrow' => 'Agreement renewal',
            'title' => 'Prepare agreement renewal',
            'description' => 'Review performance, propose the next term, update financial conditions, and route the renewal for approval.',
            'sectionTitle' => 'Renewal proposal',
            'fields' => [
                ['Renewal Start Date', 'date', []],
                ['Renewal Expiry Date', 'date', []],
                ['Revised Consideration / Fee', 'text', []],
                ['Renewal Approval Route', 'select', ['Management Committee', 'University Secretary', 'University Council']],
            ],
            'notes' => 'Renewal Assessment',
            'button' => 'Create renewal draft',
        ],
        'termination' => [
            'eyebrow' => 'Agreement termination',
            'title' => 'Initiate agreement termination',
            'description' => 'Record the basis, effective date, notice, clearance requirements, and authorization for termination.',
            'sectionTitle' => 'Termination instruction',
            'fields' => [
                ['Termination Reason', 'select', ['Expiry without renewal', 'Breach of terms', 'Mutual agreement', 'Administrative decision']],
                ['Effective Date', 'date', []],
                ['Notice Reference', 'text', []],
                ['Authorizing Officer', 'text', []],
            ],
            'notes' => 'Clearance and Closure Requirements',
            'button' => 'Create termination draft',
        ],
    ];

    $actionDesign = $actionDesigns[$agreementAction];
@endphp

<div class="mx-auto flex max-w-5xl flex-col gap-6">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-body-text" aria-label="Breadcrumb"><a class="font-medium hover:text-busitema-blue" href="{{ route('operations.agreements.index') }}">Agreements &amp; Allocations</a><span aria-hidden="true">/</span><a class="font-medium hover:text-busitema-blue" href="{{ route('operations.agreements.show', ['record' => $record['reference']]) }}">{{ $record['reference'] }}</a><span aria-hidden="true">/</span><span class="font-semibold text-heading">{{ $actionDesign['eyebrow'] }}</span></nav>

    <section class="overflow-hidden rounded-2xl bg-busitema-blue shadow-sm transition-colors dark:bg-blue-950"><div class="relative px-6 py-7 sm:px-8"><div class="absolute -top-16 right-4 size-52 rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div><div class="relative"><p class="text-xs font-semibold tracking-[0.14em] text-busitema-gold uppercase">{{ $actionDesign['eyebrow'] }} · {{ $record['reference'] }}</p><h2 class="mt-2 text-2xl font-semibold text-white sm:text-3xl">{{ $actionDesign['title'] }}</h2><p class="mt-2 max-w-3xl text-sm leading-6 text-white/75">{{ $actionDesign['description'] }}</p></div></div></section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">@foreach ([['Agreement', $record['name']], ['Beneficiary', 'Campus Bookshop Ltd'], ['Current Status', $record['status']], ['Current Term', '01 Jan 2025 – 31 Dec 2026']] as [$label, $value])<article class="rounded-xl border border-border bg-white p-4 shadow-sm"><p class="text-xs text-body-text">{{ $label }}</p><p class="mt-1.5 text-sm font-semibold text-heading">{{ $value }}</p></article>@endforeach</section>

    <form class="flex flex-col gap-6" onsubmit="return false">
        <section class="rounded-xl border border-border bg-white p-6 shadow-sm sm:p-8"><div class="border-b border-border pb-5"><p class="text-xs font-semibold tracking-[0.12em] text-busitema-blue uppercase">Lifecycle control</p><h2 class="mt-1.5 text-xl font-semibold text-heading">{{ $actionDesign['sectionTitle'] }}</h2></div><div class="mt-6 grid gap-5 sm:grid-cols-2">@foreach ($actionDesign['fields'] as [$label, $type, $options])<label class="flex flex-col gap-2 text-sm font-semibold text-heading"><span class="inline-flex items-center gap-1">{{ $label }} <span class="text-red-600">*</span></span>@if ($type === 'select')<select class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none focus:border-busitema-blue" required><option value="">Select {{ strtolower($label) }}</option>@foreach ($options as $option)<option>{{ $option }}</option>@endforeach</select>@else<input class="min-h-11 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none focus:border-busitema-blue" type="{{ $type }}" required>@endif</label>@endforeach<label class="flex flex-col gap-2 text-sm font-semibold text-heading sm:col-span-2"><span>{{ $actionDesign['notes'] }} <span class="font-normal text-body-text">(optional)</span></span><textarea class="min-h-32 rounded-lg border border-border bg-white px-3 py-3 text-sm font-normal text-heading outline-none focus:border-busitema-blue" placeholder="Add administrative context, conditions, and follow-up requirements"></textarea></label></div></section>
        <section class="rounded-xl border border-border bg-white p-6 shadow-sm"><h2 class="text-base font-semibold text-heading">Activity and accountability</h2><dl class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4"><div><dt class="text-xs text-body-text">Created By</dt><dd class="mt-1 text-sm font-semibold text-heading">{{ $record['createdBy'] }}</dd></div><div><dt class="text-xs text-body-text">Created At</dt><dd class="mt-1 text-sm font-semibold text-heading">{{ $record['createdAt'] }}</dd></div><div><dt class="text-xs text-body-text">Last Updated By</dt><dd class="mt-1 text-sm font-semibold text-heading">{{ $record['updatedBy'] }}</dd></div><div><dt class="text-xs text-body-text">Last Updated At</dt><dd class="mt-1 text-sm font-semibold text-heading">{{ $record['updatedAt'] }}</dd></div></dl></section>
        <div class="flex flex-wrap justify-end gap-3 rounded-xl border border-border bg-white p-4 shadow-sm"><a class="inline-flex min-h-11 items-center rounded-lg border border-border px-5 text-sm font-semibold text-busitema-deep-blue" href="{{ route('operations.agreements.show', ['record' => $record['reference']]) }}">Cancel</a><button class="inline-flex min-h-11 items-center rounded-lg bg-busitema-gold px-5 text-sm font-semibold text-busitema-navy" type="button">{{ $actionDesign['button'] }}</button></div>
    </form>
</div>
