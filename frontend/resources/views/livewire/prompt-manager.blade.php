<div class="space-y-8">
    <div class="border-b border-slate-800 pb-6 flex items-center justify-between">
        <div class="flex items-start gap-3">
            <span class="text-emerald-400 text-lg select-none mt-0.5">$</span>
            <div>
                <h1 class="text-slate-100 text-xl font-bold tracking-tight">
                    sudo prompt --manage
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    Gerencie os prompts do sistema (Adicionar / Editar / Excluir).
                </p>
            </div>
        </div>
        <a href="{{ route('prompts.create') }}" class="px-4 py-2 bg-emerald-950/40 border border-emerald-900 text-emerald-400 text-sm hover:bg-emerald-900/60 transition-colors duration-200">
            [+] Novo Prompt
        </a>
    </div>

    @if (session('status'))
        <div class="p-4 bg-emerald-950/40 border border-emerald-900 text-emerald-400 text-sm">
            [success] {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="p-4 bg-red-950/40 border border-red-900 text-red-400 text-sm">
            [error] {{ session('error') }}
        </div>
    @endif

    <div class="bg-slate-900 border border-slate-800 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-950 border-b border-slate-800 text-slate-400">
                <tr>
                    <th class="px-4 py-3 font-medium">ID</th>
                    <th class="px-4 py-3 font-medium">Título</th>
                    <th class="px-4 py-3 font-medium">Técnicas</th>
                    <th class="px-4 py-3 font-medium">Engines</th>
                    <th class="px-4 py-3 font-medium text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-slate-300">
                @forelse ($prompts as $prompt)
                    @php
                        $id = $prompt['id'];
                        $titulo = $prompt['title']['rendered'];
                        $termos = $prompt['_embedded']['wp:term'] ?? [];
                        $tecnicas = $termos[0] ?? [];
                        $engines = $termos[1] ?? [];
                    @endphp
                    <tr class="hover:bg-slate-800/50 transition-colors">
                        <td class="px-4 py-3 text-slate-500">#{{ $id }}</td>
                        <td class="px-4 py-3 font-medium">{{ $titulo }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @foreach ($tecnicas as $term)
                                    <span class="text-xs text-emerald-500">{{ $term['name'] }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @foreach ($engines as $term)
                                    <span class="text-xs text-violet-500">{{ $term['name'] }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('prompts.edit', $id) }}" class="text-cyan-500 hover:text-cyan-400">[editar]</a>
                            <button
                                wire:click="deletePrompt({{ $id }})"
                                wire:confirm="Tem certeza que deseja excluir o prompt #{{ $id }}?"
                                class="text-red-500 hover:text-red-400"
                            >
                                [excluir]
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                            Nenhum prompt encontrado no banco.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
