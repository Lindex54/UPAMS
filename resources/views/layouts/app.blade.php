<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'UPAMS')</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        class="min-h-screen overflow-x-hidden bg-light-background antialiased"
        x-data="{
            sidebarOpen: false,
            sidebarCollapsed: localStorage.getItem('upams-sidebar-collapsed') === 'true',
            toggleSidebar() {
                this.sidebarCollapsed = ! this.sidebarCollapsed;
                localStorage.setItem('upams-sidebar-collapsed', this.sidebarCollapsed);
            }
        }"
        @keydown.escape.window="sidebarOpen = false"
    >
        @hasSection('sidebar')
            @yield('sidebar')
        @else
            <x-sidebar />
        @endif

        <div
            class="min-h-screen pt-16 transition-[padding] duration-200 lg:pt-0"
            :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-72'"
        >
            <header class="flex min-h-20 items-center justify-between border-b border-border bg-white px-5 sm:px-8">
                <div>
                    <p class="text-xs font-semibold tracking-[0.14em] text-busitema-blue uppercase">@yield('portal-label', 'UPAMS Administration')</p>
                    <h1 class="mt-1 text-2xl font-semibold text-heading">@yield('page-heading', 'Dashboard')</h1>
                </div>

                <div class="hidden items-center gap-3 sm:flex">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-heading">{{ auth()->user()?->name ?? 'Design Preview' }}</p>
                        <p class="text-xs text-body-text">@yield('user-role', 'System Administrator')</p>
                    </div>
                    <div class="flex size-10 items-center justify-center rounded-full bg-busitema-blue text-sm font-semibold text-white" aria-hidden="true">
                        @auth
                            {{ str(auth()->user()->name)->substr(0, 1)->upper() }}
                        @else
                            @yield('user-initial', 'S')
                        @endauth
                    </div>
                </div>
            </header>

            <main class="p-5 sm:p-8">
                @yield('content')
            </main>
        </div>
    </body>
</html>
