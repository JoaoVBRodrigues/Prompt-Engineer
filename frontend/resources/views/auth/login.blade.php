<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="font-mono">
        @csrf

        <div class="mb-6 text-emerald-500 font-bold">
            >_ auth --login
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="user@prompts.local" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="********" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="rounded-sm bg-slate-900 border-slate-700 text-emerald-500 shadow-sm focus:ring-emerald-500 focus:ring-offset-slate-900" name="remember">
                <span class="ms-2 text-sm text-slate-500 group-hover:text-emerald-400 transition-colors">{{ __('Lembrar de mim') }}</span>
            </label>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-8 gap-4">
            @if (Route::has('password.request'))
                <a class="text-sm text-slate-500 hover:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-colors" href="{{ route('password.request') }}">
                    [{{ __('Esqueceu sua senha?') }}]
                </a>
            @endif

            <x-primary-button class="w-full sm:w-auto justify-center">
                {{ __('Entrar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
