<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<flux:modal name="register-modal" class="md:w-96">
    <div class="space-y-6">
        <x-auth-header :title="__('Register')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form wire:submit="register" class="flex flex-col gap-6">
            <flux:input wire:model="name" label="Name" type="text" required autofocus />
            <flux:input wire:model="email" label="Email" type="email" required />
            <flux:input wire:model="password" label="Password" type="password" required />
            <flux:input wire:model="password_confirmation" label="Confirm Password" type="password" required />

            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">Register</flux:button>
            </div>
        </form>
    </div>
</flux:modal>