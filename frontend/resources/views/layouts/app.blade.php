<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-slate-300 font-mono min-h-screen antialiased flex flex-col">

    {{-- Header de Navegação Terminal --}}
    <header class="border-b border-slate-800 bg-slate-950 p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="{{ route('prompts.index') }}" class="text-emerald-500 font-bold hover:text-emerald-400">
                >_ ~/prompts
            </a>
            
            <nav class="flex gap-4 text-sm">
                @auth
                    <a href="{{ route('prompts.manage') }}" class="text-slate-400 hover:text-slate-200">[admin]</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-400">[logout]</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-slate-400 hover:text-slate-200">[login]</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="flex-grow max-w-7xl mx-auto w-full p-4 sm:p-6 lg:p-8">
        {{ $slot }}
    </main>

</body>
</html>
