<div class="max-w-4xl mx-auto space-y-8">
    <div class="border-b border-slate-800 pb-6 flex items-center justify-between">
        <div class="flex items-start gap-3">
            <span class="text-emerald-400 text-lg select-none mt-0.5">$</span>
            <div>
                <h1 class="text-slate-100 text-xl font-bold tracking-tight">
                    {{ $promptId ? 'nano prompt_edit.sh' : 'nano prompt_new.sh' }}
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    {{ $promptId ? 'Editando o prompt #' . $promptId : 'Criando um novo prompt no repositório.' }}
                </p>
            </div>
        </div>
        <a href="{{ route('prompts.manage') }}" class="text-slate-500 hover:text-slate-300 text-sm">
            [voltar]
        </a>
    </div>

    @if (session('error'))
        <div class="p-4 bg-red-950/40 border border-red-900 text-red-400 text-sm">
            [error] {{ session('error') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        {{-- Título --}}
        <div class="space-y-2">
            <label class="block text-slate-400 text-sm">TITLE="<span class="text-emerald-500">*</span>"</label>
            <input
                type="text"
                wire:model="titulo"
                placeholder="Ex: Gerador de Componentes React"
                class="w-full bg-slate-900 border @error('titulo') border-red-500 @else border-slate-800 @enderror text-slate-200 text-sm px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
            >
            @error('titulo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Estrutura --}}
        <div class="space-y-2">
            <label class="block text-slate-400 text-sm">PROMPT_BODY="<span class="text-emerald-500">*</span>"</label>
            <textarea
                wire:model="estrutura"
                rows="8"
                placeholder="Atue como..."
                class="w-full bg-slate-900 border @error('estrutura') border-red-500 @else border-slate-800 @enderror text-slate-200 text-sm px-4 py-2.5 focus:outline-none focus:border-emerald-500/50 font-mono"
            ></textarea>
            @error('estrutura') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Variáveis e Exemplo (Grid) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="block text-slate-400 text-sm">VARIABLES (separadas por vírgula)</label>
                <input
                    type="text"
                    wire:model="variaveis"
                    placeholder="tecnologia, contexto, linguagem"
                    class="w-full bg-slate-900 border border-slate-800 text-slate-200 text-sm px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
                >
            </div>
            <div class="space-y-2">
                <label class="block text-slate-400 text-sm">EXPECTED_OUTPUT</label>
                <input
                    type="text"
                    wire:model="exemplo"
                    placeholder="Exemplo do resultado esperado"
                    class="w-full bg-slate-900 border border-slate-800 text-slate-200 text-sm px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
                >
            </div>
        </div>

        {{-- Taxonomias (Técnica e Engine) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 border border-slate-800 bg-slate-900/50">
            <div class="space-y-3">
                <label class="block text-slate-400 text-sm">--tecnica</label>
                <div class="space-y-2 max-h-40 overflow-y-auto">
                    @foreach ($tecnicasDisponiveis as $tec)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="selectedTecnicas" value="{{ $tec['id'] }}" class="bg-slate-900 border-slate-700 text-emerald-500 focus:ring-0 rounded-none">
                            <span class="text-slate-300 text-sm">{{ $tec['name'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="space-y-3">
                <label class="block text-slate-400 text-sm">--engine</label>
                <div class="space-y-2 max-h-40 overflow-y-auto">
                    @foreach ($enginesDisponiveis as $eng)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="selectedEngines" value="{{ $eng['id'] }}" class="bg-slate-900 border-slate-700 text-violet-500 focus:ring-0 rounded-none">
                            <span class="text-slate-300 text-sm">{{ $eng['name'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Ações --}}
        <div class="flex justify-end gap-4 pt-6 border-t border-slate-800">
            <a href="{{ route('prompts.manage') }}" class="px-6 py-2.5 border border-slate-700 text-slate-400 hover:text-slate-200 hover:bg-slate-800 transition-colors text-sm">
                [ Cancelar ]
            </a>
            <button type="submit" class="px-6 py-2.5 bg-emerald-950 border border-emerald-900 text-emerald-400 hover:bg-emerald-900 hover:text-emerald-300 transition-colors text-sm">
                [ Gravar Arquivo (Ctrl+O) ]
            </button>
        </div>
    </form>
</div>
