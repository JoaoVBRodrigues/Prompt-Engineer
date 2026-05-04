<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-slate-300 font-mono min-h-screen antialiased flex flex-col items-center justify-center relative overflow-hidden">

    {{-- Efeito de grid de fundo --}}
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#0f172a_1px,transparent_1px),linear-gradient(to_bottom,#0f172a_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-20 pointer-events-none"></div>

    <div class="relative z-10 text-center space-y-8 px-4 max-w-3xl">
        {{-- Glitch Title --}}
        <h1 class="text-4xl sm:text-6xl font-bold tracking-tighter text-slate-100">
            <span class="text-emerald-500">>_</span> Engenharia de Prompts
        </h1>

        <p class="text-lg text-slate-400 max-w-2xl mx-auto leading-relaxed">
            Plataforma 100% Headless para armazenamento, curadoria e distribuição de
            <span class="text-emerald-400">templates avançados</span> de inteligência artificial.
        </p>

        {{-- Terminal mock window --}}
        <div class="bg-slate-950 border border-slate-800 rounded-lg shadow-2xl text-left overflow-hidden max-w-xl mx-auto">
            <div class="bg-slate-900 border-b border-slate-800 px-4 py-2 flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                <span class="ml-2 text-xs text-slate-500">system_init.sh</span>
            </div>
            <div class="p-6 text-sm text-slate-300 space-y-2">
                <p><span class="text-emerald-500">root@prompts:~#</span> systemctl start wordpress-headless</p>
                <p class="text-slate-500">[ OK ] Started WordPress Headless CMS via API.</p>
                <p><span class="text-emerald-500">root@prompts:~#</span> ./fetch_prompts --cache=true</p>
                <p class="text-cyan-400">✓ Conectado. 12 templates carregados na memória.</p>
                <p><span class="text-emerald-500">root@prompts:~#</span> <span class="terminal-cursor"></span></p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-8">
            <a href="{{ route('prompts.index') }}" class="w-full sm:w-auto px-8 py-3 bg-emerald-950 border border-emerald-900 text-emerald-400 hover:bg-emerald-900 hover:text-emerald-300 transition-colors font-medium">
                [ Acessar Biblioteca ]
            </a>
            @auth
                <a href="{{ route('prompts.manage') }}" class="w-full sm:w-auto px-8 py-3 bg-slate-900 border border-slate-700 text-slate-300 hover:bg-slate-800 transition-colors">
                    [ sudo Painel Admin ]
                </a>
            @else
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3 bg-slate-900 border border-slate-700 text-slate-300 hover:bg-slate-800 transition-colors">
                    [ auth login ]
                </a>
            @endauth
        </div>
    </div>

</body>
</html>
