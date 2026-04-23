<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
        <style>body { font-family: 'Inter', sans-serif; }</style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased" style="background-color:#f4f1e8;">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-6">
                <a href="/" wire:navigate class="text-2xl font-black tracking-tight" style="color:#1a3327;">MentorHouse</a>
            </div>

            <div class="w-full sm:max-w-md px-6 py-8 overflow-hidden sm:rounded-2xl" style="background:white; border:1px solid #e6e0d0; box-shadow:0 2px 12px rgba(26,51,39,.07);">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
