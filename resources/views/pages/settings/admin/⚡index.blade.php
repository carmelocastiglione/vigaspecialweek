<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<section class="w-full">
    @include('partials.settings-admin-heading')

    <flux:heading level="2" class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-pages::settings.admin.layout heading="Impostazioni Generali" subheading="Modifica le impostazioni generali del sito">

    </x-pages::settings.admin.layout>
</section>