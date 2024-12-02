<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex justify-center items-center max-s-screen bg-gray-100">
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-md">
        <div class="flex justify-center items-center">
            <img src="{{ asset('storage/images/login_logo.jpg') }}" alt="" width="50%">
        </div>
        <h1 class="text-2xl font-bold text-center mb-6">Iniciar Sesión</h1>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form wire:submit="login">
            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                <input 
                    wire:model="form.email" 
                    id="email" 
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                    type="email" 
                    name="email" 
                    required 
                    autofocus 
                    autocomplete="username" 
                />
                <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input 
                    wire:model="form.password" 
                    id="password" 
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="current-password" 
                />
                <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
            </div>

            <!-- Register and Forgot Password Links -->
            <div class="flex justify-between items-center mt-4">
                <a class="text-sm text-green-600 hover:underline" href="{{ route('register') }}" wire:navigate>
                    Registrarse
                </a>
                @if (Route::has('password.request'))
                    <a class="text-sm text-green-600 hover:underline" href="{{ route('password.request') }}" wire:navigate>
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <button 
                    type="submit" 
                    class="w-full px-4 py-2 text-white bg-green-600 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Iniciar Sesión
                </button>
            </div>
        </form>
    </div>
</div>

