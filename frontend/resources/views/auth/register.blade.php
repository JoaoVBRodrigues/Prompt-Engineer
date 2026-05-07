<x-guest-layout>
    <x-slot:tabFile>register_prompt.sh</x-slot:tabFile>
    <form method="POST" action="{{ route('register') }}" class="font-mono" x-data="{
        showPassword: false,
        showConfirm: false,
        password: '',
        strength: 0,
        strengthLabel: '',
        strengthColor: '',
        calcStrength() {
            let s = 0;
            if (this.password.length >= 8) s++;
            if (this.password.length >= 12) s++;
            if (/[A-Z]/.test(this.password)) s++;
            if (/[0-9]/.test(this.password)) s++;
            if (/[^A-Za-z0-9]/.test(this.password)) s++;
            this.strength = s;
            const levels = [
                { label: '', color: '' },
                { label: 'Fraca', color: '#ef4444' },
                { label: 'Razoável', color: '#eab308' },
                { label: 'Boa', color: '#f97316' },
                { label: 'Forte', color: '#22c55e' },
                { label: 'Excelente', color: '#10b981' },
            ];
            this.strengthLabel = levels[s]?.label ?? '';
            this.strengthColor = levels[s]?.color ?? '';
        }
    }">
        @csrf

        {{-- Header terminal --}}
        <div class="mb-6 text-emerald-500 font-bold">
            &gt;_ auth --register
        </div>

        {{-- Nome --}}
        <div>
            <x-input-label for="name" :value="__('Nome de usuário')" />
            <x-text-input
                id="name"
                class="block mt-1 w-full"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
                placeholder="seu_nome_aqui"
                maxlength="255"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Email --}}
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username"
                placeholder="user@prompts.local"
                maxlength="255"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Senha --}}
        <div class="mt-4" x-data>
            <x-input-label for="password" :value="__('Senha')" />
            <div class="relative mt-1">
                <x-text-input
                    id="password"
                    class="block w-full pr-10"
                    :type="$showPassword ?? 'password'"
                    x-bind:type="showPassword ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="mínimo 8 caracteres"
                    x-model="password"
                    @input="calcStrength()"
                />
                {{-- Toggle show/hide --}}
                <button
                    type="button"
                    tabindex="-1"
                    class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-500 hover:text-emerald-400 transition-colors"
                    @click="showPassword = !showPassword"
                    :aria-label="showPassword ? 'Ocultar senha' : 'Mostrar senha'"
                >
                    <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/></svg>
                </button>
            </div>

            {{-- Indicador de força da senha --}}
            <div class="mt-2" x-show="password.length > 0" x-transition>
                <div class="flex gap-1 mb-1">
                    <template x-for="i in 5">
                        <div
                            class="h-1 flex-1 rounded-full transition-all duration-300"
                            :style="i <= strength ? 'background-color:' + strengthColor : 'background-color:#1e293b'"
                        ></div>
                    </template>
                </div>
                <span class="text-xs" :style="'color:' + strengthColor" x-text="'// força: ' + strengthLabel"></span>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirmar Senha --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar Senha')" />
            <div class="relative mt-1">
                <x-text-input
                    id="password_confirmation"
                    class="block w-full pr-10"
                    x-bind:type="showConfirm ? 'text' : 'password'"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="repita a senha"
                />
                <button
                    type="button"
                    tabindex="-1"
                    class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-500 hover:text-emerald-400 transition-colors"
                    @click="showConfirm = !showConfirm"
                    :aria-label="showConfirm ? 'Ocultar confirmação' : 'Mostrar confirmação'"
                >
                    <svg x-show="!showConfirm" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="showConfirm" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Termos e aviso de verificação --}}
        <div class="mt-5 p-3 bg-slate-900 border border-slate-800 rounded-sm text-xs text-slate-500 leading-relaxed">
            <span class="text-emerald-600">[INFO]</span>
            Após o cadastro, você receberá um <span class="text-slate-400">email de verificação</span>.
            O acesso completo à biblioteca será liberado somente após confirmá-lo.
        </div>

        {{-- Ações --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-6 gap-4">
            <a
                class="text-sm text-slate-500 hover:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-colors"
                href="{{ route('login') }}"
            >
                [{{ __('Já possui conta? Entrar') }}]
            </a>

            <x-primary-button class="w-full sm:w-auto justify-center" id="btn-register">
                {{ __('Criar Conta') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
