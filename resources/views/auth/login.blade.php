<x-guest-layout>
    <div class="mb-6 mt-4 flex flex-col items-center">
    <!-- Ícono/Logo -->
    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-700" fill="none" viewBox="0 0 24 24"
         stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M5 13c0 4 3 7 7 7 4.418 0 8-3.582 8-8 0-4-3-7-7-7-3 0-6 2-7 5m0 0c3 0 5-2 5-5" />
    </svg>
    <!-- Nombre debajo del logo -->
    <span class="mt-1 font-extrabold text-xl text-green-700">
        {{ config('app.name', 'EcoGestión') }}
    </span>
</div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end mt-4 gap-2">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            @if (Route::has('register'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}">
                    {{ __('Need an account? Register') }}
                </a>
            @endif

            <x-primary-button class="sm:ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
