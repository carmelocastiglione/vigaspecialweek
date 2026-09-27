<div class="flex items-start justify-between mb-6">
    <div>
        <flux:heading size="xl" level="1">{{ $title }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ $description }}</flux:subheading>
    </div>
    <div>
        @if ($createRoute)
            <flux:button href="{{ route($createRoute) }}" icon="plus">
                {{ $createLabel }}
            </flux:button>
        @endif
    </div>
</div>