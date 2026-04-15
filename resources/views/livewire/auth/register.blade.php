<?php

use App\Domain\IdentityAndAccess\Actions\RegisterUserAction;
use App\Domain\IdentityAndAccess\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use function Livewire\Volt\{layout, rules, state};

layout('components.layouts.app');

state([
    'name' => '',
    'email' => '',
    'password' => '',
    'password_confirmation' => '',
    'role' => UserRole::Buyer->value,
]);

rules([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
    'password' => ['required', 'string', 'confirmed', 'min:8'],
    'role' => ['required', 'in:buyer,vendor'],
]);

$register = function (RegisterUserAction $registerUserAction) {
    $validated = $this->validate();

    $user = $registerUserAction->execute(
        name: $validated['name'],
        email: $validated['email'],
        password: $validated['password'],
        role: $validated['role'],
    );

    Auth::login($user);

    $this->redirectRoute('market.index');
};
?>

<div class="mx-auto mt-12 max-w-md rounded-lg border p-6 shadow-sm">
    <h1 class="mb-6 text-2xl font-semibold">Create account</h1>

    <form wire:submit="register" class="space-y-4">
        <div>
            <label for="name" class="mb-1 block text-sm font-medium">Name</label>
            <input id="name" type="text" wire:model="name" class="w-full rounded-md border px-3 py-2" />
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="mb-1 block text-sm font-medium">Email</label>
            <input id="email" type="email" wire:model="email" class="w-full rounded-md border px-3 py-2" />
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="role" class="mb-1 block text-sm font-medium">Role</label>
            <select id="role" wire:model="role" class="w-full rounded-md border px-3 py-2">
                <option value="buyer">Buyer</option>
                <option value="vendor">Vendor</option>
            </select>
            @error('role') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm font-medium">Password</label>
            <input id="password" type="password" wire:model="password" class="w-full rounded-md border px-3 py-2" />
            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-medium">Confirm password</label>
            <input id="password_confirmation" type="password" wire:model="password_confirmation" class="w-full rounded-md border px-3 py-2" />
        </div>

        <button type="submit" class="w-full rounded-md bg-black px-4 py-2 text-white">Register</button>
    </form>
</div>
