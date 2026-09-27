<x-layouts::auth :title="'Entra'">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="'Entra nel tuo account'" :description="'Usa le credenziali Google per accedere'" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" :error="session('error')" />

        <flux:button variant="primary" class="w-full" href="{{ route('auth.google.redirect') }}">Entra con Google</flux:button>
        <flux:text class="text-center">
            Accesso per tutti gli account <span class="font-bold">issvigano.org</span>
        </flux:text>

        <flux:separator text="OPPURE" />

        <flux:button variant="primary" class="w-full" href="{{ route('auth.email') }}">Entra con email e password</flux:button>
        <flux:text class="text-center">
            Usa questa opzione se ti è stata fornita un'email e una password per accedere
        </flux:text>
    </div>
</x-layouts::auth>