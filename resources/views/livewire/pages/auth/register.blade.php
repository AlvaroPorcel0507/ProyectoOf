<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $lastName = '';
    public string $secondLastName = '';
    public string $email = '';
    public string $location = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'secondLastName' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'location' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Determinar el rol
        $validated['role'] = 'Cliente';

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
};
?>
<div class="flex justify-center items-center max-s-screen bg-gray-100">
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-md">
        <div class="flex justify-center items-center">
            <img src="{{ asset('storage/images/register_logo.jpg') }}" alt="" width="50%">
        </div>
        <h1 class="text-2xl font-bold text-center mb-6">Registrarse</h1>

        <form wire:submit="register">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input 
                    wire:model="name" 
                    id="name" 
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                    type="text" 
                    name="name" 
                    required 
                    autofocus 
                    autocomplete="name" 
                />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Last Name -->
            <div class="mt-4">
                <label for="lastName" class="block text-sm font-medium text-gray-700">Primer Apellido</label>
                <input 
                    wire:model="lastName" 
                    id="lastName" 
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                    type="text" 
                    name="lastName" 
                    required 
                />
                <x-input-error :messages="$errors->get('lastName')" class="mt-2" />
            </div>

            <!-- Second Last Name -->
            <div class="mt-4">
                <label for="secondLastName" class="block text-sm font-medium text-gray-700">Segundo Apellido</label>
                <input 
                    wire:model="secondLastName" 
                    id="secondLastName" 
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                    type="text" 
                    name="secondLastName" 
                />
                <x-input-error :messages="$errors->get('secondLastName')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                <input 
                    wire:model="email" 
                    id="email" 
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                    type="email" 
                    name="email" 
                    required 
                    autocomplete="username" 
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Location -->
            <div class="mt-4">
                <label for="location" class="block text-sm font-medium text-gray-700">Dirección</label>
                <input 
                    wire:model="location" 
                    id="location" 
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                    type="text" 
                    name="location" 
                    required 
                />
                <x-input-error :messages="$errors->get('location')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input 
                    wire:model="password" 
                    id="password" 
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="new-password" 
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                <input 
                    wire:model="password_confirmation" 
                    id="password_confirmation" 
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                    type="password" 
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password" 
                />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Register and Login Links -->
            <div class="flex justify-between items-center mt-4">
                <a class="text-sm text-green-600 hover:underline" href="{{ route('login') }}" wire:navigate>
                    ¿Ya tienes una cuenta?
                </a>
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <button 
                    type="submit" 
                    class="w-full px-4 py-2 text-white bg-green-600 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Registrarse
                </button>
            </div>
        </form>
    </div>
</div>
