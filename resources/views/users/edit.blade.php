@extends('layouts.app')

@section('title', 'Edit User | Property Management')
@section('page-heading', 'Edit User')

@section('content')
    <div class="mx-auto flex max-w-3xl flex-col gap-6">
        <section class="rounded-xl border border-border bg-white p-6 shadow-sm sm:p-8">
            <div>
                <p class="text-sm font-semibold tracking-[0.12em] text-busitema-blue uppercase">System Administration</p>
                <h2 class="mt-2 text-2xl font-semibold text-heading">Edit user details</h2>
                <p class="mt-2 text-sm leading-6 text-body-text">Update the account name or email address. Account status is managed from the Users list.</p>
            </div>

            @if ($errors->any())
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    Please check the highlighted details and try again.
                </div>
            @endif

            <form class="mt-8 flex flex-col gap-8" method="POST" action="{{ route('users.update', $user) }}">
                @csrf
                @method('PUT')

                @include('users._form', ['user' => $user, 'includePassword' => false])

                <div class="flex flex-wrap items-center justify-end gap-3 border-t border-border pt-6">
                    <a class="inline-flex min-h-11 items-center rounded-lg border border-border px-5 text-sm font-semibold text-busitema-deep-blue transition hover:border-busitema-blue hover:text-busitema-blue" href="{{ route('users.index') }}">Cancel</a>
                    <button class="inline-flex min-h-11 items-center rounded-lg bg-busitema-gold px-5 text-sm font-semibold text-busitema-navy shadow-sm transition hover:bg-busitema-yellow focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-busitema-blue" type="submit">Save changes</button>
                </div>
            </form>
        </section>
    </div>
@endsection
