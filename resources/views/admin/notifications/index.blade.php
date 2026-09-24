@extends('layouts.app')

@section('title', 'Notifications | Property Management')
@section('portal-label', 'Governance')
@section('page-heading', 'Notifications')

@section('content')
    @include('admin.partials.flash')

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold tracking-[0.12em] text-busitema-blue uppercase">System communication</p>
            <h2 class="mt-1 text-2xl font-bold text-heading">Notification centre</h2>
            <p class="mt-1 text-sm text-body-text">View available notification types and messages sent through Property Management.</p>
        </div>
        <a href="{{ route('notifications.create') }}" class="inline-flex min-h-12 items-center rounded-lg bg-busitema-gold px-5 font-bold text-slate-950 transition hover:bg-busitema-yellow">
            Compose notification
        </a>
    </div>

    <section class="mt-6 grid gap-4 sm:grid-cols-3" aria-label="Notification summary">
        <article class="rounded-xl border border-border bg-white p-5 shadow-sm dark:bg-slate-900">
            <p class="text-xs font-semibold tracking-wide text-body-text uppercase">Available templates</p>
            <p class="mt-2 text-3xl font-bold text-heading">{{ $templates->count() }}</p>
        </article>
        <article class="rounded-xl border border-border bg-white p-5 shadow-sm dark:bg-slate-900">
            <p class="text-xs font-semibold tracking-wide text-body-text uppercase">Notification history</p>
            <p class="mt-2 text-3xl font-bold text-heading">{{ $notificationCount }}</p>
        </article>
        <article class="rounded-xl border border-border bg-white p-5 shadow-sm dark:bg-slate-900">
            <p class="text-xs font-semibold tracking-wide text-body-text uppercase">Failed deliveries</p>
            <p class="mt-2 text-3xl font-bold {{ $failedCount > 0 ? 'text-red-600 dark:text-red-400' : 'text-heading' }}">{{ $failedCount }}</p>
        </article>
    </section>

    <section class="mt-6 rounded-2xl border border-border bg-white p-5 shadow-sm dark:bg-slate-900 sm:p-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-heading">Available notification templates</h2>
                <p class="mt-1 text-sm text-body-text">Active system messages ready for administrators to use.</p>
            </div>
            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-busitema-blue dark:bg-sky-950 dark:text-sky-300">{{ $templates->count() }} available</span>
        </div>

        <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($templates as $template)
                <article class="flex flex-col gap-4 rounded-xl border border-border bg-light-background p-5 dark:bg-slate-950">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold tracking-wide text-busitema-blue uppercase">{{ $template->event_type }}</p>
                            <h3 class="mt-1 text-base font-bold text-heading">{{ $template->name }}</h3>
                        </div>
                        <span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">Active</span>
                    </div>

                    <div class="flex-1">
                        <p class="text-sm font-semibold text-heading">{{ $template->subject }}</p>
                        <p class="mt-2 text-sm leading-6 text-body-text">{{ str($template->body)->limit(150) }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2" aria-label="Available channels">
                        @foreach ($template->channels as $channel)
                            <span class="rounded-md border border-border bg-white px-2.5 py-1 text-xs font-semibold text-body-text dark:bg-slate-900">
                                {{ ['in_system' => 'In-system', 'email' => 'Email-ready', 'sms' => 'SMS-ready'][$channel] ?? str($channel)->headline() }}
                            </span>
                        @endforeach
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-border p-8 text-center md:col-span-2 xl:col-span-3">
                    <p class="font-semibold text-heading">No notification templates are available.</p>
                    <p class="mt-1 text-sm text-body-text">Create a template below to make it available to administrators.</p>
                </div>
            @endforelse
        </div>
    </section>

    <section class="mt-6 overflow-hidden rounded-2xl border border-border bg-white shadow-sm dark:bg-slate-900">
        <div class="border-b border-border px-5 py-5 sm:px-6">
            <h2 class="text-lg font-bold text-heading">Sent notification history</h2>
            <p class="mt-1 text-sm text-body-text">All notifications composed and sent through the system.</p>
        </div>
        <div class="overflow-x-auto p-5 sm:p-6">
            <table class="w-full min-w-5xl text-left text-sm">
                <thead>
                    <tr class="border-b border-border text-xs uppercase text-body-text">
                        <th class="p-3">Reference / event</th>
                        <th class="p-3">Message</th>
                        <th class="p-3">Target</th>
                        <th class="p-3">Channels</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Created by / sent at</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($notifications as $notification)
                        <tr class="border-b border-border last:border-b-0">
                            <td class="p-3 font-semibold text-heading">{{ $notification->reference }}<br><span class="text-xs font-normal text-body-text">{{ $notification->event_type }}</span></td>
                            <td class="p-3"><strong class="text-heading">{{ $notification->title }}</strong><br><span class="text-xs text-body-text">{{ str($notification->message)->limit(80) }}</span></td>
                            <td class="p-3">{{ $notification->recipient?->name ?? $notification->target_role ?? $notification->campus?->name ?? '—' }}</td>
                            <td class="p-3">{{ implode(', ', $notification->channels) }}</td>
                            <td class="p-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $notification->status === 'Failed' ? 'bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-300' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' }}">
                                    {{ $notification->status }}
                                </span>
                            </td>
                            <td class="p-3 text-xs">{{ $notification->creator?->name ?? 'System' }}<br>{{ $notification->sent_at?->format('d M Y H:i') }}</td>
                            <td class="p-3">
                                <form method="POST" action="{{ route('notifications.resend', $notification) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="font-semibold text-busitema-blue dark:text-sky-300">Resend</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-8 text-center text-body-text">No notifications sent.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $notifications->links() }}</div>
        </div>
    </section>

    <form method="POST" action="{{ route('notifications.templates.store') }}" class="mt-6 rounded-2xl border border-border bg-white p-5 shadow-sm dark:bg-slate-900 sm:p-6">
        @csrf
        <h2 class="text-lg font-bold text-heading">Create notification template</h2>
        <p class="mt-1 text-sm text-body-text">Add another reusable message to the available notification list.</p>
        <div class="mt-4 grid gap-3 md:grid-cols-2">
            <input name="name" required class="rounded-lg border border-border bg-transparent p-3" placeholder="Template name">
            <select name="event_type" class="rounded-lg border border-border bg-transparent p-3">
                @foreach (['Expiry', 'Arrears', 'Servicing', 'Calibration', 'Inspection', 'Maintenance', 'Approval', 'General'] as $type)
                    <option>{{ $type }}</option>
                @endforeach
            </select>
            <input name="subject" required class="rounded-lg border border-border bg-transparent p-3 md:col-span-2" placeholder="Subject">
            <textarea name="body" required class="rounded-lg border border-border bg-transparent p-3 md:col-span-2" placeholder="Reusable message body"></textarea>
            <label><input type="checkbox" name="channels[]" value="in_system" checked> In-system</label>
            <label><input type="checkbox" name="channels[]" value="email"> Email-ready</label>
            <label><input type="checkbox" name="channels[]" value="sms"> SMS-ready</label>
        </div>
        <input type="hidden" name="is_active" value="1">
        <button class="mt-4 rounded-lg bg-busitema-blue px-4 py-2 font-semibold text-white">Save template</button>
    </form>
@endsection
