<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        @include('partials.head')
        <title>{{ $title ?? config('app.name') }}</title>
    </head>
    <body class="min-h-screen antialiased bg-white text-zinc-900 dark:bg-zinc-950 dark:text-white">

        <x-admin-navbar />

        <main class="pt-16">
            <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

        <x-notification-toast />
        <x-delete-confirmation />

        @fluxScripts
    </body>
</html>
