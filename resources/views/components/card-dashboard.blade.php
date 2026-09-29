<div class="group relative rounded-2xl border border-gray-200 bg-white p-6 hover:border-accent hover:shadow-xl transition-all duration-300 overflow-hidden">
    <!-- Gradient Background on Hover -->
    <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-accent/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    
    <!-- Content -->
    <div class="relative z-10">
        <!-- Icon and Title -->
        <div class="flex items-start justify-between mb-4">
            <flux:text size="sm" class="font-semibold text-gray-600 uppercase tracking-wide">
                {{ $title }}
            </flux:text>
            @if ($icon ?? false)
                <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-br from-accent to-accent/80 shadow-md">
                    {{ $icon }}
                </div>
            @endif
        </div>

        <!-- Value with Gradient -->
        <flux:heading size="2xl" class="mt-0 mb-4 text-transparent bg-gradient-to-r from-accent to-accent/70 bg-clip-text">
            {{ $value }}
        </flux:heading>

        <!-- Link -->
        <flux:link href="{{ $href }}" class="text-sm inline-flex items-center gap-1 font-medium text-accent hover:text-accent/80 transition-colors">
            {{ $label }}
            <flux:icon.chevron-right variant="micro" />
        </flux:link>
    </div>
</div>