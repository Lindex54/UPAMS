@extends('layouts.guest')

@section('title', 'Sign in | UPAMS')

@section('content')
    <div
        class="grid min-h-dvh bg-busitema-white lg:grid-cols-[minmax(22rem,0.88fr)_minmax(34rem,1.12fr)]"
        x-data="{ showPassword: false, showRecoveryHelp: false }"
    >
        <section class="hidden bg-busitema-blue px-10 py-12 text-white lg:flex lg:flex-col xl:px-16 xl:py-14">
            <div>
                <div class="theme-brand-surface inline-flex rounded-2xl bg-white p-4 shadow-lg shadow-black/10">
                    <img
                        class="h-auto w-64"
                        src="{{ asset('images/busitema-logo.png') }}"
                        alt="Busitema University"
                    >
                </div>
            </div>

            <div class="my-auto w-full max-w-2xl py-16">
                <h1 class="text-[clamp(1.875rem,3vw,3.75rem)] font-semibold leading-[1.14] tracking-tight text-white">
                    <span class="block whitespace-nowrap">University Property and</span>
                    <span class="block whitespace-nowrap">Asset Management System</span>
                    <span class="block whitespace-nowrap">(UPAMS)</span>
                </h1>
            </div>

            <p class="text-sm text-white/80">
                Busitema University &middot; Pursuing Excellence
            </p>
        </section>

        <section class="flex min-h-dvh items-center justify-center bg-light-background px-5 py-8 sm:px-10 sm:py-12 lg:px-14 xl:px-20">
            <div class="w-full max-w-md">
                <div class="mb-9 flex justify-center lg:hidden">
                    <img
                        class="h-auto w-full max-w-72"
                        src="{{ asset('images/busitema-logo.png') }}"
                        alt="Busitema University"
                    >
                </div>

                <div class="rounded-2xl border border-border bg-white p-6 shadow-[0_18px_55px_rgba(0,31,63,0.08)] sm:p-9 lg:border-0 lg:bg-transparent lg:p-0 lg:shadow-none">
                    <div>
                        <p class="text-sm font-semibold tracking-[0.14em] text-busitema-blue uppercase">UPAMS Portal</p>
                        <h2 class="mt-2 text-3xl font-semibold tracking-tight text-heading sm:text-4xl">Sign in to your account</h2>
                        <p class="mt-3 text-base leading-7 text-body-text">
                            Enter your university account details to continue.
                        </p>
                    </div>

                    @if (session('status'))
                        <div class="mt-6 rounded-lg border border-busitema-blue/20 bg-busitema-blue/5 px-4 py-3 text-sm text-busitema-deep-blue" role="status">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                            Please check the highlighted details and try again.
                        </div>
                    @endif

                    <form class="mt-8 flex flex-col gap-6" method="POST" action="{{ route('login.store') }}" novalidate>
                        @csrf

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-heading" for="email">Email address</label>
                            <input
                                class="block w-full rounded-lg border bg-white px-4 py-3 text-base text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-3 focus:ring-busitema-blue/15 @error('email') border-red-400 @else border-border @enderror"
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                placeholder="name@busitema.ac.ug"
                                autocomplete="username"
                                inputmode="email"
                                required
                                autofocus
                                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                            >
                            @error('email')
                                <p class="mt-2 text-sm text-red-600" id="email-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-heading" for="password">Password</label>
                            <div class="relative">
                                <input
                                    class="block w-full rounded-lg border bg-white px-4 py-3 pr-12 text-base text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-3 focus:ring-busitema-blue/15 @error('password') border-red-400 @else border-border @enderror"
                                    id="password"
                                    name="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    placeholder="Enter your password"
                                    autocomplete="current-password"
                                    required
                                    @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                                >
                                <button
                                    class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-body-text transition hover:text-busitema-blue focus-visible:rounded-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-busitema-blue"
                                    type="button"
                                    @click="showPassword = ! showPassword"
                                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                    :aria-pressed="showPassword"
                                >
                                    <svg x-show="! showPassword" class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-6 9.75-6 9.75 6 9.75 6-3.5 6-9.75 6S2.25 12 2.25 12Z" />
                                        <circle cx="12" cy="12" r="2.75" />
                                    </svg>
                                    <svg x-cloak x-show="showPassword" class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.6 6.15A10.7 10.7 0 0 1 12 6c6.25 0 9.75 6 9.75 6a16.7 16.7 0 0 1-2.4 3.1M6.25 7.25C3.65 9.05 2.25 12 2.25 12S5.75 18 12 18c1.25 0 2.4-.24 3.43-.64M9.88 9.88a3 3 0 0 0 4.24 4.24" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600" id="password-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <label class="inline-flex cursor-pointer items-center gap-2.5 text-sm text-body-text" for="remember">
                                <input
                                    class="size-4 rounded border-border text-busitema-blue focus:ring-busitema-blue/25"
                                    id="remember"
                                    name="remember"
                                    type="checkbox"
                                    value="1"
                                    @checked(old('remember'))
                                >
                                <span>Remember me</span>
                            </label>

                            <button
                                class="text-sm font-semibold text-busitema-blue underline-offset-4 transition hover:text-busitema-deep-blue hover:underline focus-visible:rounded-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-busitema-blue"
                                type="button"
                                @click="showRecoveryHelp = ! showRecoveryHelp"
                                :aria-expanded="showRecoveryHelp"
                                aria-controls="password-assistance"
                            >
                                Forgot password?
                            </button>
                        </div>

                        <div
                            x-cloak
                            x-show="showRecoveryHelp"
                            x-transition.opacity.duration.150ms
                            class="rounded-lg border border-busitema-gold/50 bg-busitema-gold/10 px-4 py-3 text-sm leading-6 text-busitema-deep-blue"
                            id="password-assistance"
                            role="status"
                        >
                            Contact your UPAMS system administrator to securely reset your university account password.
                        </div>

                        <button
                            class="inline-flex min-h-12 w-full cursor-pointer items-center justify-center rounded-lg bg-busitema-gold px-5 py-3 text-base font-semibold text-busitema-navy shadow-sm transition hover:bg-busitema-yellow focus-visible:outline-3 focus-visible:outline-offset-2 focus-visible:outline-busitema-blue active:translate-y-px"
                            type="submit"
                        >
                            Sign in to UPAMS
                        </button>
                    </form>
                </div>

                <p class="mt-8 text-center text-sm text-body-text/80">
                    &copy; {{ now()->year }} Busitema University. Authorized access only.
                </p>
            </div>
        </section>
    </div>
@endsection
