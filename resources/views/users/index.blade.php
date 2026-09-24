@extends('layouts.app')

@section('title', 'Users | Property Management')
@section('page-heading', 'Users')

@section('content')
    <div class="flex flex-col gap-6">
        <section class="rounded-xl border border-border bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold tracking-[0.12em] text-busitema-blue uppercase">System Administration</p>
                    <h2 class="mt-2 text-2xl font-semibold text-heading">User accounts</h2>
                    <p class="mt-2 text-sm leading-6 text-body-text">Create accounts, update basic details, and control access to Property Management.</p>
                </div>

                <a
                    class="inline-flex min-h-11 items-center justify-center gap-2 self-start rounded-lg bg-busitema-gold px-5 text-sm font-semibold text-busitema-navy shadow-sm transition hover:bg-busitema-yellow focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-busitema-blue"
                    href="{{ route('users.create') }}"
                >
                    <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                    </svg>
                    Add user
                </a>
            </div>
        </section>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                {{ session('status') }}
            </div>
        @endif

        @error('account')
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                {{ $message }}
            </div>
        @enderror

        <section class="overflow-hidden rounded-xl border border-border bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-3xl text-left text-sm">
                    <thead class="bg-light-background text-xs font-semibold tracking-wide text-body-text uppercase">
                        <tr>
                            <th class="px-6 py-3">User</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Created</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse ($users as $user)
                            <tr class="transition hover:bg-light-background/70">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-busitema-blue/10 font-semibold text-busitema-blue" aria-hidden="true">
                                            {{ str($user->name)->substr(0, 1)->upper() }}
                                        </span>
                                        <div>
                                            <p class="font-semibold text-heading">{{ $user->name }}</p>
                                            <p class="mt-0.5 text-xs text-body-text">{{ $user->email }}</p>
                                            @if ($user->role || $user->campus)
                                                <p class="mt-1 text-xs text-body-text">
                                                    {{ collect([$user->role?->name, $user->campus?->name])->filter()->implode(' · ') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($user->is_active)
                                        <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Active</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">Inactive</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-body-text">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            class="inline-flex min-h-9 items-center rounded-lg border border-border px-3 text-xs font-semibold text-busitema-deep-blue transition hover:border-busitema-blue hover:text-busitema-blue"
                                            href="{{ route('users.edit', $user) }}"
                                        >
                                            Edit
                                        </a>

                                        @if (auth()->id() === $user->id)
                                            <span class="whitespace-nowrap text-xs font-medium text-body-text">Current account</span>
                                        @else
                                            <form method="POST" action="{{ route('users.status.update', $user) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="is_active" value="{{ $user->is_active ? '0' : '1' }}">
                                                <button
                                                    @class([
                                                        'inline-flex min-h-9 items-center rounded-lg border px-3 text-xs font-semibold transition focus-visible:outline-2 focus-visible:outline-offset-2',
                                                        'border-red-200 text-red-700 hover:bg-red-50 focus-visible:outline-red-600' => $user->is_active,
                                                        'border-emerald-200 text-emerald-700 hover:bg-emerald-50 focus-visible:outline-emerald-600' => ! $user->is_active,
                                                    ])
                                                    type="submit"
                                                >
                                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-12 text-center text-body-text" colspan="4">No user accounts have been created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="border-t border-border px-6 py-4">
                    {{ $users->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
