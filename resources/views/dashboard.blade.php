@extends('layouts.app')

@section('title', 'Dashboard | UPAMS')
@section('page-heading', 'Dashboard')

@section('content')
    <section class="rounded-xl border border-border bg-white p-6 sm:p-8">
        <p class="text-sm font-semibold tracking-[0.12em] text-busitema-blue uppercase">System Administrator</p>
        <h2 class="mt-2 text-2xl font-semibold text-heading">Welcome to UPAMS</h2>
        <p class="mt-3 max-w-2xl text-base leading-7 text-body-text">
            Use the navigation to manage university property, operational records, finance, governance, and system administration.
        </p>
    </section>
@endsection
