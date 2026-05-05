<div
    x-data="{
        search: '',
        copied: null,
        selectedPrompt: null,
        copyPrompt(id, text) {
            navigator.clipboard.writeText(text).then(() => {
                this.copied = id;
                setTimeout(() => this.copied = null, 2000);
            });
        }
    }"
    class="space-y-8"
>
    {{-- ─── Cabeçalho da seção ──────────────────────────────────────────── --}}
    <div class="border-b border-slate-800 pb-6">
        <div class="flex items-start gap-3">
            <span class="text-emerald-400 text-lg select-none mt-0.5">$</span>
            <div>
                <h1 class="text-slate-100 text-xl font-bold tracking-tight">
                    prompt --list
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    Repositório de templates de engenharia de prompts para IA.
                    Filtre por técnica ou engine e copie com um clique.
                </p>
            </div>
        </div>
    </div>

    {{-- ─── Barra de filtros + busca ───────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row gap-3">

        {{-- Busca client-side via Alpine.js (sem roundtrip ao servidor) --}}
        <div class="relative flex-1">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-600 text-xs select-none pointer-events-none">grep</span>
            <input
                type="text"
                x-model="search"
                placeholder="&quot;chain of thought&quot; | &quot;claude&quot; ..."
                class="w-full bg-slate-900 border border-slate-800 text-slate-300 text-sm pl-12 pr-4 py-2.5 placeholder-slate-700
                       focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-colors duration-200"
            >
        </div>

        {{-- Filtro: Técnica (Livewire reactive) --}}
        <select
            wire:model.live="tecnica"
            class="bg-slate-900 border border-slate-800 text-slate-400 text-sm px-3 py-2.5
                   focus:outline-none focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/20 transition-colors duration-200
                   cursor-pointer appearance-none pr-8"
        >
            <option value="">--tecnica=*</option>
            @foreach ($this->tecnicas as $term)
                <option value="{{ $term['slug'] }}">{{ $term['name'] }} ({{ $term['count'] }})</option>
            @endforeach
        </select>

        {{-- Filtro: Engine (Livewire reactive) --}}
        <select
            wire:model.live="engine"
            class="bg-slate-900 border border-slate-800 text-slate-400 text-sm px-3 py-2.5
                   focus:outline-none focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 transition-colors duration-200
                   cursor-pointer appearance-none pr-8"
        >
            <option value="">--engine=*</option>
            @foreach ($this->engines as $term)
                <option value="{{ $term['slug'] }}">{{ $term['name'] }} ({{ $term['count'] }})</option>
            @endforeach
        </select>

        {{-- Botão limpar filtros --}}
        @if ($tecnica || $engine)
            <button
                wire:click="clearFilters"
                class="text-xs text-slate-600 hover:text-red-400 border border-slate-800 hover:border-red-900 px-3 py-2.5
                       transition-colors duration-200 whitespace-nowrap"
            >
                [x] limpar
            </button>
        @endif
    </div>

    {{-- ─── Loading state (Livewire wire:loading) ──────────────────────── --}}
    <div wire:loading.flex class="items-center gap-2 text-xs text-slate-600">
        <span class="inline-block w-3 h-3 border border-emerald-500 border-t-transparent animate-spin rounded-full"></span>
        <span>Consultando API WordPress...</span>
    </div>

    {{-- ─── Grid de cards de prompts ────────────────────────────────────── --}}
    <div
        wire:loading.class="opacity-40 pointer-events-none"
        class="transition-opacity duration-200"
    >
        @php
            // Filtragem textual client-side acontece via Alpine x-show no card.
            // O $prompts já vem filtrado por taxonomia do Livewire/Service.
            $hasPrompts = count($this->prompts) > 0;
        @endphp

        @if (! $hasPrompts)
            {{-- Estado vazio --}}
            <div class="border border-slate-800 bg-slate-950 p-8 text-center">
                <p class="text-slate-600 text-sm font-mono">
                    <span class="text-red-500">error</span>: nenhum prompt encontrado para os filtros selecionados.
                </p>
                <p class="text-slate-700 text-xs mt-2">
                    Verifique se a API WordPress está acessível em
                    <span class="text-cyan-700">{{ config('services.wordpress.api_url') }}</span>
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach ($this->prompts as $prompt)
                    @php
                        $id           = $prompt['id'] ?? 0;
                        $titulo       = $prompt['title']['rendered'] ?? 'Sem título';
                        $acf          = $prompt['acf'] ?? [];
                        $estrutura    = $acf['estrutura_prompt'] ?? '';
                        $variaveis    = $acf['variaveis_necessarias'] ?? [];
                        $exemplo      = $acf['exemplo_saida'] ?? '';

                        // Extrai taxonomias do _embedded
                        $termos       = $prompt['_embedded']['wp:term'] ?? [];
                        $tecnicas     = $termos[0] ?? [];
                        $engines      = $termos[1] ?? [];
                    @endphp

                    {{-- Card: controlado por x-show para busca textual client-side --}}
                    <div
                        @click='selectedPrompt = { id: {{ $id }}, title: {!! json_encode($titulo) !!}, structure: {!! json_encode($estrutura) !!}, example: {!! json_encode($exemplo) !!} }; $wire.loadComments({{ $id }}); document.body.style.overflow = "hidden"'
                        x-show="search === '' || {!! json_encode(strtolower($titulo)) !!}.includes(search.toLowerCase()) || {!! json_encode(strtolower($estrutura)) !!}.includes(search.toLowerCase())"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="bg-slate-900 border border-slate-800 hover:border-emerald-500/30 transition-colors duration-200 flex flex-col cursor-pointer"
                    >
                        {{-- Cabeçalho do card --}}
                        <div class="px-4 py-3 border-b border-slate-800 flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <h2 class="text-slate-200 text-sm font-semibold leading-tight truncate">
                                    {{ $titulo }}
                                </h2>
                                {{-- Tags de taxonomias --}}
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    @foreach ($tecnicas as $tecnicaTerm)
                                        <span class="inline-block text-xs px-1.5 py-0.5 border border-emerald-900/60 text-emerald-400 bg-emerald-950/40">
                                            {{ $tecnicaTerm['name'] ?? '' }}
                                        </span>
                                    @endforeach
                                    @foreach ($engines as $engineTerm)
                                        <span class="inline-block text-xs px-1.5 py-0.5 border border-violet-900/60 text-violet-400 bg-violet-950/40">
                                            {{ $engineTerm['name'] ?? '' }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Botão Copiar (Alpine.js — sem roundtrip ao servidor) --}}
                            <button
                                @click.stop='copyPrompt({{ $id }}, {!! json_encode($estrutura) !!})'
                                class="shrink-0 text-xs border px-2 py-1 transition-all duration-200"
                                :class="copied === {{ $id }}
                                    ? 'border-emerald-500 text-emerald-400 bg-emerald-950/40'
                                    : 'border-slate-700 text-slate-500 hover:border-cyan-700 hover:text-cyan-400'"
                            >
                                <span x-show="copied !== {{ $id }}">[ copiar ]</span>
                                <span x-show="copied === {{ $id }}" x-cloak>[ ✓ copiado ]</span>
                            </button>
                        </div>

                        {{-- Corpo: estrutura do prompt --}}
                        <div class="px-4 py-3 flex-1">
                            @if ($estrutura)
                                <pre class="text-xs text-slate-400 leading-relaxed whitespace-pre-wrap break-words font-mono overflow-hidden line-clamp-5">{{ $estrutura }}</pre>
                            @else
                                <p class="text-xs text-slate-700 italic">// sem estrutura definida</p>
                            @endif
                        </div>

                        {{-- Rodapé do card: variáveis necessárias --}}
                        @if (! empty($variaveis))
                            <div class="px-4 py-2.5 border-t border-slate-800 flex flex-wrap gap-1.5">
                                <span class="text-xs text-slate-700 mr-1">vars:</span>
                                @foreach ($variaveis as $variavel)
                                    <span class="text-xs text-cyan-500/80 font-mono">
                                        {{"{"}}{{ $variavel }}{{"}"}}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ─── Rodapé de resultados ────────────────────────────────────────── --}}
    <div class="text-xs text-slate-700 border-t border-slate-900 pt-4">
        <span class="text-slate-600">{{ count($this->prompts) }}</span> prompts retornados
        @if ($tecnica || $engine)
            <span class="text-slate-700">
                · filtro:
                @if ($tecnica) <span class="text-emerald-700">--tecnica={{ $tecnica }}</span> @endif
                @if ($engine)  <span class="text-violet-700"> --engine={{ $engine }}</span>  @endif
            </span>
        @endif
        <span class="float-right text-slate-800">cache: {{ config('services.wordpress.cache_ttl') }}s</span>
    </div>

    {{-- ─── Modal de Detalhes do Prompt ───────────────────────────────────── --}}
    <div
        x-show="selectedPrompt !== null"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
        role="dialog"
        aria-modal="true"
    >
        {{-- Overlay escura --}}
        <div
            x-show="selectedPrompt !== null"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/80 backdrop-blur-sm"
            @click="selectedPrompt = null; document.body.style.overflow = ''"
        ></div>

        {{-- Container do Modal (Terminal look) --}}
        <div
            x-show="selectedPrompt !== null"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative w-full max-w-4xl bg-slate-950 border border-slate-700 shadow-2xl rounded-lg overflow-hidden flex flex-col max-h-full"
            @click.stop
        >
            {{-- Header do Terminal Modal --}}
            <div class="bg-slate-900 border-b border-slate-800 px-4 py-3 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    <span class="ml-2 text-xs text-slate-400 font-mono" x-text="selectedPrompt ? selectedPrompt.title + '.sh' : ''"></span>
                </div>
                <button @click="selectedPrompt = null; document.body.style.overflow = ''" class="text-slate-500 hover:text-red-400 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Conteúdo (Scrollable) --}}
            <div class="p-6 overflow-y-auto font-mono text-sm space-y-6">
                
                {{-- Title / Header Command --}}
                <div class="text-emerald-500 font-bold text-lg mb-4">
                    >_ cat <span x-text="selectedPrompt ? selectedPrompt.title : ''"></span>
                </div>

                {{-- Estrutura do Prompt --}}
                <div>
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2 mb-3">
                        <h3 class="text-slate-500 uppercase tracking-wider text-xs">Estrutura do Prompt</h3>
                        <button
                            @click="copyPrompt(selectedPrompt.id, selectedPrompt.structure)"
                            class="text-xs border px-2 py-1 transition-all duration-200"
                            :class="copied === selectedPrompt?.id
                                ? 'border-emerald-500 text-emerald-400 bg-emerald-950/40'
                                : 'border-slate-700 text-slate-500 hover:border-cyan-700 hover:text-cyan-400'"
                        >
                            <span x-show="copied !== selectedPrompt?.id">[ copiar prompt ]</span>
                            <span x-show="copied === selectedPrompt?.id" x-cloak>[ ✓ copiado ]</span>
                        </button>
                    </div>
                    <div class="bg-slate-900/50 p-4 rounded border border-slate-800/50">
                        <pre class="text-slate-300 leading-relaxed whitespace-pre-wrap break-words font-mono" x-text="selectedPrompt ? selectedPrompt.structure : ''"></pre>
                    </div>
                </div>

                {{-- Exemplo de Saída --}}
                <div x-show="selectedPrompt && selectedPrompt.example">
                    <h3 class="text-slate-500 uppercase tracking-wider text-xs border-b border-slate-800 pb-2 mb-3 mt-6">Exemplo de Saída Esperada</h3>
                    <div class="bg-slate-900/30 p-4 rounded border border-slate-800/30 border-l-4 border-l-emerald-600/50">
                        <pre class="text-emerald-500/80 leading-relaxed whitespace-pre-wrap break-words font-mono text-xs" x-text="selectedPrompt ? selectedPrompt.example : ''"></pre>
                    </div>
                </div>

                {{-- Seção de Comentários --}}
                <div x-show="selectedPrompt" class="mt-8 pt-6 border-t border-slate-800">
                    <h3 class="text-slate-500 uppercase tracking-wider text-xs mb-4">
                        Comentários (<span x-text="$wire.comments.length"></span>)
                    </h3>
                    
                    {{-- Loading State para comentários --}}
                    <div wire:loading wire:target="loadComments" class="text-xs text-slate-500 flex items-center gap-2 mb-4">
                        <span class="inline-block w-3 h-3 border border-emerald-500 border-t-transparent animate-spin rounded-full"></span>
                        Carregando comentários...
                    </div>

                    {{-- Lista de Comentários --}}
                    <div wire:loading.remove wire:target="loadComments" class="space-y-4">
                        @if(empty($comments))
                            <p class="text-slate-600 text-xs italic">Nenhum comentário ainda. Seja o primeiro a comentar!</p>
                        @else
                            @foreach($comments as $comment)
                                <div class="bg-slate-900/40 p-4 border border-slate-800/50 rounded flex flex-col gap-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-emerald-400 font-bold text-xs">{{ $comment['author_name'] ?? 'Anônimo' }}</span>
                                        <span class="text-slate-600 text-[10px]">{{ isset($comment['date']) ? \Carbon\Carbon::parse($comment['date'])->diffForHumans() : '' }}</span>
                                    </div>
                                    <div class="text-slate-300 text-xs leading-relaxed whitespace-pre-wrap break-words">
                                        {!! strip_tags($comment['content']['rendered'] ?? '') !!}
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    {{-- Formulário de Comentário --}}
                    @auth
                        <div class="mt-6 flex flex-col gap-2">
                            <textarea 
                                wire:model="newComment" 
                                required 
                                rows="3"
                                class="w-full bg-slate-900 border border-slate-700 text-slate-300 text-sm p-3 focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-colors" 
                                placeholder="Deixe seu comentário sobre este prompt..."
                            ></textarea>
                            <button 
                                wire:click="submitComment(selectedPrompt.id)"
                                wire:loading.attr="disabled"
                                wire:target="submitComment"
                                class="self-end bg-emerald-900/50 text-emerald-400 border border-emerald-800 px-6 py-2 text-xs hover:bg-emerald-800/50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-mono"
                            >
                                <span wire:loading.remove wire:target="submitComment">[ Enviar Comentário ]</span>
                                <span wire:loading wire:target="submitComment">[ Enviando... ]</span>
                            </button>
                        </div>
                    @else
                        <div class="mt-6 p-4 bg-slate-900/30 border border-slate-800 text-center rounded">
                            <p class="text-slate-500 text-xs">Você precisa estar autenticado para comentar.</p>
                            <a href="{{ route('login') }}" class="text-emerald-500 hover:text-emerald-400 text-xs mt-2 inline-block font-mono border-b border-emerald-500/30">
                                [ auth --login ]
                            </a>
                        </div>
                    @endauth
                </div>

            </div>
            
            {{-- Footer do Modal --}}
            <div class="bg-slate-900 border-t border-slate-800 px-6 py-4 text-right shrink-0">
                <button 
                    @click="selectedPrompt = null; document.body.style.overflow = ''"
                    class="px-4 py-2 bg-slate-800 border border-slate-700 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors font-mono text-sm"
                >
                    [ Fechar ]
                </button>
            </div>
        </div>
    </div>
</div>
