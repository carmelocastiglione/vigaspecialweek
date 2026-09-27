<flux:card class="py-4">
    <flux:text size="lg" class="font-medium" variant="subtle">
        {{ $title }}
    </flux:text>

    <flux:heading size="xl" class="mt-0">
        {{ $value }}
    </flux:heading>

    <flux:link href="{{ $href }}" class="text-sm inline-flex items-center gap-1" variant="subtle">
        {{ $label }}
        <flux:icon.chevron-right variant="micro" />
    </flux:link>
</flux:card>