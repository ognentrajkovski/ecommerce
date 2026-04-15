<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use function Livewire\Volt\{rules, state};

state([
    'email' => '',
    'password' => '',
]);

rules([
    'email' => ['required', 'email'],
    'password' => ['required', 'string'],
]);

$login = function () {
    $validated = $this->validate();

    if (! Auth::attempt($validated)) {
        $this->addError('email', 'The provided credentials are incorrect.');

        return;
    }

    Session::regenerate();

    $this->redirect('/', navigate: true);
};
?>

<div class="mx-auto mt-12 max-w-md rounded-lg border p-6 shadow-sm">
    <h1 class="mb-6 text-2xl font-semibold">Login</h1>

    <form wire:submit="login" class="space-y-4">
        <div>
            <label for="email" class="mb-1 block text-sm font-medium">Email</label>
            <input id="email" type="email" wire:model="email" class="w-full rounded-md border px-3 py-2" />
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm font-medium">Password</label>
            <input id="password" type="password" wire:model="password" class="w-full rounded-md border px-3 py-2" />
            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full rounded-md bg-black px-4 py-2 text-white">Sign in</button>
    </form>
</div>
