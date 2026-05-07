<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-black text-slate-300 font-mono min-h-screen antialiased flex flex-col items-center justify-center relative overflow-hidden">
        {{-- Efeito de grid de fundo --}}
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#0f172a_1px,transparent_1px),linear-gradient(to_bottom,#0f172a_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-20 pointer-events-none"></div>

        <div class="relative z-10 w-full sm:max-w-md px-4 flex flex-col items-center">
            <div class="mb-8">
                <a href="/" class="text-3xl font-bold tracking-tighter text-slate-100 flex items-center justify-center">
                    <span class="text-emerald-500">>_</span> <span class="ml-2">auth</span>
                </a>
            </div>

            <div class="w-full bg-slate-950 border border-slate-800 rounded-lg shadow-2xl overflow-hidden">
                <div class="bg-slate-900 border-b border-slate-800 px-4 py-2 flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    <span class="ml-2 text-xs text-slate-500">{{ $tabFile ?? 'auth_prompt.sh' }}</span>
                </div>
                <div class="p-6 text-sm text-slate-300">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
