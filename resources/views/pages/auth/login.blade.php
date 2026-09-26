<x-layouts::auth :title="'Entra'">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="'Entra nel tuo account'" :description="'Usa le credenziali Google per accedere'" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <flux:button variant="primary" class="w-full" href="{{ route('auth.email') }}">Entra con Google</flux:button>

        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-zinc-200 dark:border-zinc-700"></div>
            </div>
            <div class="relative flex justify-center text-xs uppercase">
                <span class="px-2 text-zinc-500 dark:text-zinc-400 bg-white dark:bg-zinc-900">
                    Oppure
                </span>
            </div>
        </div>
        <flux:button variant="primary" class="w-full" href="{{ route('auth.email') }}">Entra come admin</flux:button>
    </div>
</x-layouts::auth>